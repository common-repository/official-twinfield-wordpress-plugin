=== Plugin Name ===
Contributors: joblammers 
Donate link: http://jjlammers.com/
Tags: Twinfield, SSO, Online Accounting
Requires at least: 3.3.0
Tested up to: 2.1
Stable tag: 4.3

Enables a single signon (SSO) login with Twinfield Online Accounting

== Description ==
Enables a single signon (SSO) login with Twinfield Online Accounting

== Installation ==

This section describes how to install the plugin and get it working.

1. Upload `twinfield.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Enter in the menu Settings>Twinfield the Twinfield environment code
4. Per WP user enter the Twinfield credentials, the office code is optional. 
5. Add the Twinfield widget to a sidebar to enable the SSO option.

== Frequently Asked Questions ==

= Is Twinfield SSO enabled for all users? =

No, SSO is not enabled by default, you need to contact your Twinfield accountmanager or the Customer Support Organization (cso@twinfield.com) to receive an addendum to your default contract. When this document is signed, Twinfield will enable the SSO option on your environment.

= Do I need to have a SSL certificate to setup a SSO connection? =

Yes, we strongly advice to use a valid SSL certificate.

== Screenshots ==


== Changelog ==

= 1.1 =
* Fixes in the sso.php file
* Removal of the Twinfield credentials from the users has been updated

== Upgrade Notice ==

= 1.0 =
No upgrade information available yet.

= 1.1 =
* Fixes in the sso.php file
* Removal of the Twinfield credentials from the users has been updated


== Arbitrary section ==

The usage of this plugin is for your own risk. The plugin needs valid Twinfield credentials to logon in Twinfield, the user information is stored encrypted, but can be decrypted, by anyone who has access to the WordPress database and/or this plugin.

== A brief Markdown Example ==

Ordered list:

1. Setup the Twinfield enviroment connection
2. Addition to store the Twinfield credentials on a WordPress user
3. Widget to show the SSO button on sidebar
