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

require_once __DIR__ . '/vendor/autoload.php';

const MY_UNIQUE_OPTION_KEY = 'my_demo_option';
const MY_UNIQUE_ACTION = 'my_demo_action';

$notifier = new Notifier(MY_UNIQUE_OPTION_KEY, MY_UNIQUE_ACTION);

add_action('admin_notices', [$notifier, 'renderNotices']);
add_action('wp_ajax_' . MY_UNIQUE_ACTION, [$notifier, 'dismissNotice']);
add_action('admin_footer', [$notifier, 'renderScript']);

add_action(
    'admin_init',
    function () use ($notifier) {
        $triggerNotice = new Notice(
            'trigger',
            '<p><strong>WPAdminNotices</strong>: Update a post to trigger notices.</p>'
        );
        $notifier->enqueue($triggerNotice);
    }
);

add_action(
    'post_updated',
    function ($post_id) use ($notifier) {
        $notices = [];

        $notices[] = new Notice(
            'notice-1',
            "<p><strong>WPAdminNotices</strong>: Post ID: $post_id has been updated.</p>",
            'success'
        );
        $notices[] = new Notice(
            'notice-2',
            '<p><strong>WPAdminNotices</strong>: Notices will self-destruct after showing up once.</p>',
            'warning'
        );
        $notices[] = new StickyNotice(
            'notice-3',
            '<p><strong>WPAdminNotices</strong>: StickyNotice persists in database until user clicks to dismiss it.</p>'
        );
        $notices[] = new StickyNotice(
            'notice-4',
            '<p><strong>WPAdminNotices</strong>: You can set notice type to change its color.</p>',
            'error'
        );
        $notices[] = new StickyNotice(
            'notice-5',
            '<p><strong>WPAdminNotices</strong>: <code>HTML</code> code is allowed.</p>'
        );
        $notices[] = new StickyNotice(
            'notice-6',
            '<p><strong>WPAdminNotices</strong>: I am a link to <a href="https://cnhv.co/47ka">www.typist.tech</a></p>'
        );

        $notifier->enqueue(...$notices);
    }
);
