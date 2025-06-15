#!/bin/bash
# sudo apt install -y wmctrl

RS_WIN_ID=$(wmctrl -l -x | grep "RuneScape.RuneScape" | awk '{print $1}')

if [ -z "$RS_WIN_ID" ]; then
    echo "RuneScape is not running"
    exit
fi

if xprop -id "$RS_WIN_ID" | grep -q "_NET_WM_ACTION_MINIMIZE"; then
    wmctrl -i -r "$RS_WIN_ID" -b add,skip_taskbar
    echo "Hidden"
else
    wmctrl -i -r "$RS_WIN_ID" -b remove,skip_taskbar
    echo "Visible"
fi
