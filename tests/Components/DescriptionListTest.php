<?php

namespace Phossil\Tests\Components;

use Phossil\Components\{
    DescriptionList,
    DescriptionListItem,
    DescriptionListOptions
};
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(DescriptionList::class)]
#[CoversClass(DescriptionListItem::class)]
#[CoversClass(DescriptionListOptions::class)]
class DescriptionListTest extends ComponentTestCase
{
    #[Test]
    public function it_renders_each_pair_in_order(): void
    {
        $html = DescriptionList::create(new DescriptionListOptions(
            items: [
                new DescriptionListItem(dt: 'Name', dd: 'phossil'),
                new DescriptionListItem(dt: 'License', dd: 'MIT'),
            ],
            id: 'details',
            ob: true
        ));

        $this->assertStringContainsString('<dl id="details" class="sm:grid sm:grid-cols-[auto_1fr]">', $html);
        $this->assertSame(2, substr_count($html, '<dt '));
        $this->assertSame(2, substr_count($html, '<dd '));
        $this->assertLessThan(strpos($html, '>License<'), strpos($html, '>Name<'));
        $this->assertStringContainsString('>phossil</dd>', $html);
        $this->assertStringContainsString('>MIT</dd>', $html);
    }

    #[Test]
    public function it_skips_null_items(): void
    {
        $html = DescriptionList::create(new DescriptionListOptions(
            items: [new DescriptionListItem(dt: 'Name', dd: 'phossil'), null],
            ob: true
        ));

        $this->assertSame(1, substr_count($html, '<dt '));
    }

    #[Test]
    public function it_suffixes_an_items_id_for_its_term_and_description(): void
    {
        $html = DescriptionList::create(new DescriptionListOptions(
            items: [new DescriptionListItem(dt: 'Name', dd: 'phossil', id: 'name')],
            ob: true
        ));

        $this->assertStringContainsString('id="name-dt">Name</dt>', $html);
        $this->assertStringContainsString('id="name-dd">phossil</dd>', $html);
    }

    #[Test]
    public function it_omits_the_ids_for_an_item_without_one(): void
    {
        $html = DescriptionList::create(new DescriptionListOptions(
            items: [new DescriptionListItem(dt: 'Name', dd: 'phossil')],
            ob: true
        ));

        $this->assertStringContainsString('mr-3">Name</dt>', $html);
        $this->assertStringContainsString('sm:mt-0">phossil</dd>', $html);
        $this->assertStringNotContainsString('id=""', $html);
    }

    #[Test]
    public function it_echoes_and_returns_nothing_without_output_buffering(): void
    {
        $returned = null;
        $html = $this->echoed(function () use (&$returned) {
            $returned = DescriptionList::create(new DescriptionListOptions(items: [new DescriptionListItem(dt: 'Name', dd: 'phossil')]));
        });

        $this->assertSame('', $returned);
        $this->assertStringContainsString('<dl ', $html);
    }
}
