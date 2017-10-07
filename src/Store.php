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

/**
 * You should never depend on any methods in this class.
 *
 * @internal
 */
class Store
{
    /**
     * Count all enqueued notices from database.
     *
     * You should never depend on any methods in this class.
     *
     * @internal
     *
     * @param string $optionKey Key in options table that holds all enqueued notices.
     *
     * @return int
     */
    public static function size(string $optionKey): int
    {
        return count(self::all($optionKey));
    }

    /**
     * Get all enqueued notices from database.
     *
     * You should never depend on any methods in this class.
     *
     * @internal
     *
     * @param string $optionKey Key in options table that holds all enqueued notices.
     *
     * @return NoticeInterface[]
     */
    public static function all(string $optionKey): array
    {
        return self::normalize(
            (array) get_option($optionKey, [])
        );
    }

    /**
     * Normalize notice array.
     *  - Use notice handle as array index
     *  - Filter out non-NoticeInterface objects
     *
     * @param array $maybeNotices Array of objects to be normalized.
     *
     * @return NoticeInterface[]
     */
    private static function normalize(array $maybeNotices): array
    {
        return array_reduce(
            $maybeNotices,
            function ($carry, $notice) {
                if ($notice instanceof NoticeInterface) {
                    $carry[$notice->getHandle()] = $notice;
                }

                return $carry;
            },
            []
        );
    }

    /**
     * Get sticky notices from database.
     *
     * You should never depend on any methods in this class.
     *
     * @internal
     *
     * @param string $optionKey Key in options table that holds all enqueued notices.
     *
     * @return NoticeInterface[]
     */
    public static function sticky(string $optionKey): array
    {
        return array_filter(
            self::all($optionKey),
            function (NoticeInterface $notice) {
                return $notice instanceof StickyNotice;
            }
        );
    }

    /**
     * Enqueue an admin notice to database.
     *
     * You should never depend on any methods in this class.
     *
     * @internal
     *
     * @param string            $optionKey  Key in options table that holds all enqueued notices.
     * @param NoticeInterface[] ...$notices Notices to be enqueued.
     *
     * @return void
     */
    public static function add(string $optionKey, NoticeInterface ...$notices)
    {
        update_option(
            $optionKey,
            array_merge(
                self::all($optionKey),
                self::normalize($notices)
            )
        );
    }

    /**
     * Delete an enqueued notice from database.
     *
     * You should never depend on any methods in this class.
     *
     * @internal
     *
     * @param string $optionKey Key in options table that holds all enqueued notices.
     * @param string $handle    Handle of the notice to be deleted.
     *
     * @return void
     */
    public static function delete(string $optionKey, string $handle)
    {
        $notices = self::all($optionKey);

        if (array_key_exists($handle, $notices)) {
            unset($notices[$handle]);
            self::reset($optionKey, ...array_values($notices));
        }
    }

    /**
     * Reset enqueued notices in database.
     *
     * You should never depend on any methods in this class.
     *
     * @internal
     *
     * @param string            $optionKey  Key in options table that holds all enqueued notices.
     * @param NoticeInterface[] ...$notices New notice states.
     *
     * @return void
     */
    public static function reset(string $optionKey, NoticeInterface ...$notices)
    {
        $normalizedNotices = self::normalize($notices);

        if (empty($normalizedNotices)) {
            delete_option($optionKey);
        } else {
            update_option($optionKey, $normalizedNotices);
        }
    }
}
