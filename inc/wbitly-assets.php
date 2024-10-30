<?php

/**
 * @Author: Codehaveli
 * @Date:   2020-05-18 15:25:51
 * @Last Modified by:   Codehaveli
 * @Website: www.codehaveli.com
 * @Email: hello@codehaveli.com
 * @Last Modified time: 2021-02-07 22:42:08
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


function wbitly_load_admin_script() {
	
	wp_enqueue_script( 'wbitly-js', WBITLY_PLUGIN_URL . 'assets/js/wbitly.js', array( 'jquery' ), WBITLY_PLUGIN_VERSION , true );
	wp_enqueue_style( 'wbitly-css', WBITLY_PLUGIN_URL . 'assets/css/wbitly.css',[], WBITLY_PLUGIN_VERSION , 'all' );
	wp_localize_script( 'wbitly-js', 'wbitlyJS' , ['ajaxurl' => admin_url( 'admin-ajax.php' )]);

}
add_action('admin_enqueue_scripts', 'wbitly_load_admin_script');


add_action('wp_footer', 'wbitly_add_click_to_copy_script', PHP_INT_MAX);
function wbitly_add_click_to_copy_script() {

	$default_roles = ['administrator'];
	$allowed_roles = apply_filters( 'wbitly_script_for_allowed_roles', $default_roles );

	foreach ($allowed_roles as $role) {
		if( current_user_can( $role ) ){

			?>

			<script>
				(function($) {
					$(".wbitly-copy-class").on("click",function(t){if(t.preventDefault(),$wbitly_link_a=$(this).find("a"),$wbitly_link=$wbitly_link_a.attr("href"),$wbitly_link_title=$wbitly_link_a.attr("title"),$wbitly_link){var i=$("<textarea />");i.val($wbitly_link).css({width:"1px",height:"1px"}).appendTo("body"),i.select(),document.execCommand("copy")&&(i.remove(),$wbitly_link_a.html("Copied: "+$wbitly_link),setTimeout(function(){$wbitly_link_a.html($wbitly_link_title)},2100))}});
				})(jQuery);
			</script>

			<?php
		} 
	}
}