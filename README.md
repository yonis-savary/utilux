# utilux
UNIX/Linux Utils (Alias, Scripts...),
this repository holds every scripts/informations needed to make my optimal linux setup

## How to install

```bash
cd ~ && git clone https://github.com/YonisSavary/utilux && cd ./utilux && ./install.sh
```

## Commands

```bash
utilux-setup          # Indicate which needed packages are missing (also install usual VSCode extensions)
utilux-install-code   # (re)install visual studio code
utilux-install [FILE] # Install a file in a special directory link to your $PATH
utilux-share [FILE]   # Start a web server that can serve files
utilux-lf [DIRECTORY] # Recursively convert CRLF to LF inside a directory (. by default)
utilux-git            # Git utilitary tool that can create and manage your repos

dx   # alias for docker compose
dxu  # docker compose up -d
dxd  # docker compose down
dxr  # docker compose restart
dxdu # dxd && dxu
dxp  # docker compose ps
dxl  # docker compose logs
dxe  # docker compose exec

gx           # help script for every gx* scripts
gxa          # Commit ammend with no edit
gxb          # get current branch name
gxb+ [name]  # create a new branch tracking current one
gxf [name]   # change current branch upstream
gxf          # fetch prune
gxp          # push force with lease
gxap         # gxa && gxp
gxpm         # purge merged branches (Force)
gxr          # pull rebase
gxrc         # rebase continue

gxs [name]   # create a git stash (name is optionnal)
gxsa [name]  # apply a git stash (name is optionnal)
gxsd [name]  # drop a git stash (name is optionnal)
gxsf {name}  # find a git stash by its name
gxsl         # list all git stashes
gxsp [name]  # pop a git stash (name is optionnal)
```

### `utilux-git`

`utilux-git` is a utility tool to perform actions on your git repositories and regroup them in a single directory (usually `~/.config/utilux/git-utils/repositories`)

```bash
# Display a list of available commands
utilux-git help

# Register and get your api token
utilux-git register-key <API_KEY>
utilux-git get-key

# Dis/enable credential cache/storing
utilux-git enable-cache
utilux-git disable-cache

# Clone all of your repositories (inclunding organizations repositories)
utilux-git clone-all
# Make a repository and push it to github
utilux-git make <PROJECT_NAME>
# Pull every modifications to your repos excluding where changes where made without committing
utilux-git pull-all

# Make a symlink to your repository directory on your desktop
utilux-git symlink
# Make a symlink to one of your directory on your desktop
utilux-git symlink <Author/Repo>

# List installed and availables directories
utilux-git list-local
utilux-git list-dist
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






