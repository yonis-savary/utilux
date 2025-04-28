#!/bin/bash

export UTILUX_SCRIPT=$(readlink -f ${BASH_SOURCE[0]})
export UTILUX_PATH=$(dirname "$UTILUX_SCRIPT")
export UTILUX_BIN_PATH="$UTILUX_PATH/bin";
export PATH="$PATH:$UTILUX_BIN_PATH"

CONFIG_PATH=$(realpath "~/.config/utilux")
# Create dir in .config if inexistent
if [ ! -d "$CONFIG_PATH" ]
then
    mkdir -p "$CONFIG_PATH"
fi


# Custom prompt
PS1="\[\e]0;\u@\h: \w\a\]${debian_chroot:+($debian_chroot)}\[\033[01;32m\]\u@\h\[\033[00m\]:\[\033[01;36m\]\W\[\033[00m\]\$"


# Load setup custom bashrc file
if [ -f "$UTILUX_PATH/.bashrc.custom" ]
then
    logger "utilux: loading custom $UTILUX_PATH/.bashrc.custom script"
    . "$UTILUX_PATH/.bashrc.custom"
else
    cp "$UTILUX_PATH/.bashrc.custom.example" "$UTILUX_PATH/.bashrc.custom"
fi


# Load installed modules
MODULES_PATH="$UTILUX_PATH/modules"
for file in $(ls -1 "$MODULES_PATH" ); do
    file="$MODULES_PATH/$file"
    bashrc_file="$file/.bashrc"
    if [ -f "$bashrc_file" ]
    then
        logger "utilux: loading $bashrc_file"
        . "$bashrc_file"
    fi
done

