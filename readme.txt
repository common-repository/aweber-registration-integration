=== Plugin Name ===
Contributors: GKauten
Donate link: http://www.gkauten.com/playground
Tags: AWeber, User Registration, Email Marketing
Requires at least: 2.8
Tested up to: 2.8.6
Stable tag: 1.2.8

Integrates the AWeber contact registration script into your WordPress user registration process.

== Description ==

Integrates the AWeber contact registration script into your WordPress registration process. 
Users are seamlessly added to your AWeber account during registration on your site, either 
by request or silently. If you do not yet have an AWeber account, you will need to 
visit their website (http://www.aweber.com) and sign up for one.

If you find this plugin useful, please donate. Donations help pay for my AWeber account which
allows me to continue supporting and improving this plugin as WordPress and Aweber continue
to evolve on their respective paths.

Special thanks to Guru Consulting Services, Inc. (http://www.gurucs.com) for their 
original platform on which some of these functions derive.

== Installation ==

1. Upload 'aweber-registration-integration.php' to the '/wp-content/plugins/' directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Visit the 'AWeber Integration' sub-menu under the 'Settings' menu in WordPress
1. Enter the required information retrieved from your AWeber control panel. This information 
is found after clicking 'Get HTML' for the form you wish to use, and then selecting the 'Raw 
HTML Version'. Using the new form generator utility, at the end you will need to select 'I Will 
Install My Form' to access the 'Raw HTML Version'.
1. The plugin will now automatically integrate with the WordPress registration process unless 
your settings reflect otherwise.

== Frequently Asked Questions ==

= Will the user automatically be registered with AWeber? =

No. The user will still receive a confirmation email from AWeber confirming their intent to register 
for your mailings.

= If I deactivate the plugin will my settings remain? =

No. Deactivating the plugin through the WordPress Plugin menu will trigger a built in 'clean-up' 
function that will uninstall the plugin. If you wish to turn off the plugin temporarily, you can 
use the built in setting found in the 'AWeber Integration' menu.

= Are the registered users shown on the Registered Users screen the same as in my AWeber account? =

Probably not. The Registered Users section allows you to see which of your members elected to 
register with your AWeber account when they signed up with your website. However, since AWeber a
lso sends a confirmation email before submitting the user's information, there is a chance they 
might not have signed up, thus creating an inconsistency between your AWeber control panel, and 
WordPress users screen. This feature is intended to give you an idea of how many users have elected 
to register without having to go to AWeber to find out.

== Screenshots ==

1. Displays the administration panel for the plugin and available options.

== Changelog ==

= 1.2.8 =
* Resolved some functionality concerns with the Opt-In feature.
* Renamed some functions for increased unique nature to prevent possible problems with other plugins.

= 1.2.6 =
* Updated to coincide with updates made to the HTML form code generated from the AWeber control panel.

= 1.2.5 =
* Added Registered Users section to administration.

= 1.2.1 =
* Corrected errors within the Opt In functions which occur after upgrading to WordPress version 2.8.5 and up.
* Corrected errors within the email tracking to prevent duplicate registrations.

= 1.2 =
* Opt-In option added.
* Disable Integration option added.

= 1.1 =
* Installation error handling added.
* Process error handling increased.

= 1.0 =
* Initial release.
* User silently registered with respective AWeber account.

`<?php code(); // goes in backticks ?>`
