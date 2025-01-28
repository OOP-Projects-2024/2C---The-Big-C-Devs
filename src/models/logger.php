<?php
class Logger {
    private static $logDirectory = __DIR__ . '/../../logs';
    private static $defaultLogFile = 'app.log';
    private static $dbLogFile = 'database.log';
    private static $maxLogSize = 5242880; // 5MB

    /**
     * Initialize logger
     */
    public static function init() {
        if (!file_exists(self::$logDirectory)) {
            mkdir(self::$logDirectory, 0777, true);
        }
    }

    /**
     * Log general application messages
     */
    public static function log($message, $level = 'INFO') {
        self::init();
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[{$timestamp}] [{$level}] {$message}" . PHP_EOL;
        $logFile = self::$logDirectory . '/' . self::$defaultLogFile;
        
        self::writeLog($logFile, $logMessage);
    }

    /**
     * Log database changes
     */
    public static function logDatabaseChange($action, $table, $data, $userId = null) {
        self::init();
        $timestamp = date('Y-m-d H:i:s');
        $logFile = self::$logDirectory . '/' . self::$dbLogFile;
        
        $dataString = json_encode($data);
        $userInfo = $userId ? "User ID: {$userId}" : "System";
        
        $logMessage = "[{$timestamp}] [{$action}] Table: {$table} | {$userInfo} | Data: {$dataString}" . PHP_EOL;
        
        self::writeLog($logFile, $logMessage);
    }

    /**
     * Log errors
     */
    public static function error($message, $exception = null) {
        $errorMessage = $message;
        if ($exception) {
            $errorMessage .= " | Exception: " . $exception->getMessage();
            $errorMessage .= " | Stack Trace: " . $exception->getTraceAsString();
        }
        self::log($errorMessage, 'ERROR');
    }

    /**
     * Write to log file with rotation
     */
    private static function writeLog($logFile, $message) {
        // Rotate log if it exceeds max size
        if (file_exists($logFile) && filesize($logFile) > self::$maxLogSize) {
            $info = pathinfo($logFile);
            $rotatedFile = $info['dirname'] . '/' . $info['filename'] . '_' . date('Y-m-d_H-i-s') . '.' . $info['extension'];
            rename($logFile, $rotatedFile);
        }

        file_put_contents($logFile, $message, FILE_APPEND);
    }

    /**
     * Clear logs
     */
    public static function clearLogs() {
        self::init();
        $files = glob(self::$logDirectory . '/*.log');
        foreach ($files as $file) {
            unlink($file);
        }
    }

    /**
     * Get log contents
     */
    public static function getLogContents($logFile = null) {
        self::init();
        $file = self::$logDirectory . '/' . ($logFile ?: self::$defaultLogFile);
        
        if (file_exists($file)) {
            return file_get_contents($file);
        }
        
        return '';
    }
}

