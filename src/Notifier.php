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

class Notifier
{
    /**
     * AJAX request's 'action' property for sticky notices.
     *
     * @var string
     */
    private $action;

    /**
     * Key in options table that holds all enqueued notices.
     *
     * @var string
     */
    private $optionKey;

    /**
     * Notifier constructor.
     *
     * @param string $optionKey Key in options table that holds all enqueued notices.
     * @param string $action    AJAX request's 'action' property for sticky notices.
     */
    public function __construct(string $optionKey, string $action)
    {
        $this->optionKey = $optionKey;
        $this->action = $action;
    }

    /**
     * Enqueue an admin notice to database.
     *
     * @param NoticeInterface[] ...$notices Notices to be enqueued.
     *
     * @return void
     */
    public function enqueue(NoticeInterface ...$notices)
    {
        Store::add($this->optionKey, ...$notices);
    }

    /**
     * Render all notices.
     *
     * @return void
     */
    public function renderNotices()
    {
        foreach (Store::all($this->optionKey) as $handle => $notice) {
            $notice->render($this->action);
        }

        Store::reset(
            $this->optionKey,
            ...array_values(Store::sticky($this->optionKey))
        );
    }

    /**
     * Render ajax script for dismissing sticky notices.
     *
     * @return void
     */
    public function renderScript()
    {
        if (Store::size($this->optionKey) < 1) {
            return;
        }

        $script = <<<'EOT'
<script>
    jQuery(document).ready(function ($) {
        $('.is-dismissible[data-action="%1$s"]').on('click', 'button.notice-dismiss', function (event) {
            $.post(ajaxurl, {
                action: $(this).parent().data('action'),
                handle: $(this).parent().data('handle'),
                nonce: '%2$s',
            })
        });
    });
</script>
EOT;

        // @codingStandardsIgnoreStart
        printf(
            $script,
            esc_attr($this->action),
            wp_create_nonce($this->action)
        );
        // @codingStandardsIgnoreEnd
    }

    /**
     * Dismiss a sticky notice from database.
     * AJAX request's handler.
     *
     * @return void
     */
    public function dismissNotice()
    {
        check_ajax_referer($this->action, 'nonce');

        $handle = null;
        if (isset($_POST['handle'])) { // Input var okay.
            $handle = sanitize_key($_POST['handle']); // Input var okay.
        }

        Store::delete($this->optionKey, $handle);

        wp_send_json_success(null, 204);
    }
}
