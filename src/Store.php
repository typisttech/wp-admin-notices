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
 * Connector to notice storage, using wp_option table.
 */
class Store implements StoreInterface
{
    /**
     * Key in options table that holds all enqueued notices.
     *
     * @var string
     */
    private $optionKey;

    /**
     * Store constructor.
     *
     * @param string $optionKey Key in options table that holds all enqueued notices.
     */
    public function __construct($optionKey)
    {
        $this->optionKey = $optionKey;
    }

    /**
     * Count all enqueued notices from database.
     *
     * @return int
     */
    public function size(): int
    {
        return count($this->all());
    }

    /**
     * Get all enqueued notices from database.
     *
     * @return NoticeInterface[]
     */
    public function all(): array
    {
        return $this->normalize(
            (array) get_option($this->optionKey, [])
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
    private function normalize(array $maybeNotices): array
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
     * @return NoticeInterface[]
     */
    public function sticky(): array
    {
        return array_filter(
            $this->all(),
            function (NoticeInterface $notice) {
                return $notice instanceof StickyNotice;
            }
        );
    }

    /**
     * Enqueue an admin notice to database.
     *
     * @param NoticeInterface[] ...$notices Notices to be enqueued.
     *
     * @return void
     */
    public function add(NoticeInterface ...$notices)
    {
        update_option(
            $this->optionKey,
            array_merge(
                $this->all(),
                $this->normalize($notices)
            )
        );
    }

    /**
     * Delete an enqueued notice from database.
     *
     * @param string $handle Handle of the notice to be deleted.
     *
     * @return void
     */
    public function delete(string $handle)
    {
        $notices = $this->all();

        if (array_key_exists($handle, $notices)) {
            unset($notices[$handle]);
            $this->reset(...array_values($notices));
        }
    }

    /**
     * Reset enqueued notices in database.
     *
     * @param NoticeInterface[] ...$notices New notice states.
     *
     * @return void
     */
    public function reset(NoticeInterface ...$notices)
    {
        $normalizedNotices = $this->normalize($notices);

        if (empty($normalizedNotices)) {
            delete_option($this->optionKey);
        } else {
            update_option($this->optionKey, $normalizedNotices);
        }
    }
}
