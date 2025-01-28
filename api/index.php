<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$request_uri = $_SERVER['REQUEST_URI'];
$base_path = '/studentmanagementsystem/api/';
$path = substr(parse_url($request_uri, PHP_URL_PATH), strlen($base_path));
$path = trim($path, '/');

// Extract the endpoint and ID
$parts = explode('/', $path);
$endpoint = $parts[0];
$id = isset($parts[1]) ? $parts[1] : null;

// If an ID is present, add it to $_GET
if ($id) {
    $_GET['id'] = $id;
}

try {
    switch ($endpoint) {
        case 'students':
            require_once __DIR__ . '/endpoints/students.php';
            break;
            
        case 'courses':
            require_once __DIR__ . '/endpoints/courses.php';
            break;
            
        case 'grades':
            require_once __DIR__ . '/endpoints/grades.php';
            break;
            
        case 'assignments':
            require_once __DIR__ . '/endpoints/assignments.php';
            break;
            
        case 'attendance':
            require_once __DIR__ . '/endpoints/attendance.php';
            break;
            
        case '':
            echo json_encode([
                'status' => 'success',
                'message' => 'Student Management System API',
                'version' => '1.0',
                'endpoints' => [
                    '/students - Student management',
                    '/courses - Course management',
                    '/grades - Grade management',
                    '/assignments - Assignment management',
                    '/attendance - Attendance management'
                ]
            ]);
            break;
            
        default:
            http_response_code(404);
            echo json_encode([
                'status' => 'error',
                'message' => "Endpoint not found: $endpoint"
            ]);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}

