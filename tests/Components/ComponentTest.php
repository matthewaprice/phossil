<?php

namespace Phossil\Tests\Components;

use Phossil\Components\{
    AnchorOptions,
    Card,
    CardOptions,
    Component,
    ComponentOptions
};
use PHPUnit\Framework\Attributes\{
    CoversClass,
    Test
};

#[CoversClass(Component::class)]
#[CoversClass(ComponentOptions::class)]
class ComponentTest extends ComponentTestCase
{
    #[Test]
    public function it_generates_an_id_from_the_class_name_when_none_is_given(): void
    {
        $this->assertSame('phossil_components_card-0', new Card(new CardOptions())->id);
    }

    #[Test]
    public function it_numbers_generated_ids_in_order(): void
    {
        $first = new Card(new CardOptions());
        $second = new Card(new CardOptions());

        $this->assertSame('phossil_components_card-0', $first->id);
        $this->assertSame('phossil_components_card-1', $second->id);
    }

    #[Test]
    public function it_keeps_a_generated_id_once_read(): void
    {
        $card = new Card(new CardOptions());

        $this->assertSame($card->id, $card->id);
        $this->assertSame(1, Component::$component_num);
    }

    #[Test]
    public function it_uses_and_trims_a_given_id(): void
    {
        $this->assertSame('pricing', new Card(new CardOptions(id: '  pricing '))->id);
        $this->assertSame(0, Component::$component_num);
    }

    #[Test]
    public function it_builds_data_attributes(): void
    {
        $this->assertSame(
            ' data-id="7" data-label="Tom &amp; &quot;Jerry&quot;"',
            $this->dataAttributes(['id' => '7', 'label' => 'Tom & "Jerry"'])
        );
    }

    #[Test]
    public function it_builds_no_data_attributes_from_an_empty_array(): void
    {
        $this->assertSame('', $this->dataAttributes([]));
    }

    #[Test]
    public function it_defaults_the_shared_options(): void
    {
        $options = new ComponentOptions();

        $this->assertSame('', $options->id);
        $this->assertSame('', $options->class);
        $this->assertFalse($options->ob);
    }

    #[Test]
    public function component_options_forward_shared_options_to_the_base(): void
    {
        $options = new AnchorOptions(href: '/', id: 'home', class: 'nav', ob: true);

        $this->assertSame('home', $options->id);
        $this->assertSame('nav', $options->class);
        $this->assertTrue($options->ob);
    }

    /**
     * Calls the protected Component::dataAttributes() through a bare subclass.
     *
     * @param array<string, string> $data
     */
    private function dataAttributes(array $data): string
    {
        $component = new class extends Component {
            public static function attributes(array $data): string
            {
                return self::dataAttributes($data);
            }
        };

        return $component::attributes($data);
    }
}
