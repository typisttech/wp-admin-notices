<?php

declare(strict_types=1);

namespace TypistTech\WPAdminNotices;

use Codeception\TestCase\WPTestCase;
use InvalidArgumentException;

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
    public function it_is_an_instance_of_abstract_notice()
    {
        $notice = new StickyNotice('my-handle', 'My content.');
        $this->assertInstanceOf(AbstractNotice::class, $notice);
    }

    /** @test */
    public function it_is_sticky()
    {
        $notice = new StickyNotice('my-handle', 'My content.');
        $this->assertTrue($notice->isSticky());
    }

    /** @test */
    public function it_renders_html_content()
    {
        $notice = new StickyNotice('my-handle', 'My content.', StickyNotice::ERROR);
        $expected = '<div id="my-handle" data-handle="my-handle" data-action="my-action" ';
        $expected .= 'class="is-dismissible notice notice-error">My content.</div>';

        ob_start();
        $notice->render('my-action');
        $actual = ob_get_clean();

        $this->assertSame($expected, $actual);
    }

    /** @test */
    public function it_throws_invalid_argument_exception_when_type_is_update_nag()
    {
        $expectedErrorMessage = 'Type "UPDATE_NAG" not found. '
                                . 'Valid options are: ERROR, WARNING, INFO, SUCCESS.';

        $this->tester->expectException(
            new InvalidArgumentException($expectedErrorMessage),
            function () {
                new StickyNotice('my-handle', 'My content.', AbstractNotice::UPDATE_NAG);
            }
        );
    }
}
