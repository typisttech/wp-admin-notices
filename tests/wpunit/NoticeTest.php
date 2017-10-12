<?php

declare(strict_types=1);

namespace TypistTech\WPAdminNotices;

use Codeception\TestCase\WPTestCase;
use InvalidArgumentException;

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
    public function it_is_an_instance_of_abstract_notice()
    {
        $notice = new Notice('my-handle', 'My content.');
        $this->assertInstanceOf(AbstractNotice::class, $notice);
    }

    /** @test */
    public function it_is_not_sticky()
    {
        $notice = new Notice('my-handle', 'My content.');
        $this->assertFalse($notice->isSticky());
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
        $notice = new Notice('my-handle', 'My content.', Notice::ERROR);
        $expected = '<div id="my-handle" class="notice notice-error">My content.</div>';

        ob_start();
        $notice->render('my-action');
        $actual = ob_get_clean();

        $this->assertSame($expected, $actual);
    }

    /** @test */
    public function it_throws_invalid_argument_exception_when_type_is_not_supported()
    {
        $expectedErrorMessage = 'Type "something-else" not found. '
                                . 'Valid options are: UPDATE_NAG, ERROR, WARNING, INFO, SUCCESS.';

        $this->tester->expectException(
            new InvalidArgumentException($expectedErrorMessage),
            function () {
                new Notice('my-handle', 'My content.', 'something-else');
            }
        );
    }
}
