# CLAUDE.md

@AGENTS.md

## Claude Code

- Skills in `.claude/skills/`: `docblock-generation` (read it before changing what the package writes or supporting a new relation type or laravel-ide-helper version), `testing-best-practices`, `octobercms-model-development`.
- A changed PHP file is formatted by the `PostToolUse` hook in `.claude/settings.json`; still run `make ready` before you say a change is done, and report its result.
- To see what a change does to a real project, run `php artisan ide-helper:models --nowrite` in an October project that requires this package through a path repository; never edit that project's vendor directory.
