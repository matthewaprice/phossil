<?php

namespace Phossil\Components;

use Phossil\Enums\Buttons;
use Phossil\Support\{
    Heroicon,
    Request
};

/**
 * Renders a group of navigation buttons with connected borders.
 *
 * Detects which item matches the current page and styles it as active.
 *
 * @author Matthew A Price
 */
class ButtonGroup extends Component
{
    public function __construct(public readonly ButtonGroupOptions $options)
    {
        $this->id = $options->id;
    }

    /**
     * Builds a button group and returns its markup.
     *
     * @return string Rendered HTML, or an empty string when output buffering is off.
     */
    public static function create(ButtonGroupOptions $options): string
    {
        return new self($options)->get();
    }

    /**
     * Generates the button group markup.
     *
     * @return string Rendered HTML, or an empty string when there are no items or buffering is off.
     */
    private function get(): string
    {

        $this->options->items = array_values($this->filterItems());

        if (empty($this->options->items)) {
            return '';
        }

        if ($this->options->ob) {
            ob_start();
        }

        ?>
        <nav id="<?= $this->id; ?>" class="<?= $this->options->stacked ? 'inline-block lg:inline-flex lg:flex-col' : 'inline-flex'; ?> rounded-lg<?= !empty($this->options->class) ? ' ' . $this->options->class : ''; ?>" aria-label="Button Group">
            <?php
            foreach ($this->options->items as $i => $item) {
                $item_class = $this->itemClass($item, $i + 1);

                Anchor::create(
                    new AnchorOptions(
                        id: $item->id,
                        class: $item_class,
                        text: $this->itemText($item),
                        href: $item->href,
                        title: $item->title,
                        target: $item->target,
                        data: $item->data,
                    )
                );
            }
            ?>
        </nav>
        <?php
        if ($this->options->ob) {
            $contents = (string) ob_get_contents();
            ob_end_clean();

            return $contents;
        }

        return '';
    }

    /**
     * Drops items flagged with `skip`.
     *
     * @return array<int, ButtonGroupItem> Renderable items, keys not yet reindexed.
     */
    private function filterItems(): array
    {

        return array_filter($this->options->items, fn (ButtonGroupItem $item) => !$item->skip);
    }

    /**
     * Builds the CSS classes for one item from its position and active state.
     *
     * @param ButtonGroupItem $item One entry from {@see ButtonGroupOptions}.
     * @param int              $i    One-based position within the group.
     * @return string Compiled CSS classes.
     */
    private function itemClass(ButtonGroupItem $item, int $i = 0): string
    {

        $item_class[] = $item->class;
        $item_class[] = Buttons::make($this->options->compact ? Buttons::Small : Buttons::Large);
        $item_class[] = 'rounded-none border-zinc-300';

        if ($this->determineActive($item)) {
            $item_class[] = $this->options->active_class;
        }

        array_push($item_class, ...$this->positionClass($i));

        return implode(' ', array_filter($item_class));
    }

    /**
     * Builds an item's label, leading with its icon when one is set.
     *
     * With an icon present the text is wrapped in a hidden-mobile span, so only
     * the icon shows on small screens.
     *
     * @param ButtonGroupItem $item One entry from {@see ButtonGroupOptions}.
     * @return string Label markup combining the icon and text.
     */
    private function itemText(ButtonGroupItem $item): string
    {

        $text = $item->text ?? '';

        if (empty($item->icon)) {
            return $text;
        }

        if ($text === '') {
            return Heroicon::render($item->icon);
        }

        return Heroicon::render($item->icon) . '<span class="hidden-mobile"> ' . $text . '</span>';
    }

    /**
     * Determines border and rounding classes from an item's position.
     *
     * @param int $i One-based position within the group.
     * @return list<string> Border and rounding classes for that position.
     */
    private function positionClass(int $i): array
    {

        $classes = [];
        $is_first = $i === 1;
        $is_last = $i === count($this->options->items);

        if ($this->options->stacked) {
            $classes[] = 'border-t border-b lg:border-l lg:border-r lg:border-t';
            $classes[] = $is_first ? 'border-l rounded-l-md lg:rounded-t-md lg:rounded-bl-none' : 'lg:border-t-0';

            if ($is_last) {
                $classes[] = 'border-r rounded-r-md lg:rounded-r-none lg:rounded-b-md lg:border-b';
            }
        } else {
            $classes[] = 'border-t border-b';

            if ($is_first) {
                $classes[] = 'rounded-l-md border-l';
            }
            if ($is_last) {
                $classes[] = 'rounded-r-md border-r';
            }
        }

        return $classes;
    }

    /**
     * Reports whether an item matches the current request path.
     *
     * Matches on the item's `href` path, falling back to its `active` segments.
     *
     * @param ButtonGroupItem $item One entry from {@see ButtonGroupOptions}.
     * @return bool True when the item represents the current page.
     */
    private function determineActive(ButtonGroupItem $item): bool
    {

        if (!empty($item->href)) {
            $href_path = parse_url($item->href, PHP_URL_PATH);

            if (is_string($href_path) && trim($href_path, '/') === Request::path()) {
                return true;
            }
        }

        if (!empty($item->active)) {
            return !empty(array_intersect($item->active, Request::segments()));
        }

        return false;
    }
}
