<?php

namespace App\Components;

/**
 * Renders an HTML anchor element with consistent styling.
 *
 * Supports data attributes, custom link targets and optional output buffering.
 *
 * @author Matthew A Price
 */
class Anchor extends Component
{
    public function __construct(public readonly AnchorOptions $options)
    {
        $this->id = $options->id;
    }

    /**
     * Builds an anchor and returns its markup.
     *
     * @return string Rendered HTML, or an empty string when output buffering is off.
     */
    public static function create(AnchorOptions $options): string
    {
        return new self($options)->get();
    }

    /**
     * Generates the anchor markup.
     *
     * @return string Rendered HTML, or an empty string when output buffering is off.
     */
    private function get(): string
    {
        $link_data = self::dataAttributes($this->options->data);

        if ($this->options->ob) {
            ob_start();
        }
        ?>

        <a id="<?= $this->id; ?>" class="<?= $this->options->class; ?>" href="<?= e($this->options->href); ?>" target="<?= $this->options->target; ?>"<?= $this->options->title !== '' ? ' title="' . e($this->options->title) . '"' : ''; ?><?= $this->options->current ? ' aria-current="page"' : ''; ?><?= $link_data; ?>>
            <?= $this->options->text; ?>
        </a>

        <?php
        if ($this->options->ob) {
            $contents = (string) ob_get_contents();
            ob_end_clean();
            return $contents;
        }

        return '';
    }
}
