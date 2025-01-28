<?php
require_once __DIR__ . '/../../src/config/database.php';
require_once __DIR__ . '/../../src/models/get.php';
require_once __DIR__ . '/../../src/models/post.php';
require_once __DIR__ . '/../../src/models/patch.php';
require_once __DIR__ . '/../../src/models/logger.php';

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

$database = new Database();
$db = $database->getConnection();
$get = new Get($db);
$post = new Post($db);
$patch = new Patch($db);

try {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            if (isset($_GET['id'])) {
                $result = $get->getStudentById($_GET['id']);
                if ($result) {
                    echo json_encode($result);
                } else {
                    http_response_code(404);
                    echo json_encode(['error' => 'Student not found']);
                }
            } elseif (isset($_GET['courseID'])) {
                $result = $get->getStudentsByCourse($_GET['courseID']);
                if ($result) {
                    echo json_encode($result);
                } else {
                    echo json_encode([]);
                }
            } else {
                $result = $get->getAllStudents();
                echo json_encode($result);
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['courseID']) || !isset($data['firstName']) || 
                !isset($data['lastName']) || !isset($data['email'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing required fields']);
                exit();
            }
            
            $result = $post->addStudent(
                $data['courseID'],
                $data['firstName'],
                $data['lastName'],
                $data['email']
            );
            
            if ($result) {
                Logger::logDatabaseChange('INSERT', 'sk_students', $data);
                http_response_code(201);
                echo json_encode([
                    'status' => 'success',
                    'message' => 'Student created successfully',
                    'studentID' => $db->lastInsertId()
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to create student']);
            }
            break;

        case 'PATCH':
            $studentId = basename($_SERVER['REQUEST_URI']);
            $data = json_decode(file_get_contents("php://input"), true);
            
            if (!$studentId) {
                http_response_code(400);
                echo json_encode(["error" => "Student ID is required"]);
                exit();
            }
            
            $result = $patch->updateStudent($studentId, $data);
            
            if ($result) {
                echo json_encode([
                    "status" => "success",
                    "message" => "Student updated successfully",
                    "updatedFields" => $result
                ]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Failed to update student"]);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(["error" => "Method not allowed"]);
            break;
    }
} catch (Exception $e) {
    Logger::error("Student endpoint error", $e);
    http_response_code(500);
    echo json_encode([
        "error" => "Server error",
        "message" => $e->getMessage()
    ]);
}

