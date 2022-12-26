<?php
/**
 * This plugin adds support for Cloudflare Web Analytics in WordPress.
 *
 * @package "Cloudflare Web Analytics"
 * @version 1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:       Cloudflare Web Analytics
 * Plugin URI:        https://www.bobmatyas.com
 * Description:       Easily add Cloudflare Web Analytics to WordPress
 * Version:           1.0.0
 * Requires at least: 5.3
 * Requires PHP:      5.6
 * Author:            Bob Matyas
 * Author URI:        https://www.bobmatyas.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       cf-web-analytics
 * Domain Path:       /public/lang
 */

/*
Copyright (C) 2022  Bob Matyas

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License along
with this program; if not, write to the Free Software Foundation, Inc.,
51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA.
*/


add_action( 'admin_menu', 'cf_web_analytics_add_settings_menu' );

/**
 * Add settings menu to WP Admin for plugin
 *
 * @return void
 */
function cf_web_analytics_add_settings_menu() {

	add_options_page(
		'Cloudflare Web Analytics Settings',
		'Cloudflare Web Analytics',
		'manage_options',
		'cf_web_analytics',
		'cf_web_analytics_option_page'
	);

}

/**
 * Configures options page
 *
 * @return void
 */
function cf_web_analytics_option_page() {
	?>
	<div class="cfwa-container">
		<h2>Cloudflare Web Analytics</h2>
		<form action="options.php" method="post">
			<?php
			settings_fields( 'cf_web_analytics_options' );
			do_settings_sections( 'cf_web_analytics' );
			submit_button( 'Save', 'primary' );
			?>
		</form>
	</div>
	<?php
}

add_action( 'admin_init', 'cf_web_analytics_admin_init' );

/**
 * Initializes admin page.
 *
 * @return void
 */
function cf_web_analytics_admin_init() {

	$args = array(
		'type'              => 'string',
		'sanitize_callback' => 'cf_web_analytics_validate_options',
		'default'           => null,
	);

	register_setting( 'cf_web_analytics_options', 'cf_web_analytics_options', $args );

	add_settings_section(
		'cf_web_analytics_main',
		'Enter your token Cloudflare Web Analytics token.',
		'cf_web_analytics_section_text',
		'cf_web_analytics'
	);

	add_settings_field(
		'cf_web_analytics_token',
		'Token',
		'cf_web_analytics_setting_token',
		'cf_web_analytics',
		'cf_web_analytics_main'
	);
}

add_action( 'admin_enqueue_scripts', 'cf_web_analytics_admin_styles' );

/**
 * Enqueues admin CSS
 *
 * @return void
 */
function cf_web_analytics_admin_styles() {
	$admin_css = plugins_url( '/public/css/admin-styles.css', __FILE__ );
	wp_enqueue_style( 'my-css', $admin_css, false, '1.0.0' );
}

/**
 * Displays instructions for entering Cloudflare token
 *
 * @return void
 */
function cf_web_analytics_section_text() {

	echo '<details><summary>No token? View instructions</a></summary>

	<h3>How to Configure</h3>
	
	<ol>
	<li><a href="https://dash.cloudflare.com/sign-up/web-analytics" target="blank">Sign-up for a Cloudflare account</a> or <a href="https://dash.cloudflare.com/login" target="blank">log in to your existing account.</a></li>
	<li>Once logged in, navigate to <b>"Analytics > Web Analytics"</b></li>
	<li>Add a new website or view the JS snippet for an existing site</li>
	<li>In the snippet code, copy the <code>token</code> value (i.e. <code>"token": "<b><u>999d231dasda123kllklkdasc2</u></b>"</code>)
	<details><summary class="example">Example Snippet</summary>
	<code class="details-inner">'. htmlentities( '<script defer src=\'https://static.cloudflareinsights.com/beacon.min.js\' data-cf-beacon=\'{"token": "999d231dasda123kllklkdasc2"}\'></script>' ) .' </code>
	</details>
	</li>
	<li>The plugin uses the value <b>999d231dasda123kllklkdasc2</b> from the example above</li>
	<li>Paste the token into the field.</li>
	</ol>
	
	<h3>Questions about Cloudflare Web Analytics?</h3>
	
	<p>Check out <a href="https://developers.cloudflare.com/analytics/web-analytics/" target="blank">the documentation on Cloudflare\'s site</a>.</p></details>';
}

/**
 * Adds input box for Cloudflare token
 *
 * @return void
 */
function cf_web_analytics_setting_token() {

	$token = cf_web_analytics_get_token();

	echo "<input id='token' name='cf_web_analytics_options[token]' minlength='8' pattern='[a-zA-Z0-9-]+' type='text' value='" . esc_attr( $token ) . "' placeholder='ex: absd312dcdd312dasdas13' />";

}


/**
 * Check user-inputted token
 *
 * Checks token and makes sure it is valid.
 *
 * @param  string $input User inputed token.
 * @return boolean
 */
function cf_web_analytics_validate_options( $input ) {

	$valid          = array();
	$valid['token'] = preg_replace(
		'/[^A-Za-z0-9]/',
		'',
		$input['token']
	);

	if ( $valid['token'] !== $input['token'] ) {

		add_settings_error(
			'cf_web_analytics_text_string',
			'cf_web_analytics_texterror',
			'Incorrect value entered. Token should be only letters and numbers.',
			'error'
		);

	}

	$valid['token'] = sanitize_text_field( $input['token'] );

	return $valid;

}

/**
 * Gets token from database and returns it.
 *
 * @return string
 */
function cf_web_analytics_get_token() {

	$options = get_option( 'cf_web_analytics_options' );
	$token   = $options['token'];

	return $token;

}

/**
 * Loads Cloudflare Web Analytics script
 *
 * If the token exists and passes the check, load the Cloudflare JavaScript.
 *
 * @return void
 */
function cf_web_analytics_load_scripts() {

	$token = cf_web_analytics_get_token();

	if ( '' === $token || null === $token ) {

		return;

	}

	wp_enqueue_script(
		'cf-web-analytics',
		'https://static.cloudflareinsights.com/beacon.min.js',
		null,
		'1.0',
		true
	);

}

add_action( 'wp_enqueue_scripts', 'cf_web_analytics_load_scripts' );


/**
 * Modifies Cloudflare Web Analytics to return properly script tag
 *
 * @param  mixed $tag     Script tag.
 * @param  mixed $handle  Script handle.
 * @param  mixed $src     Cloudflare Web Analytics script URL.
 * @return string $tag
 */
function cf_web_analytics_add_attributes( $tag, $handle, $src ) {

	$token = cf_web_analytics_get_token();

	if ( 'cf-web-analytics' === $handle ) {
		$tag = "<script defer src='" . esc_url( $src ) . "'  data-cf-beacon='{\"token\": \"". $token ."\"}'></script>";
	}

	return $tag;
}

add_filter( 'script_loader_tag', 'cf_web_analytics_add_attributes', 10, 3 );
