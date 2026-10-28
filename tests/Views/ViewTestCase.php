<?php

namespace Phossil\Tests\Views;

use Phossil\Components\{
    Component,
    Dialog
};
use Phossil\Tests\Support\FakesViteDevServer;
use PHPUnit\Framework\TestCase;

/**
 * Base class for view tests.
 *
 * Gives each test a fresh page: generated ids start from zero, no dialogs are
 * waiting for the footer, and the request is for the home page. Views that use
 * the Layout get the same Vite tags whether or not the vite container runs.
 *
 * @author Matthew A Price
 */
abstract class ViewTestCase extends TestCase
{
    use FakesViteDevServer;

    protected function setUp(): void
    {
        Component::$component_num = 0;
        Dialog::$dialogs = [];
        unset($_SERVER['REQUEST_URI']);

        $this->startFakeViteDevServer();
    }

    protected function tearDown(): void
    {
        $this->stopFakeViteDevServer();

        unset($_SERVER['REQUEST_URI']);
    }
}
