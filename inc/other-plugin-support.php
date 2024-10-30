<?php

/**
 * @Author: Codehaveli
 * @Date:   08-09-2023 15:25:51
 * @Last Modified by:   Codehaveli
 * @Website: www.codehaveli.com
 * @Email: hello@codehaveli.com
 * @Last Modified time: 08-09-2023 19:25:51
 */


 /**
  * Yoast Duplicate Post Support
  * This code block will stop copying same url to the new dupliccated post
  */

 add_filter('duplicate_post_excludelist_filter', 'exclude_bitly_meta_during_copy' , 10 , 1);
 function exclude_bitly_meta_during_copy($meta_blacklist){
    $meta_blacklist[] = '_wbitly_shorturl';
    return $meta_blacklist;
 }