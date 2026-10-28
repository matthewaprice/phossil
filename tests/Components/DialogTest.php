<?php

namespace Phossil\Tests\Components;

use Phossil\Components\{
    Dialog,
    DialogOptions
};
use Phossil\Enums\Buttons;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(Dialog::class)]
#[CoversClass(DialogOptions::class)]
class DialogTest extends ComponentTestCase
{
    /**
     * @return array<string, array{string, string}>
     */
    public static function centerSizes(): array
    {
        return [
            'sm' => ['sm', 'sm:w-1/3'],
            'md' => ['md', 'sm:w-1/2'],
            'lg' => ['lg', 'sm:w-3/4'],
        ];
    }

    /**
     * @return array<string, array{string, string, string}>
     */
    public static function panelSizes(): array
    {
        return [
            'right sm' => ['right', 'sm', 'sm:max-w-sm'],
            'right md' => ['right', 'md', 'sm:max-w-lg'],
            'left lg' => ['left', 'lg', 'sm:max-w-2xl'],
            'top sm' => ['top', 'sm', 'sm:max-w-xl sm:mx-auto'],
            'top md' => ['top', 'md', 'sm:max-w-3xl sm:mx-auto'],
            'bottom lg' => ['bottom', 'lg', 'sm:max-w-5xl sm:mx-auto'],
        ];
    }

    /**
     * @return array<string, array{string, string, string}>
     */
    public static function positions(): array
    {
        return [
            'right' => ['right', 'flex justify-end h-full', ' h-full overflow-y-auto w-full '],
            'left' => ['left', 'flex justify-start h-full', ' h-full overflow-y-auto w-full '],
            'top' => ['top', 'flex items-start justify-center w-full', ' rounded-b-lg p-6'],
            'bottom' => ['bottom', 'flex items-end justify-center w-full h-full', ' rounded-t-lg p-6'],
        ];
    }

    #[Test]
    public function it_registers_its_markup_instead_of_echoing_it(): void
    {
        $html = $this->echoed(fn () => Dialog::create(new DialogOptions(id: 'help', title: 'Help')));

        $this->assertSame('', $html);
        $this->assertCount(1, Dialog::$dialogs);
        $this->assertStringContainsString('<dialog', Dialog::$dialogs[0]);
    }

    #[Test]
    public function it_echoes_every_registered_dialog_in_order(): void
    {
        Dialog::create(new DialogOptions(id: 'first'));
        Dialog::create(new DialogOptions(id: 'second'));

        $html = $this->echoed(fn () => Dialog::addDialogs());

        $this->assertLessThan(strpos($html, 'id="second"'), strpos($html, 'id="first"'));
    }

    #[Test]
    public function it_echoes_nothing_without_dialogs(): void
    {
        $this->assertSame('', $this->echoed(fn () => Dialog::addDialogs()));
    }

    #[Test]
    public function it_renders_its_title_and_content(): void
    {
        $html = $this->dialog(id: 'help', title: 'Help', content: '<p>Ask us.</p>');

        $this->assertStringContainsString('aria-labelledby="dialog-title-help"', $html);
        $this->assertMatchesRegularExpression('/id="dialog-title-help"[^>]*>\s*Help\s*</', $html);
        $this->assertMatchesRegularExpression('/class="dialog-content pt-4 mt-2 text-zinc-600">\s*<p>Ask us\.<\/p>/', $html);
    }

    #[Test]
    public function it_renders_its_footer_below_the_content(): void
    {
        $html = $this->dialog(content: '<p>Delete this page?</p>', footer: '<p>This can’t be undone.</p>');

        $this->assertMatchesRegularExpression(
            '/<div class="dialog-footer mt-6 border-t border-zinc-200 pt-4 text-sm">\s*<p>This can’t be undone\.<\/p>\s*<\/div>/',
            $html
        );
        $this->assertGreaterThan(strpos($html, 'Delete this page?'), strpos($html, 'dialog-footer'));
    }

    #[Test]
    public function it_omits_an_empty_footer(): void
    {
        $this->assertStringNotContainsString('dialog-footer', $this->dialog(content: 'Body'));
    }

    #[Test]
    public function it_renders_a_close_button(): void
    {
        $html = $this->dialog(id: 'help');

        $this->assertStringContainsString('<button id="dialog_close-help" type="button" class="' . Buttons::make(Buttons::Small, Buttons::Bordered) . ' cursor-pointer">', $html);
        $this->assertStringContainsString('Close', $html);
    }

    #[Test]
    public function it_labels_itself_by_its_generated_id_when_none_is_given(): void
    {
        $html = $this->dialog(title: 'Help');

        $this->assertMatchesRegularExpression('/<dialog\s+id="phossil_components_dialog-0"/', $html);
        $this->assertStringContainsString('aria-labelledby="dialog-title-phossil_components_dialog-0"', $html);
        $this->assertStringContainsString('id="dialog-title-phossil_components_dialog-0"', $html);
        $this->assertStringContainsString('<button id="dialog_close-phossil_components_dialog-0"', $html);
    }

    #[Test]
    public function it_gives_dialogs_without_ids_distinct_labels(): void
    {
        $first = $this->dialog();
        $second = $this->dialog();

        $this->assertStringContainsString('aria-labelledby="dialog-title-phossil_components_dialog-0"', $first);
        $this->assertStringContainsString('aria-labelledby="dialog-title-phossil_components_dialog-1"', $second);
    }

    #[Test]
    public function it_names_the_field_to_focus(): void
    {
        $this->assertStringContainsString(' data-field-focus="email"', $this->dialog(focus: ['field' => 'email']));
        $this->assertStringNotContainsString('data-field-focus', $this->dialog());
    }

    #[Test]
    public function it_can_open_on_page_load(): void
    {
        $this->assertStringContainsString(' data-open="true"', $this->dialog(open: true));
        $this->assertStringNotContainsString('data-open', $this->dialog());
    }

    #[Test]
    public function it_defaults_to_the_center(): void
    {
        $html = $this->dialog();

        $this->assertStringContainsString('data-position="center"', $html);
        $this->assertStringContainsString('w-screen overflow-y-auto', $html);
        $this->assertStringContainsString('flex items-end justify-center p-4 text-center sm:items-center sm:p-0', $html);
        $this->assertStringContainsString('transform overflow-hidden rounded-lg', $html);
    }

    #[Test]
    #[DataProvider('centerSizes')]
    public function it_maps_its_size_to_a_width_in_the_center(string $size, string $class): void
    {
        $this->assertStringContainsString(" {$class} ", $this->dialog(size: $size));
    }

    #[Test]
    #[DataProvider('positions')]
    public function it_positions_its_panel(string $position, string $container, string $panel): void
    {
        $html = $this->dialog(position: $position);

        $this->assertStringContainsString("data-position=\"{$position}\"", $html);
        $this->assertStringContainsString("class=\"{$container}\"", $html);
        $this->assertStringContainsString($panel, $html);
        $this->assertStringNotContainsString('w-screen', $html);
    }

    #[Test]
    #[DataProvider('panelSizes')]
    public function it_maps_its_size_to_a_max_width_for_side_panels(string $position, string $size, string $class): void
    {
        $this->assertStringContainsString(" {$class} ", $this->dialog(position: $position, size: $size));
    }

    /**
     * Creates a dialog and returns its registered markup.
     */
    private function dialog(mixed ...$options): string
    {
        Dialog::create(new DialogOptions(...$options));

        return array_pop(Dialog::$dialogs);
    }
}
