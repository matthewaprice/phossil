<?php

namespace Phossil\Tests\Components;

use Phossil\Components\{
    Badge,
    BadgeOptions
};
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(Badge::class)]
#[CoversClass(BadgeOptions::class)]
class BadgeTest extends ComponentTestCase
{
    /**
     * @return array<string, array{string, string, string}>
     */
    public static function colors(): array
    {
        return [
            'amber' => ['amber', 'bg-amber-100', 'text-amber-800'],
            'green' => ['green', 'bg-green-200', 'text-green-800'],
            'blue' => ['blue', 'bg-blue-200', 'text-blue-800'],
            'red' => ['red', 'bg-red-200', 'text-red-800'],
            'gray' => ['gray', 'bg-zinc-200', 'text-zinc-800'],
            'unrecognised' => ['purple', 'bg-zinc-200', 'text-zinc-800'],
        ];
    }

    #[Test]
    public function it_renders_a_span_with_its_text(): void
    {
        $html = Badge::create(new BadgeOptions(text: 'Draft', id: 'status', ob: true));

        $this->assertStringContainsString('<span id="status" class="', $html);
        $this->assertStringContainsString('Draft', $html);
        $this->assertStringContainsString('</span>', $html);
    }

    #[Test]
    #[DataProvider('colors')]
    public function it_maps_its_color_to_background_and_text_classes(string $color, string $bg, string $text): void
    {
        $html = Badge::create(new BadgeOptions(color: $color, ob: true));

        $this->assertStringContainsString(" {$bg} ", $html);
        $this->assertStringContainsString(" {$text} ", $html);
    }

    #[Test]
    public function it_defaults_to_blue(): void
    {
        $html = Badge::create(new BadgeOptions(ob: true));

        $this->assertStringContainsString(' bg-blue-200 ', $html);
    }

    #[Test]
    public function it_uses_less_padding_when_condensed(): void
    {
        $condensed = Badge::create(new BadgeOptions(condensed: true, ob: true));
        $regular = Badge::create(new BadgeOptions(ob: true));

        $this->assertStringContainsString(' py-1 ', $condensed);
        $this->assertStringContainsString(' py-2 ', $regular);
    }

    #[Test]
    public function it_sets_the_font_size(): void
    {
        $default = Badge::create(new BadgeOptions(ob: true));
        $large = Badge::create(new BadgeOptions(font_size: 'text-sm', ob: true));

        $this->assertStringContainsString(' text-xs ', $default);
        $this->assertStringContainsString(' text-sm ', $large);
    }

    #[Test]
    public function it_appends_extra_classes(): void
    {
        $html = Badge::create(new BadgeOptions(class: 'ml-2', ob: true));

        $this->assertStringContainsString('ring-zinc-400 ml-2"', $html);
    }

    #[Test]
    public function it_adds_no_trailing_space_without_extra_classes(): void
    {
        $html = Badge::create(new BadgeOptions(ob: true));

        $this->assertStringContainsString('ring-zinc-400"', $html);
    }

    #[Test]
    public function it_echoes_and_returns_nothing_without_output_buffering(): void
    {
        $returned = null;
        $html = $this->echoed(function () use (&$returned) {
            $returned = Badge::create(new BadgeOptions(text: 'Live'));
        });

        $this->assertSame('', $returned);
        $this->assertStringContainsString('Live', $html);
    }
}
