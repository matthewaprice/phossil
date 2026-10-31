<?php

namespace Phossil;

use RuntimeException;

/**
 * Where the app lives and how it is set up, for the parts of phossil that read
 * its files: asset(), vite() and the build.
 *
 * The app's bootstrap calls boot() once, before anything renders.
 *
 * @author Matthew A Price
 */
class Phossil
{
    /** Settings an app can leave out of its config. */
    private const array DEFAULTS = [
        // URL the app's img/ and Vite's built CSS and JS are served under.
        'assets' => '/assets',
    ];

    /** The app folder, set by boot(). */
    private static string $root = '';

    /** @var array<string, mixed> */
    private static array $config = self::DEFAULTS;

    /**
     * Points phossil at the app.
     *
     * @param string               $root   The app folder, holding img/, Vite's hot file and dist/. The static build goes to a dist/ beside it.
     * @param array<string, mixed> $config The app's settings, over the defaults.
     */
    public static function boot(string $root, array $config = []): void
    {
        self::$root = rtrim($root, '/');
        self::$config = [...self::DEFAULTS, ...$config];
    }

    /**
     * The app folder.
     *
     * @throws RuntimeException When boot() hasn't run, so nothing reads files from the wrong place.
     */
    public static function root(): string
    {
        if (self::$root === '') {
            throw new RuntimeException('phossil has not booted. Call Phossil\Phossil::boot() with your app folder before rendering.');
        }

        return self::$root;
    }

    /**
     * A path inside the app folder.
     *
     * @param string $path Relative path, e.g. 'img/phossil-logo.svg'; '' gives the folder itself.
     */
    public static function path(string $path = ''): string
    {
        $path = ltrim($path, '/');

        return self::root() . ($path !== '' ? '/' . $path : '');
    }

    /**
     * One of the app's settings.
     *
     * @return mixed The value, or null when neither the app nor the defaults set it.
     */
    public static function config(string $key): mixed
    {
        return self::$config[$key] ?? null;
    }
}
