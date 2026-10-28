<?php

namespace Phossil\Tests\Components;

use Phossil\Components\{
    AnchorOptions,
    Button,
    ButtonOptions
};
use Phossil\Enums\Buttons;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(Button::class)]
#[CoversClass(ButtonOptions::class)]
class ButtonTest extends ComponentTestCase
{
    #[Test]
    public function it_renders_a_button(): void
    {
        $html = Button::create(new ButtonOptions(text: 'Save', id: 'save', ob: true));

        $this->assertStringContainsString('<button id="save" type="button" class="' . Buttons::make(Buttons::Medium) . ' cursor-pointer">', $html);
        $this->assertStringContainsString('Save', $html);
        $this->assertStringContainsString('</button>', $html);
    }

    #[Test]
    public function it_composes_its_classes_from_styles_cursor_and_class(): void
    {
        $html = Button::create(new ButtonOptions(
            styles: [Buttons::Small, Buttons::Primary],
            cursor: 'cursor-wait',
            class: 'w-full',
            ob: true
        ));

        $this->assertStringContainsString('class="' . Buttons::make(Buttons::Small, Buttons::Primary) . ' cursor-wait w-full"', $html);
    }

    #[Test]
    public function it_can_be_disabled(): void
    {
        $disabled = Button::create(new ButtonOptions(disabled: true, ob: true));
        $enabled = Button::create(new ButtonOptions(ob: true));

        $this->assertStringContainsString(' disabled="disabled"', $disabled);
        $this->assertStringNotContainsString('disabled=', $enabled);
    }

    #[Test]
    public function it_renders_data_attributes(): void
    {
        $html = Button::create(new ButtonOptions(data: ['dialog' => 'help'], ob: true));

        $this->assertStringContainsString(' data-dialog="help"', $html);
    }

    #[Test]
    public function it_renders_a_styled_anchor_when_given_link_text(): void
    {
        $html = Button::create(new ButtonOptions(
            text: 'Ignored',
            link: new AnchorOptions(href: '/docs/', text: 'Docs', id: 'docs', target: '_blank', data: ['track' => 'cta']),
            ob: true
        ));

        $this->assertStringNotContainsString('<button', $html);
        $this->assertStringNotContainsString('Ignored', $html);
        $this->assertStringContainsString(
            '<a id="docs" class="' . Buttons::make(Buttons::Medium) . ' cursor-pointer" href="/docs/" target="_blank" data-track="cta">',
            $html
        );
        $this->assertStringContainsString('Docs', $html);
    }

    #[Test]
    public function it_generates_a_link_id_when_the_link_has_none(): void
    {
        $html = Button::create(new ButtonOptions(link: new AnchorOptions(href: '/', text: 'Home'), ob: true));

        $this->assertMatchesRegularExpression('/<a id="link_id_\d{4}"/', $html);
    }

    #[Test]
    public function it_echoes_and_returns_nothing_without_output_buffering(): void
    {
        $returned = null;
        $html = $this->echoed(function () use (&$returned) {
            $returned = Button::create(new ButtonOptions(text: 'Save'));
        });

        $this->assertSame('', $returned);
        $this->assertStringContainsString('<button', $html);
    }
}
