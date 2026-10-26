<?php

namespace App\Components;

/**
 * Base class for every UI component.
 *
 * Provides component identification and shared attribute rendering.
 *
 * @author Matthew A Price
 */
abstract class Component
{
    /** Counter behind the auto-generated default component IDs. */
    public static int $component_num = 0;

    /** Unique identifier for this instance, auto-generated when options supply none. */
    public string $id = ''
    {
        get {
            if ($this->id === '') {
                $this->id = str_replace('\\', '_', strtolower(static::class)) . '-' . self::$component_num++;
            }

            return $this->id;
        }
        set(string $value) => trim($value);
    }

    /**
     * Builds an HTML data-attribute string from an associative array.
     *
     * @param array<string, string> $data Attribute names without the `data-` prefix, mapped to values.
     * @return string Space-prefixed attribute string, or an empty string when there is no data.
     */
    protected static function dataAttributes(array $data = []): string
    {
        if (empty($data)) {
            return '';
        }

        return ' ' . implode(' ', array_map(
            fn($key, $value) => 'data-' . $key . '="' . e($value) . '"',
            array_keys($data),
            $data
        ));
    }
}
