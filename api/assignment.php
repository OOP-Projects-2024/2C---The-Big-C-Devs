<?php
class Assignment {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createAssignment($courseID, $title, $description, $dueDate) {
        $query = 'INSERT INTO assignments (courseID, title, description, dueDate) 
                  VALUES (:courseID, :title, :description, :dueDate)';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':courseID', $courseID);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':dueDate', $dueDate);

        if($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    public function getAssignmentsByCourse($courseID) {
        $query = 'SELECT * FROM assignments WHERE courseID = :courseID ORDER BY dueDate';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':courseID', $courseID);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateAssignment($assignmentID, $title, $description, $dueDate) {
        $query = 'UPDATE assignments 
                  SET title = :title, description = :description, dueDate = :dueDate 
                  WHERE assignmentID = :assignmentID';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':assignmentID', $assignmentID);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':dueDate', $dueDate);

        return $stmt->execute();
    }

    public function deleteAssignment($assignmentID) {
        $query = 'DELETE FROM assignments WHERE assignmentID = :assignmentID';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':assignmentID', $assignmentID);

        return $stmt->execute();
    }
}

