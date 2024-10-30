<?php

/**
 * @Author: Codehaveli
 * @Date:   2020-06-28 21:48:00
 * @Last Modified by:   Codehaveli
 * @Website: www.codehaveli.com
 * @Email: hello@codehaveli.com
 * @Last Modified time: 2020-11-22 21:27:03
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

function get_wbitly_short_url($post_id = null){

	if(!$post_id){
		global $post;
		$post_id = isset($post->ID) ? $post->ID : 0;
	}

    if(!$post_id){
        return false;
    }

	$wbitly_url = get_post_meta($post_id, '_wbitly_shorturl', true);

	return $wbitly_url ? $wbitly_url : false;

}



function save_wbitly_short_url($shorten_url , $post_id = null){
	if(!$post_id){
		global $post;
		$post_id = isset($post->ID) ? $post->ID : 0;
	}

    if(!$post_id){
        return false;
    }

	update_post_meta($post_id, '_wbitly_shorturl', $shorten_url);
    do_action('wbitly_shorturl_updated' , $shorten_url);
}



function wbitly_remove_http($url) {
   $disallowed = array('http://', 'https://');
   foreach($disallowed as $d) {
      if(strpos($url, $d) === 0) {
         return str_replace($d, '', $url);
      }
   }
   return $url;
}


function get_wbitly_headers(){

	$wbitly_settings = new WbitlyURLSettings();
    $access_token   	 =  $wbitly_settings->get_wbitly_access_token();

	$headers = array (
        "Host"          => "api-ssl.bitly.com",
        "Authorization" => "Bearer ".$access_token ,
        "Content-Type"  => "application/json"
    );

    return $headers;
}





if (!function_exists('wbitly_write_log')) {

    function wbitly_write_log($log) {
        if (true === WP_DEBUG) {
            if (is_array($log) || is_object($log)) {
                error_log(print_r($log, true));
            } else {
                error_log($log);
            }
        }
    }

}


function wbitly_get_template( $template_name, $template_path = '', $default_path = '' ) {

    $located = wbitly_locate_template( $template_name, $template_path, $default_path );

    if ( ! file_exists( $located ) ) {
        _doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', esc_html( $located ) ), WBITLY_PLUGIN_VERSION );
        return;
    }

    include( $located );
}


function wbitly_locate_template( $template_name,  $default_path = '') {


    if ( ! $default_path ) {
        $default_path = untrailingslashit(WBITLY_PLUGIN_PATH). '/templates/';
    }

    $template = $default_path . $template_name;

    // Return what we found
    return $template;
}

