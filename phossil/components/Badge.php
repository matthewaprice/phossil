<?php

namespace Phossil\Components;

/**
 * Renders a small status label with a colored background and matching text.
 *
 * Names a record's state or kind beside whatever it annotates, so a reader does
 * not have to infer it from the surrounding copy. It carries no link and no
 * control; a badge that needs either wants {@see Anchor} or {@see Button}.
 *
 * @author Matthew A Price
 */
class Badge extends Component
{
    public function __construct(public readonly BadgeOptions $options)
    {
        $this->id = $options->id;
    }

    /**
     * Builds a badge and returns its markup.
     *
     * @return string Rendered HTML, or an empty string when output buffering is off.
     */
    public static function create(BadgeOptions $options): string
    {
        return new self($options)->get();
    }

    /**
     * Generates the badge markup.
     *
     * An unrecognised color falls through to gray rather than rendering
     * unstyled, so a badge is always legible against the page behind it.
     *
     * @return string Rendered HTML, or an empty string when output buffering is off.
     */
    private function get(): string
    {

        if ($this->options->ob) {
            ob_start();
        }

        [ $bg_color, $text_color ] = match ($this->options->color) {
            'primary' => ['bg-primary-100', 'text-primary-800'],
            'green' => [ 'bg-green-200', 'text-green-800' ],
            'blue' => [ 'bg-blue-200', 'text-blue-800' ],
            'red' => [ 'bg-red-200', 'text-red-800' ],
            default => [ 'bg-gray-200', 'text-gray-800' ],
        };
        ?>
        <span id="<?= $this->id; ?>" class="rounded-sm <?= $bg_color; ?> px-2 py-<?= $this->options->condensed ? '1' : '2'; ?> text-<?= $this->options->font_size; ?> font-bold <?= $text_color; ?> ring-1 ring-inset ring-gray-400<?= !empty($this->options->class) ? ' ' . $this->options->class : ''; ?>">
            <?= $this->options->text; ?>
        </span>
        <?php

        if ($this->options->ob) {
            $badge = (string) ob_get_contents();
            ob_end_clean();
            return $badge;
        }

        return '';
    }
}
