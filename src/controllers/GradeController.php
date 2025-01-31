<?php

namespace App\Controllers;

use App\Models\Grade;
use App\Config\Database;
use PDO;

class GradeController
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function index()
    {
        $query = "SELECT * FROM grades";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $grades = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return ['data' => $grades];
    }

    public function show($id)
    {
        $query = "SELECT * FROM grades WHERE gradeID = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $grade = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$grade) {
            throw new \Exception("Grade not found");
        }
        return ['data' => $grade];
    }

    public function store()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $query = "INSERT INTO grades (studentID, assignmentID, score) VALUES (:studentID, :assignmentID, :score)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':studentID', $data['studentID']);
        $stmt->bindParam(':assignmentID', $data['assignmentID']);
        $stmt->bindParam(':score', $data['score']);
        
        if ($stmt->execute()) {
            return ['message' => 'Grade recorded successfully', 'gradeID' => $this->db->lastInsertId()];
        } else {
            throw new \Exception("Failed to record grade");
        }
    }

    public function update($id)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $query = "UPDATE grades SET score = :score WHERE gradeID = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':score', $data['score']);
        
        if ($stmt->execute()) {
            return ['message' => 'Grade updated successfully'];
        } else {
            throw new \Exception("Failed to update grade");
        }
    }
}

