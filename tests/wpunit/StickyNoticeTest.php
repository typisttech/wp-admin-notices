<?php

declare(strict_types=1);

namespace TypistTech\WPAdminNotices;

use Codeception\TestCase\WPTestCase;

/**
 * @covers \TypistTech\WPAdminNotices\StickyNotice
 */
class StickyNoticeTest extends WPTestCase
{
    /** @test */
    public function it_is_an_instance_of_notice_interface()
    {
        $notice = new StickyNotice('my-handle', 'My content.');
        $this->assertInstanceOf(NoticeInterface::class, $notice);
    }

    /** @test */
    public function it_is_an_instance_of_notice()
    {
        $notice = new StickyNotice('my-handle', 'My content.');
        $this->assertInstanceOf(Notice::class, $notice);
    }

    /** @test */
    public function it_renders_html_content()
    {
        $notice = new StickyNotice('my-handle', 'My content.', 'error');
        $expected = '<div id="my-handle" data-handle="my-handle" data-action="my-action" ';
        $expected .= 'class="is-dismissible notice notice-error">My content.</div>';

        ob_start();
        $notice->render('my-action');
        $actual = ob_get_clean();

        $this->assertSame($expected, $actual);
    }
}
