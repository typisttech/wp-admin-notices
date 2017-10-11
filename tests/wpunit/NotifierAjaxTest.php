<?php

declare(strict_types=1);

namespace TypistTech\WPAdminNotices;

use Codeception\TestCase\WPAjaxTestCase;
use WPAjaxDieContinueException;
use WPAjaxDieStopException;

/**
 * @covers \TypistTech\WPAdminNotices\Notifier
 */
class NotifierAjaxTest extends WPAjaxTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->stickyNotice1 = new StickyNotice('my-sticky-1', 'My content.');
        $this->stickyNotice2 = new StickyNotice('my-sticky-2', 'My content.');
        $this->notice1 = new Notice('my-notice-1', 'My content.');
        $this->notice2 = new Notice('my-notice-2', 'My content.');

        $this->store = new Store('my_option_store');
        $this->store->reset(
            [
                $this->stickyNotice1,
                $this->stickyNotice2,
                $this->notice1,
                $this->notice2,
            ]
        );

        $this->notifier = new Notifier('my_ajax_action', $this->store);
        add_action('wp_ajax_my_ajax_action', [$this->notifier, 'dismissNotice']);
    }

    public function tearDown()
    {
        $this->store->reset();
        parent::tearDown();
    }

    /** @test */
    public function it_rejects_invalid_nonce()
    {
        $this->_setRole('subscriber');

        $_POST['nonce'] = wp_create_nonce('not-my-action');
        $_POST['handle'] = 'my-sticky-2';

        $this->tester->expectException(
            new WPAjaxDieStopException('-1'),
            function () {
                $this->_handleAjax('my_ajax_action');
            }
        );

        $this->assertEquals(
            [
                $this->stickyNotice1,
                $this->stickyNotice2,
                $this->notice1,
                $this->notice2,
            ],
            $this->store->all()
        );
    }

    /** @test */
    public function it_rejects_anonymous_user()
    {
        $this->logout();
        $_POST['nonce'] = wp_create_nonce('my_ajax_action');
        $_POST['handle'] = 'my-sticky-2';

        $this->tester->expectException(
            new WPAjaxDieStopException('-1'),
            function () {
                $this->_handleAjax('my_ajax_action');
            }
        );

        $this->assertEquals(
            [
                $this->stickyNotice1,
                $this->stickyNotice2,
                $this->notice1,
                $this->notice2,
            ],
            $this->store->all()
        );
    }

    /** @test */
    public function it_skips_non_existing_handle()
    {
        $this->_setRole('subscriber');

        $_POST['nonce'] = wp_create_nonce('my_ajax_action');
        $_POST['handle'] = 'non-existing';

        $this->tester->expectException(
            new WPAjaxDieContinueException(''),
            function () {
                $this->_handleAjax('my_ajax_action');
            }
        );

        $this->assertEquals(
            [
                $this->stickyNotice1,
                $this->stickyNotice2,
                $this->notice1,
                $this->notice2,
            ],
            $this->store->all()
        );
    }

    /** @test */
    public function it_dismisses_notice_by_handle()
    {
        $this->_setRole('subscriber');

        $_POST['nonce'] = wp_create_nonce('my_ajax_action');
        $_POST['handle'] = 'my-sticky-2';

        $this->tester->expectException(
            new WPAjaxDieContinueException(''),
            function () {
                $this->_handleAjax('my_ajax_action');
            }
        );

        $this->assertEquals(
            [
                $this->stickyNotice1,
                $this->notice1,
                $this->notice2,
            ],
            $this->store->all()
        );
    }
}
