# RuneScape AFK Script

Script bring runescape window to foreground every X minutes. Useful when you need to afk. Script is made for Ubuntu with X11, won't work with Wayland.

## Requirments

```
apt install x11-utils xdotool imagemagick ffmpeg wmctrl
```

## Alias

```
alias afk='php /data/sites/projects/afk/afk.php'
alias afk-cb='php /data/sites/projects/afk/afk-combat.php'
alias qq='php /data/sites/projects/afk/afktime.php'
alias rs='/data/sites/projects/afk/rs'
```


## Commands used

/usr/bin/xwininfo

Find Runescape Window ID

wmctrl -l -x | grep "RuneScape.RuneScape" | awk '{print $1}'

Hide Window

wmctrl -i -r "$RS_WIN_ID" -b add,skip_taskbar

UnHide Window

wmctrl -i -r "$RS_WIN_ID" -b remove,skip_taskbar


/www/my-pc/afk/template_match.py /www/my-pc/afk/tpl/arch.png /home/boby/rs3.png



source /home/boby/venv/bin/activate

pip install opencv-python

cp /dev/shm/rs3.png ~/

## Screenshot background window

/usr/bin/xwd -silent -id 8388622 -out /home/boby/rs3.xwd

scrot -w 8388622 /home/boby/rs3.png

works, but shows notification

import -window 8388622 /home/boby/rs3.png  

works, but a beep sound

/www/my-pc/afk/tpl/arch2.png

cp /dev/shm/rs3.png ~/

/www/my-pc/afk/template_match.py /www/my-pc/afk/tpl/arch.png /home/boby/rs3.png

