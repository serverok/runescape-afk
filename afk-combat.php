#!/usr/bin/php
<?php

if (file_exists('/tmp/rs3cb')) {
    unlink('/tmp/rs3cb');
    echo "Combat Disabled";
} else {
    touch('/tmp/rs3cb');
    echo "Combat Enabled";
}

echo "\n";
