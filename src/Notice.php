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

class Notice extends AbstractNotice
{
    /**
     * Echo notice to screen.
     *
     * @param string $action Unused.
     *
     * @return void
     */
    public function render(string $action)
    {
        printf(
            '<div id="%1$s" class="%2$s">%3$s</div>',
            esc_attr($this->getHandle()),
            esc_attr("notice notice-$this->type"),
            wp_kses_post($this->content)
        );
    }
}
