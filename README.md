<p align="center">
    <img src="./img/utilux-128.png" width="96" height="96" alt="Resound logo">
</p>

# utilux: Posix Utilities 🚀

This repository gathers scripts and tools to make my optimal Linux setup. (Supports Bash & Zsh)

## Installation

Install utilux in one command:

```bash
git clone https://github.com/yonis-savary/utilux ~/utilux && source ~/utilux/install
```

After installation, please restart your session.

Then, at any moment, you can configure utilux with:

```bash
utilux-config # (OR) nano ~/.config/utilux/utilux-config
```

> [!IMPORTANT]
> It is advised that you configure every constant in your config if you want to use utilux to its full potential.

### Uninstall

1. Remove the `~/utilux` directory
2. Remove `~/.config/utilux` directory
3. Delete the `. ~/utilux/.shellrc` line from your `.bashrc`/`.zshrc` file

## Dashboard configuration

> Requirements: PHP 8

Utilux's dashboard shows you:

- Your pending Jira tickets (token required)
- Your pending GitLab merge requests (token required)
- Your local branches in configured directories

Utilux's dashboard is configured through `utilux-dashboard-config`, then you can start it with:

```bash
utilux-dashboard
```

If all credentials in your utilux are configured, you should be able to connect to your dashboard without any
additional configuration.

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
utilux-install [FILE|URL] # Copy/Download a file into your $PATH (Supports URL to binaries and deb files)
utilux-install-ln [FILE]  # Link a file into your $PATH
utilux-install-code       # (Re)install the latest Visual Studio Code version
utilux-lf [DIRECTORY]     # Recursively convert CRLF to LF (default: current dir)
utilux-setup              # Check/install required packages & VSCode extensions
utilux-share [FILE]       # Start a web server to share files
utilux-ssh                # SSH key utilities
utilux-update             # utilux self-update script
utilux-update-config      # update your utilux configuration with latest changes

dx                      # alias for docker compose
dxb                     # docker compose build
dxu                     # docker compose up -d
dxd                     # docker compose down
dxr [service]           # docker compose restart
dxdu                    # dxd && dxu
dxp                     # docker compose ps
dxl [service]           # docker compose logs
dxe [service] [program] # docker compose exec

gx                              # Help for all gx* scripts
gxa                             # Commit amend (no edit)
gxap                            # Amend and push
gxb                             # Print current branch name
gxb+ [name|jira-ticket]         # Create a new branch tracking current one
gxbf [name]                     # Change current branch upstream
gxc                             # Alias for git checkout
gxd                             # Show short diff message
gxf                             # Fetch & prune
gxi                             # Git initialize/reset, cleanup repo
gxl                             # Git lookup, look for a branch name
gxp                             # Push (force with lease)
gxpm                            # Purge merged branches (force)
gxr [target_branch] [-y|--yes]  # Pull rebase
gxrc                            # Rebase continue
```

### Custom GNOME Shortcuts

`utilux-setup` can create some custome keyboard for GNOME environments, which are
  - Launch Opera (Web) : <kbd>Super</kbd> + <kbd>W</kbd>
  - Launch Terminal (Terminal) : <kbd>Super</kbd> + <kbd>T</kbd>
  - Open Home Folder (Files) : <kbd>Super</kbd> + <kbd>F</kbd>

## dbwand Module (WIP)

> [!CAUTION]
> This module is still in developpement and needs to be used with caution

A PHP-based database utility to fetch and manipulate data locally.

**Configuration:**

```bash
utilux-dbwand-config
```

**Example configuration:**

```json
{
    "connections": {
        "my-first-app": "pgsql://user:password@localhost:5432/my-first-app",
        "my-second-app": "pgsql://user:password@localhost:5432/my-first-app"
    }
}
```

**Example usage:**

```bash
dbwand
connect my-second-app
select * from contact
only id first_name last_name is_client
select * from dbwand where is_client = true # Use the fake `dbwand` table to filter current dataset
remove is_client
show 0-99 --json
template INSERT INTO contact_client (contact_id) VALUES (:id)
```

## Tools & Resources

Utilux creation was motivated by a desire to have a unified Linux setup between all my devices.

So, naturally, some notes/scripts were written here:

- [Mount Remote SSH Directory](./docs/remote_sshfs.md)
- [Fresh Linux install script](./scripts/fresh-install.sh)