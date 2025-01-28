<?php
require_once __DIR__ . '/logger.php';

class Post {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addStudent($courseID, $firstName, $lastName, $email) {
        try {
            $query = "INSERT INTO sk_students (courseID, firstName, lastName, email) 
                     VALUES (:courseID, :firstName, :lastName, :email)";
            
            $stmt = $this->conn->prepare($query);
            
            // Bind parameters
            $stmt->bindParam(':courseID', $courseID);
            $stmt->bindParam(':firstName', $firstName);
            $stmt->bindParam(':lastName', $lastName);
            $stmt->bindParam(':email', $email);
            
            // Execute query
            if ($stmt->execute()) {
                // Log the successful insertion
                Logger::logDatabaseChange(
                    'INSERT',
                    'sk_students',
                    [
                        'courseID' => $courseID,
                        'firstName' => $firstName,
                        'lastName' => $lastName,
                        'email' => $email,
                        'studentID' => $this->conn->lastInsertId()
                    ]
                );
                return true;
            }
            
            return false;
        } catch (PDOException $e) {
            Logger::error("Failed to add student", $e);
            throw new Exception("Database error occurred");
        }
    }

    public function addCourse($courseID, $courseName) {
        try {
            $query = "INSERT INTO sk_courses (courseID, courseName) 
                     VALUES (:courseID, :courseName)";
            
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':courseID', $courseID);
            $stmt->bindParam(':courseName', $courseName);
            
            if ($stmt->execute()) {
                // Log the successful insertion
                Logger::logDatabaseChange(
                    'INSERT',
                    'sk_courses',
                    [
                        'courseID' => $courseID,
                        'courseName' => $courseName
                    ]
                );
                return true;
            }
            
            return false;
        } catch (PDOException $e) {
            Logger::error("Failed to add course", $e);
            throw new Exception("Database error occurred");
        }
    }
}

