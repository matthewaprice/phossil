<?php

namespace Phossil\Tests\Components;

use Phossil\Components\{
    Card,
    CardOptions
};
use PHPUnit\Framework\Attributes\{
    CoversClass,
    Test
};

#[CoversClass(Card::class)]
#[CoversClass(CardOptions::class)]
class CardTest extends ComponentTestCase
{
    #[Test]
    public function it_renders_its_content(): void
    {
        $html = Card::create(new CardOptions(content: '<p>Body</p>', id: 'plan', ob: true));

        $this->assertStringContainsString('<div id="plan" class="divide-y divide-zinc-200 rounded-lg border border-zinc-200 bg-white shadow-sm">', $html);
        $this->assertStringContainsString('<div class="px-5 py-4 text-zinc-600"><p>Body</p></div>', $html);
    }

    #[Test]
    public function it_renders_a_title_and_footer_when_given(): void
    {
        $html = Card::create(new CardOptions(title: 'Pricing', footer: '<a href="/">More</a>', ob: true));

        $this->assertStringContainsString('<div class="px-5 py-3 font-semibold text-zinc-900">Pricing</div>', $html);
        $this->assertStringContainsString('<div class="px-5 py-3 text-sm"><a href="/">More</a></div>', $html);
    }

    #[Test]
    public function it_omits_an_empty_title_and_footer(): void
    {
        $html = Card::create(new CardOptions(content: 'Body', ob: true));

        $this->assertStringNotContainsString('font-semibold', $html);
        $this->assertStringNotContainsString('text-sm', $html);
    }

    #[Test]
    public function it_appends_extra_classes(): void
    {
        $html = Card::create(new CardOptions(class: 'h-full', ob: true));

        $this->assertStringContainsString('shadow-sm h-full"', $html);
    }

    #[Test]
    public function it_renders_data_attributes(): void
    {
        $html = Card::create(new CardOptions(data: ['plan' => 'pro'], ob: true));

        $this->assertStringContainsString('shadow-sm" data-plan="pro">', $html);
    }

    #[Test]
    public function it_echoes_and_returns_nothing_without_output_buffering(): void
    {
        $returned = null;
        $html = $this->echoed(function () use (&$returned) {
            $returned = Card::create(new CardOptions(content: 'Body'));
        });

        $this->assertSame('', $returned);
        $this->assertStringContainsString('Body', $html);
    }
}
