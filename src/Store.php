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
    public function __construct(string $optionKey)
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
     *  - Filter out non-NoticeInterface objects
     *
     * @param array $maybeNotices Array of objects to be normalized.
     *
     * @return NoticeInterface[]
     */
    private function normalize(array $maybeNotices): array
    {
        return array_values(
            array_filter(
                $maybeNotices,
                function ($notice) {
                    return $notice instanceof NoticeInterface;
                }
            )
        );
    }

    /**
     * Get sticky notices from database.
     *
     * @return NoticeInterface[]
     */
    public function sticky(): array
    {
        return $this->normalize(
            array_filter(
                $this->all(),
                function (NoticeInterface $notice) {
                    return $notice->isSticky();
                }
            )
        );
    }

    /**
     * Enqueue admin notices to database.
     *
     * @param NoticeInterface[] ...$notices Notices to be enqueued.
     *
     * @return void
     */
    public function add(NoticeInterface ...$notices)
    {
        $normalizedNewNotices = $this->normalize($notices);

        $newNoticeHandles = array_map(
            function (NoticeInterface $notice) {
                return $notice->getHandle();
            },
            $normalizedNewNotices
        );

        $oldNotices = array_filter(
            $this->all(),
            function (NoticeInterface $notice) use ($newNoticeHandles) {
                return ! in_array($notice->getHandle(), $newNoticeHandles, true);
            }
        );

        update_option(
            $this->optionKey,
            $this->normalize(
                array_merge(
                    $oldNotices,
                    $normalizedNewNotices
                )
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
        $this->reset(
            array_filter(
                $this->all(),
                function (NoticeInterface $notice) use ($handle) {
                    return $notice->getHandle() !== $handle;
                }
            )
        );
    }

    /**
     * Reset enqueued notices in database.
     *
     * @param NoticeInterface[] $notices Optional. New notice states.
     *
     * @return void
     */
    public function reset(array $notices = null)
    {
        $normalizedNotices = $this->normalize($notices ?? []);

        if (empty($normalizedNotices)) {
            delete_option($this->optionKey);
        } else {
            update_option($this->optionKey, $normalizedNotices);
        }
    }
}
