<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Controllers\StudentController;
use App\Controllers\CourseController;
use App\Controllers\AssignmentController;
use App\Controllers\AttendanceController;
use App\Controllers\GradeController;

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
        $path = parse_url($path, PHP_URL_PATH);
        
        foreach ($this->routes as $route) {
            if ($route['method'] === $method && $this->matchPath($route['path'], $path)) {
                try {
                    $response = call_user_func($route['handler']);
                    $this->sendResponse($response);
                    return;
                } catch (Exception $e) {
                    $this->handleError($e);
                    return;
                }
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

        $params = [];
        for ($i = 0; $i < count($routeParts); $i++) {
            if ($routeParts[$i][0] === ':') {
                $params[substr($routeParts[$i], 1)] = $requestParts[$i];
            } elseif ($routeParts[$i] !== $requestParts[$i]) {
                return false;
            }
        }

        $_REQUEST = array_merge($_REQUEST, $params);
        return true;
    }

    private function sendResponse($response) {
        header('Content-Type: application/json');
        echo json_encode($response);
    }

    private function notFound() {
        header("HTTP/1.0 404 Not Found");
        echo json_encode(['error' => '404 Not Found']);
    }

    private function handleError($e) {
        header("HTTP/1.0 500 Internal Server Error");
        echo json_encode(['error' => $e->getMessage()]);
    }
}

$router = new Router();

// Student routes
$router->addRoute('GET', '/api/students', [StudentController::class, 'index']);
$router->addRoute('GET', '/api/students/:id', [StudentController::class, 'show']);
$router->addRoute('POST', '/api/students', [StudentController::class, 'store']);
$router->addRoute('PATCH', '/api/students/:id', [StudentController::class, 'update']);
$router->addRoute('DELETE', '/api/students/:id', [StudentController::class, 'destroy']);

// Course routes
$router->addRoute('GET', '/api/courses', [CourseController::class, 'index']);
$router->addRoute('GET', '/api/courses/:id', [CourseController::class, 'show']);
$router->addRoute('POST', '/api/courses', [CourseController::class, 'store']);
$router->addRoute('PATCH', '/api/courses/:id', [CourseController::class, 'update']);
$router->addRoute('DELETE', '/api/courses/:id', [CourseController::class, 'destroy']);

// Assignment routes
$router->addRoute('GET', '/api/assignments', [AssignmentController::class, 'index']);
$router->addRoute('GET', '/api/assignments/:id', [AssignmentController::class, 'show']);
$router->addRoute('POST', '/api/assignments', [AssignmentController::class, 'store']);
$router->addRoute('PATCH', '/api/assignments/:id', [AssignmentController::class, 'update']);
$router->addRoute('DELETE', '/api/assignments/:id', [AssignmentController::class, 'destroy']);

// Attendance routes
$router->addRoute('GET', '/api/attendance', [AttendanceController::class, 'index']);
$router->addRoute('POST', '/api/attendance', [AttendanceController::class, 'store']);
$router->addRoute('PATCH', '/api/attendance/:id', [AttendanceController::class, 'update']);

// Grade routes
$router->addRoute('GET', '/api/grades', [GradeController::class, 'index']);
$router->addRoute('GET', '/api/grades/:id', [GradeController::class, 'show']);
$router->addRoute('POST', '/api/grades', [GradeController::class, 'store']);
$router->addRoute('PATCH', '/api/grades/:id', [GradeController::class, 'update']);

// Handle the request
$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];
$router->handleRequest($method, $path);

