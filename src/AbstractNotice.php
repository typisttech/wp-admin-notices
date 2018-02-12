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
 * @license   GPL-2.0+
 *
 * @see       https://typist.tech/projects/wp-admin-notices
 * @see       https://github.com/TypistTech/wp-admin-notices
 */

declare(strict_types=1);

namespace TypistTech\WPAdminNotices;

use InvalidArgumentException;

abstract class AbstractNotice implements NoticeInterface
{
    const IS_STICKY = false;
    const HTML_CLASSES = [];

    const UPDATE_NAG = 'UPDATE_NAG';
    const ERROR = 'ERROR';
    const WARNING = 'WARNING';
    const INFO = 'INFO';
    const SUCCESS = 'SUCCESS';

    /**
     * The notice's unique identifier. Also used to permanently dismiss a sticky notice.
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
     * HTML class for the notice.
     *
     * @var string
     */
    protected $htmlClass;

    /**
     * Notice constructor.
     *
     * @param string      $handle  The notice's unique identifier. Also used to permanently dismiss a sticky
     *                             notice.
     * @param string      $content The HTML content of the notice.
     * @param string|null $type    The notice's type. Expecting one of UPDATE_NAG, ERROR, WARNING, INFO, SUCCESS.
     *                             Default is INFO.
     *
     * @throws InvalidArgumentException When $type is not supported.
     */
    public function __construct(
        string $handle,
        string $content,
        string $type = null
    ) {
        $type = $type ?? static::INFO;
        if (! array_key_exists($type, static::HTML_CLASSES)) {
            $errorMessage = sprintf(
                'Type "%1$s" not found. Valid options are: %2$s.',
                $type,
                implode(', ', array_keys(static::HTML_CLASSES))
            );

            throw new InvalidArgumentException($errorMessage);
        }

        $this->handle = sanitize_key($handle);
        $this->content = wp_kses_post($content);
        $this->htmlClass = static::HTML_CLASSES[$type];
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
