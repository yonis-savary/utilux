#!/bin/bash

declare -r SCRIPT=$(readlink -f ${BASH_SOURCE[0]})

SCRIPT_PATH="$(dirname "$SCRIPT")/scripts";

export PATH="$PATH:$SCRIPT_PATH"