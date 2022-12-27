<?php
/**
 * Configures settings display in admin view for plugin.
 *
 * @package "Cloudflare Web Analytics"
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
			wp_nonce_field( 'cf_web_analytics_nonce_action', 'cf_web_analytics_nonce_token' );
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
	$admin_css = plugins_url( '../public/css/admin-styles.css', __FILE__ );
	wp_enqueue_style( 'my-css', $admin_css, false, '1.0.0' );
}

/**
 * Displays instructions for entering Cloudflare token
 *
 * @return void
 */
// phpcs:disable
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
// phpcs:enable
/**
 * Adds input box for Cloudflare token
 *
 * @return void
 */
function cf_web_analytics_setting_token() {

	$token = cf_web_analytics_get_token();

	echo "<input id='token' name='cf_web_analytics_options[token]' minlength='8' pattern='[\Sa-zA-Z0-9-]+' type='text' value='" . esc_attr( $token ) . "' placeholder='ex: absd312dcdd312dasdas13' title='absd312dcdd312dasdas13' />";

}


/**
 * Check user-inputed token
 *
 * Checks token and makes sure it is valid.
 *
 * @param  string $input User inputed token.
 * @return boolean
 */
function cf_web_analytics_validate_options( $input ) {

	if ( ! empty( $_POST ) && check_admin_referer( 'cf_web_analytics_nonce_action', 'cf_web_analytics_nonce_token' ) ) {

		$valid          = array();
		$valid['token'] = preg_replace(
			'/[^A-Za-z0-9]/',
			'',
			$input['token']
		);

		if ( $valid['token'] !== $input['token'] || '' === $input['token'] ) {

			if ( '' === $input['token'] ) {
				$error_msg = 'Token must not be empty.';
			} else {
				$error_msg = 'Incorrect value entered. Token should be only letters and numbers.';
			}

			add_settings_error(
				'cf_web_analytics_text_string',
				'cf_web_analytics_texterror',
				$error_msg,
				'error'
			);
		}

		// TODO: This may need to be moved.
		$valid['token'] = sanitize_text_field( $input['token'] );
	}

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
?>
