#!/bin/bash

export UTILUX_SCRIPT=$(readlink -f ${BASH_SOURCE[0]})
export UTILUX_PATH=$(dirname "$UTILUX_SCRIPT")

export UTILUX_SCRIPT_PATH="$UTILUX_PATH/scripts";
export UTILUX_GX_SCRIPT_PATH="$UTILUX_PATH/scripts/git";
export UTILUX_DX_SCRIPT_PATH="$UTILUX_PATH/scripts/docker";

export UTILUX_BIN_PATH="$UTILUX_PATH/bin";

export PATH="$PATH:$UTILUX_SCRIPT_PATH:$UTILUX_BIN_PATH:$UTILUX_GX_SCRIPT_PATH:$UTILUX_DX_SCRIPT_PATH"

PS1="\[\e]0;\u@\h: \w\a\]${debian_chroot:+($debian_chroot)}\[\033[01;32m\]\u@\h\[\033[00m\]:\[\033[01;36m\]\W\[\033[00m\]\$"