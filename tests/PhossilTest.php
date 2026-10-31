<?php

namespace Phossil\Tests;

use Phossil\Phossil;
use PHPUnit\Framework\Attributes\{
    CoversClass,
    Test
};
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use RuntimeException;

#[CoversClass(Phossil::class)]
class PhossilTest extends TestCase
{
    /** The root and config the site booted with, restored after each test. */
    private array $site_state;

    protected function setUp(): void
    {
        $phossil = new ReflectionClass(Phossil::class);

        $this->site_state = [
            'root' => $phossil->getStaticPropertyValue('root'),
            'config' => $phossil->getStaticPropertyValue('config'),
        ];
    }

    protected function tearDown(): void
    {
        $phossil = new ReflectionClass(Phossil::class);

        foreach ($this->site_state as $property => $value) {
            $phossil->setStaticPropertyValue($property, $value);
        }
    }

    #[Test]
    public function it_gives_the_root_it_booted_with_without_a_trailing_slash(): void
    {
        Phossil::boot('/srv/site/');

        $this->assertSame('/srv/site', Phossil::root());
    }

    #[Test]
    public function it_builds_paths_inside_the_root(): void
    {
        Phossil::boot('/srv/site');

        $this->assertSame('/srv/site/img/logo.svg', Phossil::path('img/logo.svg'));
        $this->assertSame('/srv/site/hot', Phossil::path('/hot'));
        $this->assertSame('/srv/site', Phossil::path());
    }

    #[Test]
    public function it_serves_assets_under_assets_by_default(): void
    {
        Phossil::boot('/srv/site');

        $this->assertSame('/assets', Phossil::config('assets'));
    }

    #[Test]
    public function the_apps_config_overrides_the_defaults_and_adds_its_own_settings(): void
    {
        Phossil::boot('/srv/site', ['assets' => '/static', 'debug' => true]);

        $this->assertSame('/static', Phossil::config('assets'));
        $this->assertTrue(Phossil::config('debug'));
        $this->assertNull(Phossil::config('missing'));
    }

    #[Test]
    public function it_refuses_to_give_a_root_before_booting(): void
    {
        new ReflectionClass(Phossil::class)->setStaticPropertyValue('root', '');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('phossil has not booted');

        Phossil::root();
    }
}
