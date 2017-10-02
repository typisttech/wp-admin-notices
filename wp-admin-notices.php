<?php
/**
 * WP Admin Notices
 *
 * A simplified OOP implementation of the WordPress admin notices.
 *
 * @package   TypistTech\WPAdminNotices
 *
 * @author    Typist Tech <wp-admin-notices@typist.tech>
 * @copyright 2017 Typist Tech
 * @license   GPL-2.0+
 *
 * @see       https://www.typist.tech/projects/wp-admin-notices
 * @see       https://github.com/TypistTech/wp-admin-notices
 */

/**
 * Plugin Name: WP Admin Notices
 * Plugin URI:  https://github.com/TypistTech/wp-admin-notices
 * Description: Example Plugin for WP Admin Notices
 * Version:     0.11.0
 * Author:      Tang Rufus
 * Author URI:  https://www.typist.tech/
 * Text Domain: wp-admin-notices
 * Domain Path: src/languages
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

declare(strict_types=1);

namespace TypistTech\WPAdminNotices;

// If this file is called directly, abort.
if (! defined('WPINC')) {
    die;
}

require_once __DIR__ . 'vendor/autoload.php';
require_once __DIR__ . 'class-plugin.php';

// TODO: Run the demo plugin.
wp_die('TODO: Run the demo plugin.');
