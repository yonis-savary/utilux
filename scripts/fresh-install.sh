# Core Utilities
sudo apt-get install \
    git \
    jq \
    sqlite3 \
    ffmpeg \
    curl \
    iproute2 \
    net-tools

# Desktop Software
sudo apt-get install \
    filezilla \
    libreoffice \
    gimp \
    inkscape \

# Music Utilities
sudo apt-get install \
    easytag \
    asunder

# NodeJS
sudo apt-get install \
    nodejs \
    npm

git clone https://github.com/yonis-savary/utilux ~/utilux && source ~/utilux/install

# Docker
curl -fsSL https://get.docker.com -o install-docker.sh
cat install-docker.sh
sh install-docker.sh --dry-run
sudo sh install-docker.sh

sudo groupadd docker
sudo usermod -aG docker $USER
newgrp docker
chmod 666 /var/run/docker.sock

# PHP 8.4
/bin/bash -c "$(curl -fsSL https://php.new/install/linux)"

# Visual Studio Code
utilux-install-code

# Check setup - VSC Extensions/Settings
utilux-setup

# PHP-CS-FIXER
utilux-install https://cs.symfony.com/download/php-cs-fixer-v3.phar php-cs-fixer