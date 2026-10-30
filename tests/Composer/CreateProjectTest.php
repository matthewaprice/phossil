<?php

namespace Phossil\Tests\Composer;

use Phossil\Composer\CreateProject;
use Phossil\Tests\Composer\Fixtures\{
    FakeIO,
    RecordingCreateProject
};
use PHPUnit\Framework\Attributes\{
    CoversClass,
    Test
};
use PHPUnit\Framework\TestCase;

#[CoversClass(CreateProject::class)]
class CreateProjectTest extends TestCase
{
    /** The folder the tests ran from, restored after each one. */
    private string $real_cwd;

    /** A new, empty site folder, as create-project leaves it. */
    private string $site;

    protected function setUp(): void
    {
        RecordingCreateProject::$calls = [];
        RecordingCreateProject::$fails = '';

        $this->real_cwd = (string) getcwd();
        $this->site = sys_get_temp_dir() . '/phossil-create-project-' . bin2hex(random_bytes(4));
        mkdir($this->site);
        $this->site = (string) realpath($this->site);
        chdir($this->site);
    }

    protected function tearDown(): void
    {
        chdir($this->real_cwd);

        if (is_dir($this->site . '/.git')) {
            rmdir($this->site . '/.git');
        }
        rmdir($this->site);
    }

    #[Test]
    public function it_initializes_a_repository_and_commits_when_the_answer_is_yes(): void
    {
        $io = $this->offer(new FakeIO(answer: true));

        $this->assertSame(
            [
                [$this->site, ['init', '--quiet']],
                [$this->site, ['add', '--all']],
                [$this->site, ['commit', '--quiet', '--message', 'Start from the phossil starter']],
            ],
            RecordingCreateProject::$calls
        );
        $this->assertStringContainsString('Initialized a git repository with a first commit.', $io->written[0]);
    }

    #[Test]
    public function it_leaves_the_folder_alone_when_the_answer_is_no(): void
    {
        $io = $this->offer(new FakeIO(answer: false));

        $this->assertCount(1, $io->questions);
        $this->assertSame([], RecordingCreateProject::$calls);
        $this->assertSame([], $io->written);
    }

    #[Test]
    public function it_does_not_ask_without_someone_at_the_keyboard(): void
    {
        $io = $this->offer(new FakeIO(interactive: false));

        $this->assertSame([], $io->questions);
        $this->assertSame([], RecordingCreateProject::$calls);
    }

    #[Test]
    public function it_does_not_ask_in_a_folder_that_is_already_a_repository(): void
    {
        mkdir($this->site . '/.git');

        $io = $this->offer(new FakeIO());

        $this->assertSame([], $io->questions);
        $this->assertSame([], RecordingCreateProject::$calls);
    }

    #[Test]
    public function it_says_so_when_git_cannot_run(): void
    {
        RecordingCreateProject::$fails = 'init';

        $io = $this->offer(new FakeIO());

        $this->assertCount(1, RecordingCreateProject::$calls);
        $this->assertStringContainsString('Could not run git init. Is git installed?', $io->written[0]);
    }

    #[Test]
    public function it_says_so_when_the_first_commit_fails(): void
    {
        RecordingCreateProject::$fails = 'commit';

        $io = $this->offer(new FakeIO());

        $this->assertCount(3, RecordingCreateProject::$calls);
        $this->assertStringContainsString('the first commit failed', $io->written[0]);
    }

    /**
     * Runs the callback the way Composer does, with an event that hands over the IO.
     */
    private function offer(FakeIO $io): FakeIO
    {
        $event = new class ($io) {
            public function __construct(private readonly FakeIO $io)
            {
            }

            public function getIO(): FakeIO
            {
                return $this->io;
            }
        };

        RecordingCreateProject::offerGitInit($event);

        return $io;
    }
}
