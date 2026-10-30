<?php

namespace Phossil\Tests\Composer\Fixtures;

use Phossil\Composer\CreateProject;

/**
 * CreateProject with git stubbed out, so tests see which git commands it would
 * run without needing git installed.
 *
 * @author Matthew A Price
 */
class RecordingCreateProject extends CreateProject
{
    /**
     * Every git command asked for, as [folder, arguments].
     *
     * @var list<array{string, list<string>}>
     */
    public static array $calls = [];

    /** The git subcommand that fails, such as 'commit', or '' for none. */
    public static string $fails = '';

    protected static function git(string $cwd, string ...$args): bool
    {
        self::$calls[] = [$cwd, $args];

        return $args[0] !== self::$fails;
    }
}
