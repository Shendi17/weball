<?php

require_once __DIR__ . '/../classes/utils/LogRotator.php';

function writeSearchLog($message) {
    static $logRotator = null;
    
    if ($logRotator === null) {
        $config = require __DIR__ . '/../config/log_config.php';
        $logRotator = new LogRotator(
            $config['search_log']['file'],
            $config['search_log']['max_size'],
            $config['search_log']['max_files']
        );
    }
    
    $logRotator->write($message);
}
