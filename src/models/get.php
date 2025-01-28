<?php
class Get {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllCourses() {
        $query = 'SELECT * FROM sk_courses ORDER BY courseID';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStudentsByCourse($courseID) {
        $query = 'SELECT * FROM sk_students WHERE courseID = :courseID';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':courseID', $courseID);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCourseById($courseID) {
        $query = 'SELECT * FROM sk_courses WHERE courseID = :courseID';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':courseID', $courseID);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getStudentById($studentID) {
        $query = 'SELECT * FROM sk_students WHERE studentID = :studentID';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':studentID', $studentID);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllStudents() {
        $query = 'SELECT * FROM sk_students ORDER BY lastName, firstName';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

