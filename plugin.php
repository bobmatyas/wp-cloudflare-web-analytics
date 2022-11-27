<?php
/**
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

function cf_web_analytics_add_settings_menu() {

    add_options_page( 'Cloudflare Web Analytics Settings', 'Cloudflare Web Analytics', 'manage_options',
        'cf_web_analytics', 'cf_web_analytics_option_page' );

}

function cf_web_analytics_option_page() {
    ?>
    <div class="wrap">
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

add_action('admin_init', 'cf_web_analytics_admin_init');


function cf_web_analytics_admin_init(){

    $args = array( 
        'type'              => 'string',
        'sanitize_callback' => 'cf_web_analytics_validate_options',
        'default'           => NULL
    );

    register_setting( 'cf_web_analytics_options', 'cf_web_analytics_options', $args );

    add_settings_section(
        'cf_web_analytics_main',
        'Cloudflare Web Analytics Settings',
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

function cf_web_analytics_section_text() {
    
    echo '<p>Enter your token. Add instructions here.</p>';

}

function cf_web_analytics_setting_token() {

    $options = get_option( 'cf_web_analytics_options' );
    $token = $options['token'];

    echo "<input id='token' name='cf_web_analytics_options[token]' pattern='[a-zA-Z0-9-]+' type='text' value='" . esc_attr( $token ) . "'/>";

}

function cf_web_analytics_validate_options( $input ) {

    $valid = array();
    $valid['token'] = preg_replace(
        '/[^A-Za-z0-9]/',
        '',
        $input['token'] );

    if( $valid['token'] !== $input['token'] ) {

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

function cf_web_analytics_getToken() {

    $options = get_option( 'cf_web_analytics_options' );
    $token = $options['token'];

    return $token;
}

function cf_web_analytics_load_scripts() {

    $token = cf_web_analytics_getToken();

    print 'token: '. $token .'';

    if ( '' == $token || null == $token ) {
        return;
    }
    
    wp_enqueue_script(
        'cf-web-analytics',
        'https://static.cloudflareinsights.com/beacon.min.js',
        null,
        null,
        true
    );
    
}

add_action( 'wp_enqueue_scripts', 'cf_web_analytics_load_scripts' );


function cf_web_analytics_add_attributes( $tag, $handle, $src ) {

    $token = cf_web_analytics_getToken();

    if ( 'cf-web-analytics' === $handle ) {
        $tag = "<script defer src='" . esc_url( $src ) . "'  data-cf-beacon='{\"token\": \"". $token ."\"}'></script>";
    }

    return $tag;
}

add_filter( 'script_loader_tag', 'cf_web_analytics_add_attributes', 10, 3 );
