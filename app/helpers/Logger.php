<?php

class Logger {
    private static $logFile = __DIR__ . '/../../logs/app.log';

    public static function log($message) {
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] $message\n";
        
        // Създаваме директорията ако не съществува
        if (!file_exists(dirname(self::$logFile))) {
            mkdir(dirname(self::$logFile), 0777, true);
        }
        
        // Записваме съобщението
        file_put_contents(self::$logFile, $logMessage, FILE_APPEND);
    }

    public static function clear() {
        if (file_exists(self::$logFile)) {
            unlink(self::$logFile);
        }
    }
} 