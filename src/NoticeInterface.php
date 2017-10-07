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

declare(strict_types=1);

namespace TypistTech\WPAdminNotices;

interface NoticeInterface
{
    /**
     * Echo notice to screen.
     *
     * @param string $action AJAX request's 'action' property for sticky notices.
     *
     * @return void
     */
    public function render(string $action);

    /**
     * Handle getter.
     *
     * @return string
     */
    public function getHandle(): string;
}
