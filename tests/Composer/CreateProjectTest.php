<?php

namespace Phossil\Tests\Composer;

use Phossil\Composer\CreateProject;
use Phossil\Tests\Composer\Fixtures\{
    FakeIO,
    RecordingCreateProject
};
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\{
    CoversClass,
    DataProvider,
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
    public function it_initializes_a_repository_on_main_and_commits_when_the_answers_are_the_defaults(): void
    {
        $io = $this->offer(new FakeIO());

        $this->assertSame(
            [
                [$this->site, ['init', '--quiet']],
                [$this->site, ['symbolic-ref', 'HEAD', 'refs/heads/main']],
                [$this->site, ['add', '--all']],
                [$this->site, ['commit', '--quiet', '--message', 'Start from the phossil starter']],
            ],
            RecordingCreateProject::$calls
        );
        $this->assertCount(2, $io->questions);
        $this->assertStringContainsString('Default branch name? [<comment>main</comment>]', $io->questions[1]);
        $this->assertStringContainsString('Initialized a git repository on main with a first commit.', $io->written[0]);
    }

    #[Test]
    public function it_uses_the_branch_name_typed(): void
    {
        $io = $this->offer(new FakeIO(branch: 'trunk'));

        $this->assertSame([$this->site, ['symbolic-ref', 'HEAD', 'refs/heads/trunk']], RecordingCreateProject::$calls[1]);
        $this->assertStringContainsString('on trunk', $io->written[0]);
    }

    #[Test]
    public function it_leaves_the_folder_alone_when_the_answer_is_no(): void
    {
        $io = $this->offer(new FakeIO(answer: false));

        // Only the yes/no question: no branch question after a no.
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
    public function it_says_so_when_the_branch_cannot_be_named(): void
    {
        RecordingCreateProject::$fails = 'symbolic-ref';

        $io = $this->offer(new FakeIO());

        $this->assertCount(2, RecordingCreateProject::$calls);
        $this->assertStringContainsString('could not name its branch main', $io->written[0]);
    }

    #[Test]
    public function it_says_so_when_the_first_commit_fails(): void
    {
        RecordingCreateProject::$fails = 'commit';

        $io = $this->offer(new FakeIO());

        $this->assertCount(4, RecordingCreateProject::$calls);
        $this->assertStringContainsString('on main, but the first commit failed', $io->written[0]);
    }

    #[Test]
    #[DataProvider('branch_names_git_accepts')]
    public function it_accepts_branch_names_git_accepts(string $branch): void
    {
        $this->assertSame($branch, CreateProject::branch($branch));
    }

    #[Test]
    #[DataProvider('branch_names_git_rejects')]
    public function it_asks_again_for_branch_names_git_rejects(string $branch): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("can't be a git branch name");

        CreateProject::branch($branch);
    }

    #[Test]
    public function it_trims_the_branch_name(): void
    {
        $this->assertSame('main', CreateProject::branch('  main '));
    }

    /**
     * @return array<string, array{string}>
     */
    public static function branch_names_git_accepts(): array
    {
        return array_map(fn (string $branch): array => [$branch], [
            'main' => 'main',
            'with a slash' => 'feature/new-page',
            'with dots' => 'release-1.0.2',
            'with an at sign' => 'a@b',
        ]);
    }

    /**
     * Each breaks one of git's rules for ref names.
     *
     * @return array<string, array{string}>
     */
    public static function branch_names_git_rejects(): array
    {
        return array_map(fn (string $branch): array => [$branch], [
            'empty' => '',
            'a space' => 'my branch',
            'a tilde' => 'a~b',
            'a caret' => 'a^b',
            'a colon' => 'a:b',
            'a question mark' => 'a?b',
            'an asterisk' => 'a*b',
            'an open bracket' => 'a[b',
            'a backslash' => 'a\\b',
            'two dots' => 'a..b',
            'at brace' => 'a@{b',
            'two slashes' => 'a//b',
            'a leading dash' => '-main',
            'a leading slash' => '/main',
            'a trailing slash' => 'main/',
            'a trailing dot' => 'main.',
            'a part starting with a dot' => 'a/.b',
            'a part ending in .lock' => 'main.lock',
            'just an at sign' => '@',
            'HEAD' => 'HEAD',
        ]);
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
