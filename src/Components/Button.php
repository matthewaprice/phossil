<?php

namespace Phossil\Components;

use Phossil\Enums\Buttons;

/**
 * Renders a styled button, or a styled anchor when given link options.
 *
 * Its classes are composed by Buttons::make() from the cases in its options.
 *
 * @author Matthew A Price
 */
class Button extends Component
{
    public function __construct(public readonly ButtonOptions $options)
    {
        $this->id = $options->id;
    }

    /**
     * Builds a button and returns its markup.
     *
     * @return string Rendered HTML, or an empty string when output buffering is off.
     */
    public static function create(ButtonOptions $options): string
    {
        return new self($options)->get();
    }

    /**
     * Generates the button markup with its styling applied.
     *
     * @return string Rendered HTML, or an empty string when output buffering is off.
     */
    private function get(): string
    {

        if ($this->options->ob) {
            ob_start();
        }

        $button_class = $this->buttonClass();

        if ($this->options->link->text) {
            Anchor::create(
                new AnchorOptions(
                    text: $this->options->link->text,
                    href: $this->options->link->href,
                    id: $this->options->link->id ?: 'link_id_' . random_int(1000, 5000),
                    class: $button_class,
                    target: $this->options->link->target,
                    data: $this->options->link->data
                )
            );
        } else {
            $button_data = self::dataAttributes($this->options->data);
            ?>
            <button id="<?= $this->id; ?>" type="button" class="<?= $button_class; ?>"<?= $button_data; ?><?= $this->options->disabled ? ' disabled="disabled"' : ''; ?>>
                <?= $this->options->text; ?>
            </button>
            <?php
        }

        if ($this->options->ob) {
            $button = (string) ob_get_contents();
            ob_end_clean();
            return $button;
        }

        return '';
    }

    /**
     * Builds the complete CSS class string for the button.
     *
     * @return string Compiled CSS classes.
     */
    private function buttonClass(): string
    {

        return trim(Buttons::make(...$this->options->styles) . ' ' . $this->options->cursor . ' ' . $this->options->class);
    }
}
