#!/bin/sh

# This script holds every command/snippets used to install
# every software I use everyday, on a fresh debian/ubuntu installation

echo "This scripts is meant to be executed by hand step by step"
echo "It can be dangerous and lacks of verifications steps"
exit 1


#################
# Common / Core #
#################

sudo apt-get install \
    git \
    jq \
    sqlite3 \
    ffmpeg \
    curl \
    iproute2 \
    net-tools \
    htop


# Desktop Software
sudo apt-get install \
    filezilla \
    libreoffice \
    gimp \
    inkscape \


# Music/Video Utilities
sudo apt-get install \
    easytag \
    asunder


# NodeJS
sudo apt-get install \
    nodejs \
    npm






####################
# Non-Apt Software #
####################



# NVM (Node Version Manager) Script
curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.40.3/install.sh | bash


# Docker
curl -fsSL https://get.docker.com -o install-docker.sh
cat install-docker.sh
sh install-docker.sh --dry-run
sudo sh install-docker.sh

# Docker group/Authorization post-installation
sudo groupadd docker
sudo usermod -aG docker $USER
newgrp docker
chmod 666 /var/run/docker.sock


# PHP 8.4
/bin/bash -c "$(curl -fsSL https://php.new/install/linux)"


# Utilux
git clone https://github.com/yonis-savary/utilux ~/utilux && source ~/utilux/install


# Check setup - VSC Extensions/Settings
utilux-setup

# Visual Studio Code
utilux-install-code

# Opera
# Set opera as default web browser (after installation): xdg-settings set default-web-browser opera.desktop
utilux-install "https://download5.operacdn.com/ftp/pub/opera/desktop/119.0.5497.88/linux/opera-stable_119.0.5497.88_amd64.deb" opera.deb

# Bruno (Postman Alternative)
utilux-install "https://github.com/usebruno/bruno/releases/download/v2.5.0/bruno_2.5.0_amd64_linux.deb" bruno.deb

# PHP-CS-FIXER
utilux-install https://cs.symfony.com/download/php-cs-fixer-v3.phar php-cs-fixer

