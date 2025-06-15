#!/usr/bin/php
<?php

require __DIR__ . '/functions.php';
require __DIR__ . '/Alarm.php';

$debug = false;
$screenShot = false;

# sudo apt install xdotool 
# AFK Time in seconds. Every X seconds, RuneScape Windows will get activated.

$timeAfk = 9 * 60; // 9 minutes

if (isset($argv[1])) {
    $timeAfkCustom = $argv[1];
    if ($timeAfkCustom == 'a') {
        $screenShot = true;
        $tplImage = "/www/my-pc/afk/tpl/arch.png";
        $tplResult = "True";
        $afkPlugin = "Arch";
        $afkPluginSleep = 30;
    } elseif ($timeAfkCustom == 'cb') {
        $screenShot = true;
        $tplImage = "/www/my-pc/afk/tpl/not-attacking.png";
        $tplResult = "True";
        $afkPlugin = "Combat";
        $afkPluginSleep = 10;
    } else {
        if ($timeAfkCustom > 0 && $timeAfkCustom < 10) {
            $timeAfk = $timeAfkCustom * 60;
        } else if ($timeAfkCustom > 30 && $timeAfkCustom < 300) {
            $timeAfk = $timeAfkCustom;
        }
    }
}

log2("AFK Time = " . SecToMinutes($timeAfk));
$rsWinID = findRSID('RuneScape');
log2("RuneScape WindowID = $rsWinID");

$timeLastActive = 0;
$timeLastScreenshot = time();
$timeNotInAction = time();
$isRsActive = false;

$alarm = new Alarm();
$alarm->cancelRepeat();

while(1) {

    $isRsActive = false;

    if (lookForRuneScape($rsWinID)) {
        $timeLastActive = time();
        if (!$isRsActive) {
            log2("RS Active - $rsWinID");
            $isRsActive = true;
        }
    }
    
    $timeElapsed = time() - $timeLastActive;

    if ($timeElapsed > $timeAfk || $alarm->check() || isForceUp()) {
        log2("RuneScape Needs You!");
        $timeLastActive = time();
        activateWindow($rsWinID);
    }

    $filePath = '/home/boby/alarm.txt';

    $lines = file($filePath, FILE_IGNORE_NEW_LINES);
    $currentDateTime = time();
    $updateAlarmFile = false;
    
    foreach ($lines as $index => $line) {
        $line = trim($line);
        if (empty($line)) continue;
        if (preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $line)) {
            $alarmDateTime = strtotime($line);
            if ($currentDateTime > $alarmDateTime) {
                exec("/usr/bin/ffplay -nodisp -autoexit /www/my-pc/afk/time-up.wav 2>/dev/null");
                $updateAlarmFile = true;
                unset($lines[$index]);
            }
        }
    }
    
    if ($updateAlarmFile) {
        file_put_contents($filePath, implode("\n", $lines));
    }

    if (! $isRsActive && $screenShot) {
        sleep($afkPluginSleep);
        $timeSinceLastScreenshot = time() - $timeLastScreenshot;
        if ($timeSinceLastScreenshot > 15) {
            $timeLastScreenshot = time();
            log2("take screenshot - $afkPlugin");
            $cmd = "/usr/bin/xwd -silent -id " . $rsWinID . " -out /dev/shm/rs3.xwd";
            exec($cmd);
            $cmd = "/usr/bin/convert /dev/shm/rs3.xwd /dev/shm/rs3.png";
            exec($cmd);
            if (file_exists('/dev/shm/rs3.png')) {
                // /www/my-pc/afk/template_match.py /www/my-pc/afk/tpl/arch.png /dev/shm/rs3.png
                $cmd = "/www/my-pc/afk/template_match.py " . $tplImage . " /dev/shm/rs3.png";
                $result = exec($cmd);
                if ($result == 'True') {
                    if ((time() - $timeNotInAction) > 120) {
                        log2("Plugin $afkPlugin need you.");
                        $timeNotInAction = time();
                        activateWindow($rsWinID);
                    }
                }
            } else {
                log2('rs3.png not found');
            }
        } else {
            if ($debug) log2("Skip screenshot " . $timeSinceLastScreenshot);
        }
    }

    sleep(2);

}
