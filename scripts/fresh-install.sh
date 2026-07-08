#!/bin/sh

# This script holds every command/snippets used to install
# every software I use everyday, on a fresh debian/ubuntu installation

# One liner-install
# Copy-paste this line to execute this script
# sudo apt install git && git clone https://github.com/yonis-savary/utilux ~/utilux && bash ~/utilux/scripts/fresh-install.sh

check_whiptail() {
    if ! command -v whiptail >/dev/null 2>&1; then
        echo "whiptail not found, installing it..."
        sudo apt install -y whiptail
    fi
}

is_selected() {
    case "$SELECTED" in
        *"\"$1\""*) return 0 ;;
        *) return 1 ;;
    esac
}

already_installed() {
    echo "$1 already installed"
}

#################
# Common / Core #
#################

install_core() {
    # System utilities
    sudo apt install -y \
        git \
        jq \
        sqlite3 \
        ffmpeg \
        curl \
        iproute2 \
        net-tools \
        sshfs \
        htop
}

install_desktop() {
    # Desktop utilities/software
    sudo apt install -y \
        filezilla \
        libreoffice \
        gimp \
        inkscape
}

install_media() {
    # Music/Video utilities
    sudo apt install -y \
        easytag \
        asunder
}

install_nodejs() {
    sudo apt install -y \
        nodejs \
        npm
}

####################
# Non-Apt Software #
####################

install_nvm() {
    if [ -d "$HOME/.nvm" ]; then
        already_installed "nvm"
        return
    fi
    # NVM (Node Version Manager)
    curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.40.3/install.sh | bash
}

install_docker() {
    if command -v docker >/dev/null 2>&1; then
        already_installed "docker"
        return
    fi
    curl -fsSL https://get.docker.com -o install-docker.sh
    cat install-docker.sh
    sh install-docker.sh --dry-run
    sudo sh install-docker.sh
    # Docker group/Authorization post-installation
    sudo groupadd docker
    sudo usermod -aG docker "$USER"
    newgrp docker
    chmod 666 /var/run/docker.sock
}

install_php() {
    if command -v php >/dev/null 2>&1; then
        already_installed "php"
        return
    fi
    /bin/bash -c "$(curl -fsSL https://php.new/install/linux)"
}

install_utilux() {
    if [ -d "$HOME/utilux" ]; then
        already_installed "utilux"
        return
    fi
    git clone https://github.com/yonis-savary/utilux ~/utilux && . ~/utilux/install
}

install_claude() {
    if command -v claude >/dev/null 2>&1; then
        already_installed "claude"
        return
    fi
    curl -fsSL https://claude.ai/install.sh | bash
}

install_vscode() {
    if command -v code >/dev/null 2>&1; then
        already_installed "vscode"
    else
        utilux-install code
    fi
    # Check setup - VSC Extensions/Settings
    utilux-setup
}

########
# Menu #
########

check_whiptail

SELECTED=$(whiptail --checklist "Select what you want to install" 22 78 14 \
    "core"        "System utilities (git, jq, curl, htop...)" ON \
    "desktop"     "Desktop software (filezilla, gimp, inkscape...)" ON \
    "media"       "Music/Video utilities (easytag, asunder)" ON \
    "nodejs"      "NodeJS + npm (apt)" ON \
    "nvm"         "NVM (Node Version Manager)" ON \
    "docker"      "Docker" ON \
    "php"         "PHP" ON \
    "utilux"      "Utilux (personal dotfiles/config)" ON \
    "claude"      "Claude Code" ON \
    "vscode"      "Visual Studio Code + setup" ON \
    3>&1 1>&2 2>&3)

if [ -z "$SELECTED" ]; then
    echo "Nothing selected, aborting."
    exit 0
fi

is_selected core       && install_core
is_selected desktop    && install_desktop
is_selected media      && install_media
is_selected nodejs     && install_nodejs
is_selected nvm        && install_nvm
is_selected docker     && install_docker
is_selected php        && install_php
is_selected utilux     && install_utilux
is_selected claude     && install_claude
is_selected vscode     && install_vscode
