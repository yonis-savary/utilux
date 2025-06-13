FROM ubuntu:22.04

# Éviter les invites interactives pendant l'installation
ENV DEBIAN_FRONTEND=noninteractive

# Mettre à jour les paquets et installer les dépendances
RUN apt-get update && apt-get install -y \
    zsh \
    git \
    curl \
    wget \
    vim \
    nano \
    htop \
    tree \
    unzip \
    ca-certificates \
    locales \
    sudo \
    iproute2 \
    && rm -rf /var/lib/apt/lists/*

# Configurer les locales
RUN locale-gen en_US.UTF-8
ENV LANG=en_US.UTF-8
ENV LANGUAGE=en_US:en
ENV LC_ALL=en_US.UTF-8

# Créer un utilisateur non-root
RUN useradd -m -s /bin/zsh -G sudo devuser && \
    echo 'devuser:devuser' | chpasswd

# Installer Oh My Zsh pour une meilleure expérience zsh
USER devuser
WORKDIR /home/devuser

ENV TERM=xterm

RUN bash -c "$(curl -fsSL https://php.new/install/linux/8.4)"

RUN sh -c "$(curl -fsSL https://raw.githubusercontent.com/ohmyzsh/ohmyzsh/master/tools/install.sh)" "" --unattended

# Configurer zsh comme shell par défaut
USER root
RUN chsh -s /bin/zsh devuser

USER devuser

# Commande par défaut pour garder le conteneur en vie
CMD ["/bin/zsh"]