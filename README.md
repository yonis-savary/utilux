# utilux
UNIX/Linux Utils (Alias, Scripts...),
this repository holds every scripts/informations needed to make my optimal linux setup

## How to install

First clone this repo to your home repository
```bash
cd ~
git clone https://github.com/YonisSavary/utilux
cd utilux
./install.sh
```

## Commands

```bash
# Indicate which needed packages are missing
# Can also install VSCode extensions
utilux-setup

# Install a file in a special directory link to your $PATH
utilux-install [FILE]

# Start a web server that can serve files
utilux-share [FILE]

# Recursively convert CRLF to LF inside a directory (. by default)
utilux-lf [DIRECTORY]

# Git utilitary tool that can create and manage your repos
utilux-git 

dos # docker compose up -d
dod # docker compose down
dor # docker compose restart
dop # docker compose ps
dol # docker compose logs
doe # docker compose exec

gxb  # get current branch name
gxc  # rebase continue
gxf  # fetch (prune)
gxp  # push force with lease
gxpm # delete merged branches (force)
gxr  # pull rebase on followed branch
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






