<?php

namespace App\Controllers;

use App\Models\Course;
use App\Config\Database;
use PDO;

class CourseController
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function index()
    {
        $query = "SELECT * FROM sk_courses";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return ['data' => $courses];
    }

    public function show($id)
    {
        $query = "SELECT * FROM sk_courses WHERE courseID = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $course = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$course) {
            throw new \Exception("Course not found");
        }
        return ['data' => $course];
    }

    public function store()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $query = "INSERT INTO sk_courses (courseID, courseName) VALUES (:courseID, :courseName)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':courseID', $data['courseID']);
        $stmt->bindParam(':courseName', $data['courseName']);
        
        if ($stmt->execute()) {
            return ['message' => 'Course created successfully', 'courseID' => $data['courseID']];
        } else {
            throw new \Exception("Failed to create course");
        }
    }

    public function update($id)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $query = "UPDATE sk_courses SET courseName = :courseName WHERE courseID = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':courseName', $data['courseName']);
        
        if ($stmt->execute()) {
            return ['message' => 'Course updated successfully'];
        } else {
            throw new \Exception("Failed to update course");
        }
    }

    public function destroy($id)
    {
        $query = "DELETE FROM sk_courses WHERE courseID = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        
        if ($stmt->execute()) {
            return ['message' => 'Course deleted successfully'];
        } else {
            throw new \Exception("Failed to delete course");
        }
    }
}

