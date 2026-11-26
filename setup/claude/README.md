# Claude Code configuration

`~/.claude` is a symlink to [`config/`](./config), so my guidelines and skills are the same on every setup.

Claude Code keeps its runtime data in that same directory (credentials, history, sessions, project memory...).
Two whitelist `.gitignore` files keep everything out of the repository except what is listed:

| File | Tracked entries |
|---|---|
| `config/.gitignore` | `CLAUDE.md`, `guidelines/` (`guidelines.md`, `style.md`), `settings-template.json`, `skills/` |
| `config/skills/.gitignore` | The utilux skills (`clean-comments`) |

Anything else (a local skill, a skill linked with `add-skill`, `settings.json`) stays local without showing up in
`git status`. To share a new skill across setups, add it to `config/skills/.gitignore`.

> [!WARNING]
> The credentials and history live inside the utilux working tree: never `git add -f` in `config/`,
> and never run `git clean -x` in utilux.

## Apply

```bash
~/utilux/setup/claude/apply-config
```

The script:
1. Moves every entry of an existing `~/.claude` into `config/`, unless the repository already holds it
2. Saves what could not be moved (the local `CLAUDE.md`, for instance) in `~/.local/share/utilux/claude-backups/<date>/`
3. Replaces `~/.claude` with a symlink to `config/`
4. Warns you to restart the Claude Code sessions still running

`utilux-setup` also lists it in its menu when Claude Code is installed.

`settings.json` stays per machine: copy `config/settings-template.json` to start one.
