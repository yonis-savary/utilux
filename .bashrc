#!/bin/bash

declare -r SCRIPT=$(readlink -f ${BASH_SOURCE[0]})

SCRIPT_PATH="$(dirname "$SCRIPT")/scripts";

export PATH="$PATH:$SCRIPT_PATH"

PS1="\[\e]0;\u@\h: \w\a\]${debian_chroot:+($debian_chroot)}\[\033[01;32m\]\u@\h\[\033[00m\]:\[\033[01;36m\]\W\[\033[00m\]\$"