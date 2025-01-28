<?php
require_once('../src/config/database.php');
require_once('../src/models/get.php');
require_once('../src/models/post.php');
require_once('../src/models/patch.php');
require_once('../src/models/delete.php');

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

try {
    $database = new Database();
    $db = $database->getConnection();
    $get = new Get($db);
    $post = new Post($db);
    $patch = new Patch($db);
    $delete = new Delete($db);

    $method = $_SERVER['REQUEST_METHOD'];

    switch($method) {
        case 'GET':
            if(isset($_GET['courseID'])) {
                $result = $get->getCourseById($_GET['courseID']);
            } else {
                $result = $get->getAllCourses();
            }
            echo json_encode($result);
            break;

        case 'POST':
            $data = json_decode(file_get_contents("php://input"), true);
            
            if(!isset($data['courseID']) || !isset($data['courseName'])) {
                http_response_code(400);
                echo json_encode(["error" => "Missing required fields"]);
                exit();
            }

            $result = $post->addCourse($data['courseID'], $data['courseName']);
            if($result) {
                http_response_code(201);
                echo json_encode(["success" => true, "message" => "Course added successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Failed to add course"]);
            }
            break;

        case 'PATCH':
            $data = json_decode(file_get_contents("php://input"), true);
            
            if(!isset($_GET['courseID']) || !isset($data['courseName'])) {
                http_response_code(400);
                echo json_encode(["error" => "Missing required fields"]);
                exit();
            }

            $result = $patch->updateCourse($_GET['courseID'], $data['courseName']);
            if($result) {
                echo json_encode(["success" => true, "message" => "Course updated successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Failed to update course"]);
            }
            break;

        case 'DELETE':
            if(!isset($_GET['courseID'])) {
                http_response_code(400);
                echo json_encode(["error" => "Course ID is required"]);
                exit();
            }

            $result = $delete->deleteCourse($_GET['courseID']);
            if($result) {
                echo json_encode(["success" => true, "message" => "Course deleted successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Failed to delete course"]);
            }
            break;

        default:
            http_response_code(405);
            echo json_encode(["error" => "Method not allowed"]);
            break;
    }
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}

