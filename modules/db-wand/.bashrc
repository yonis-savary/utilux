#!/bin/bash
SCRIPT_DIR=$(dirname "$(realpath "$BASH_SOURCE")")

DBWAND_BIN_DIR="$SCRIPT_DIR/bin"

if ! echo "$PATH" | grep -q "$DBWAND_BIN_DIR"; then
    logger "utilux: adding $DBWAND_BIN_DIR to path"
    export PATH="$PATH:$DBWAND_BIN_DIR"
fi

DBWAND_CONFIG_DIR="$(realpath ~)/.config/utilux/dbwand"
mkdir -p "$DBWAND_CONFIG_DIR" 2>/dev/null

export UTILUX_DBWAND_CONFIG_FILE="$DBWAND_CONFIG_DIR/dbwand.json"
alias utilux-dbwand-config="nano $UTILUX_DBWAND_CONFIG_FILE"