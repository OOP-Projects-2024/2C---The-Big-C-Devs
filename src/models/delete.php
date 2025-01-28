<?php
class Delete {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function deleteStudent($studentID) {
        $query = 'DELETE FROM sk_students WHERE studentID = :studentID';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':studentID', $studentID);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    public function deleteCourse($courseID) {
        $query = 'DELETE FROM sk_courses WHERE courseID = :courseID';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':courseID', $courseID);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }
}

