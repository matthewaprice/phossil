<?php

namespace Phossil\Tests\Controllers\Home;

use Phossil\Controllers\Home\HomeController;
use Phossil\Enums\Icon;
use Phossil\Views\Home\Home;
use PHPUnit\Framework\Attributes\{
    CoversClass,
    Test
};
use PHPUnit\Framework\TestCase;

/**
 * Home controller test
 *
 * Ensures the controller hands the view the features it lists.
 *
 * @author Matthew A Price
 */
#[CoversClass(HomeController::class)]
class HomeControllerTest extends TestCase
{
    #[Test]
    public function it_hands_the_home_view_each_feature(): void
    {
        $home = new HomeController()->view();

        $this->assertInstanceOf(Home::class, $home);
        $this->assertSame(['Routing', 'Components', 'Static build'], array_keys($home->features));
    }

    #[Test]
    public function each_feature_has_an_icon_and_a_description(): void
    {
        foreach (new HomeController()->view()->features as [$icon, $description]) {
            $this->assertInstanceOf(Icon::class, $icon);
            $this->assertNotSame('', $description);
        }
    }
}
