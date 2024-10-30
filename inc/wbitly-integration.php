<?php

/**
 * @Author: Codehaveli
 * @Date:   2020-05-18 15:25:51
 * @Last Modified by:   Codehaveli
 * @Website: www.codehaveli.com
 * @Email: hello@codehaveli.com
 * @Last Modified time: 2020-11-22 21:26:53
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

/**
 * Generate short URL from permalink
 *
 * @param      <type>  $permalink  The permalink
 *
 * @return     <type>  ( description_of_the_return_value )
 */
function wbitly_generate_shorten_url($permalink){

   if ( ! class_exists( 'WbitlyURLSettings' ) ) {
      return;
    }
     $wbitly_settings = new WbitlyURLSettings();

     $permalink      =  apply_filters( 'wbitly_url_before_process', $permalink );
     $access_token   =  $wbitly_settings->get_wbitly_access_token();
     $group_guid     =  $wbitly_settings->get_wbitly_guid();
     $shorten_domain =  $wbitly_settings->get_wbitly_domain();



    if(!$shorten_domain){
       $payload = array(
        "group_guid" =>"".$group_guid."",
        "long_url"   =>"".$permalink.""
      );
    }else{
      $payload = array(
        "group_guid" =>"".$group_guid."",
        "domain"     =>"".$shorten_domain."",
        "long_url"   =>"".$permalink.""
      );
    }


    $json_payload = json_encode($payload);
    
    $headers      = get_wbitly_headers();


    $response = wp_remote_post( WBITLY_API_URL . "/v4/shorten" , array(
        'method'      => 'POST',
        'headers'     => $headers,
        'body'        => $json_payload
        )
    );


    if ( is_wp_error( $response ) ) {
      wbitly_write_log($response->get_error_message());
      return false;
    } else {
      $response_array = json_decode($response['body']);
      return $response_array->link ? $response_array->link : false;

    }

}






/**
 * Generate and return URL or return false;
 * Will be removed in future update
 *
 * @param      string   $shorten_url  The shorten url
 */

function wbitly_shorten_url ($permalink) {

  _deprecated_function( 'wbitly_shorten_url', '1.1', 'wbitly_generate_shorten_url' );


  return wbitly_generate_shorten_url($permalink);

}


/**
 * Add Colum for custom post list
 * 
 */

add_action( 'admin_init', function(){


   $wbitly_settings = new WbitlyURLSettings();
   $active_post_types = $wbitly_settings->get_wbitly_active_post_status();


   foreach ($active_post_types as $active_post) {
     
     /**
     * Add Short URL Column in Post List 
     */

      $wbitly_column_key        = 'manage_'.$active_post.'_posts_columns';
      $wbitly_column_value_key  = 'manage_'.$active_post.'_posts_custom_column';

      add_filter($wbitly_column_key, function($columns) {
        return array_merge($columns, ['wbitly_url' => __('Short URL', 'wbitly')]);
      });


      /**
       * Display the value of bitly URL
       * If Access token not added or Guid not added column will show settings link
       * If Post Short URL is not generated "Not Generated yet" message will show
       */
       
      add_action($wbitly_column_value_key, function($column_key, $post_id) {
        if ($column_key == 'wbitly_url') {


          if( 'publish' != get_post_status($post_id)){
              return;
          }

          $wbitly_settings = new WbitlyURLSettings();
          $access_token =  $wbitly_settings->get_wbitly_access_token();
          $guid         =  $wbitly_settings->get_wbitly_guid();

          if(!$access_token || !$guid){

            $plugin_url = admin_url( 'tools.php?page=wbitly');
            echo '<a  class="wbitly_settings" href="'.$plugin_url .'">Get Started</a>';
          }else{

            echo '<div class="wbitly_column_container">';

            $bitly_url = get_wbitly_short_url($post_id);
            if ($bitly_url) {
              ?>
                <div class="wbitly_tooltip wbitly copy_bitly">
                  <p><span class="copy_bitly_link"><?php echo $bitly_url; ?></span>  <span class="wbitly_tooltiptext">Click to Copy</span></p>
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
                    <span class="wbitly_tooltiptext">Click to Generate</span>
                   Generate URL
                  </button>
                </div>


              <?php
            }

            echo "</div>";

          }


        }
      }, 10, 2);

   }



});




/**
 * Generate and Save Bitly URL in `_wbitly_shorturl` post meta key
 * `wbitly_shorturl_updated` hook is available after value is updated with $shorten_url argument 
 */

add_action('transition_post_status', 'wbitly_update_shorturl' , 10 , 3 );
function wbitly_update_shorturl($new_status, $old_status, $post) {




    if('publish' === $new_status && 'publish' !== $old_status) {
      
      $wbitly_settings = new WbitlyURLSettings();
      $active_post_types = $wbitly_settings->get_wbitly_active_post_status();

      if(in_array($post->post_type, $active_post_types)){

          $post_id     = $post->ID;
          $shorten_url = get_wbitly_short_url($post_id);

          if( empty( $shorten_url ) && ! wp_is_post_revision( $post_id ) ) {
           
            $permalink   = get_permalink($post_id);
            $shorten_url = wbitly_generate_shorten_url($permalink);
            
            if($shorten_url){
              save_wbitly_short_url($shorten_url , $post_id);
            }
            
          }
      }     

    }
}
