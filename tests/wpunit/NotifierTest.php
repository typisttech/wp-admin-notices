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

    /**
     * @var Store
     */
    private $store;

    public function setUp()
    {
        parent::setUp();
        delete_option('my_option_key');
        $this->store = new Store('my_option_key');
        $this->notifier = new Notifier('my_ajax_action', $this->store);
    }

    /** @test */
    public function it_renders_notices()
    {
        $notice1 = Test::double(Notice::class);
        $notice2 = Test::double(StickyNotice::class);
        $this->store->add(
            $notice1->construct('my-handle-1', 'My content.'),
            $notice2->construct('my-handle-2', 'My content.')
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
        $this->store->add($notice, $stickyNotice);

        $this->notifier->renderNotices();

        $this->assertEquals(
            [
                'sticky' => $stickyNotice,
            ],
            $this->store->all()
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

        $this->store->add(
            new Notice('my-handle', 'My content.')
        );
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
