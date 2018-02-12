<?php
/**
 * WP Admin Notices
 *
 * A simplified OOP implementation of the WordPress admin notices.
 *
 * @package   TypistTech\WPAdminNotices
 *
 * @author    Typist Tech <wp-admin-notices@typist.tech>
 * @copyright 2017-2018 Typist Tech
 * @license   GPL-2.0-or-later
 *
 * @see       https://typist.tech/projects/wp-admin-notices
 * @see       https://github.com/TypistTech/wp-admin-notices
 */

declare(strict_types=1);

namespace TypistTech\WPAdminNotices;

final class Factory
{
    /**
     * Set up store and notifier hooks.
     *
     * @param string $optionKey Key in options table that holds all enqueued notices.
     * @param string $action    AJAX request's 'action' property for sticky notices.
     *
     * @return Store
     */
    public static function build(string $optionKey, string $action): Store
    {
        $store = new Store($optionKey);
        $notifier = new Notifier($action, $store);

        add_action('admin_notices', [$notifier, 'renderNotices']);
        add_action("wp_ajax_$action", [$notifier, 'dismissNotice']);
        add_action('admin_footer', [$notifier, 'renderScript']);

        return $store;
    }
}
