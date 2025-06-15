<?php

function lookForRuneScape($rsWinID) {
    $cmd = "/usr/bin/xdotool getwindowfocus";
    exec($cmd, $result);
    while (! isset($result[0])) {
        log2("ERROR: Failed to find active window using xdotool getwindowfocus");
        return false;
    }
    if ($result[0] == $rsWinID) {
        return true;
    } else {
        return false;
    }
}

function activateWindow($winID) {
    $cmd = "/usr/bin/xdotool windowactivate $winID 1>/dev/null 2>&1";
    exec($cmd, $r, $status);
    if ($status) {
        log2("$cmd");
        log2("Failed to activate runescape window. Game client closed ?");
        $cmd = 'zenity --error --width=400 --text "<big>Failed to activate rs3 window.</big>"';
        exec($cmd);
        exit;
    }
}

function log2($str) {
    echo "[" . date("g:i:s A") . "] " . $str . "\n";
}

function isForceUp() {
    $forceUpFile = __DIR__ . '/tmp/force';
    if (file_exists($forceUpFile)) {
        log2("Force up");
        unlink($forceUpFile);
        return true;
    } else {
        return false;
    }
}

function findRSID($title) {

    exec('/usr/bin/xdotool search --onlyvisible "RuneScape" 2>/dev/null', $winIds);

    if (!isset($winIds[0])) {
        log2("Unable to find RuneScape Window");
        exit;
    }
    $selectedId = null;

    if (count($winIds) == 1) {
        $selectedId = $winIds[0];
        log2("Found a single RuneScape window with ID: $selectedId");
        return $selectedId;
    } else if (count($winIds) == 2) {
        $window_1 = $winIds[0];
        $window_2 = $winIds[1];

        if (isChildWindow($window_1, $window_2)) {
            return $window_2;
        } else if (isChildWindow($window_2, $window_1)) {
            return $window_1;
        } else {
            log2("Found 2 RuneScape windows, but neither is a child of the other. Investigate manually.");
        }
    } else {
        log2("Found more windows, investigate them.");
    }

    foreach ($winIds as $index => $winId) {
        echo "[$index] Window ID: $winId\n";
    }

    $selectedIndex = null;
    do {
        $selectedIndex = readline("Select the window index: ");
        if (!isset($winIds[$selectedIndex])) {
            echo "Invalid selection. Please try again.\n";
        }
    } while (!isset($winIds[$selectedIndex]));

    $selectedId = $winIds[$selectedIndex];
    echo "User selected window ID: $selectedId";
    return $selectedId;
}

function isChildWindow($parentId, $childId) {
    $parentId = strtolower(sprintf("0x%X", $parentId));
    $childId = strtolower(sprintf("0x%X", $childId));
    exec("xwininfo -id $parentId -tree", $output);
    foreach ($output as $line) {
        if (strpos($line, (string)$childId) !== false) {
            return true;
        }
    }
    return false;
}

function SecToMinutes($seconds) {
    $seconds = round($seconds);
    $minutes = floor($seconds / 60);
    $seconds = $seconds % 60;
    return sprintf('%02d:%02d', $minutes, $seconds);
}