=== Bitly URL Shortener ===
Contributors: codehaveli,royrakesh
Tags: Bitly, Short url, Url shortener, post, connector
Requires at least: 4.0
Tested up to: 6.6
Requires PHP: 5.6
Stable tag: trunk
License: GPLv2 or later
Donate link: https://www.paypal.com/paypalme/royrakesh92
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Bitly URL Shortener uses the functionality of Bitly API to generate Bitly short link without leaving your WordPress site.

== Description ==

**Bitly URL Shortener** (Previously Codehaveli Bitly URL Shortener) uses the functionality of Bitly API to generate bitly short link automatically from your WordPress dashboard when you publish new post.

Bitly URL Shortener allows you to connect your WordPress Website to the Bitly API via access code.

### Features 

* Generate Bitly link without leaving your site.
* Share Bitly link from your Post List and Post Edit page.
* Support for Custom Post Type.
* Support for Custom Bitly Domain.
* Generate Bitly link of your old posts with just one click.
* Option to choose from your list of post types. 
* Optimized code and Small in Size 16KB ( Compressed ).


### Terms of Use 

This is not a Official plugin of [Bitly](https://bitly.com)

This plugin only connect your [https://bitly.com](https://bitly.com) account to WordPress.

Please read [privacy](https://bitly.com/pages/privacy) and [terms of service](https://bitly.com/pages/terms-of-service) of [Bitly](https://bitly.com) before using this plugin.



### Bug reports

Bug reports for Bitly URL Shortener are welcomed in our Bitly URL Shortener [repository on GitHub](https://github.com/codehaveli/codehaveli-bitly-url-shortener). Please note that GitHub is not a support forum, and that issues that are not properly qualified as bugs will be closed.

### Further Reading

For more info on Bitly and Codehaveli , check out the following:

* [Codehaveli](https://www.codehaveli.com/) official homepage.
* Read "How to generate Bitly OAuth access token?" from [Codehaveli Blog](https://bit.ly/2UMx7u9)
* Bitly [API Documentation](https://bitly.is/2XxT9BN) 
* Follow Codehaveli on [Facebook](https://www.facebook.com/codehaveli), [Instagram](https://www.instagram.com/codehaveli/) & [Twitter](https://twitter.com/codehaveli).

### Frequently Asked Questions

Read FAQ from our [Github](https://github.com/codehaveli/codehaveli-bitly-url-shortener/blob/master/FAQ.md) Page.


== Installation ==

1. Visit the plugins page within your dashboard and select Add New
1. Search for **Bitly URL Shortener**
1. Activate Bitly URL Shortener from your Plugins page.
1. Go to settings from Tools Menu or Settings fromm WordPress Plugin list
1. Get Your access token from  [https://bitly.com](https://bitly.com) and save it in access token field
1. Click on Get GUID if you don't have the Group GUID. [Plugin will get it from your bitly account via API call]
1. You are ready to go, From now when ever you publish a post your link will be generate automatically



== Frequently Asked Questions ==

= How to get the short link of a post? =

`$link = get_wbitly_short_url($post_id); // link or false`



== Screenshots ==
1. Settings Panel Screenshot
2. You can go to settings from Plugn list settings
3. Generated URL column in post list
4. Share button in post list
5. Custom Post type selection checkbox.
6. Copy Shortner link from Front end.


== Changelog ==

= 1.3.3 = 
* Updated: tested up to value

= 1.3.2 = 
* Updated: tested up to value
* Add support for [ Yoast Duplicate Post ](https://wordpress.org/plugins/duplicate-post/ )

= 1.3.1 = 
* PHP Warning: Fixed

= 1.2.2 =
* Name chnaged from `Codehaveli Bitly URL Shortener` to `Bitly URL Shortener` due to SEO conflict with codehaveli.com 

= 1.2.1 =
* Support WordPress Core Shortlinks Filters.
* Shortener link from Front end.
* Code optimized.

= 1.1.4 =
* Meta Box added in Post Edit Page, now you can share link from your post page
* Some security issue fixed.

= 1.1.3 =
* Custom Post Type Selection Added and default set to 'post'.
* Small bug fixed

= 1.1.2 =
* Custom Post Type Support Added

= 1.1.1 =
* Versioning fixed. 
* Code optimized. 

= 1.1 =
* Option added to share link from post list
* Genereate old posts link from post list
* Function refactored
* Error handler added
* Some bug fixed

= 1.0 =
Initaial Release
