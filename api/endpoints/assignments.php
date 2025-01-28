<?php
require_once __DIR__ . '/../../src/config/database.php';
require_once __DIR__ . '/../../src/models/assignment.php';
require_once __DIR__ . '/../../src/models/patch.php';
require_once __DIR__ . '/../../src/models/logger.php';

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$database = new Database();
$db = $database->getConnection();
$assignment = new Assignment($db);
$patch = new Patch($db);

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if (isset($_GET['id'])) {
                $result = $assignment->getAssignmentById($_GET['id']);
                if ($result) {
                    echo json_encode($result);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'Assignment not found']);
                }
            } elseif (isset($_GET['courseID'])) {
                $result = $assignment->getAssignmentsByCourse($_GET['courseID']);
                echo json_encode($result);
            } else {
                http_response_code(400);
                echo json_encode(['error' => 'Missing required parameters']);
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['courseID']) || !isset($data['title']) || 
                !isset($data['description']) || !isset($data['dueDate'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing required fields']);
                exit();
            }
            
            $result = $assignment->createAssignment(
                $data['courseID'],
                $data['title'],
                $data['description'],
                $data['dueDate']
            );
            
            if ($result) {
                http_response_code(201);
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Assignment created successfully',
                    'assignmentID' => $result
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to create assignment']);
            }
            break;

        case 'PATCH':
            $assignmentId = basename($_SERVER['REQUEST_URI']);
            $data = json_decode(file_get_contents("php://input"), true);
            
            if (!$assignmentId) {
                http_response_code(400);
                echo json_encode(["error" => "Assignment ID is required"]);
                exit();
            }
            
            $result = $patch->updateAssignment($assignmentId, $data);
            
            if ($result) {
                echo json_encode([
                    "status" => "success",
                    "message" => "Assignment updated successfully",
                    "updatedFields" => $result
                ]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Failed to update assignment"]);
            }
            break;

        case 'DELETE':
            $assignmentId = basename($_SERVER['REQUEST_URI']);
            
            if (!$assignmentId) {
                http_response_code(400);
                echo json_encode(['error' => 'Assignment ID is required']);
                exit();
            }
            
            $result = $assignment->deleteAssignment($assignmentId);
            
            if ($result) {
                Logger::logDatabaseChange('DELETE', 'assignments', ['assignmentID' => $assignmentId]);
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Assignment deleted successfully'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to delete assignment']);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(["error" => "Method not allowed"]);
            break;
    }
} catch (Exception $e) {
    Logger::error("Assignment endpoint error", $e);
    http_response_code(500);
    echo json_encode([
        "error" => "Server error",
        "message" => $e->getMessage()
    ]);
}

