# utilux
UNIX/Linux Utils (Alias, Scripts...),
this repository holds every scripts/informations needed to make my optimal linux setup

## How to install

```bash
git clone https://github.com/YonisSavary/utilux ~/utilux && bash ~/utilux/install
```

Configuration
```bash
utilux-config
```

## Dashboard Module

Configurable productivity dashboard that automatically start on `127.0.0.1:9999` 
(Port can be changed by setting `UTILUX_DASHBOARD_PORT`)

Configuration:
```bash
utilux-dashboard-config
```

So far, this dashboard displays:
- Your pending tickets on Jira (Need token)
- Your pending merge-request on Gitlab (Need token)
- Your local branches on configured directories

## dbwand Module (WIP)

PHP-Based database-utility to fetch and manipulate data locally

Configuration

```bash
utilux-dbwand-config
```

Example configuration
```json
{
    "connections": {
        "my-first-app": "pgsql://user:password@localhost:5432/my-first-app",
        "my-second-app": "pgsql://user:password@localhost:5432/my-first-app"
    }
}
```

Example of usage
```bash
dbwand
connect my-second-app
select * from contact
only id first_name last_name is_client
select * from dbwand where is_client = true # fake `dbwand` table can be used to select from current dataset
remove is_client
show 0-99 --json
template INSERT INTO contact_client (contact_id) VALUES (:id)
```

## Script Module

### Commands

```bash
utilux-install [FILE] # Install a file in a special directory link to your $PATH
utilux-install-code   # (re)install visual studio code
utilux-lf [DIRECTORY] # Recursively convert CRLF to LF inside a directory (. by default)
utilux-setup          # Indicate which needed packages are missing (also install usual VSCode extensions)
utilux-share [FILE]   # Start a web server that can serve files
utilux-ssh            # SSH Key utilities
utilux-git            # Git utilitary tool that can create and manage your repos
utilux-update         # Utilux self update

dx   # alias for docker compose
dxu  # docker compose up -d
dxd  # docker compose down
dxr  # docker compose restart
dxdu # dxd && dxu
dxp  # docker compose ps
dxl  # docker compose logs
dxe  # docker compose exec

gx           # help script for every gx* scripts
gxa          # commit ammend (no edit)
gxap         # ammend and push
gxb          # print current branch name
gxb+ [name]  # create a new branch tracking current one
gxbf [name]  # change current branch upstream
gxc          # git checkout
gxd          # show short diff message
gxf          # fetch prune
gxi          # git initialize/reset, cleanup your git repository
gxp          # push force with lease
gxpm         # purge merged branches (force)
gxr          # pull rebase
gxra         # rebase abort
gxrc         # rebase continue
gxrs         # rebase skip

gxs [name]   # create a git stash (name is optionnal)
gxsa [name]  # apply a git stash (name is optionnal)
gxsd [name]  # drop a git stash (name is optionnal)
gxsf {name}  # find a git stash by its name
gxsl         # list all git stashes
gxsp [name]  # pop a git stash (name is optionnal)
```

## Ressources / Tools

- [ANSI Terminal Codes](https://gist.github.com/fnky/458719343aabd01cfb17a3a4f7296797)
- [Coolors](https://coolors.co/)
- [Bootstrap Icons](https://icons.getbootstrap.com/)
- [Online PHP Editor](https://onlinephp.io/)
- [Regex101](https://regex101.com/)

- [DevURLs Aggregator](https://devurls.com/)
- [Bartosz Ciechanowski Blog](https://ciechanow.ski/)
- [UXPin Blog](https://www.uxpin.com/studio/blog/)






