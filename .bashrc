#!/bin/bash

export UTILUX_SCRIPT=$(readlink -f ${BASH_SOURCE[0]})
export UTILUX_PATH=$(dirname "$UTILUX_SCRIPT")
export UTILUX_BIN_PATH="$UTILUX_PATH/bin";
export PATH="$PATH:$UTILUX_BIN_PATH"

export UTILUX_CONFIG_PATH="$HOME/.config/utilux"
mkdir -p "$UTILUX_CONFIG_PATH" 2>/dev/null

if [ ! -d "$UTILUX_BIN_PATH" ]; then 
    mkdir "$UTILUX_BIN_PATH"
fi

# Custom prompt
PS1='\[\e[92;1m\]\W\[\e[0m\]\$'

# Load setup custom bashrc file
if [ -f "$UTILUX_PATH/.bashrc.custom" ]
then
    logger "utilux: loading custom $UTILUX_PATH/.bashrc.custom script"
    . "$UTILUX_PATH/.bashrc.custom"
else
    cp "$UTILUX_PATH/.bashrc.custom.example" "$UTILUX_PATH/.bashrc.custom"
fi

alias utilux-config="nano $UTILUX_PATH/.bashrc.custom"

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

