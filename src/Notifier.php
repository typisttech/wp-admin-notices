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

class Notifier
{
    /**
     * AJAX request's 'action' property for sticky notices.
     *
     * @var string
     */
    private $action;

    /**
     * Connector to notice storage.
     *
     * @var StoreInterface
     */
    private $store;

    /**
     * Notifier constructor.
     *
     * @param string         $action AJAX request's 'action' property for sticky notices.
     * @param StoreInterface $store  Connector to notice storage.
     */
    public function __construct(string $action, StoreInterface $store)
    {
        $this->action = $action;
        $this->store = $store;
    }

    /**
     * Render all notices.
     *
     * @return void
     */
    public function renderNotices()
    {
        foreach ($this->store->all() as $handle => $notice) {
            $notice->render($this->action);
        }

        $this->store->reset(
            $this->store->sticky()
        );
    }

    /**
     * Render ajax script for dismissing sticky notices.
     *
     * @return void
     */
    public function renderScript()
    {
        if ($this->store->size() < 1) {
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
        if (! wp_doing_ajax() || ! is_user_logged_in()) {
            wp_die(-1, 403);
        }

        check_ajax_referer($this->action, 'nonce');

        if (isset($_POST['handle'])) { // Input var okay.
            $handle = sanitize_key($_POST['handle']); // Input var okay.
            $this->store->delete($handle);
        }

        wp_send_json_success(null, 204);
    }
}
