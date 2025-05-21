#!/bin/bash
SCRIPT_DIR=$(dirname "$(realpath "$BASH_SOURCE")")

DBWAND_BIN_DIR="$SCRIPT_DIR/bin"

if ! echo "$PATH" | grep -q "$DBWAND_BIN_DIR"; then
    logger "utilux: adding $DBWAND_BIN_DIR to path"
    export PATH="$PATH:$DBWAND_BIN_DIR"
fi

EXAMPLE_CONFIG_FILE="$SCRIPT_DIR/config.example.json"
DBWAND_CONFIG_DIR="$UTILUX_CONFIG_PATH/dbwand"
mkdir -p "$DBWAND_CONFIG_DIR" 2>/dev/null

export UTILUX_DBWAND_CONFIG_FILE="$DBWAND_CONFIG_DIR/dbwand.json"

[ -f "$UTILUX_DBWAND_CONFIG_FILE" ] || cp "$EXAMPLE_CONFIG_FILE" "$UTILUX_DBWAND_CONFIG_FILE"

alias utilux-dbwand-config="nano $UTILUX_DBWAND_CONFIG_FILE"