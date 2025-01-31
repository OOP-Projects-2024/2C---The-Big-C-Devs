<?php

namespace App\Controllers;

use App\Models\Student;

class StudentController
{
    public function index()
    {
        $students = Student::all();
        return ['data' => $students];
    }

    public function show($id)
    {
        $student = Student::find($id);
        if (!$student) {
            throw new \Exception("Student not found");
        }
        return ['data' => $student];
    }

    public function store()
    {
        $data = $_POST;
        $student = Student::create($data);
        return ['data' => $student, 'message' => 'Student created successfully'];
    }

    public function update($id)
    {
        $student = Student::find($id);
        if (!$student) {
            throw new \Exception("Student not found");
        }
        $data = json_decode(file_get_contents('php://input'), true);
        $student->update($data);
        return ['data' => $student, 'message' => 'Student updated successfully'];
    }

    public function destroy($id)
    {
        $student = Student::find($id);
        if (!$student) {
            throw new \Exception("Student not found");
        }
        $student->delete();
        return ['message' => 'Student deleted successfully'];
    }
}

