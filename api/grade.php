<?php
require_once('../src/config/database.php');
require_once('../src/models/grade.php');

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE");
header("Access-Control-Allow-Headers: Content-Type");

try {
    $database = new Database();
    $db = $database->getConnection();
    $grade = new Grade($db);

    $method = $_SERVER['REQUEST_METHOD'];

    switch($method) {
        case 'GET':
            if(isset($_GET['studentID']) && isset($_GET['courseID'])) {
                $result = $grade->getGradesByStudent($_GET['studentID'], $_GET['courseID']);
                echo json_encode($result);
            } elseif(isset($_GET['assignmentID'])) {
                $result = $grade->getGradesByAssignment($_GET['assignmentID']);
                echo json_encode($result);
            } else {
                http_response_code(400);
                echo json_encode(["error" => "Missing required parameters"]);
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents("php://input"), true);
            
            if(!isset($data['studentID']) || !isset($data['assignmentID']) || !isset($data['score'])) {
                http_response_code(400);
                echo json_encode(["error" => "Missing required fields"]);
                exit();
            }

            $result = $grade->addGrade($data['studentID'], $data['assignmentID'], $data['score']);
            if($result) {
                http_response_code(201);
                echo json_encode(["success" => true, "message" => "Grade added successfully"]);
            } else {
                http_response_code(500);
                echo json_encode(["error" => "Failed to add grade"]);
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

