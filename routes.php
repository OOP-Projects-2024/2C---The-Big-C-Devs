<?php

use src\models\Course;
use src\models\Student;
use src\models\Assignment;
use src\models\Attendance;
use src\models\Grade;
use src\models\Auth;
use src\models\Log;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, PATCH, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Include the necessary files
require_once 'src/config/Database.php';
require_once 'src/models/Course.php';
require_once 'src/models/Student.php';
require_once 'src/models/Assignment.php';
require_once 'src/models/Attendance.php';
require_once 'src/models/Grade.php';
require_once 'src/models/Auth.php';
require_once 'src/models/Logger.php';
require_once 'src/Patch.php';

// Initialize database connection
$db = new Database();
$conn = $db->getConnection();

$method = $_SERVER['REQUEST_METHOD'];
$entity = $_GET['entity'] ?? '';

// Helper function to get request data
function getRequestData() {
    return json_decode(file_get_contents("php://input"), true);
}

// Helper function to send JSON response
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

// Route requests based on entity type
switch ($entity) {
    case 'courses':
        handleCourses($method, $conn);
        break;
    case 'students':
        handleStudents($method, $conn);
        break;
    case 'assignments':
        handleAssignments($method, $conn);
        break;
    case 'attendance':
        handleAttendance($method, $conn);
        break;
    case 'grades':
        handleGrades($method, $conn);
        break;
    case 'auth':
        handleUsers($method, $conn);
        break;
    case 'logs':
        handleLogs($method, $conn);
        break;
    default:
        sendJsonResponse(['error' => 'Invalid entity'], 400);
}

// Handle Courses (CRUD)
function handleCourses($method, $conn) {
    $course = new Course();
    $patch = new Patch($conn);
    $data = getRequestData();

    switch ($method) {
        case 'POST':
            $result = $course->create($data['courseID'], $data['courseName']);
            sendJsonResponse($result, 201);
            break;
        case 'GET':
            $id = $_GET['id'] ?? null;
            $result = $id ? $course->getById($id) : $course->getAll();
            sendJsonResponse($result);
            break;
        case 'PUT':
            if (!isset($_GET['id'])) {
                sendJsonResponse(['error' => 'Missing ID'], 400);
            }
            $result = $course->update($_GET['id'], $data['courseName']);
            sendJsonResponse($result);
            break;
        case 'PATCH':
            if (!isset($_GET['id'])) {
                sendJsonResponse(['error' => 'Missing ID'], 400);
            }
            $result = $patch->updateCourse($_GET['id'], $data);
            sendJsonResponse($result);
            break;
        case 'DELETE':
            if (!isset($_GET['id'])) {
                sendJsonResponse(['error' => 'Missing ID'], 400);
            }
            $result = $course->delete($_GET['id']);
            sendJsonResponse($result);
            break;
        default:
            sendJsonResponse(['error' => 'Method Not Allowed'], 405);
    }
}

// Handle Students (CRUD)
function handleStudents($method, $conn) {
    $student = new Student();
    $patch = new Patch($conn);
    $data = getRequestData();

    switch ($method) {
        case 'POST':
            $result = $student->create($data['courseID'], $data['firstName'], $data['lastName'], $data['email']);
            sendJsonResponse($result, 201);
            break;
        case 'GET':
            $id = $_GET['id'] ?? null;
            $result = $id ? $student->getById($id) : $student->getAll();
            sendJsonResponse($result);
            break;
        case 'PUT':
            if (!isset($_GET['id'])) {
                sendJsonResponse(['error' => 'Missing ID'], 400);
            }
            $result = $student->update($_GET['id'], $data['courseID'], $data['firstName'], $data['lastName'], $data['email']);
            sendJsonResponse($result);
            break;
        case 'PATCH':
            if (!isset($_GET['id'])) {
                sendJsonResponse(['error' => 'Missing ID'], 400);
            }
            $result = $patch->updateStudent($_GET['id'], $data);
            sendJsonResponse($result);
            break;
        case 'DELETE':
            if (!isset($_GET['id'])) {
                sendJsonResponse(['error' => 'Missing ID'], 400);
            }
            $result = $student->delete($_GET['id']);
            sendJsonResponse($result);
            break;
        default:
            sendJsonResponse(['error' => 'Method Not Allowed'], 405);
    }
}

// Handle Assignments (CRUD)
function handleAssignments($method, $conn) {
    $assignment = new Assignment();
    $patch = new Patch($conn);
    $data = getRequestData();

    switch ($method) {
        case 'POST':
            $result = $assignment->create($data['courseID'], $data['title'], $data['description'], $data['dueDate']);
            sendJsonResponse($result, 201);
            break;
        case 'GET':
            $id = $_GET['id'] ?? null;
            $result = $id ? $assignment->getById($id) : $assignment->getAll();
            sendJsonResponse($result);
            break;
        case 'PUT':
            if (!isset($_GET['id'])) {
                sendJsonResponse(['error' => 'Missing ID'], 400);
            }
            $result = $assignment->update($_GET['id'], $data['title'], $data['description'], $data['dueDate']);
            sendJsonResponse($result);
            break;
        case 'PATCH':
            if (!isset($_GET['id'])) {
                sendJsonResponse(['error' => 'Missing ID'], 400);
            }
            $result = $patch->updateAssignment($_GET['id'], $data);
            sendJsonResponse($result);
            break;
        case 'DELETE':
            if (!isset($_GET['id'])) {
                sendJsonResponse(['error' => 'Missing ID'], 400);
            }
            $result = $assignment->delete($_GET['id']);
            sendJsonResponse($result);
            break;
        default:
            sendJsonResponse(['error' => 'Method Not Allowed'], 405);
    }
}

// Handle Attendance (CRUD)
function handleAttendance($method, $conn) {
    $attendance = new Attendance();
    $patch = new Patch($conn);
    $data = getRequestData();

    switch ($method) {
        case 'POST':
            $result = $attendance->create($data['studentID'], $data['courseID'], $data['date'], $data['status']);
            sendJsonResponse($result, 201);
            break;
        case 'GET':
            $id = $_GET['id'] ?? null;
            $result = $id ? $attendance->getById($id) : $attendance->getAll();
            sendJsonResponse($result);
            break;
        case 'PUT':
            if (!isset($_GET['id'])) {
                sendJsonResponse(['error' => 'Missing ID'], 400);
            }
            $result = $attendance->update($_GET['id'], $data['status']);
            sendJsonResponse($result);
            break;
        case 'PATCH':
            if (!isset($_GET['id'])) {
                sendJsonResponse(['error' => 'Missing ID'], 400);
            }
            $result = $patch->updateAttendance($_GET['id'], $data);
            sendJsonResponse($result);
            break;
        case 'DELETE':
            if (!isset($_GET['id'])) {
                sendJsonResponse(['error' => 'Missing ID'], 400);
            }
            $result = $attendance->delete($_GET['id']);
            sendJsonResponse($result);
            break;
        default:
            sendJsonResponse(['error' => 'Method Not Allowed'], 405);
    }
}

// Handle Grades (CRUD)
function handleGrades($method, $conn) {
    $grade = new Grade();
    $patch = new Patch($conn);
    $data = getRequestData();

    switch ($method) {
        case 'POST':
            $result = $grade->create($data['studentID'], $data['assignmentID'], $data['score']);
            sendJsonResponse($result, 201);
            break;
        case 'GET':
            $id = $_GET['id'] ?? null;
            $result = $id ? $grade->getById($id) : $grade->getAll();
            sendJsonResponse($result);
            break;
        case 'PUT':
            if (!isset($_GET['id'])) {
                sendJsonResponse(['error' => 'Missing ID'], 400);
            }
            $result = $grade->update($_GET['id'], $data['score']);
            sendJsonResponse($result);
            break;
        case 'PATCH':
            if (!isset($_GET['id'])) {
                sendJsonResponse(['error' => 'Missing ID'], 400);
            }
            $result = $patch->updateGrade($_GET['id'], $data);
            sendJsonResponse($result);
            break;
        case 'DELETE':
            if (!isset($_GET['id'])) {
                sendJsonResponse(['error' => 'Missing ID'], 400);
            }
            $result = $grade->delete($_GET['id']);
            sendJsonResponse($result);
            break;
        default:
            sendJsonResponse(['error' => 'Method Not Allowed'], 405);
    }
}

// Handle Users (CRUD)
function handleUsers($method, $conn) {
    $user = new Auth($conn);
    $data = getRequestData();

    switch ($method) {
        case 'POST':
            $result = $user->create($data['username'], $data['email'], $data['password'], $data['role']);
            sendJsonResponse($result, 201);
            break;
        case 'GET':
            $id = $_GET['id'] ?? null;
            $result = $id ? $user->getById($id) : $user->getAll();
            sendJsonResponse($result);
            break;
        case 'PUT':
            if (!isset($_GET['id'])) {
                sendJsonResponse(['error' => 'Missing ID'], 400);
            }
            $result = $user->update($_GET['id'], $data['username'], $data['email'], $data['password'], $data['role']);
            sendJsonResponse($result);
            break;
        case 'DELETE':
            if (!isset($_GET['id'])) {
                sendJsonResponse(['error' => 'Missing ID'], 400);
            }
            $result = $user->delete($_GET['id']);
            sendJsonResponse($result);
            break;
        default:
            sendJsonResponse(['error' => 'Method Not Allowed'], 405);
    }
}

// Handle Logs (CRUD)
function handleLogs($method, $conn) {
    $log = new Log($conn);
    $data = getRequestData();

    switch ($method) {
        case 'POST':
            $result = $log->create($data['user_id'], $data['action'], $data['entity_type'], $data['entity_id']);
            sendJsonResponse($result, 201);
            break;
        case 'GET':
            $id = $_GET['id'] ?? null;
            $result = $id ? $log->getById($id) : $log->getAll();
            sendJsonResponse($result);
            break;
        default:
            sendJsonResponse(['error' => 'Method Not Allowed'], 405);
    }
}

