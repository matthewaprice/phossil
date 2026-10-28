<?php

namespace Phossil\Tests\Components;

use Phossil\Components\{
    Anchor,
    AnchorOptions
};
use PHPUnit\Framework\Attributes\{
    CoversClass,
    Test
};

#[CoversClass(Anchor::class)]
#[CoversClass(AnchorOptions::class)]
class AnchorTest extends ComponentTestCase
{
    #[Test]
    public function it_renders_an_anchor(): void
    {
        $html = Anchor::create(new AnchorOptions(href: '/about/', text: 'About', id: 'about', class: 'nav-link', ob: true));

        $this->assertStringContainsString('<a id="about" class="nav-link" href="/about/" target="_self">', $html);
        $this->assertStringContainsString('About', $html);
        $this->assertStringContainsString('</a>', $html);
    }

    #[Test]
    public function it_escapes_the_href_and_title(): void
    {
        $html = Anchor::create(new AnchorOptions(href: '/search?q=a&b="c"', title: 'Tom & "Jerry"', ob: true));

        $this->assertStringContainsString('href="/search?q=a&amp;b=&quot;c&quot;"', $html);
        $this->assertStringContainsString('title="Tom &amp; &quot;Jerry&quot;"', $html);
    }

    #[Test]
    public function it_leaves_the_text_as_trusted_html(): void
    {
        $html = Anchor::create(new AnchorOptions(href: '/', text: '<strong>Home</strong>', ob: true));

        $this->assertStringContainsString('<strong>Home</strong>', $html);
    }

    #[Test]
    public function it_omits_an_empty_title(): void
    {
        $html = Anchor::create(new AnchorOptions(href: '/', ob: true));

        $this->assertStringNotContainsString('title=', $html);
    }

    #[Test]
    public function it_sets_the_target(): void
    {
        $html = Anchor::create(new AnchorOptions(href: 'https://phossil.dev', target: '_blank', ob: true));

        $this->assertStringContainsString('target="_blank"', $html);
    }

    #[Test]
    public function it_marks_the_current_page(): void
    {
        $current = Anchor::create(new AnchorOptions(href: '/', current: true, ob: true));
        $other = Anchor::create(new AnchorOptions(href: '/', ob: true));

        $this->assertStringContainsString(' aria-current="page"', $current);
        $this->assertStringNotContainsString('aria-current', $other);
    }

    #[Test]
    public function it_renders_data_attributes(): void
    {
        $html = Anchor::create(new AnchorOptions(href: '/', data: ['track' => 'nav'], ob: true));

        $this->assertStringContainsString(' data-track="nav">', $html);
    }

    #[Test]
    public function it_generates_an_id_when_none_is_given(): void
    {
        $html = Anchor::create(new AnchorOptions(href: '/', ob: true));

        $this->assertStringContainsString('id="phossil_components_anchor-0"', $html);
    }

    #[Test]
    public function it_echoes_and_returns_nothing_without_output_buffering(): void
    {
        $returned = null;
        $html = $this->echoed(function () use (&$returned) {
            $returned = Anchor::create(new AnchorOptions(href: '/', text: 'Home'));
        });

        $this->assertSame('', $returned);
        $this->assertStringContainsString('<a ', $html);
        $this->assertStringContainsString('Home', $html);
    }
}
