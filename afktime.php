#!/usr/bin/php
<?php

require 'Alarm.php';

$timeRepeat = 6;

$arg_1 = isset($argv[1]) ? $argv[1] : '';
$arg_2 = isset($argv[2]) ? $argv[2] : '';

if (empty($arg_1)) {
    echo "\n";
    echo "Usage: \n\n";
    echo "qq 5.5 = Set alaram\n";
    echo "qq rm = Remove alarm\n";
    echo "qq r = Set repeatable alaram every $timeRepeat minutes\n";
    echo "qq r 5.5 = Set repeatable alaram\n";
    echo "qq r rm = Remove repeatable alarm\n";
    echo "\n";
    exit;
}

$alarm = new Alarm();

if ($arg_1 == 'rm') {
    $alarm->cancel();
    echo "Alaram cancelled.\n\n";
    exit;
}
if ($arg_1 == 'r' && $arg_2 == 'rm') {
    $alarm->cancelRepeat();
    exit;
}

if ($arg_1 == 'r') {
    if (empty($arg_2)) {
        $arg_2 = $timeRepeat;
    }
    $timeAfk = $arg_2;
    $alarm->setRepeat($arg_2);
} else {
    $timeAfk = $arg_1;
}

$timeAfkSeconds = $timeAfk * 60;

if ($timeAfkSeconds < 20) {
    echo "[ERROR] afk time must be greater than 20 seconds\n\n";
    exit;
}

if ($timeAfkSeconds > (60 * 70)) {
    echo "[ERROR] afk time must be less than 1 hour\n\n";
    exit;
}

$time = time() + $timeAfkSeconds;
$alarm->setAlarm($time);
echo "Alarm set\n";
