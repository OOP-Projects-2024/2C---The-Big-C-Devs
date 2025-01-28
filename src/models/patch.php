<?php  
require_once __DIR__ . '/logger.php';  

class Patch {  
    private $conn;  

    public function __construct($db) {  
        $this->conn = $db;  
    }  

    // General update function to avoid code duplication  
    private function updateRecord($table, $idField, $idValue, $data) {  
        $updateFields = [];  
        $params = [":$idField" => $idValue];  

        foreach ($data as $field => $value) {  
            if (isset($value)) {  
                $updateFields[] = "$field = :$field";  
                $params[":$field"] = $value;  
            }  
        }  

        if (empty($updateFields)) {  
            return false;  // No fields to update  
        }  

        $query = "UPDATE $table SET " . implode(', ', $updateFields) . " WHERE $idField = :$idField";  
        
        try {  
            $stmt = $this->conn->prepare($query);  
            $result = $stmt->execute($params);  
            
            if ($result) {  
                Logger::logDatabaseChange('UPDATE', $table, $data, $idValue);  
            }  
            
            return $result;  
        } catch (PDOException $e) {  
            Logger::error("Failed to update $table", $e);  
            return false;  
        }  
    }  

    // Update methods using the general update function  
    public function updateStudent($studentID, $data) {  
        return $this->updateRecord('sk_students', 'studentID', $studentID, $data);  
    }  

    public function updateCourse($courseID, $data) {  
        return $this->updateRecord('sk_courses', 'courseID', $courseID, $data);  
    }  

    public function updateGrade($gradeID, $data) {  
        return $this->updateRecord('grades', 'gradeID', $gradeID, $data);  
    }  

    public function updateAssignment($assignmentID, $data) {  
        return $this->updateRecord('assignments', 'assignmentID', $assignmentID, $data);  
    }  

    public function updateAttendance($attendanceID, $data) {  
        return $this->updateRecord('attendance', 'attendanceID', $attendanceID, $data);  
    }  
}