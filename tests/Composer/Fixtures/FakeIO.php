<?php

namespace Phossil\Tests\Composer\Fixtures;

/**
 * Stands in for Composer's IO: answers the yes/no question and the branch
 * question as it was told to, and keeps what was asked and written.
 *
 * @author Matthew A Price
 */
class FakeIO
{
    /** @var list<string> */
    public array $questions = [];

    /** @var list<string> */
    public array $written = [];

    /**
     * @param bool        $interactive Whether someone is at the keyboard.
     * @param bool        $answer      The answer to every yes/no question.
     * @param string|null $branch      What to type for the branch; null presses Enter for the default.
     */
    public function __construct(
        private readonly bool $interactive = true,
        private readonly bool $answer = true,
        private readonly ?string $branch = null
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

    /**
     * Answers like Composer does: Enter gives the default, and the validator
     * checks the answer and returns it, or throws.
     */
    public function askAndValidate(string $question, callable $validator, ?int $attempts = null, mixed $default = null): mixed
    {
        $this->questions[] = $question;

        return $validator($this->branch ?? $default);
    }

    public function write(string $message): void
    {
        $this->written[] = $message;
    }
}
