# WezTerm configuration

My WezTerm configuration, linked into the home folder so that editing either side edits the same file.

| File | Content |
|---|---|
| `wezterm.lua` | WezTerm configuration (font, colors, transparency, blur, keybindings) |
| `apply-config` | Links `~/.wezterm.lua` to `wezterm.lua` |

## Apply

```bash
~/utilux/setup/wezterm/apply-config
```

The script:
1. Backs up an existing `~/.wezterm.lua` in `~/.local/share/utilux/wezterm-backups/<date>/`, unless it already is a symlink
2. Replaces `~/.wezterm.lua` with a symlink to `wezterm.lua`

WezTerm reloads the configuration on save, no restart needed.

## Version

The configuration targets `wezterm-nightly`: the latest stable release dates back to February 2024 and lacks
options used here, such as `wayland_window_background_blur`. `fresh-install.sh` installs the nightly from the
official apt repository.

## Keybindings

| Keys | Action |
|---|---|
| <kbd>Shift</kbd> + <kbd>Enter</kbd> | New line (sends Alt+Enter, understood by Claude Code; bash binds it in `.shellrc`) |
| <kbd>Ctrl</kbd> + <kbd>Backspace</kbd> | Delete previous word |
| <kbd>Ctrl</kbd> + <kbd>T</kbd> | New tab |
| <kbd>Ctrl</kbd> + <kbd>W</kbd> | Close tab |
| <kbd>Ctrl</kbd> + <kbd>Shift</kbd> + <kbd>V</kbd> | Paste (WezTerm default) |
