<?php

namespace Phossil\Composer;

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
    /**
     * Offers to make the new site a git repository, with everything in a first commit.
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

        if (!static::git($root, 'init', '--quiet')) {
            $io->write('<warning>Could not run git init. Is git installed?</warning>');
            return;
        }

        if (!static::git($root, 'add', '--all') || !static::git($root, 'commit', '--quiet', '--message', 'Start from the phossil starter')) {
            $io->write('<warning>Initialized a git repository, but the first commit failed. Check that git has your name and email, then commit.</warning>');
            return;
        }

        $io->write('<info>Initialized a git repository with a first commit.</info>');
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
