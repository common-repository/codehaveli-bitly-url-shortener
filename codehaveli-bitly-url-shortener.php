<?php

/*
Plugin Name: Bitly URL Shortener
Plugin URI: https://github.com/codehaveli/
Description: Bitly URL Shortener uses the functionality of Bitly API to generate Bitly short link without leaving your WordPress site.
Version: 1.3.3
Author: Codehaveli
Author URI: https://www.codehaveli.com/
License: GPLv2 or later
Text Domain: wbitly
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


define( 'WBITLY_PLUGIN_PATH', plugin_dir_path( __FILE__ ) ); 
define( 'WBITLY_PLUGIN_URL', plugin_dir_url( __FILE__ ) );	
define( 'WBITLY_PLUGIN_VERSION', '1.3.3' );
define( 'WBITLY_API_URL', 'https://api-ssl.bitly.com' );
define( 'WBITLY_BASENAME', plugin_basename( __FILE__ ) );
define( 'WBITLY_SETTINGS_URL', admin_url( 'tools.php?page=wbitly' ) );


/**
 * Load Admin Assets
 */

require_once 'inc/wbitly-assets.php';


/**
 * Load Util Functions
 */

require_once 'inc/wbitly-util.php';


/**
 * Load Settings file
 */


require_once 'inc/wbitly-settings.php';



/**
 * Load Bity Integration
 */


require_once 'inc/wbitly-integration.php';



/**
 * Load WordPress related hooks
 */


require_once 'inc/wbitly-wp-functions.php';

/**
 * Meta Box 
 */
require_once 'inc/wbitly-metabox.php';



/**
 * Thrird Party Plugin Support
 */
require_once 'inc/other-plugin-support.php';