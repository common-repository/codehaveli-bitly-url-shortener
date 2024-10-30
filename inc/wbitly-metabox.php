<?php

/**
 * @Author: Codehaveli
 * @Date:   2020-11-19 17:32:34
 * @Last Modified by:   Codehaveli
 * @Website: www.codehaveli.com
 * @Email: hello@codehaveli.com
 * @Last Modified time: 2021-02-09 14:59:44
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

function wbitly_add_meta_box_to_post_types() {


	$wbitly_settings = new WbitlyURLSettings();
  $active_post_types = $wbitly_settings->get_wbitly_active_post_status();

 
    foreach ( $active_post_types as $post_type ) {
        add_meta_box(
            'wbitly-bitly-url-metabox',
            __( 'Bitly Short URL', 'wbitly' ),
            'wbitly_add_meta_box_content',
            $post_type,
            'side',
			'default'
        );
    }
}
add_action( 'add_meta_boxes', 'wbitly_add_meta_box_to_post_types' );


function wbitly_add_meta_box_content($post){

	$post_id = $post->ID;

	if( 'publish' != get_post_status($post_id)){

	    echo '<h4>Publish to Generate Bitly URL<h4>';

       return;
    }

	$wbitly_settings = new WbitlyURLSettings();
	$access_token =  $wbitly_settings->get_wbitly_access_token();
	$guid         =  $wbitly_settings->get_wbitly_guid();

	if(!$access_token || !$guid){

		$plugin_url = admin_url( 'tools.php?page=wbitly');
		echo '<a  class="wbitly_settings" href="'.$plugin_url .'">Get Started</a>';

	}else{

        echo '<div class="wbitly_metabox_container wbitly-mt-5">';

            $bitly_url = get_wbitly_short_url($post_id);
            if ($bitly_url) {
              ?>
                <div class="wbitly_tooltip wbitly copy_bitly">
                  <p><span class="copy_bitly_link wbitly-meta-bg-link"><?php echo $bitly_url; ?></span>  <span class="wbitly_tooltiptext">Click to Copy</span></p>
                </div>
                <?php 

                $wbitly_socal_share_status =  $wbitly_settings->get_wbitly_socal_share_status();

                if( $wbitly_socal_share_status){
                  wbitly_get_template('share.php');
                }

                ?>
              <?php
              
            } else {
              ?>
                <div class="wbitly_tooltip">
                  <p><?php echo $bitly_url; ?></p>
                  <button  class="wbitly generate_bitly" data-post_id="<?php echo $post_id;?>">
                    <span class="wbitly_tooltiptext">Click to Generate</span>Generate URL
                  </button>
                </div>


              <?php
            }

        echo "</div>";

    }


}



function add_wbitly_shortlink_frontend($wp_admin_bar) {

  $wbitly_settings   = new WbitlyURLSettings();
  $active_post_types = $wbitly_settings->get_wbitly_active_post_status();
  $default_roles     = ['administrator'];
  $allowed_roles     = apply_filters( 'wbitly_script_for_allowed_roles', $default_roles );


  foreach ($allowed_roles as $role) {
    if( current_user_can( $role ) ){
      foreach ( $active_post_types as $post_type ) {
        
        if(is_singular($post_type)){

          global $post;

          $post_id   = $post->ID;
          $bitly_url = get_wbitly_short_url($post_id);


          if ($bitly_url) {

              $args = array(
                'id'    => 'wbilty_link'.$post_id,
                'title' => 'Click to Copy Bitly Link', 
                'href'  => $bitly_url,
                'meta'  => array(
                  'class'           => 'wbitly-copy-class', 
                  'title'           => 'Click to Copy Bitly Link',

                )
              );
            
              $wp_admin_bar->add_node($args);
          }
        }
      }
    }   
  }
}
add_action('admin_bar_menu', 'add_wbitly_shortlink_frontend', 999);