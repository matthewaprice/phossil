<?php

namespace Phossil\Support;

use Phossil\Phossil;
use RuntimeException;

/**
 * Writes the tags that load Vite entries: from the dev server while it runs,
 * otherwise the hashed files that `pnpm build` writes to the app folder's dist/.
 *
 * The vite container writes a hot file in the app folder, holding the dev server's URL, while it runs.
 *
 * @author Matthew A Price
 */
class Vite
{
    /**
     * The tags for some entries, in the order given.
     *
     * @param string ...$entries Paths inside the app folder, e.g. 'css/app.css'.
     * @return string <link> and <script> tags, one per line.
     */
    public static function tags(string ...$entries): string
    {
        $origin = self::devServer();

        $tags = $origin !== null
            ? self::devTags($origin, $entries)
            : self::buildTags($entries);

        return implode("\n", array_unique($tags));
    }

    /**
     * The dev server's URL, or null when pages should link the built files.
     *
     * The static build always gets the built files, even while the dev server
     * runs. Build::page() marks its requests with an X-Static-Build header.
     */
    private static function devServer(): ?string
    {
        $hot = Phossil::path('hot');
        if (isset($_SERVER['HTTP_X_STATIC_BUILD']) || !is_file($hot)) {
            return null;
        }

        return rtrim(trim(file_get_contents($hot)), '/');
    }

    /**
     * Points each entry at the dev server, after Vite's client for hot reload.
     *
     * @param list<string> $entries
     * @return list<string>
     */
    private static function devTags(string $origin, array $entries): array
    {
        $tags = [self::script($origin . '/@vite/client')];
        foreach ($entries as $entry) {
            $url = $origin . '/' . ltrim($entry, '/');
            $tags[] = self::isCss($entry) ? self::stylesheet($url) : self::script($url);
        }

        return $tags;
    }

    /**
     * Looks each entry up in the manifest and links its built file, the CSS
     * it imports, and the chunks it needs.
     *
     * @param list<string> $entries
     * @return list<string>
     */
    private static function buildTags(array $entries): array
    {
        $manifest = self::manifest();

        $tags = [];
        foreach ($entries as $entry) {
            $entry = ltrim($entry, '/');
            $chunk = $manifest[$entry] ?? throw new RuntimeException(
                "{$entry} is not in the Vite manifest. Add it to build.rolldownOptions.input in vite.config.js and build again."
            );

            foreach (self::css($manifest, $entry) as $file) {
                $tags[] = self::stylesheet('/' . $file);
            }

            if (!self::isCss($chunk['file'])) {
                foreach ($chunk['imports'] ?? [] as $import) {
                    $tags[] = '<link rel="modulepreload" href="' . e('/' . $manifest[$import]['file']) . '">';
                }
                $tags[] = self::script('/' . $chunk['file']);
            }
        }

        return $tags;
    }

    /**
     * Every CSS file a chunk needs, its own and its imports'.
     *
     * @param array<string, array<string, mixed>> $manifest
     * @return list<string> Paths inside the app folder's dist/.
     */
    private static function css(array $manifest, string $key): array
    {
        $chunk = $manifest[$key];

        $files = self::isCss($chunk['file']) ? [$chunk['file']] : [];
        foreach ($chunk['imports'] ?? [] as $import) {
            $files = [...$files, ...self::css($manifest, $import)];
        }

        return [...$files, ...($chunk['css'] ?? [])];
    }

    /**
     * Reads the app folder's dist/.vite/manifest.json, which maps each source file to its hashed build.
     *
     * @return array<string, array<string, mixed>>
     */
    private static function manifest(): array
    {
        $file = Phossil::path('dist/.vite/manifest.json');
        if (!is_file($file)) {
            throw new RuntimeException('No Vite build in ' . Phossil::path('dist') . '/. Start the vite container with `docker compose up -d`, or run `pnpm build`.');
        }

        return json_decode(file_get_contents($file), true, flags: JSON_THROW_ON_ERROR);
    }

    private static function isCss(string $path): bool
    {
        return str_ends_with($path, '.css');
    }

    private static function stylesheet(string $url): string
    {
        return '<link rel="stylesheet" href="' . e($url) . '">';
    }

    private static function script(string $url): string
    {
        return '<script type="module" src="' . e($url) . '"></script>';
    }
}
