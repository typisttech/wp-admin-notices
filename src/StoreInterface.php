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
 * Connector to notice storage.
 */
interface StoreInterface
{
    /**
     * Count all enqueued notices.
     *
     * @return int
     */
    public function size(): int;

    /**
     * Get all enqueued notices.
     *
     * @return NoticeInterface[]
     */
    public function all(): array;

    /**
     * Get sticky notices.
     *
     * @return NoticeInterface[]
     */
    public function sticky(): array;

    /**
     * Enqueue an admin notice.
     *
     * @param NoticeInterface[] ...$notices Notices to be enqueued.
     *
     * @return void
     */
    public function add(NoticeInterface ...$notices);

    /**
     * Delete an enqueued notice.
     *
     * @param string $handle Handle of the notice to be deleted.
     *
     * @return void
     */
    public function delete(string $handle);

    /**
     * Reset enqueued notices.
     *
     * @param NoticeInterface[] $notices Optional. New notice states.
     *
     * @return void
     */
    public function reset(array $notices = null);
}
