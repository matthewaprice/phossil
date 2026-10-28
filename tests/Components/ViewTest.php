<?php

namespace Phossil\Tests\Components;

use Phossil\Components\View;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(View::class)]
class ViewTest extends ComponentTestCase
{
    #[Test]
    public function it_renders_when_cast_to_a_string(): void
    {
        $this->assertSame('<p>Hello, World</p>', (string) $this->view('World'));
    }

    #[Test]
    public function it_renders_when_echoed(): void
    {
        $view = $this->view('World');

        $this->assertSame('<p>Hello, World</p>', $this->echoed(function () use ($view) {
            echo $view;
        }));
    }

    #[Test]
    public function it_captures_a_template_that_reads_the_views_properties(): void
    {
        $this->assertSame('<p>Hello, phossil</p>', $this->view('phossil')->render());
    }

    #[Test]
    public function it_closes_the_buffer_it_captures_with(): void
    {
        $level = ob_get_level();

        $this->view('World')->render();

        $this->assertSame($level, ob_get_level());
    }

    /**
     * A view that greets a name through capture().
     */
    private function view(string $name): View
    {
        return new readonly class($name) extends View {
            public function __construct(public string $name)
            {
            }

            public function render(): string
            {
                return $this->capture(function () {
                    ?><p>Hello, <?= e($this->name); ?></p><?php
                });
            }
        };
    }
}
