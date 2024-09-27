# utilux
UNIX/Linux Utils (Alias, Scripts...),
this repository holds every scripts/informations needed to make my optimal linux setup

## How to install

First clone this repo to your home repository
```bash
cd ~
git clone https://github.com/YonisSavary/utilux
```

then add this line to `~/.bashrc`
```bash
. ~/utilux/.bashrc
```

## Commands

```bash
# Indicate which needed packages are missing
# Can also install VSCode extensions
popasetup

# Open code to current or given path and exit console
popacode [PATH]

# Same as popacode but also launch php -S
# in either Public/public/htdocs with given port (5000 by default)
popaweb [PATH] [PORT]

# Recursively convert CRLF to LF inside a directory (. by default)
popaLF [DIRECTORY]
```

### `git-utils`

`git-utils` is a utility tool to perform actions on your git repositories and regroup them in a single directory (usually `~/.config/utilux/git-utils/repositories`)

```bash
# Display a list of available commands
git-utils help

# Register and get your api token
git-utils register-key <API_KEY>
git-utils get-key

# Dis/enable credential cache/storing
git-utils enable-cache
git-utils disable-cache

# Clone all of your repositories (inclunding organizations repositories)
git-utils clone-all
# Make a repository and push it to github
git-utils make <PROJECT_NAME>
# Pull every modifications to your repos excluding where changes where made without committing
git-utils pull-all

# Make a symlink to your repository directory on your desktop
git-utils symlink
# Make a symlink to one of your directory on your desktop
git-utils symlink <Author/Repo>

# List installed and availables directories
git-utils list-local
git-utils list-dist
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






