---
name: pull-request
description: Write the current branch's PR description in the house format (Title, Description, Type of Change, Impact, Changes, UI/UX, Tests, QA steps), then open the PR on GitHub after confirmation.
---

# PR Description Generation

## Preconditions

1. Verify the working tree is clean (`git status`). If anything is uncommitted, stop and tell the user — the description must cover exactly what will be in the PR.
2. Verify the branch is not `main`. There is nothing to open a PR for from the base branch.
3. Check for an existing PR with `gh pr view --json number,state,url`. If one is `OPEN` for this branch, **stop**: report its number and URL, print the description for manual copy-paste, and do not edit or create anything. Re-running against a live PR is the user's call, not the skill's. An error saying no PR exists, or a closed or merged one, means carry on.
4. Base the description on the real branch contents: `git log main..HEAD` and `git diff -M main..HEAD`. Describe everything in the branch, not just work from the current conversation. If `git log main..HEAD` is empty, stop — the branch has no commits to propose.
   - Start from `git diff -M --stat main..HEAD`. `-M` shows moved files as renames rather than a delete and an add.
   - Don't read the SVGs under `phossil/img/heroicons/` or anything in `dist/`. Summarize icon changes by count.

## Checks

Phossil has no automated test suite, so these checks are what the Tests section reports. Run them all and keep each one's result:

1. `pnpm lint` — oxlint over the JS.
2. `pnpm format:check` — oxfmt over JS, CSS, JSON and Markdown.
3. `docker compose exec -T php php -l <file>` for each PHP file the branch adds or changes.
4. `composer build` — renders every route and the 404 page through nginx, and fails on any unexpected status. It writes only to `dist/` and `phossil/dist/`, which are gitignored, so the tree stays clean.

Checks 3 and 4 need the containers. If `docker compose ps` shows they aren't running, ask the user to start them with `docker compose up -d` rather than starting them yourself. If any check fails, stop and show the user its output. Don't describe a branch that doesn't build.

## Output format

Write the description as a single markdown code block (triple backticks with `markdown` as the language identifier) so it stays copyable even when the PR is created for the user. Output raw markdown inside the block — do not render it.

Structure, in order:

1. **Title** — first line, plain text prefixed `Title: ` — no markdown, it becomes the PR title.
2. **`# Description`** — two to three sentences giving a high-level understanding of the PR.
3. **`# Type of Change`** — select all that apply: Bug Fix, New Feature, Feature Update, Breaking Change, Code Standards, Documentation, Internal/Cleanup, Infrastructure. Breaking Change means anything a site built on Phossil would have to update for: a renamed namespace, class, component option or path. Infrastructure means Docker, nginx, Vite or the build.
4. **`# Impact of Changes`** — why we did this and how it makes the application better.
5. **`# Changes`** — what changed; use `##` subheadings to group related changes, such as Components, Views, Routing, Build, Docker or Docs.
6. **`# UI/UX`** — whether the PR changes the rendered pages and how, including any change to the built HTML in `dist/`.
7. **`# Tests`** — each check from the Checks section and its result. Say plainly that the project has no automated tests.

All section headers use a single `#`; only subsections under Changes use `##`.

Then append this block verbatim, replacing only the placeholder paragraph under "Steps To Test" with concrete, numbered reproduction steps for this PR. They usually start with `docker compose up --build`, name the pages to open at http://localhost:8000, and end with `composer build` and what to look for in `dist/`.

```
### Steps To Test For Other Developers/QA
Please describe the tests that you ran to verify your changes. Provide instructions so we can reproduce. Please also list any relevant details for your test configuration.

## By submitting the PR, you are saying that you have followed these guidelines:
- My code follows the style guidelines of this project
- I have performed a self-review of my code
- I have commented my code, particularly in hard-to-understand areas if necessary
- I have made corresponding changes to the documentation
- My changes generate no new warnings
- Any dependent changes have been merged and published in downstream modules
```

## Creating the PR

After printing the description, offer to open the PR.

1. Confirm that `gh` is installed and authenticated (`gh auth status`). If it is not, say so and stop — the printed description is still there to paste.
2. In one AskUserQuestion call, ask two questions: whether the authenticated `gh` account is the right one, and whether to open the PR as a normal PR, as a draft, or not at all. Do not run `gh pr create` without both answers.
3. If the user declines or says the account is wrong, stop. The description has already been printed.
4. If the user approves:
   - Push the branch. Use `git push -u origin HEAD` when it has no upstream, `git push` when it is merely ahead. If the push fails, show the error and stop. Never force-push.
   - Write the PR body to a file in the scratchpad directory. **Never pass the body inline via `--body`**: it contains backticks, `$`, and quotes that the shell will mangle.
   - The body file holds everything **except** the `Title: ` line — that value goes to `--title`.
   - Run `gh pr create --base main --head "$(git branch --show-current)" --assignee @me --title "<title>" --body-file <scratchpad path>`, adding `--draft` if that is what the user chose. `@me` resolves to the authenticated `gh` account, so the PR lands assigned to whoever opened it.
5. Report the PR URL that `gh` prints. Do not claim the PR was opened without that URL in hand.

End the body file with this line, separated from the checklist by a blank line:

```
🤖 PR Title/Description Originally Generated with [Claude Code](https://claude.com/claude-code)
```
