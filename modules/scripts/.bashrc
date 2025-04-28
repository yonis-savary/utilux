#!/bin/bash
SCRIPT_DIR=$(dirname "$(realpath "$BASH_SOURCE")")

DOCKER_SCRIPTS="$SCRIPT_DIR/docker"
GIT_SCRIPTS="$SCRIPT_DIR/git"
UTILUX_SCRIPTS="$SCRIPT_DIR/utilux"

if ! echo "$PATH" | grep -q "$DOCKER_SCRIPTS"; then
    logger "utilux: adding $DOCKER_SCRIPTS to path"
    logger "utilux: adding $GIT_SCRIPTS to path"
    logger "utilux: adding $UTILUX_SCRIPTS to path"
    export PATH="$PATH:$DOCKER_SCRIPTS:$GIT_SCRIPTS:$UTILUX_SCRIPTS"
fi

alias dx="docker compose"
alias dxr="docker compose restart"
alias dxl="docker compose logs"
alias dxe="docker compose exec"