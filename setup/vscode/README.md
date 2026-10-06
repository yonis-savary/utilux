# VSCode configuration

My VSCode settings, keybindings and extensions, packaged as a template that can be applied on any machine.

| File | Content |
|---|---|
| `config/settings.json` | User settings |
| `config/keybindings.json` | User keybindings |
| `config/extensions.txt` | Installed extensions, one id per line |
| `apply-config` | Applies the template on the current machine |
| `export-config` | Replaces the template with the current machine configuration |

## Apply

```bash
~/utilux/setup/vscode/apply-config
```

`utilux-setup` also offers to run it when VSCode is installed.

The script:
1. Backs up the current `settings.json` and `keybindings.json` in `~/.local/share/utilux/vscode-backups/<date>/`
2. Merges the template settings into the local ones: the template wins on conflicts, settings only present locally are kept
3. Merges the template keybindings into the local ones: template entries are appended last, so they take precedence
4. Installs the missing extensions

As it is a merge, a setting or keybinding removed from the template is not removed from the machine.

## Update the template

```bash
~/utilux/setup/vscode/export-config
```
