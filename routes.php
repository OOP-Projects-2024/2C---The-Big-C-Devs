<?php
require_once 'add_student.php';
require_once 'add_course.php';
require_once 'delete_student.php';
require_once 'record_attendance.php';
require_once 'add_assignment.php';

class Router {
    private $routes = [];

    public function addRoute($method, $path, $handler) {
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'handler' => $handler
        ];
    }

    public function handleRequest($method, $path) {
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchPath($route['path'], $path)) {
                return call_user_func($route['handler']);
            }
        }
        $this->notFound();
    }

    private function matchPath($routePath, $requestPath) {
        $routeParts = explode('/', trim($routePath, '/'));
        $requestParts = explode('/', trim($requestPath, '/'));

        if (count($routeParts) !== count($requestParts)) {
            return false;
        }

        for ($i = 0; $i < count($routeParts); $i++) {
            if ($routeParts[$i] !== $requestParts[$i] && $routeParts[$i][0] !== ':') {
                return false;
            }
        }

        return true;
    }

    private function notFound() {
        header("HTTP/1.0 404 Not Found");
        echo "404 Not Found";
    }
}

$router = new Router();

// Add Student route
$router->addRoute('POST', '/add_student', function() {
    $studentAdder = new StudentAdder();
    return $studentAdder->addStudent();
});

// Add Course route
$router->addRoute('POST', '/add_course', function() {
    $courseAdder = new CourseAdder();
    return $courseAdder->addCourse();
});

// Delete Student route
$router->addRoute('POST', '/delete_student', function() {
    $studentDeleter = new StudentDeleter();
    return $studentDeleter->deleteStudent();
});

// Record Attendance route
$router->addRoute('POST', '/record_attendance', function() {
    $attendanceRecorder = new AttendanceRecorder();
    return $attendanceRecorder->recordAttendance();
});

// Add Assignment route
$router->addRoute('POST', '/add_assignment', function() {
    $assignmentAdder = new AssignmentAdder();
    return $assignmentAdder->addAssignment();
});

// Handle the request
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$router->handleRequest($method, $path);

