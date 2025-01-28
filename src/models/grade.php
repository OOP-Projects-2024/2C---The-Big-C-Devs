<?php
class Grade {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function addGrade($studentID, $assignmentID, $score) {
        $query = 'INSERT INTO grades (studentID, assignmentID, score) 
                  VALUES (:studentID, :assignmentID, :score)
                  ON DUPLICATE KEY UPDATE score = :score';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':studentID', $studentID);
        $stmt->bindParam(':assignmentID', $assignmentID);
        $stmt->bindParam(':score', $score);

        return $stmt->execute();
    }

    public function getGradesByStudent($studentID, $courseID) {
        $query = 'SELECT a.title, g.score 
                  FROM grades g 
                  JOIN assignments a ON g.assignmentID = a.assignmentID 
                  WHERE g.studentID = :studentID AND a.courseID = :courseID';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':studentID', $studentID);
        $stmt->bindParam(':courseID', $courseID);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getGradesByAssignment($assignmentID) {
        $query = 'SELECT s.studentID, s.firstName, s.lastName, g.score 
                  FROM sk_students s 
                  LEFT JOIN grades g ON s.studentID = g.studentID 
                  WHERE g.assignmentID = :assignmentID';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':assignmentID', $assignmentID);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function calculateAverageGrade($studentID, $courseID) {
        $query = 'SELECT AVG(g.score) as average_grade 
                  FROM grades g 
                  JOIN assignments a ON g.assignmentID = a.assignmentID 
                  WHERE g.studentID = :studentID AND a.courseID = :courseID';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':studentID', $studentID);
        $stmt->bindParam(':courseID', $courseID);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['average_grade'];
    }
}

