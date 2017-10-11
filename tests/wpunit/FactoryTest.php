<?php

declare(strict_types=1);

namespace TypistTech\WPAdminNotices;

use AspectMock\Test;
use Codeception\TestCase\WPTestCase;

/**
 * @covers \TypistTech\WPAdminNotices\Factory
 */
class FactoryTest extends WPTestCase
{
    const DUMMY_ACTION = 'my_dummy_action';
    const DUMMY_OPTION_KEY = 'my_dummy_option_key';

    public function setUp()
    {
        parent::setUp();

        $this->addAction = Test::func(__NAMESPACE__, 'add_action', true);
        $this->store = new Store(self::DUMMY_OPTION_KEY);
        $this->notifier = new Notifier(self::DUMMY_ACTION, $this->store);
    }

    /** @test */
    public function it_builds_a_store()
    {
        $store = Factory::build(self::DUMMY_OPTION_KEY, self::DUMMY_ACTION);

        $this->assertInstanceOf(StoreInterface::class, $store);
    }

    /** @test */
    public function it_builds_a_store_with_given_option_key()
    {
        $actual = Factory::build(self::DUMMY_OPTION_KEY, self::DUMMY_ACTION);

        $this->assertEquals(
            $this->store,
            $actual
        );
    }

    /** @test */
    public function it_hooks_notifier_into_admin_notices()
    {
        Factory::build(self::DUMMY_OPTION_KEY, self::DUMMY_ACTION);

        $actualParams = $this->addAction->getCallsForMethod('add_action');
        $this->tester->assertContains(['admin_notices', [$this->notifier, 'renderNotices']], $actualParams);
    }

    /** @test */
    public function it_hooks_notifier_into_wp_ajax_action()
    {
        Factory::build(self::DUMMY_OPTION_KEY, self::DUMMY_ACTION);

        $actualParams = $this->addAction->getCallsForMethod('add_action');
        $this->tester->assertContains(
            ['wp_ajax_' . self::DUMMY_ACTION, [$this->notifier, 'dismissNotice']],
            $actualParams
        );
    }

    /** @test */
    public function it_hooks_notifier_into_admin_footer()
    {
        Factory::build(self::DUMMY_OPTION_KEY, self::DUMMY_ACTION);

        $actualParams = $this->addAction->getCallsForMethod('add_action');
        $this->tester->assertContains(['admin_footer', [$this->notifier, 'renderScript']], $actualParams);
    }
}
