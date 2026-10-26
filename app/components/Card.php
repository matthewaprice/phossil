<?php

namespace App\Components;

/**
 * Renders a content card with an optional title and footer.
 *
 * @author Matthew A Price
 */
class Card extends Component
{
    public function __construct(public readonly CardOptions $options)
    {
        $this->id = $options->id;
    }

    /**
     * Builds a card and returns its markup.
     *
     * @return string Rendered HTML, or an empty string when output buffering is off.
     */
    public static function create(CardOptions $options): string
    {
        return new self($options)->get();
    }

    /**
     * Generates the card markup with all of its sections.
     *
     * @return string Rendered HTML, or an empty string when output buffering is off.
     */
    private function get(): string
    {
        if ($this->options->ob) {
            ob_start();
        }

        ?>
        <div id="<?= $this->id; ?>" class="divide-y divide-slate-200 rounded-lg border border-slate-200 bg-white shadow-sm<?= !empty($this->options->class) ? ' ' . $this->options->class : ''; ?>"<?= self::dataAttributes($this->options->data); ?>>
            <?php
            if (!empty($this->options->title)) {
                ?>
                <div class="px-5 py-3 font-semibold text-slate-900"><?= $this->options->title; ?></div>
                <?php
            }
            ?>
            <div class="px-5 py-4 text-slate-600"><?= $this->options->content; ?></div>
            <?php
            if (!empty($this->options->footer)) {
                ?>
                <div class="px-5 py-3 text-sm"><?= $this->options->footer; ?></div>
                <?php
            }
            ?>
        </div>
        <?php

        if ($this->options->ob) {
            $contents = (string) ob_get_contents();
            ob_end_clean();
            return $contents;
        }

        return '';
    }
}
