#!/bin/bash 

SCRIPT_DIR=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )

source $SCRIPT_DIR/sprint_env/bin/activate

$SCRIPT_DIR/sprint_env/bin/python3 $SCRIPT_DIR/build_app.py "$@"

