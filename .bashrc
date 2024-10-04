#!/bin/bash

declare -r UTILUX_SCRIPT=$(readlink -f ${BASH_SOURCE[0]})
declare -r UTILUX_SCRIPT_PATH="$(dirname "$UTILUX_SCRIPT")/scripts";

export PATH="$PATH:$UTILUX_SCRIPT_PATH"

PS1="\[\e]0;\u@\h: \w\a\]${debian_chroot:+($debian_chroot)}\[\033[01;32m\]\u@\h\[\033[00m\]:\[\033[01;36m\]\W\[\033[00m\]\$"