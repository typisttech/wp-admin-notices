<?php
/**
 * WP Admin Notices
 *
 * A simplified OOP implementation of the WordPress admin notices.
 *
 * @package TypistTech\WPAdminNotices
 *
 * @author Typist Tech <wp-admin-notices@typist.tech>
 * @copyright 2017 Typist Tech
 * @license GPL-2.0+
 *
 * @see https://www.typist.tech/projects/wp-admin-notices
 * @see https://github.com/TypistTech/wp-admin-notices
 */

declare(strict_types=1);

namespace TypistTech\WPAdminNotices;

abstract class AbstractNotice implements NoticeInterface
{
    const IS_STICKY = false;

    /**
     * The notice's unique identifier. Also used to permanently dismiss a dismissible notice.
     *
     * @var string
     */
    protected $handle;

    /**
     * The HTML content of the notice.
     *
     * @var string
     */
    protected $content;

    /**
     * The notice's type. Expecting one of error, warning, info, success.
     *
     * @var string
     */
    protected $type;

    /**
     * Notice constructor.
     *
     * @param string      $handle  The notice's unique identifier. Also used to permanently dismiss a dismissible
     *                             notice.
     * @param string      $content The HTML content of the notice.
     * @param string|null $type    The notice's type. Expecting one of error, warning, info, success.
     */
    public function __construct(
        string $handle,
        string $content,
        string $type = null
    ) {
        $this->handle = sanitize_key($handle);
        $this->content = wp_kses_post($content);
        $this->type = sanitize_html_class($type ?? 'info');
    }

    /**
     * Echo notice to screen.
     *
     * @param string $action Unused.
     *
     * @return void
     */
    abstract public function render(string $action);

    /**
     * {@inheritdoc}
     */
    public function getHandle(): string
    {
        return $this->handle;
    }

    /**
     * {@inheritdoc}
     */
    public function isSticky(): bool
    {
        return static::IS_STICKY;
    }
}
