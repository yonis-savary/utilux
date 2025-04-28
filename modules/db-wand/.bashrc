#!/bin/bash
SCRIPT_DIR=$(dirname "$(realpath "$BASH_SOURCE")")

DBWAND_BIN_DIR="$SCRIPT_DIR/bin"

if ! echo "$PATH" | grep -q "$DBWAND_BIN_DIR"; then
    logger "utilux: adding $DBWAND_BIN_DIR to path"
    export PATH="$PATH:$DBWAND_BIN_DIR"
fi
