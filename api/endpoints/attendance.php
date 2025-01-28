<?php
require_once __DIR__ . '/../../src/config/database.php';
require_once __DIR__ . '/../../src/models/attendance.php';
require_once __DIR__ . '/../../src/models/patch.php';
require_once __DIR__ . '/../../src/models/logger.php';

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$database = new Database();
$db = $database->getConnection();
$attendance = new Attendance($db);
$patch = new Patch($db);

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            // ... (existing GET logic)
            break;

        case 'POST':
            // ... (existing POST logic)
            break;

        case 'PATCH':
            $attendanceId = basename($_SERVER['REQUEST_URI']);
            $data = json_decode(file_get_contents("php://input"), true);
            
            if (!$attendanceId) {
                http_response_code(400);
                echo json_encode(["error" => "Attendance ID is required"]);
                exit();
            }
            
            $result = $patch->updateAttendance($attendanceId, $data);
            
            if ($result) {
                echo json_encode([
                    "status" => "success",
                    "message" => "Attendance record updated successfully",
                    "updatedFields" => $result
                ]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Failed to update attendance record"]);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(["error" => "Method not allowed"]);
            break;
    }
} catch (Exception $e) {
    Logger::error("Attendance endpoint error", $e);
    http_response_code(500);
    echo json_encode([
        "error" => "Server error",
        "message" => $e->getMessage()
    ]);
}

