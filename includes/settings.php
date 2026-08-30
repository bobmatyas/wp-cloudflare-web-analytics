<?php
/**
 * Configures settings display in admin view for plugin.
 *
 * @package "Helper for Cloudflare Web Analytics"
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_menu', 'cf_web_analytics_add_settings_menu' );

/**
 * Add settings menu to WP Admin for plugin
 *
 * @return void
 */
function cf_web_analytics_add_settings_menu() {

	add_options_page(
		__( 'Helper for Cloudflare Web Analytics Settings', 'helper-for-cloudflare-web-analytics' ),
		__( 'Helper for Cloudflare Web Analytics', 'helper-for-cloudflare-web-analytics' ),
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
		<h2><?php esc_html_e( 'Helper for Cloudflare Web Analytics', 'helper-for-cloudflare-web-analytics' ); ?></h2>

		<form action="options.php" method="post">
			<?php
			wp_nonce_field( 'cf_web_analytics_nonce_action', 'cf_web_analytics_nonce_token' );
			settings_fields( 'cf_web_analytics_options' );
			do_settings_sections( 'cf_web_analytics' );
			submit_button( __( 'Save', 'helper-for-cloudflare-web-analytics' ) );
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
		__( 'Enter your token Cloudflare Web Analytics token.', 'helper-for-cloudflare-web-analytics' ),
		'cf_web_analytics_section_text',
		'cf_web_analytics'
	);

	add_settings_field(
		'cf_web_analytics_token',
		__( 'Token', 'helper-for-cloudflare-web-analytics' ),
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
function cf_web_analytics_section_text() {
	$allowed_inline_tags = array(
		'a'    => array(
			'href'   => array(),
			'target' => array(),
		),
		'b'    => array(),
		'u'    => array(),
		'code' => array(),
	);

	$example_snippet = htmlentities(
		'<' . 'script defer src=\'https://static.cloudflareinsights.com/beacon.min.js\' data-cf-beacon=\'{"token": "999d231dasda123kllklkdasc2"}\'></' . 'script>'
	);
	?>
	<details>
		<summary><?php esc_html_e( 'No token? View instructions', 'helper-for-cloudflare-web-analytics' ); ?></summary>

		<h3><?php esc_html_e( 'How to Configure', 'helper-for-cloudflare-web-analytics' ); ?></h3>

		<ol>
			<li>
				<?php
				echo wp_kses(
					sprintf(
						/* translators: 1: URL to Cloudflare sign-up, 2: URL to Cloudflare login */
						__( '<a href="%1$s" target="_blank">Sign-up for a Cloudflare account</a> or <a href="%2$s" target="_blank">log in to your existing account.</a>', 'helper-for-cloudflare-web-analytics' ),
						esc_url( 'https://dash.cloudflare.com/sign-up/web-analytics' ),
						esc_url( 'https://dash.cloudflare.com/login' )
					),
					$allowed_inline_tags
				);
				?>
			</li>
			<li>
				<?php
				echo wp_kses(
					__( 'Once logged in, navigate to <b>"Analytics &gt; Web Analytics"</b>', 'helper-for-cloudflare-web-analytics' ),
					array( 'b' => array() )
				);
				?>
			</li>
			<li><?php esc_html_e( 'Add a new website or view the JS snippet for an existing site', 'helper-for-cloudflare-web-analytics' ); ?></li>
			<li>
				<?php
				echo wp_kses(
					__( 'In the snippet code, copy the <code>token</code> value (i.e. <code>"token": "<b><u>999d231dasda123kllklkdasc2</u></b>"</code>)', 'helper-for-cloudflare-web-analytics' ),
					$allowed_inline_tags
				);
				?>
				<details>
					<summary class="example"><?php esc_html_e( 'Example Snippet', 'helper-for-cloudflare-web-analytics' ); ?></summary>
					<code class="details-inner"><?php echo esc_html( $example_snippet ); ?></code>
				</details>
			</li>
			<li>
				<?php
				echo wp_kses(
					__( 'The plugin uses the value <b>999d231dasda123kllklkdasc2</b> from the example above', 'helper-for-cloudflare-web-analytics' ),
					array( 'b' => array() )
				);
				?>
			</li>
			<li><?php esc_html_e( 'Paste the token into the field.', 'helper-for-cloudflare-web-analytics' ); ?></li>
		</ol>

		<h3><?php esc_html_e( 'Questions about Cloudflare Web Analytics?', 'helper-for-cloudflare-web-analytics' ); ?></h3>

		<p>
			<?php
			echo wp_kses(
				sprintf(
					/* translators: %s: URL to Cloudflare documentation */
					__( 'Check out <a href="%s" target="_blank">the documentation on Cloudflare\'s site</a>.', 'helper-for-cloudflare-web-analytics' ),
					esc_url( 'https://developers.cloudflare.com/analytics/web-analytics/' )
				),
				$allowed_inline_tags
			);
			?>
		</p>
	</details>
	<?php
}

/**
 * Adds input box for Cloudflare token
 *
 * @return void
 */
function cf_web_analytics_setting_token() {

	$token = cf_web_analytics_get_token();

	printf(
		"<input id='token' name='cf_web_analytics_options[token]' minlength='8' pattern='[\Sa-zA-Z0-9-]+' type='text' value='%s' placeholder='%s' title='%s' />",
		esc_attr( $token ),
		esc_attr__( 'ex: absd312dcdd312dasdas13', 'helper-for-cloudflare-web-analytics' ),
		esc_attr__( 'absd312dcdd312dasdas13', 'helper-for-cloudflare-web-analytics' )
	);

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
				$error_msg = __( 'Token must not be empty.', 'helper-for-cloudflare-web-analytics' );
			} else {
				$error_msg = __( 'Incorrect value entered. Token should be only letters and numbers.', 'helper-for-cloudflare-web-analytics' );
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

	if ( ! is_array( $options ) || ! isset( $options['token'] ) ) {
		return '';
	}

	return $options['token'];

}
