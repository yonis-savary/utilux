# KDE Plasma layout

My Plasma panels and Konsole profile, packaged as a template that can be applied on any Plasma 6 session.

| File | Content |
|---|---|
| `themes/panels.js` | Plasma scripting layout: a top bar (launcher, system tray, clock, show desktop) and a bottom task manager |
| `themes/konsole/Utilux.profile` | Konsole profile (JetBrains Mono 13, green cursor, 10k lines history) |
| `themes/konsole/kubuntu-black.colorscheme` | Color scheme used by the profile |
| `apply-layout` | Applies the template on the current session |
| `export-layout` | Replaces the template with the current session |

## Apply

```bash
~/utilux/kde/apply-layout
```

`utilux-setup` also offers to run it when launched in a KDE session.

The script:
1. Backs up the current layout and Konsole files in `~/.local/share/utilux/kde-backups/<date>/`
2. Removes every existing panel and creates the template ones
3. Installs the Konsole profile and color scheme, and sets `Utilux` as the default profile

## Restore a backup

```bash
qdbus6 org.kde.plasmashell /PlasmaShell org.kde.PlasmaShell.evaluateScript "$(cat ~/.local/share/utilux/kde-backups/<date>/layout.js)"
cp ~/.local/share/utilux/kde-backups/<date>/konsolerc ~/.config/
```

## Update the template

```bash
~/utilux/kde/export-layout
```

Replaces the template with the current session: the panels (the `desktops` part, holding wallpapers with absolute paths, is left out) and the default Konsole profile, renamed `Utilux`, with its color scheme when it is not a built-in one.

The panels length mode, floating state, visibility, opacity and screen are not part of Plasma's serialized layout: they are exported into `panelSettings`, at the end of `themes/panels.js`, and applied once the panels are created. To move a panel to another screen, edit its `screen` there (Plasma screen index, starting at 0).
