<?php
/**
 * This plugin adds support for Cloudflare Web Analytics in WordPress.
 *
 * @package "Helper for Cloudflare Web Analytics"
 * @version 1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:       Helper for Cloudflare Web Analytics
 * Description:       Adds Cloudflare Web Analytics JavaScript to WordPress.
 * Version:           1.0.1
 * Requires at least: 5.3
 * Requires PHP:      5.6
 * Author:            Bob Matyas
 * Author URI:        https://www.bobmatyas.com
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       cf-web-analytics
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

require __DIR__ . '/includes/settings.php';
require __DIR__ . '/includes/load-script.php';
