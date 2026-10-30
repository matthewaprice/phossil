<?php

namespace Phossil\Enums;

/**
 * Tailwind classes for buttons, composed by {@see Buttons::make()}.
 *
 * Each case holds the classes it adds on top of the shared base.
 *
 * @author Matthew A Price
 */
enum Buttons: string
{
    case Small = 'px-2.5 py-1.5 text-xs';
    case Medium = 'px-3.5 py-2 text-sm';
    case Large = 'px-5 py-2.5 text-base';
    case Bordered = 'border border-zinc-300';
    case Primary = 'bg-primary-400 text-zinc-900 hover:bg-primary-300';

    /** Classes every button gets, whatever cases it is made from. */
    private const string BASE = 'inline-flex items-center gap-x-1.5 rounded-md font-semibold transition disabled:opacity-50';

    /** Colors for a button that isn't Primary. Kept apart so the two never compete. */
    private const string DEFAULT_COLORS = 'bg-white text-zinc-700 hover:bg-zinc-100';

    /**
     * Builds the class string for a button from the base plus the given cases.
     *
     * @param Buttons ...$buttons Cases to apply, such as a size and Bordered.
     * @return string Compiled CSS classes.
     */
    public static function make(Buttons ...$buttons): string
    {
        $colors = in_array(self::Primary, $buttons, true) ? [] : [self::DEFAULT_COLORS];

        return implode(' ', [self::BASE, ...$colors, ...array_map(fn(Buttons $button) => $button->value, $buttons)]);
    }
}
