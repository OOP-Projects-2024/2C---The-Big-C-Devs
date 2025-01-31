<?php

namespace App\Controllers;

use App\Models\Attendance;
use App\Config\Database;
use PDO;

class AttendanceController
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function index()
    {
        $query = "SELECT * FROM attendance";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $attendanceRecords = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return ['data' => $attendanceRecords];
    }

    public function store()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $query = "INSERT INTO attendance (studentID, courseID, date, status) VALUES (:studentID, :courseID, :date, :status)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':studentID', $data['studentID']);
        $stmt->bindParam(':courseID', $data['courseID']);
        $stmt->bindParam(':date', $data['date']);
        $stmt->bindParam(':status', $data['status']);
        
        if ($stmt->execute()) {
            return ['message' => 'Attendance recorded successfully', 'attendanceID' => $this->db->lastInsertId()];
        } else {
            throw new \Exception("Failed to record attendance");
        }
    }

    public function update($id)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $query = "UPDATE attendance SET status = :status WHERE attendanceID = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':status', $data['status']);
        
        if ($stmt->execute()) {
            return ['message' => 'Attendance updated successfully'];
        } else {
            throw new \Exception("Failed to update attendance");
        }
    }
}

