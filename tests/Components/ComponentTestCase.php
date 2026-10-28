<?php

namespace Phossil\Tests\Components;

use Phossil\Components\{
    Component,
    Dialog
};
use PHPUnit\Framework\TestCase;

/**
 * Base class for component tests.
 *
 * Resets the static state components share across a request, so each test
 * starts from a fresh page.
 *
 * @author Matthew A Price
 */
abstract class ComponentTestCase extends TestCase
{
    protected function setUp(): void
    {
        Component::$component_num = 0;
        Dialog::$dialogs = [];
        unset($_SERVER['REQUEST_URI']);
    }

    protected function tearDown(): void
    {
        unset($_SERVER['REQUEST_URI']);
    }

    /**
     * Runs a component that echoes and returns what it wrote.
     *
     * @param callable $render Calls the component's create().
     * @return string The echoed markup.
     */
    protected function echoed(callable $render): string
    {
        ob_start();
        $render();
        return (string) ob_get_clean();
    }
}
