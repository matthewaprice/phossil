<?php

namespace Phossil\Tests\Components;

use Phossil\Components\{
    ButtonGroup,
    ButtonGroupItem,
    ButtonGroupOptions
};
use Phossil\Enums\{
    Buttons,
    Icon
};
use PHPUnit\Framework\Attributes\{
    CoversClass,
    Test
};

#[CoversClass(ButtonGroup::class)]
#[CoversClass(ButtonGroupItem::class)]
#[CoversClass(ButtonGroupOptions::class)]
class ButtonGroupTest extends ComponentTestCase
{
    #[Test]
    public function it_renders_a_nav_of_anchors(): void
    {
        $html = $this->group([new ButtonGroupItem(href: '/', text: 'Home'), new ButtonGroupItem(href: '/about/', text: 'About')], id: 'tabs');

        $this->assertStringContainsString('<nav id="tabs" class="inline-flex rounded-lg" aria-label="Button Group">', $html);
        $this->assertCount(2, $this->itemClasses($html));
        $this->assertStringContainsString('Home', $html);
        $this->assertStringContainsString('About', $html);
    }

    #[Test]
    public function it_renders_nothing_without_items(): void
    {
        $returned = null;
        $html = $this->echoed(function () use (&$returned) {
            $returned = ButtonGroup::create(new ButtonGroupOptions());
        });

        $this->assertSame('', $returned);
        $this->assertSame('', $html);
    }

    #[Test]
    public function it_drops_skipped_items(): void
    {
        $html = $this->group([
            new ButtonGroupItem(href: '/', text: 'Home'),
            new ButtonGroupItem(href: '/admin/', text: 'Admin', skip: true),
        ]);

        $this->assertCount(1, $this->itemClasses($html));
        $this->assertStringNotContainsString('Admin', $html);
    }

    #[Test]
    public function it_renders_nothing_when_every_item_is_skipped(): void
    {
        $this->assertSame('', $this->group([new ButtonGroupItem(href: '/', skip: true)]));
    }

    #[Test]
    public function it_reports_whether_any_item_survives_skip(): void
    {
        $this->assertFalse(new ButtonGroupOptions()->hasItems());
        $this->assertFalse(new ButtonGroupOptions(items: [new ButtonGroupItem(href: '/', skip: true)])->hasItems());
        $this->assertTrue(new ButtonGroupOptions(items: [new ButtonGroupItem(href: '/', skip: true), new ButtonGroupItem(href: '/')])->hasItems());
    }

    #[Test]
    public function it_appends_extra_classes_to_the_nav(): void
    {
        $html = $this->group([new ButtonGroupItem(href: '/')], class: 'mt-4');

        $this->assertStringContainsString('class="inline-flex rounded-lg mt-4"', $html);
    }

    #[Test]
    public function it_rounds_the_outer_edges_of_an_inline_group(): void
    {
        [$first, $middle, $last] = $this->itemClasses($this->group($this->threeItems()));

        $this->assertStringContainsString('rounded-l-md border-l', $first);
        $this->assertStringNotContainsString('rounded-r-md', $first);
        $this->assertStringNotContainsString('rounded-l-md', $middle);
        $this->assertStringNotContainsString('rounded-r-md', $middle);
        $this->assertStringContainsString('rounded-r-md border-r', $last);
        $this->assertStringNotContainsString('rounded-l-md', $last);
    }

    #[Test]
    public function it_rounds_both_edges_of_a_single_item(): void
    {
        [$only] = $this->itemClasses($this->group([new ButtonGroupItem(href: '/')]));

        $this->assertStringContainsString('rounded-l-md border-l', $only);
        $this->assertStringContainsString('rounded-r-md border-r', $only);
    }

    #[Test]
    public function it_stacks_on_large_screens_when_stacked(): void
    {
        $html = $this->group($this->threeItems(), stacked: true);
        [$first, $middle, $last] = $this->itemClasses($html);

        $this->assertStringContainsString('class="inline-block lg:inline-flex lg:flex-col rounded-lg"', $html);
        $this->assertStringContainsString('border-l rounded-l-md lg:rounded-t-md lg:rounded-bl-none', $first);
        $this->assertStringContainsString('lg:border-t-0', $middle);
        $this->assertStringContainsString('lg:border-t-0', $last);
        $this->assertStringContainsString('border-r rounded-r-md lg:rounded-r-none lg:rounded-b-md lg:border-b', $last);
    }

    #[Test]
    public function it_uses_the_small_size_when_compact(): void
    {
        [$compact] = $this->itemClasses($this->group([new ButtonGroupItem(href: '/')], compact: true));
        [$regular] = $this->itemClasses($this->group([new ButtonGroupItem(href: '/')]));

        $this->assertStringContainsString(Buttons::make(Buttons::Small), $compact);
        $this->assertStringContainsString(Buttons::make(Buttons::Large), $regular);
    }

    #[Test]
    public function it_keeps_an_items_classes_ahead_of_the_compiled_ones(): void
    {
        [$item] = $this->itemClasses($this->group([new ButtonGroupItem(href: '/', class: 'item-extra')]));

        $this->assertStringStartsWith('item-extra ', $item);
    }

    #[Test]
    public function it_marks_the_item_whose_href_matches_the_request_path(): void
    {
        $_SERVER['REQUEST_URI'] = '/about/?ref=nav';

        [$home, $about] = $this->itemClasses($this->group([new ButtonGroupItem(href: '/'), new ButtonGroupItem(href: '/about/')]));

        $this->assertStringNotContainsString('bg-primary-50 text-primary-800', $home);
        $this->assertStringContainsString('bg-primary-50 text-primary-800', $about);
    }

    #[Test]
    public function it_marks_the_home_item_on_the_home_page(): void
    {
        $_SERVER['REQUEST_URI'] = '/';

        [$home, $about] = $this->itemClasses($this->group([new ButtonGroupItem(href: '/'), new ButtonGroupItem(href: '/about/')]));

        $this->assertStringContainsString('bg-primary-50 text-primary-800', $home);
        $this->assertStringNotContainsString('bg-primary-50 text-primary-800', $about);
    }

    #[Test]
    public function it_marks_an_item_whose_active_segments_match_the_request(): void
    {
        $_SERVER['REQUEST_URI'] = '/docs/guides/intro/';

        [$guides, $api] = $this->itemClasses($this->group([
            new ButtonGroupItem(href: '/docs/', active: ['guides']),
            new ButtonGroupItem(href: '/api/', active: ['reference']),
        ]));

        $this->assertStringContainsString('bg-primary-50 text-primary-800', $guides);
        $this->assertStringNotContainsString('bg-primary-50 text-primary-800', $api);
    }

    #[Test]
    public function it_uses_a_custom_active_class(): void
    {
        $_SERVER['REQUEST_URI'] = '/';

        [$home] = $this->itemClasses($this->group([new ButtonGroupItem(href: '/')], active_class: 'is-active'));

        $this->assertStringContainsString('is-active', $home);
    }

    #[Test]
    public function it_passes_each_items_attributes_to_its_anchor(): void
    {
        $html = $this->group([new ButtonGroupItem(href: '/docs/', text: 'Docs', id: 'docs', title: 'Read the docs', target: '_blank', data: ['track' => 'tab'])]);

        $this->assertStringContainsString('<a id="docs" class="', $html);
        $this->assertStringContainsString('href="/docs/" target="_blank" title="Read the docs" data-track="tab">', $html);
    }

    #[Test]
    public function it_leads_with_the_icon_and_hides_the_text_on_mobile(): void
    {
        $html = $this->group([new ButtonGroupItem(href: '/', text: 'Home', icon: Icon::home)]);

        $this->assertStringContainsString('<svg class="inline" ', $html);
        $this->assertMatchesRegularExpression('/<\/svg>\s*<span class="hidden-mobile"> Home<\/span>/', $html);
    }

    #[Test]
    public function it_renders_only_the_icon_without_text(): void
    {
        $html = $this->group([new ButtonGroupItem(href: '/', icon: Icon::home)]);

        $this->assertStringContainsString('<svg class="inline" ', $html);
        $this->assertStringNotContainsString('hidden-mobile', $html);
    }

    #[Test]
    public function it_echoes_and_returns_nothing_without_output_buffering(): void
    {
        $returned = null;
        $html = $this->echoed(function () use (&$returned) {
            $returned = ButtonGroup::create(new ButtonGroupOptions(items: [new ButtonGroupItem(href: '/', text: 'Home')]));
        });

        $this->assertSame('', $returned);
        $this->assertStringContainsString('<nav ', $html);
    }

    /**
     * Renders a buffered group.
     *
     * @param list<ButtonGroupItem> $items
     */
    private function group(array $items, mixed ...$options): string
    {
        return ButtonGroup::create(new ButtonGroupOptions(...[...$options, 'items' => $items, 'ob' => true]));
    }

    /**
     * @return list<ButtonGroupItem>
     */
    private function threeItems(): array
    {
        return [
            new ButtonGroupItem(href: '/one/'),
            new ButtonGroupItem(href: '/two/'),
            new ButtonGroupItem(href: '/three/'),
        ];
    }

    /**
     * The class attribute of each anchor in a group, in order.
     *
     * @return list<string>
     */
    private function itemClasses(string $html): array
    {
        preg_match_all('/<a id="[^"]*" class="([^"]*)"/', $html, $matches);

        return $matches[1];
    }
}
