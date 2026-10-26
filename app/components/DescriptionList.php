<?php

namespace App\Components;

/**
 * Renders a `<dl>` of term and description pairs.
 *
 * Lays the pairs out in a two-column grid from the `sm` breakpoint up.
 *
 * @author Matthew A Price
 */
class DescriptionList extends Component
{
    public function __construct(public readonly DescriptionListOptions $options)
    {
        $this->id = $options->id;
    }

    /**
     * Builds a description list and returns its markup.
     *
     * @return string Rendered HTML, or an empty string when output buffering is off.
     */
    public static function create(DescriptionListOptions $options): string
    {
        return new self($options)->get();
    }

    /**
     * Generates the description list markup.
     *
     * @return string Rendered HTML, or an empty string when output buffering is off.
     */
    private function get(): string
    {

        if ($this->options->ob) {
            ob_start();
        }
        ?>

        <dl  id="<?= $this->id; ?>" class="sm:grid sm:grid-cols-[auto_1fr]">
            <?php
            foreach ($this->options->items as $item) {
                if (!is_null($item)) {
                    ?>
                    <dt class="text-sm/6 font-medium text-gray-900 mr-3" id="<?= $item->id ? $item->id. '-dt' : ''; ?>"><?= $item->dt; ?></dt>
                    <dd class="mt-1 text-sm/6 text-gray-700 sm:mt-0" id="<?= $item->id ? $item->id . '-dd' : ''; ?>"><?= $item->dd; ?></dd>
                    <?php
                }
            }
            ?>
        </dl>

        <?php
        if ($this->options->ob) {
            $contents = (string) ob_get_contents();
            ob_end_clean();
            return $contents;
        }

        return '';
    }
}
