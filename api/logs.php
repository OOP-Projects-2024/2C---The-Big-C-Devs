<?php
require_once __DIR__ . '/../src/models/logger.php';

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            $type = $_GET['type'] ?? 'app';
            $logFile = $type === 'db' ? 'database.log' : 'app.log';
            
            $contents = Logger::getLogContents($logFile);
            $logs = array_filter(explode(PHP_EOL, $contents));
            
            echo json_encode([
                'status' => 'success',
                'logs' => $logs
            ]);
            break;
            
        case 'DELETE':
            Logger::clearLogs();
            echo json_encode([
                'status' => 'success',
                'message' => 'Logs cleared successfully'
            ]);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Server error',
        'message' => $e->getMessage()
    ]);
}

