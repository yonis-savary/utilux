<p align="center">
    <img src="./img/utilux-128.png" width="96" height="96" alt="Resound logo">
</p>

# utilux: Git, Docker and Work Utilities ! 🚀

This repository gathers posix-compatible scripts and tools to make my optimal Linux setup. (Supports Bash & Zsh)

## Installation

One-liner installer:

```bash
git clone https://github.com/yonis-savary/utilux ~/utilux && source ~/utilux/install
```

After installation, you may need to restart your terminal/session.

To configure utilux, launch

```bash
utilux-config # (OR) nano ~/.config/utilux/utilux-config
```

> [!IMPORTANT]
> It is advised that you configure every constant in your config if you want to use utilux to its full potential.

### Uninstall

1. Remove the `~/utilux` directory
2. Remove `~/.config/utilux` directory
3. Remove `~/.config/utilux-dashboard` directory
4. Remove `~/.local/share/utilux` directory (Warning: delete all your utilux command data, such as your repositories if you're using `gxt`)
5. Remove the `. ~/utilux/.shellrc` line from your `.bashrc`/`.zshrc` file

## Utilux shortcuts/aliases

The most interesting feature of utilux is not its dashboard, it is its collection of aliases and productivity scripts

- **Git Aliases (`gx`):**
  - `gxap` : Amend (no-edit) and Push (force with lease)
  - `gxf` : Fetch all & prune
  - `gxb+` : Create a new branch (with Jira issue support, e.g. `gxb+ APP-3290`)
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
  - ...and more!


> [!IMPORTANT]
> Autocompletion works for most commands ! gx will look for branch names, dx will look for service names...etc

Here is a detailed version of every utilux command:

```bash
# utilux global utils
utilux-install [FILE|URL|PROGRAM] # Copy/Download a file into your $PATH (Supports URL to binaries and deb files) (Also support code & discord keywords)
utilux-install-ln [FILE]  # Link a file into your $PATH
utilux-lf [DIRECTORY]     # Recursively convert CRLF to LF (default: current dir)
utilux-setup              # Check/install required packages & VSCode extensions
utilux-share [FILE]       # Start a web server to share files
utilux-ssh                # SSH key utilities
utilux-update             # utilux self-update script
utilux-update-config      # update your utilux configuration with latest changes
utilux-mount-ssh          # wizard to mount a ssh directory into your fstab

# dx = docker compose utils
dx                      # alias for docker compose
dxb                     # docker compose build
dxu                     # docker compose up -d
dxd                     # docker compose down
dxdu                    # dxd && dxu
dxr [service]           # docker compose restart
dxsp                    # docker system prune -a
dxp                     # docker compose ps
dxl [service]           # docker compose logs --follow
dxe [service] {program} # docker compose exec

# gx = git single-repository utils
gx                              # Help for all gx* scripts
gxa                             # Commit amend (no edit)
gxap                            # Amend and push with force
gxb                             # Print current branch name
gxb+ [name|jira-issue-key]      # Create a new branch tracking current one
gxbf [name]                     # Change current branch upstream
gxc                             # Alias for git checkout
gxd                             # Show short diff message
gxf                             # Fetch & prune
gxi                             # Git initialize/reset, cleanup repo
gxl [branch-name|keyword]       # Git lookup, look for a branch name
gxp                             # Push (force with lease)
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
gxt desktop <repo> [-o]         # Create a VSCode desktop shortcut for one 
gxt find <repo>                 # Find the absolute path of a repository
gxt code <repo>                 # Open VSCode for one repo
# Note: repo can mean the full repository name or a part of it !
# Also you can use --help with any of gxt subcommands, and tab for autocompletion of repos names !

```

## Dashboard

Utilux includes an Electron App for developers, this app can help you manage your Jira Issues, Gitlab Merge Requests, Notes...etc

> Requirements: Node, Npm

Dashboard commands

```bash
# build (once) and launch the dashboard
utilux-dashboard

# rebuild the app
utilux-dashboard-build

# create a .desktop shortcut !
utilux-dashboard-desktop
```

### Custom GNOME Shortcuts

`utilux-setup` can create some custome keyboard for GNOME environments, which are
  - Launch Web browser : <kbd>Ctrl</kbd> + <kbd>Alt</kbd> + <kbd>F</kbd>
  - Launch Terminal : <kbd>Ctrl</kbd> + <kbd>Alt</kbd> + <kbd>T</kbd>
  - Open Home Folder : <kbd>Ctrl</kbd> + <kbd>Alt</kbd> + <kbd>F</kbd>

## Tools & Resources

Utilux creation was motivated by a desire to have a unified Linux setup between all my devices.

So, naturally, some notes/scripts were written here:

- [Fresh Linux install script](./scripts/fresh-install.sh)
- [Mount Remote SSH Directory](./docs/remote_sshfs.md)