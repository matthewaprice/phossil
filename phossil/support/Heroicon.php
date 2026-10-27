<?php

namespace Phossil\Support;

use Phossil\Enums\{
    HeroiconSize,
    HeroiconStyle,
    Icon
};
use RuntimeException;

/**
 * Heroicon
 *
 * Renders inline SVG heroicons from public/vendor/heroicons based on
 * name, size, and outline/solid style.
 *
 * @author Matthew A Price
 */
class Heroicon
{
    /**
     * In-memory cache of loaded SVG markup, keyed by size/style/name.
     *
     * @var array<string, string>
     */
    private static array $cache = [];

    /**
     * Renders an icon as inline SVG markup.
     *
     * @param Icon          $name   Icon enum case identifying the heroicon
     * @param HeroiconSize  $size   Pixel size to render (16, 20, or 24)
     * @param HeroiconStyle $style  Outline or solid style
     * @param string        $class  Optional CSS classes added to the <svg> element
     * @param bool          $inline Adds the "inline" class; pass false for block display
     * @return string The inline SVG markup
     */
    public static function render(
        Icon $name,
        HeroiconSize $size = HeroiconSize::Regular,
        HeroiconStyle $style = HeroiconStyle::Outline,
        string $class = '',
        bool $inline = true
    ): string {

        $svg = self::load($name->value, $size, $style);

        if ($inline) {
            $class = trim('inline ' . $class);
        }

        if ($class === '') {
            return $svg;
        }

        return preg_replace('/<svg /', '<svg class="' . $class . '" ', $svg, 1);
    }

    /**
     * Reads and caches the raw SVG file for the given icon/size/style.
     *
     * @param string        $name  Icon name matching the heroicons filename
     * @param HeroiconSize  $size  Pixel size to render
     * @param HeroiconStyle $style Outline or solid style
     * @return string The raw SVG markup
     */
    private static function load(string $name, HeroiconSize $size, HeroiconStyle $style): string
    {
        global $app;

        $key = "{$size->value}/{$style->value}/{$name}";

        if (isset(self::$cache[$key])) {
            return self::$cache[$key];
        }

        $path = $app->root . "/img/heroicons/{$key}.svg";

        if (!is_file($path)) {
            throw new RuntimeException("Heroicon [{$name}] not found for size {$size->value} ({$style->value}).");
        }

        $svg = preg_replace('/(fill|stroke)="#[0-9A-Fa-f]{3,8}"/', '$1="currentColor"', file_get_contents($path));

        return self::$cache[$key] = $svg;
    }
}
