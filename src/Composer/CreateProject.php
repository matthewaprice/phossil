<?php

namespace Phossil\Composer;

use InvalidArgumentException;

/**
 * Composer script callbacks for a new site made with `composer create-project`.
 *
 * The starter's composer.json runs these from `post-create-project-cmd`, once,
 * in the new site's folder.
 *
 * @author Matthew A Price
 */
class CreateProject
{
    /** The default branch when the person just presses Enter. */
    public const string DEFAULT_BRANCH = 'main';

    /**
     * Offers to make the new site a git repository, with everything in a first commit
     * on a default branch the person names.
     *
     * Asks only when someone is at the keyboard, so `--no-interaction` and CI
     * skip it, and never when the folder is already a repository.
     *
     * @param \Composer\Script\Event $event The script event; only its IO is used.
     */
    public static function offerGitInit(object $event): void
    {
        $io = $event->getIO();
        $root = (string) getcwd();

        if (!$io->isInteractive() || is_dir($root . '/.git')) {
            return;
        }

        if (!$io->askConfirmation('Initialize a git repository with a first commit? [<comment>Y/n</comment>] ', true)) {
            return;
        }

        $branch = $io->askAndValidate(
            'Default branch name? [<comment>' . self::DEFAULT_BRANCH . '</comment>] ',
            self::branch(...),
            null,
            self::DEFAULT_BRANCH
        );

        if (!static::git($root, 'init', '--quiet')) {
            $io->write('<warning>Could not run git init. Is git installed?</warning>');
            return;
        }

        // Points HEAD at the branch before the first commit creates it. Unlike
        // `git init --initial-branch`, this works with any version of git.
        if (!static::git($root, 'symbolic-ref', 'HEAD', 'refs/heads/' . $branch)) {
            $io->write("<warning>Initialized a git repository, but could not name its branch {$branch}.</warning>");
            return;
        }

        if (!static::git($root, 'add', '--all') || !static::git($root, 'commit', '--quiet', '--message', 'Start from the phossil starter')) {
            $io->write("<warning>Initialized a git repository on {$branch}, but the first commit failed. Check that git has your name and email, then commit.</warning>");
            return;
        }

        $io->write("<info>Initialized a git repository on {$branch} with a first commit.</info>");
    }

    /**
     * Checks an answer is a name git accepts for a branch, so a bad one is asked
     * again instead of failing later.
     *
     * Follows git's rules for ref names: no spaces or control characters, none of
     * `~ ^ : ? * [ \`, no `..`, `@{` or `//`, and no part that starts with `.` or
     * ends with `.lock`. The whole name can't start with `-` or `/`, end with `/`
     * or `.`, or be `@` or `HEAD`.
     *
     * @param string|null $answer What the person typed; Enter gives the default.
     * @return string The branch name, trimmed.
     * @throws InvalidArgumentException When git would reject the name.
     */
    public static function branch(?string $answer): string
    {
        $branch = trim((string) $answer);

        $valid = $branch !== ''
            && !in_array($branch, ['@', 'HEAD'], true)
            && !str_starts_with($branch, '-')
            && !preg_match('#[\x00-\x20\x7f~^:?*\[\\\\]|\.\.|@\{|//|^/|(^|/)\.|\.lock(/|$)|[/.]$#', $branch);

        if (!$valid) {
            throw new InvalidArgumentException("\"{$branch}\" can't be a git branch name. Try something like main, or leave it empty for " . self::DEFAULT_BRANCH . '.');
        }

        return $branch;
    }

    /**
     * Runs one git command in a folder, quietly.
     *
     * @param string $cwd     The folder to run it in.
     * @param string ...$args The git subcommand and its arguments, each escaped.
     * @return bool Whether git exited successfully.
     */
    protected static function git(string $cwd, string ...$args): bool
    {
        $command = 'git -C ' . escapeshellarg($cwd) . ' ' . implode(' ', array_map('escapeshellarg', $args)) . ' 2>&1';
        exec($command, $output, $code);

        return $code === 0;
    }
}
