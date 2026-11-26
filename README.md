<p align="center">
    <img src="./img/utilux-128.png" width="96" height="96" alt="utilux logo">
</p>

# utilux: Git, Docker and Work Utilities ! 🚀

This repository gathers posix-compatible scripts and tools to make my optimal Linux setup. (Supports Bash & Zsh)

## Installation

One-liner installer:

```bash
sudo apt install -y git whiptail && git clone https://github.com/yonis-savary/utilux ~/utilux && source ~/utilux/install && utilux-setup
```

`utilux-setup` then lets you pick the software to install and the utilux configs to apply. It can be launched again
at any time.

After installation, you may need to restart your terminal/session.

To configure utilux, launch

```bash
utilux-config # (OR) nano ~/.config/utilux/utilux-config
```

> [!IMPORTANT]
> It is advised that you configure every constant in your config if you want to use utilux to its full potential.

### Uninstall

> [!WARNING]
> `~/.local/share/utilux` holds the `utilux-skeln` store, and `~/.claude` may point inside `~/utilux`:
> run `utilux-skeln umount` on every entry of `utilux-skeln ls`, and move `~/.claude` back to a real directory first.

1. Remove the `~/utilux` directory
2. Remove `~/.config/utilux` directory
3. Remove `~/.local/share/utilux` directory (Warning: delete all your utilux command data, such as your repositories if you're using `gxt`)
4. Remove the `. ~/utilux/.shellrc` line from your `.bashrc`/`.zshrc` file

## Utilux shortcuts/aliases

The most interesting feature of utilux is its collection of aliases and productivity scripts

- **Git Aliases (`gx`):**
  - `gxap` : Amend (no-edit) and Push (force with lease)
  - `gxf` : Fetch all & prune
  - `gxb+` : Create a new branch tracking the current one
  - ...and more!

- **Docker Compose Aliases (`dx`):**
  - `dxu` : `docker compose up -d`
  - `dxdu` : `docker compose down && docker compose up -d`
  - `dxe [process] [command]` : Exec into a process
  - ...and more!

- **Utilux Tools (`utilux-`):**
  - `utilux-install [file|url]` : Install a binary in your `$PATH` (Support url for binaries and deb files)
  - `utilux-share [file]` : Start a local Python server to share a file
  - `utilux-ssh` : Manage local SSH keys
  - `utilux-skeln mount [path]` : Move a file/directory into a central store and leave a symlink behind
  - ...and more!

- **Claude Code Tools (`add-`/`rm-`):**
  - `add-skill [path...]` : Symlink skills into `~/.claude/skills` (e.g. `add-skill ./project/.claude/skills/*`)
  - `add-agent [path...]` / `add-command [path...]` : Same, for `~/.claude/agents` and `~/.claude/commands`
  - `rm-skill [name...]` / `rm-agent` / `rm-command` : Unlink them back (`--all`, `--broken`)
  - `add-*` also accepts a directory to link everything it holds, and every command supports `--list`, `--dry-run` and `--force`


> [!IMPORTANT]
> Autocompletion works for most commands ! gx will look for branch names, dx will look for service names...etc

Here is a detailed version of every utilux command:

```bash
# utilux global utils
utilux-install [FILE|URL|PROGRAM] # Copy/Download a file into your $PATH (Supports URL to binaries and deb files) (Also supports code, discord & warp keywords)
utilux-install-ln [FILE]  # Link a file into your $PATH
utilux-setup              # Pick the software to install, then the configs to apply (VSCode, WezTerm, Claude Code, GNOME shortcuts, KDE layout)
utilux-share [FILE]       # Start a web server to share files
utilux-ssh                # SSH key utilities
utilux-update             # utilux self-update script
utilux-update-config      # update your utilux configuration with latest changes
utilux-mount-ssh          # wizard to mount a ssh directory into your fstab
utilux-launch-backup      # rsync UTILUX_BACKUP_SOURCE to UTILUX_BACKUP_TARGET (dry-run first)
utilux-temp-code          # open VSCode in a new temporary directory
utilux-vault              # password protected remote directory (mount, umount)
utilux-skeln              # central skeleton directory (mount, umount, ls, install, zip, unzip)

# claude code utils (link project resources into ~/.claude)
add-skill [PATH...]     # Link skills (directories holding a SKILL.md) into ~/.claude/skills
add-agent [PATH...]     # Link agents (.md files) into ~/.claude/agents
add-command [PATH...]   # Link commands (.md files) into ~/.claude/commands
rm-skill [NAME...]      # Unlink skills from ~/.claude/skills (also accepts the paths given to add-skill)
rm-agent [NAME...]      # Unlink agents from ~/.claude/agents
rm-command [NAME...]    # Unlink commands from ~/.claude/commands
# add-* accepts several paths (add-skill ./project/.claude/skills/*) or a parent directory
# rm-* accepts --all and --broken (only symlinks are ever removed)
# Every command supports --list, --dry-run, --force and --help

# dx = docker compose utils
dx                      # alias for docker compose
dxb                     # docker compose build
dxu                     # docker compose up -d
dxd                     # docker compose down
dxdu                    # dxd && dxu
dxr [service]           # docker compose restart
dxsp                    # docker system prune -a
dxsa                    # stop and remove every container
dxp                     # docker compose ps
dxl [service]           # docker compose logs --follow
dxe [service] {program} # docker compose exec

# gx = git single-repository utils
gx                              # Help for all gx* scripts
gxa                             # Commit amend (no edit)
gxap                            # Amend and force push
gxb                             # Print current branch name
gxb+ <name>                     # Create a new branch tracking current one
gxbf [name]                     # Change current branch upstream
gxc                             # Alias for git checkout
gxd                             # List unpushed commits
gxf                             # Fetch & prune
gxi                             # Git initialize/reset, cleanup repo
gxl [branch-name|keyword]       # Git lookup, look for a branch name
gxm [branch-name|keyword]       # Merge origin/<branch> into the current branch
gxp                             # Push on current branch
gxpf                            # Push (force with lease)
gxpm                            # Purge merged branches (force)
gxr [target_branch] [-y|--yes]  # Pull rebase
gxrc                            # Rebase continue

# gxt = git global utils (repos management !)
gxt help                        # Print the manual
gxt list [-l] [-r]              # List every cloned repository
gxt status [-q]                 # Show status of local branches (-q to skip git fetch)
gxt clone <SSH|HTTP-URL>        # Clone a repository
gxt fetch                       # Fetch every repository changes
gxt explore <repo>              # Open file explorer in repos directory
gxt desktop <repo> [-o]         # Create a VSCode desktop shortcut for one repository
gxt find <repo>                 # Find the absolute path of a repository
gxt code <repo>                 # Open VSCode for one repo
# Note: repo can mean the full repository name or a part of it !
# Also you can use --help with any of gxt subcommands, and tab for autocompletion of repos names !
gxcd <repo>                     # cd into a repository
gxcode <repo>                   # Open VSCode for one repo and close the terminal
gxopen <repo>                   # cd into a repository, open VSCode there and start claude in the terminal

# shell aliases
e                               # exit
c                               # claude

```

## Custom GNOME Shortcuts

`utilux-setup` can create some custom keyboard shortcuts for GNOME environments, which are
  - Launch Web browser : <kbd>Ctrl</kbd> + <kbd>Alt</kbd> + <kbd>F</kbd>
  - Launch Terminal : <kbd>Ctrl</kbd> + <kbd>Alt</kbd> + <kbd>T</kbd>
  - Open Home Folder : <kbd>Ctrl</kbd> + <kbd>Alt</kbd> + <kbd>E</kbd>

## Tools & Resources

Utilux creation was motivated by a desire to have a unified Linux setup between all my devices.

So, naturally, some notes/scripts were written here:

1. Installation & Configuration
    - [KDE Plasma layout (panels & Konsole profile)](./setup/kde/README.md)
  - [VSCode configuration (settings, keybindings & extensions)](./setup/vscode/README.md)
  - [WezTerm configuration](./setup/wezterm/README.md)
  - [Claude Code configuration (guidelines & skills)](./setup/claude/README.md)

2. Files utilities
  - [Skeleton Directory (utilux-skeln)](./modules/scripts/README.md#skeleton-directory)
  - [Password-Protected Remote Vault (utilux-vault)](./modules/scripts/README.md#password-protected-vault)
  - [Mount Remote SSH Directory (utilux-mount-ssh)](./modules/scripts/README.md#mount-a-remote-ssh-directory)