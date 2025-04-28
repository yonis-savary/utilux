#!/bin/bash
SCRIPT_DIR=$(dirname "$(realpath "$BASH_SOURCE")")

PORT="${UTILUX_DASHBOARD_PORT:-9999}"
LISTENING_URL="127.0.0.1:$PORT"
IS_LISTENING="$(ss -tuln | awk '{print $5}' | grep -E "$LISTENING_URL" -c)"

EXAMPLE_CONFIG_FILE="$SCRIPT_DIR/config.example.json"
CONFIG_FILE="$SCRIPT_DIR/config.json"

if [ ! -f "$CONFIG_FILE" ]
then
    cp "$EXAMPLE_CONFIG_FILE" "$CONFIG_FILE"
fi

if [ "0" = "$IS_LISTENING" ]
then
    logger "utilux-dashboard: launching php server on $LISTENING_URL"
    original="$(pwd)"
    cd "$SCRIPT_DIR"
    nohup php -S "$LISTENING_URL" > "$SCRIPT_DIR/server.log" 2>&1 &
    cd "$original"
fi

export UTILUX_DASHBOARD_CONFIG_FILE="$SCRIPT_DIR/config.json"
alias utilux-dashboard-config="nano $UTILUX_DASHBOARD_CONFIG_FILE"
