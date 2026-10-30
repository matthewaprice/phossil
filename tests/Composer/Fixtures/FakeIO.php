<?php

namespace Phossil\Tests\Composer\Fixtures;

/**
 * Stands in for Composer's IO: answers every question the same way and keeps
 * what was asked and written.
 *
 * @author Matthew A Price
 */
class FakeIO
{
    /** @var list<string> */
    public array $questions = [];

    /** @var list<string> */
    public array $written = [];

    public function __construct(
        private readonly bool $interactive = true,
        private readonly bool $answer = true
    ) {
    }

    public function isInteractive(): bool
    {
        return $this->interactive;
    }

    public function askConfirmation(string $question, bool $default = true): bool
    {
        $this->questions[] = $question;

        return $this->answer;
    }

    public function write(string $message): void
    {
        $this->written[] = $message;
    }
}
