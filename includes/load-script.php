<?php
/**
 * Controls display of Cloudflare Web Analytics script.
 *
 * @package "Cloudflare Web Analytics"
 */

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
		// phpcs:ignore
		$tag = "<script defer src='" . esc_url( $src ) . "'  data-cf-beacon='{\"token\": \"" . $token . "\"}'></script>";
	}

	return $tag;
}

add_filter( 'script_loader_tag', 'cf_web_analytics_add_attributes', 10, 3 );
?>
