# utilux: Linux/Bash Utilities 🚀

This repository gathers scripts, tools, to make my optimal linux setup. (Supports Bash & Zsh)

## Features

- **Jira & GitLab Dashboard:**
  Track your current work, pending tickets, merge requests, and local branches in one place.

- **Git Aliases (`gx`):**
    - `gxap` : Amend (no-edit) and Push (force with lease)
    - `gxf` : Fetch all & prune
    - `gxb` : Show current branch
    - `gxb+` : Create a new branch (with Jira issue support, e.g. `gxb+ APP-3290`)
    - ...and more!

- **Docker Compose Aliases (`dx`):**
    - `dxu` : `docker compose up -d`
    - `dxdu` : `docker compose down && docker compose up -d`
    - `dxl [process]` : Show logs
    - `dxe [process] [command]` : Exec into a process
    - ...and more!

- **Utilux Tools:**
    - `utilux-install [file]` : Install a binary in your `$PATH`
    - `utilux-share [file]` : Start a local Python server to share a file
    - `utilux-ssh` : Manage local SSH keys
    - ...and more!


## 🚀 Quick Start

Install utilux in one command:

```bash
git clone https://github.com/yonis-savary/utilux ~/utilux && source ~/utilux/install
```

After installation, please restart your session

**Configuration:**
```bash
utilux-config
```


## ❌ Uninstall

1. Remove the `~/utilux` directory
3. Remove `~/.config/utilux` directory
2. Delete the line `. ~/utilux/.bashrc` from your `.bashrc` file

## 🛠️ Script Module

```bash
utilux-install [FILE|URL] # Copy/Download a file into your $PATH
utilux-install-ln [FILE]  # Link a file into your $PATH
utilux-install-code       # (Re)install the latest Visual Studio Code version
utilux-lf [DIRECTORY]     # Recursively convert CRLF to LF (default: current dir)
utilux-setup              # Check/install required packages & VSCode extensions
utilux-share [FILE]       # Start a web server to share files
utilux-ssh                # SSH key utilities
utilux-update             # Self-update utilux

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
gxb+ [name]                     # Create a new branch tracking current one
gxbf [name]                     # Change current branch upstream
gxc                             # Alias for git checkout
gxd                             # Show short diff message
gxf                             # Fetch & prune
gxi                             # Git initialize/reset, cleanup repo
gxp                             # Push (force with lease)
gxpm                            # Purge merged branches (force)
gxr [target_branch] [-y|--yes]  # Pull rebase
gxrc                            # Rebase continue
```


## 📊 Dashboard Module

A configurable gitlab/jira dashboard (starts automatically at `127.0.0.1:9999`, you can change the port with `UTILUX_DASHBOARD_PORT`)

**Configuration:**
```bash
utilux-dashboard-config
```

**Dashboard shows:**
- Your pending Jira tickets (token required)
- Your pending GitLab merge requests (token required)
- Your local branches in configured directories


## 🗄️ dbwand Module (WIP)

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


## 📚 Resources & Tools

- [ANSI Terminal Codes](https://gist.github.com/fnky/458719343aabd01cfb17a3a4f7296797)
- [Coolors](https://coolors.co/)
- [Bootstrap Icons](https://icons.getbootstrap.com/)
- [Online PHP Editor](https://onlinephp.io/)
- [Regex101](https://regex101.com/)
- [DevURLs Aggregator](https://devurls.com/)
- [Bartosz Ciechanowski Blog](https://ciechanow.ski/)
- [UXPin Blog](https://www.uxpin.com/studio/blog/)







