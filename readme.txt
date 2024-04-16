=== Helper for Cloudflare Web Analytics ===
Contributors:      lastsplash
Author:            Bob Matyas
Author URI:		   https://www.bobmatyas.com
Tags:              analytics, cloudflare, privacy, statistics, stats
Requires at Least: 5.0
Tested up to:      6.5.2
Stable tag:        1.0.1
Requires PHP:      5.6
License:           GPL-2.0-or-later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html

Allows use of Cloudflare Web Analytics.

== Description ==

This plugin adds the Cloudflare Web Analytics tracking code to your WordPress site. It provides a field for you to enter your token and then takes care of loading the Cloudflare JavaScript.

Cloudflare Web Analytics offers privacy-focused analytics. From their site:

> Cloudflare Web Analytics does not use any client-side state, such as cookies or localStorage, to collect usage metrics. We also don’t “fingerprint” individuals via their IP address, User Agent string, or any other data for the purpose of displaying analytics.

Learn more on [their marketing page](https://www.cloudflare.com/web-analytics/) and in [the documentation](https://developers.cloudflare.com/analytics/web-analytics/).

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Navigate to "Settings > Helper for Cloudflare Web Analytics" and add your site's token.

== Changelog ==

= 1.0.1 =
* Deploy from GitHub
* Test in v6.5.2

= 1.0 =
* Initial release
