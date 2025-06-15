<?php

class Alarm {

    public $alarmDataFile = __DIR__ . '/tmp/afk_alarm.txt';
    public $afkRepeatFile = __DIR__ . '/tmp/afk_repeat.txt';
    public $logRateLimit = 60; // show log on every X seconds
    public $timeLastLog = 0;

    public function check() {

        if (! file_exists($this->alarmDataFile)) {
            return 0;
        }

        $alarmData = file($this->alarmDataFile);
        $timeAlarm = $alarmData[0];
        $timeNow = time();

        if ($timeNow > $timeAlarm) {
            $this->cancel();
            echo "Time up\n";
            #exec("/usr/bin/spd-say up");
            exec("/usr/bin/ffplay -nodisp -autoexit /www/my-pc/afk/time-up.wav 2>/dev/null");
            if (file_exists($this->afkRepeatFile)) {
                $repeatAfter = file_get_contents($this->afkRepeatFile);
                echo "Reapat after = $repeatAfter\n";
                $fp = fopen($this->alarmDataFile,'w+');
                fwrite($fp, time() + $repeatAfter);
                fclose($fp);
                echo "AFK time reset\n";
            }
            return 1;
        } else {
            $timeRemaining = $timeAlarm - $timeNow;
            $timeRemaining = $this->secondsToHuman($timeRemaining);
            if (($timeNow - $this->timeLastLog) > $this->logRateLimit) {
                log2("Alarm in $timeRemaining");
                $this->timeLastLog = $timeNow;
            }
            return 0;
        }
    }

    public function setAlarm($time, $repeat = 0) {
        $fp = fopen($this->alarmDataFile,'w+');
        fwrite($fp, $time);
        fclose($fp);
    }

    public function setRepeat($timeRepeat) {
        if ($timeRepeat < 1) {
            echo "AFK Reeat time must be greater than 1 minute\n\n";
            exit;
        }
        $timeRepeat = $timeRepeat * 60;
        $fp = fopen($this->afkRepeatFile,'w+');
        fwrite($fp, $timeRepeat);
        fclose($fp);
    }

    public function secondsToHuman($seconds) {
        if ($seconds > 60) {
            $h_minutes = intval($seconds / 60);
            $h_seconds = $seconds % 60;
            if ($h_seconds <10) {
                $h_seconds = '0' . $h_seconds;
            }
            $humanTime = "$h_minutes:$h_seconds minutes";
        } else {
            $humanTime = "$seconds Seconds";
        }
        return $humanTime;
    }

    public function cancel() {
        if (file_exists($this->alarmDataFile)) {
            unlink($this->alarmDataFile);
        }
    }

    public function cancelRepeat() {
        if (file_exists($this->afkRepeatFile)) {
            unlink($this->afkRepeatFile);
            echo "Disabled repeatable alarm. rm -f " . $this->afkRepeatFile . "\n\n";
        }
    }

}
