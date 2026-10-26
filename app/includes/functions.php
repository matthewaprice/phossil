<?php

/**
 * Builds the URL of a file we serve as it is, such as a site image.
 *
 * Adds the file's modified time as a version, so a new build busts the
 * browser's cache for exactly the files that changed. CSS and JS go through
 * vite() instead.
 *
 * @param string $path Path inside the app folder, e.g. 'img/favicon.svg'.
 * @return string The URL, e.g. '/assets/img/favicon.svg?v=1761490000'.
 */
function asset(string $path): string
{
    global $app;

    $path = ltrim($path, '/');
    $file = $app->root . '/' . $path;
    $version = is_file($file) ? '?v=' . filemtime($file) : '';

    return $app->config['assets'] . '/' . $path . $version;
}

/**
 * Writes the tags that load Vite entries, like Laravel's @vite directive.
 *
 * @param string ...$entries Paths inside the app folder, e.g. 'css/app.css', 'js/app.js'.
 * @return string <link> and <script> tags for the dev server or the built files.
 */
function vite(string ...$entries): string
{
    return \App\Support\Vite::tags(...$entries);
}

/**
 * Escapes a value for safe output in HTML, like Laravel's e() helper.
 *
 * Converts special characters, including single and double quotes, to HTML
 * entities, so it is safe in element content and quoted attributes.
 *
 * @param \BackedEnum|string|int|float|null $value The value to escape; null becomes '' and a backed enum uses its value.
 * @param bool $double_encode Whether to re-encode existing entities such as '&amp;'.
 * @return string The escaped string.
 */
function e(\BackedEnum|string|int|float|null $value, bool $double_encode = true): string
{
    if ($value instanceof \BackedEnum) {
        $value = $value->value;
    }

    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8', $double_encode);
}
