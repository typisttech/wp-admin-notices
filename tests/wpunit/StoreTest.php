<?php

declare(strict_types=1);

namespace TypistTech\WPAdminNotices;

use Codeception\TestCase\WPTestCase;

/**
 * @covers \TypistTech\WPAdminNotices\Store
 */
class StoreTest extends WPTestCase
{
    public function setUp()
    {
        parent::setUp();
        delete_option('my_option_key');
        $this->store = new Store('my_option_key');
    }

    public function tearDown()
    {
        delete_option('my_option_key');
        parent::tearDown();
    }

    /** @test */
    public function it_counts_notice_size_in_database()
    {
        update_option(
            'my_option_key',
            [
                new Notice('my-handle-1', 'My content.'),
                new StickyNotice('my-handle-2', 'My content.'),
            ]
        );

        $this->assertSame(2, $this->store->size());
    }

    /** @test */
    public function it_counts_zero_when_no_notices_in_database()
    {
        $this->assertSame(0, $this->store->size());
    }

    /** @test */
    public function it_gets_notices_from_database()
    {
        $expected = [
            new Notice('my-handle-1', 'My content.'),
            new Notice('my-handle-2', 'My content.'),
        ];
        update_option('my_option_key', $expected);

        $this->assertEquals($expected, $this->store->all());
    }

    /** @test */
    public function it_gets_empty_array_when_no_notices_in_database()
    {
        delete_option('my_option_key');

        $this->assertSame([], $this->store->all());
    }

    /** @test */
    public function it_gets_sticky_notices_from_database()
    {
        $notices = [
            new Notice('my-handle-1', 'My content.'),
            new Notice('my-handle-2', 'My content.'),
            new StickyNotice('my-handle-3', 'My content.'),
            new StickyNotice('my-handle-4', 'My content.'),
        ];
        update_option('my_option_key', $notices);

        $this->assertEquals(
            [
                new StickyNotice('my-handle-3', 'My content.'),
                new StickyNotice('my-handle-4', 'My content.'),
            ],
            $this->store->sticky()
        );
    }

    /** @test */
    public function it_gets_empty_array_when_no_sticky_notices_in_database()
    {
        delete_option('my_option_key');

        $actual = $this->store->sticky();

        $this->assertSame([], $actual);
    }

    /** @test */
    public function it_adds_notices_into_fresh_database()
    {
        $notice = new Notice('my-handle-1', 'My content.');

        $this->store->add($notice);

        $this->assertEquals(
            [
                $notice,
            ],
            get_option('my_option_key')
        );
    }

    /** @test */
    public function it_adds_notices_into_existing_database()
    {
        $notice1 = new Notice('my-handle-1', 'My content.');
        $notice2 = new Notice('my-handle-2', 'My content.');
        $notice3 = new Notice('my-handle-3', 'My content.');
        $notice4 = new Notice('my-handle-4', 'My content.');

        $this->store->add($notice1);
        $this->store->add($notice2);
        $this->store->add($notice3, $notice4);

        $this->assertEquals(
            [
                $notice1,
                $notice2,
                $notice3,
                $notice4,
            ],
            get_option('my_option_key')
        );
    }

    /** @test */
    public function it_updates_notice_by_handle()
    {
        $notice1 = new Notice('my-handle-1', 'My content.');
        $notice2 = new Notice('my-handle-2', 'My content.');
        $notice3 = new Notice('my-handle-3', 'My content.');

        $newNotice2 = new Notice('my-handle-2', 'My new content.');

        $this->store->add($notice1, $notice2, $notice3);
        $this->store->add($newNotice2);

        $this->assertEquals(
            [
                $notice1,
                $notice3,
                $newNotice2,
            ],
            get_option('my_option_key')
        );
    }

    /** @test */
    public function it_deletes_notice_from_database()
    {
        $notices = [
            new Notice('my-handle-1', 'My content.'),
            new StickyNotice('my-handle-2', 'My content.'),
        ];
        update_option('my_option_key', $notices);

        $this->store->delete('my-handle-1');

        $this->assertEquals(
            [
                new StickyNotice('my-handle-2', 'My content.'),
            ],
            get_option('my_option_key')
        );
    }

    /** @test */
    public function it_deletes_nothing_when_given_handle_does_not_exist_in_database()
    {
        $notices = [
            new Notice('my-handle-1', 'My content.'),
            new StickyNotice('my-handle-2', 'My content.'),
        ];
        update_option('my_option_key', $notices);

        $this->store->delete('my-handle-9999999');

        $this->assertEquals($notices, get_option('my_option_key'));
    }

    /** @test */
    public function it_resets_database_state()
    {
        $notice1 = new Notice('my-handle-1', 'My content.');
        $notice2 = new StickyNotice('my-handle-2', 'My content.');
        $notice3 = new Notice('my-handle-3', 'My content.');
        $notice4 = new StickyNotice('my-handle-4', 'My content.');

        update_option(
            'my_option_key',
            [
                $notice1,
                $notice2,
            ]
        );

        $this->store->reset([$notice3, $notice4]);

        $this->assertEquals(
            [
                $notice3,
                $notice4,
            ],
            get_option('my_option_key')
        );
    }

    /** @test */
    public function it_deletes_option_when_no_notices()
    {
        update_option(
            'my_option_key',
            [
                new Notice('my-handle-1', 'My content.'),
                new StickyNotice('my-handle-2', 'My content.'),
            ]
        );

        $this->store->reset();

        $this->assertFalse(get_option('my_option_key'));
    }
}
