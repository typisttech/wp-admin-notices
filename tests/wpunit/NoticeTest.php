<?php

declare(strict_types=1);

namespace TypistTech\WPAdminNotices;

use Codeception\TestCase\WPTestCase;

/**
 * @covers \TypistTech\WPAdminNotices\Notice
 */
class NoticeTest extends WPTestCase
{
    /** @test */
    public function it_is_an_instance_of_notice_interface()
    {
        $notice = new Notice('my-handle', 'My content.');
        $this->assertInstanceOf(NoticeInterface::class, $notice);
    }

    /** @test */
    public function it_has_handle_getter()
    {
        $notice = new Notice('my-handle', 'My content.');
        $this->assertSame('my-handle', $notice->getHandle());
    }

    /** @test */
    public function it_sanitize_handle()
    {
        $notice = new Notice('Spaced and-<>arrows', 'My content.');
        $this->assertSame('spacedand-arrows', $notice->getHandle());
    }

    /** @test */
    public function it_renders_html_content()
    {
        $notice = new Notice('my-handle', 'My content.', 'error');
        $expected = '<div id="my-handle" class="notice notice-error">My content.</div>';

        ob_start();
        $notice->render('my-action');
        $actual = ob_get_clean();

        $this->assertSame($expected, $actual);
    }
}
