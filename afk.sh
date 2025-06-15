#!/bin/bash

if [ "$1" != "" ]; then
    AFK_TIME=$(($1*60))
else
    AFK_TIME=60
fi

WID=`xdotool search "RuneScape" | head -1`

if [ "$WID" != "" ]; then
    watch -n $AFK_TIME xdotool windowactivate $WID
else
    echo "Failed to find ruenscape client, exiting..."
fi
