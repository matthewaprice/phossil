<?php

namespace Phossil\Components;

use Phossil\Enums\Buttons;

/**
 * Renders a modal dialog overlay.
 *
 * Markup is collected in a static buffer and emitted at the page footer by
 * {@see Dialog::addDialogs()}.
 *
 * @author Matthew A Price
 */
class Dialog extends Component
{
    /**
     * Markup for every dialog registered during this request.
     *
     * @var list<string>
     */
    public static array $dialogs = [];

    public function __construct(public readonly DialogOptions $options)
    {
        $this->id = $options->id;
    }

    /**
     * Builds a dialog and registers its markup for the footer.
     */
    public static function create(DialogOptions $options): void
    {
        new self($options)->get();
    }

    /**
     * Generates the dialog markup and appends it to the static buffer.
     */
    private function get(): void
    {

        ob_start();

        $data_field_focus = '';
        if (!empty($this->options->focus)) {
            $data_field_focus = ' data-field-focus="' . $this->options->focus['field'] . '"';
        }

        $data_open = $this->options->open ? ' data-open="true"' : '';
        $position = $this->options->position;
        $is_panel = $position !== 'center';
        ?>
        <dialog
            id="<?= $this->id; ?>"
            class="relative z-40"
            aria-labelledby="dialog-title-<?= $this->options->id; ?>"
            aria-modal="true"
            <?= $data_field_focus; ?>
            <?= $data_open; ?>
            data-position="<?= $position; ?>"
        >
            <div class="fixed inset-0 bg-gray-500/75 transition-opacity"></div>

            <div class="fixed inset-0 z-10 <?= $is_panel ? '' : 'w-screen overflow-y-auto'; ?>">
                <div class="<?= $this->containerClass(); ?>">
                    <div class="<?= $this->panelClass(); ?>">
                        <div class="text-left">
                            <div class="dialog-close absolute top-5 right-6">
                                <?php
                                Button::create(
                                    new ButtonOptions(
                                        text: 'Close',
                                        id: 'dialog_close-' . $this->options->id,
                                        styles: [Buttons::Small, Buttons::Bordered],
                                    )
                                );
                                ?>
                            </div>
                            <div
                                id="dialog-title-<?= $this->options->id; ?>"
                                class="dialog-title text-amber-800 text-xl font-semibold leading-6"
                            >
                                <?= $this->options->title; ?>
                            </div>
                            <div class="dialog-content pt-4 mt-2">
                                <?= $this->options->content; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </dialog>
        <?php
        $dialog_contents = (string) ob_get_contents();
        ob_end_clean();
        self::$dialogs[] = $dialog_contents;
    }

    /**
     * Echoes every registered dialog.
     *
     * Layouts call this at the footer to emit the markup collected by
     * {@see Dialog::create()} during page generation.
     */
    public static function addDialogs(): void
    {

        if (self::$dialogs) {
            foreach (self::$dialogs as $dialog) {
                echo $dialog;
            }
        }
    }

    /**
     * Maps the size option to a Tailwind width class.
     *
     * @return string Width class for a centre-positioned dialog.
     */
    private function sizeClass(): string
    {
        return match ($this->options->size) {
            'sm' => 'sm:w-1/3',
            'lg' => 'sm:w-3/4',
            default => 'sm:w-1/2',
        };
    }

    /**
     * Maps the position option to outer container classes.
     *
     * @return string Flex classes positioning the panel within the viewport.
     */
    private function containerClass(): string
    {
        return match ($this->options->position) {
            'right' => 'flex justify-end h-full',
            'left' => 'flex justify-start h-full',
            'top' => 'flex items-start justify-center w-full',
            'bottom' => 'flex items-end justify-center w-full h-full',
            default => 'flex items-end justify-center p-4 text-center sm:items-center sm:p-0',
        };
    }

    /**
     * Maps the position option to inner panel classes.
     *
     * @return string Panel classes covering background, rounding and scroll behaviour.
     */
    private function panelClass(): string
    {
        $base = 'dialog-panel relative bg-white text-left shadow-xl';

        return match ($this->options->position) {
            'right' => $base . ' h-full overflow-y-auto w-full '
                . $this->panelSizeClass() . ' p-6',
            'left' => $base . ' h-full overflow-y-auto w-full '
                . $this->panelSizeClass() . ' p-6',
            'top' => $base . ' w-full overflow-y-auto max-h-[80vh] '
                . $this->panelSizeClass() . ' rounded-b-lg p-6',
            'bottom' => $base . ' w-full overflow-y-auto max-h-[80vh] '
                . $this->panelSizeClass() . ' rounded-t-lg p-6',
            default => $base
                . ' transform overflow-hidden rounded-lg px-4 pb-4 pt-5'
                . ' transition-all sm:my-4 w-full '
                . $this->sizeClass() . ' sm:p-6',
        };
    }

    /**
     * Maps the size option to a panel constraint class.
     *
     * @return string Max-width class, centred horizontally for top and bottom panels.
     */
    private function panelSizeClass(): string
    {
        $position = $this->options->position;
        $isHorizontal = in_array($position, ['left', 'right']);

        if ($isHorizontal) {
            return match ($this->options->size) {
                'sm' => 'sm:max-w-sm',
                'lg' => 'sm:max-w-2xl',
                default => 'sm:max-w-lg',
            };
        }

        return match ($this->options->size) {
            'sm' => 'sm:max-w-xl sm:mx-auto',
            'lg' => 'sm:max-w-5xl sm:mx-auto',
            default => 'sm:max-w-3xl sm:mx-auto',
        };
    }
}
