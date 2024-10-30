<?php

/**
 * @Author: Codehaveli
 * @Date:   2020-05-19 10:56:57
 * @Last Modified by:   Codehaveli
 * @Website: www.codehaveli.com
 * @Email: hello@codehaveli.com
 * @Last Modified time: 2021-02-09 15:16:37
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class WbitlyURLSettings {

	private $bitly_url_options;

	public function __construct() {
		add_action( 'admin_menu', array( $this, 'wbitly_url_add_plugin_page' ) );
		add_action( 'admin_init', array( $this, 'wbitly_url_page_init' ) );
		add_action( 'init',       array( $this, 'wbitly_redirect_to_get_guid'));
	    add_action( 'admin_notices', array($this , 'show_success_when_getting_guid') );
	    add_action( 'admin_notices', array($this,  'show_error_when_getting_guid') );
	}

	/**
	 * Update GUID
	 */


	public function wbitly_redirect_to_get_guid() {
				if (current_user_can('administrator')) {
					$queryParam = 'wbitly';
					$queryParamDecoded = isset($_GET['page']) ? urldecode($_GET['page']) : '';
					if ($queryParam == $queryParamDecoded) {


						if(isset($_GET['wbitly_guid']) && urldecode($_GET['wbitly_guid']) == "update"){


							$status = $this->wbitly_save_bitly_guid();

							if($status){
								set_transient( 'wbitly_guid_success', true, 5 );
							}else{
								set_transient( 'wbitly_guid_error', true, 5 );
							}

							wp_redirect( WBITLY_SETTINGS_URL);
							die();
						}
					}
				}
	}


	/**
	 * Get GUID and update the option
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */

	public function wbitly_save_bitly_guid(){

		$access_token = $this->get_wbitly_access_token();

		if(!$access_token){
			return;
		}

		$guid = $this->wbitly_get_bitly_guid_request($access_token);


		if(!$guid){
			return false;
		}

		$bitly_url_options_from_db = get_option( 'wbitly_url_option_name' );
		$bitly_url_options_from_db['group_guid'] = trim($guid);
		$is_updated = update_option('wbitly_url_option_name' , $bitly_url_options_from_db , true);

		return true;


	}


	/**
	 * Send Http request and get the first GUID from account
	 *
	 * @param      <type>   $access_token  The access token
	 *
	 * @return     boolean or string || GUID from Bitly
	 */

	public function wbitly_get_bitly_guid_request($access_token){

		$response = false;

		try {


			$headers = array (
			      "Host"          => "api-ssl.bitly.com",
			      "Authorization" => "Bearer ".$access_token ,
			      "Content-Type"  => "application/json"
  			);


			$http_response = wp_remote_get(  WBITLY_API_URL . '/v4/groups' , array(
			      'timeout'     => 0,
			      'headers'     => $headers
			      )
  			);


  			if (!is_wp_error($http_response)) {

				$response_array = json_decode($http_response['body']);
				$groups         = isset($response_array->groups) ? $response_array->groups : [];
				$guid           = isset($groups[0]->guid) ? $groups[0]->guid : '';
				$response       = $guid;
             }else{

				$error     = $http_response->get_error_message();
				$pluginlog = plugin_dir_path(__FILE__).'error.log';
				$message   = $error . PHP_EOL;
				error_log($message, 3, $pluginlog);
             }

		} catch (Exception $e) {

			$pluginlog = plugin_dir_path(__FILE__).'debug.log';
			$message   = 'Unable to get Bitly GUID' . PHP_EOL;
			error_log($message, 3, $pluginlog);
		}

		return $response;

	}





	/**
	 * Shows the error when getting guid identifier.
	 */


	public function show_error_when_getting_guid() {
		if( get_transient( 'wbitly_guid_error' ) ):
	       
	       ?>
	       <div class="notice notice-error is-dismissible">
	          <p><?php _e( 'Unable to get Group Guid please check your Access Token', 'wbitly' );?></p>
	       </div> 
	       <?php 

	       delete_transient( 'wbitly_guid_error' );

	    endif;
	}

	/**
	 * Shows the success when getting unique identifier.
	 */

	public function show_success_when_getting_guid() {
		if( get_transient( 'wbitly_guid_success' ) ):
	       ?>
	       <div class="notice notice-success is-dismissible">
	          <p><?php _e( 'Group Guid Successfully Saved', 'wbitly' );?></p>
	       </div>
	       <?php
	       delete_transient( 'wbitly_guid_success' ); 
	     endif;
	}


	/**
	 * Add Plugin Page 
	 */

	public function wbitly_url_add_plugin_page() {
		add_management_page(
			'Codehaveli Bitly Settings', // page_title
			'Codehaveli Bitly', // menu_title
			'manage_options', // capability
			'wbitly', // menu_slug
			array( $this, 'wbitly_url_create_admin_page' ) // function
		);
	}

	/**
	 * Add Plugin Page Form 
	 */

	public function wbitly_url_create_admin_page() {
		$this->bitly_url_options = get_option( 'wbitly_url_option_name' ); ?>

		<div class="wrap">
 			<h2>Codehaveli Bitly Settings</h2>
			<?php settings_errors(); ?>

			<form method="post" action="options.php">
				<?php
					settings_fields( 'wbitly_url_option_group' );
					do_settings_sections( 'wbitly-url-admin' );
					submit_button();
				?>
			</form>
		</div>
	<?php }


	/**
	 * Add Input fields to Settings Page
	 */

	public function wbitly_url_page_init() {
		register_setting(
			'wbitly_url_option_group', // option_group
			'wbitly_url_option_name', // option_name
			array( $this, 'wbitly_url_sanitize' ) // sanitize_callback
		);

		add_settings_section(
			'wbitly_url_setting_section', // id
			'Settings', // title
			array( $this, 'wbitly_url_section_info' ), // callback
			'wbitly-url-admin' // page
		);

		add_settings_field(
			'access_token', // id
			'Access Token', // title
			array( $this, 'access_token_callback' ), // callback
			'wbitly-url-admin', // page
			'wbitly_url_setting_section' // section
		);

		add_settings_field(
			'group_guid', // id
			'Group Guid', // title
			array( $this, 'group_guid_callback' ), // callback
			'wbitly-url-admin', // page
			'wbitly_url_setting_section' // section
		);

		add_settings_field(
			'bitly_domain', // id
			'Domain (Optional)', // title
			array( $this, 'bitly_domain_callback' ), // callback
			'wbitly-url-admin', // page
			'wbitly_url_setting_section' // section
		);


		add_settings_field(
			'wbitly_socal_share', // id
			'Enable Social Share Button', // title
			array( $this, 'add_wbitly_social_share_button' ), // callback
			'wbitly-url-admin', // page
			'wbitly_url_setting_section' // section
		);

		add_settings_field(
			'wbitly_custom_post', // id
			'Post Types', // title
			array( $this, 'add_wbitly_custom_posttype_settings' ), // callback
			'wbitly-url-admin', // page
			'wbitly_url_setting_section' // section
		);



	}

	/**
	 * Validate Fields
	 *
	 * @param      <type>  $input  The input
	 *
	 * @return     array   ( description_of_the_return_value )
	 */

	public function wbitly_url_sanitize($input) {
		$sanitary_values = array();
		if ( isset( $input['access_token'] ) ) {
			$sanitary_values['access_token'] = sanitize_text_field( $input['access_token'] );
		}

		if ( isset( $input['group_guid'] ) ) {
			$sanitary_values['group_guid'] = sanitize_text_field( $input['group_guid'] );
		}

		if ( isset( $input['bitly_domain'] ) ) {
			$sanitary_values['bitly_domain'] = sanitize_text_field( $input['bitly_domain'] );
		}


		if ( isset( $input['wbitly_socal_share'] ) ) {
			$sanitary_values['wbitly_socal_share'] = sanitize_text_field( $input['wbitly_socal_share'] );
		}


		if ( isset( $input['wbitly_custom_post'] ) ) {
			$sanitary_values['wbitly_custom_post'] = $input['wbitly_custom_post'];
		}


		return $sanitary_values;
	}

	public function wbitly_url_section_info() {
		
	}

	public function access_token_callback() {
		printf(
			'<input class="regular-text" type="text" name="wbitly_url_option_name[access_token]" id="access_token" value="%s">',
			isset( $this->bitly_url_options['access_token'] ) ? esc_attr( $this->bitly_url_options['access_token']) : ''
		);
		echo '<p> <small>Tutorial: </small><a href="https://www.codehaveli.com/how-to-generate-bitly-oauth-access-token/?utm_source=Wordpress%20Plugin&utm_medium=Tutorial%20Link&utm_campaign=Codehaveli%20Bitly%20URL%20Shortener" target="_blank"><small>How to generate Bitly OAuth access token?</small></a></p>';
	}

	public function group_guid_callback() {

		$guid_url = admin_url( 'tools.php?page=wbitly&wbitly_guid=update' );

		printf(
			'<input class="regular-text" type="text" name="wbitly_url_option_name[group_guid]" id="group_guid" value="%s">',
			isset( $this->bitly_url_options['group_guid'] ) ? esc_attr( $this->bitly_url_options['group_guid']) : ''
		);

		echo "<a href=".$guid_url." class='button button-primary'>Get GUID</a>";
		echo '<p> <small>Save Access Token before geting GUID </small></p>';
		
	}

	public function bitly_domain_callback() {
		printf(
			'<input class="regular-text" type="text" placeholder="Default: bit.ly" name="wbitly_url_option_name[bitly_domain]" id="bitly_domain" value="%s">',
			isset( $this->bitly_url_options['bitly_domain'] ) ? esc_attr( $this->bitly_url_options['bitly_domain']) : ''
		);
		echo '<p><small>Leave blank if you are in Free Plan</small></p>';
	}

	public function add_wbitly_social_share_button() {

		$wbitly_social_share = '';
				
	    if(isset($this->bitly_url_options['wbitly_socal_share'])){
	    	$wbitly_social_share =  $this->bitly_url_options['wbitly_socal_share'] == "enable" ? "checked" : '';   	
	    }

		printf('<label><input name="wbitly_url_option_name[wbitly_socal_share]"  id="wbitly_socal_share" type="checkbox" value="enable"  %s> Enable </label>', $wbitly_social_share);
		echo '<p><small>If you enable this you can share the link from your post list/edit screen.</small></p>';
	}




	public function add_wbitly_custom_posttype_settings(){

		$post_types         = get_post_types(array('public' => true));
		$current_post_types = [];


        $output = '<fieldset><legend class="screen-reader-text"><span>Post Types</span></legend>';


        if(isset($this->bitly_url_options['wbitly_custom_post'])){
	    	$current_post_types =  $this->bitly_url_options['wbitly_custom_post'];  	
	    }


        foreach ($post_types as $label) {
        	$random = rand();
        	$input_label = $label .'_'. $random;
            $output .= '<label for="' . $input_label . '">' . '<input id="'.$input_label.'" type="checkbox" name="wbitly_url_option_name[wbitly_custom_post][]" value="' . $label . '" ' . checked(in_array($label, $current_post_types), true, false) . '>' . $label . '</label><br>';
        }

        echo $output;
	}


	/**
	 * Return currently saved bitly access token
	 *
	 * @return     boolean  The wbitly access token.
	 */

	public function get_wbitly_access_token(){

		$bitly_url_options_from_db = get_option( 'wbitly_url_option_name' ); 
		$access_token              = isset($bitly_url_options_from_db['access_token']) ? $bitly_url_options_from_db['access_token'] : '';
		return $access_token ? trim($access_token) : false;
	}


	/**
	 * Gets the wbitly unique identifier.
	 *
	 * @return     boolean  The wbitly unique identifier.
	 */


	public function get_wbitly_guid(){

		$bitly_url_options_from_db = get_option( 'wbitly_url_option_name' ); 
		$guid                      = isset($bitly_url_options_from_db['group_guid']) ? $bitly_url_options_from_db['group_guid'] : '';
		return $guid ? trim($guid) : false;
	}

	/**
	 * Gets the wbitly domain.
	 *
	 * @return     boolean  The wbitly domain.
	 */

	public function get_wbitly_domain(){

		$bitly_url_options_from_db = get_option( 'wbitly_url_option_name' ); 
		$domain                    = isset($bitly_url_options_from_db['bitly_domain']) ? $bitly_url_options_from_db['bitly_domain'] : '';
		return $domain ? trim($domain) : "bit.ly";
	}

	/**
	 * Gets the wbitly socal share status.
	 *
	 * @return     bool  The wbitly socal share status.
	 */
	public function get_wbitly_socal_share_status(){

		$bitly_url_options_from_db = get_option( 'wbitly_url_option_name' ); 
		$wbitly_socal_share        = isset($bitly_url_options_from_db['wbitly_socal_share']) ? $bitly_url_options_from_db['wbitly_socal_share'] : '';
		return $wbitly_socal_share === "enable" ? true : false;
	}


	public function get_wbitly_active_post_status(){


		$bitly_url_options_from_db = get_option( 'wbitly_url_option_name' ); 
		$active_post_types         = isset($bitly_url_options_from_db['wbitly_custom_post']) ? $bitly_url_options_from_db['wbitly_custom_post'] : ['post'];
		return $active_post_types;

	}





}



 $wbitly_settings = new WbitlyURLSettings();

add_action( 'plugin_action_links_'.WBITLY_BASENAME, 'wbitly_add_settings_url' );

function wbitly_add_settings_url( $links ) {

		$links = array_merge( array(
			'<a href="' . esc_url( admin_url( 'tools.php?page=wbitly' ) ) . '">' . __( 'Settings', 'wbitly' ) . '</a>'
		), $links );

		return $links;

}









