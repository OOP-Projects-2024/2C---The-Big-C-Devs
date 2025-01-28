<?php  
// File: C:\xampp\htdocs\studentmanagementsystem\src\models\Auth.php  

require_once __DIR__ . '/../config/Database.php';  
use \Firebase\JWT\JWT; // Ensure this is correctly included  

class Auth {  
    private $conn;  
    private $table = 'users';  
    private $secret_key = bin2hex(random_bytes(32)); // Replace this with your actual secret key  
    private $issuer = "http://localhost/studentmanagementsystem"; // URL of your server  
    private $audience = "http://localhost/studentmanagementsystem"; // Audience URL  

    public function __construct() {  
        $database = new Database();  
        $this->conn = $database->getConnection();  
    }  

    public function register($username, $password, $email) {  
        $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);  
        $query = "INSERT INTO " . $this->table . " (username, password, email) VALUES (:username, :password, :email)";  
        $stmt = $this->conn->prepare($query);  
        $stmt->bindParam(':username', $username);  
        $stmt->bindParam(':password', $hashedPassword);  
        $stmt->bindParam(':email', $email);  

        if ($stmt->execute()) {  
            return [  
                'success' => true,  
                'message' => 'User registered successfully.'  
            ];  
        } else {  
            return [  
                'success' => false,  
                'message' => 'User registration failed. Please try again.'  
            ];  
        }  
    }  

    public function login($username, $password) {  
        $query = "SELECT id, username, password FROM " . $this->table . " WHERE username = :username";  
        $stmt = $this->conn->prepare($query);  
        $stmt->bindParam(':username', $username);  
        $stmt->execute();  
        
        if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {  
            if (password_verify($password, $user['password'])) {  
                $token = $this->generateJWT($user['id'], $user['username']);  
                return [  
                    'success' => true,  
                    'token' => $token,  
                    'message' => 'Login successful.'  
                ];  
            }  
        }  

        return [  
            'success' => false,  
            'message' => 'Invalid username or password.'  
        ];  
    }  

    private function generateJWT($userId, $username) {  
        $payload = [  
            'iat' => time(),   
            'exp' => time() + (60 * 60), // 1 hour expiration  
            'iss' => $this->issuer,  
            'aud' => $this->audience,  
            'data' => [  
                'userId' => $userId,  
                'username' => $username  
            ]  
        ];  

        return JWT::encode($payload, $this->secret_key); // Encode token  
    }  

    public function isAuthenticated($token) {  
        try {  
            $decoded = JWT::decode($token, $this->secret_key, ['HS256']);  
            return (array)$decoded->data;   
        } catch (Exception $e) {  
            return false;   
        }  
    }  

    public function logout() {  
        return ['success' => true, 'message' => 'User logged out.'];  
    }  

    public function getCurrentUser($token) {  
        $userData = $this->isAuthenticated($token);  
        if ($userData) {  
            $query = "SELECT id, username, email FROM " . $this->table . " WHERE id = ?";  
            $stmt = $this->conn->prepare($query);  
            $stmt->execute([$userData['userId']]);  
            return $stmt->fetch(PDO::FETCH_ASSOC);  
        }  
        return null;  
    }  
}