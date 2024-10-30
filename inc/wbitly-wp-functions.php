<?php

/**
 * @Author: Codehaveli
 * @Date:   2020-06-28 21:36:38
 * @Last Modified by:   Codehaveli
 * @Website: www.codehaveli.com
 * @Email: hello@codehaveli.com
 * @Last Modified time: 2021-02-09 15:02:05
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


add_action( 'wp_ajax_generate_wbitly_url_via_ajax', 'generate_wbitly_url_via_ajax');
function generate_wbitly_url_via_ajax(){

	$data  = $_POST;
	$error = false;

	if(!isset($data['post_id'])){
		echo json_encode(['status' => false , 'bitly_link_html' => 'null']);
		die();
	}

	$post_id    = (int)$data['post_id'];
	
	$permalink  = get_permalink( $post_id );

	if(!$permalink){
		echo json_encode(['status' => false , 'bitly_link_html' => 'null']);
		die();
	}
	
	$bilty_link = wbitly_generate_shorten_url($permalink);


	if(!$bilty_link){
		echo json_encode(['status' => false , 'bitly_link_html' => 'null']);
		die();
	}


	if($bilty_link){
          save_wbitly_short_url($bilty_link , $post_id);
     }


	$bitly_link_html = '<div class="wbitly_tooltip wbitly copy_bitly">
            <p><span class="copy_bitly_link">'.$bilty_link.'</span>  <span class="wbitly_tooltiptext">Click to Copy</span></p>
          </div>';


	if(!$error){

		echo json_encode(['status' => true , 'bitly_link_html' => $bitly_link_html]);
	}else{
		echo json_encode(['status' => false , 'bitly_link_html' => 'null']);
	}

	die();
}


/**
 * Filter the core shortlink with Our generated Bitly Link
 */

add_filter( 'pre_get_shortlink', 'chnage_core_short_link_with_wbitly_link', 10, 5 );
function chnage_core_short_link_with_wbitly_link($status, $id, $context, $allow_slugs ){
	
	
	$bitly_url = get_wbitly_short_url($id);
    if ($bitly_url) {
    	return $bitly_url;
    }

    return $status;
}