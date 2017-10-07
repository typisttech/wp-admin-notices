<?php

declare(strict_types=1);

namespace TypistTech\WPAdminNotices;

use AspectMock\Test;
use Codeception\TestCase\WPTestCase;

/**
 * @covers \TypistTech\WPAdminNotices\Notifier
 */
class NotifierTest extends WPTestCase
{
    /**
     * @var Notifier
     */
    private $notifier;

    public function setUp()
    {
        parent::setUp();
        delete_option('my_option_key');
        $this->notifier = new Notifier('my_option_key', 'my_ajax_action');
    }

    /** @test */
    public function it_enqueue_notices_into_fresh_database()
    {
        $notice = new Notice('my-handle-1', 'My content.');

        $this->notifier->enqueue($notice);

        $this->assertEquals(
            [
                'my-handle-1' => $notice,
            ],
            get_option('my_option_key')
        );
    }

    /** @test */
    public function it_enqueue_notices_into_existing_database()
    {
        $notice1 = new Notice('my-handle-1', 'My content.');
        $notice2 = new Notice('my-handle-2', 'My content.');
        $notice3 = new Notice('my-handle-3', 'My content.');
        $notice4 = new Notice('my-handle-4', 'My content.');

        delete_option('my_option_key');
        $this->notifier->enqueue($notice1);
        $this->notifier->enqueue($notice2);
        $this->notifier->enqueue($notice3, $notice4);

        $this->assertEquals(
            [
                'my-handle-1' => $notice1,
                'my-handle-2' => $notice2,
                'my-handle-3' => $notice3,
                'my-handle-4' => $notice4,
            ],
            get_option('my_option_key')
        );
    }

    /** @test */
    public function it_renders_notices()
    {
        $notice1 = Test::double(Notice::class);
        $notice2 = Test::double(StickyNotice::class);
        Test::double(
            Store::class,
            [
                'all' => [
                    $notice1->construct('my-handle-1', 'My content.'),
                    $notice2->construct('my-handle-2', 'My content.'),
                ],
            ]
        );

        $this->notifier->renderNotices();

        $notice1->verifyInvokedOnce('render', ['my_ajax_action']);
        $notice2->verifyInvokedOnce('render', ['my_ajax_action']);
    }

    /** @test */
    public function it_persists_sticky_notices_after_rendering()
    {
        $notice = new Notice('one-off', 'My content.');
        $stickyNotice = new StickyNotice('sticky', 'My content.');
        $this->notifier->enqueue($notice, $stickyNotice);

        $this->notifier->renderNotices();

        $this->assertEquals(
            [
                'sticky' => $stickyNotice,
            ],
            get_option('my_option_key')
        );
    }

    /** @test */
    public function it_renders_script_when_notices_exist()
    {
        $expected = <<<'EOT'
<script>
    jQuery(document).ready(function ($) {
        $('.is-dismissible[data-action="my_ajax_action"]').on('click', 'button.notice-dismiss', function (event) {
            $.post(ajaxurl, {
                action: $(this).parent().data('action'),
                handle: $(this).parent().data('handle'),
                nonce: 'abcde123456',
            })
        });
    });
</script>
EOT;

        $this->notifier->enqueue(new Notice('my-handle', 'My content.'));
        Test::func(__NAMESPACE__, 'wp_create_nonce', 'abcde123456');

        ob_start();
        $this->notifier->renderScript();
        $actual = ob_get_clean();

        $this->assertSame($expected, $actual);
    }

    /** @test */
    public function it_skips_rendering_script_when_no_notices_exist()
    {
        ob_start();
        $this->notifier->renderScript();
        $actual = ob_get_clean();

        $this->assertSame('', $actual);
    }
}
