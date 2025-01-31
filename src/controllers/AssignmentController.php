<?php

namespace App\Controllers;

use App\Models\Assignment;
use App\Config\Database;
use PDO;

class AssignmentController
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function index()
    {
        $query = "SELECT * FROM assignments";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return ['data' => $assignments];
    }

    public function show($id)
    {
        $query = "SELECT * FROM assignments WHERE assignmentID = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $assignment = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$assignment) {
            throw new \Exception("Assignment not found");
        }
        return ['data' => $assignment];
    }

    public function store()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $query = "INSERT INTO assignments (courseID, title, description, dueDate) VALUES (:courseID, :title, :description, :dueDate)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':courseID', $data['courseID']);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':dueDate', $data['dueDate']);
        
        if ($stmt->execute()) {
            return ['message' => 'Assignment created successfully', 'assignmentID' => $this->db->lastInsertId()];
        } else {
            throw new \Exception("Failed to create assignment");
        }
    }

    public function update($id)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $query = "UPDATE assignments SET title = :title, description = :description, dueDate = :dueDate WHERE assignmentID = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':title', $data['title']);
        $stmt->bindParam(':description', $data['description']);
        $stmt->bindParam(':dueDate', $data['dueDate']);
        
        if ($stmt->execute()) {
            return ['message' => 'Assignment updated successfully'];
        } else {
            throw new \Exception("Failed to update assignment");
        }
    }

    public function destroy($id)
    {
        $query = "DELETE FROM assignments WHERE assignmentID = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            return ['message' => 'Assignment deleted successfully'];
        } else {
            throw new \Exception("Failed to delete assignment");
        }
    }
}

