<?php
require_once __DIR__ . '/../models/Enrollment.php';
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/Course.php';

class EnrollmentController {
    private $model;
    private $studentModel;
    private $courseModel;
    
    public function __construct($pdo) {
        $this->model = new Enrollment($pdo);
        $this->studentModel = new Student($pdo);
        $this->courseModel = new Course($pdo);
    }
    
    public function getByStudent($studentId) {
        if (!$this->studentModel->getById($studentId)) {
            $this->jsonResponse(404, ['error' => 'Student not found']);
            return;
        }
        
        $enrollments = $this->model->getByStudent($studentId);
        
        $this->jsonResponse(200, [
            'success' => true,
            'student_id' => $studentId,
            'data' => $enrollments,
            'count' => count($enrollments)
        ]);
    }
    
    public function getByCourse($courseId) {
        if (!$this->courseModel->getById($courseId)) {
            $this->jsonResponse(404, ['error' => 'Course not found']);
            return;
        }
        
        $enrollments = $this->model->getByCourse($courseId);
        
        $this->jsonResponse(200, [
            'success' => true,
            'course_id' => $courseId,
            'data' => $enrollments,
            'count' => count($enrollments)
        ]);
    }
    
    public function store() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['student_id']) || !isset($input['course_id'])) {
            $this->jsonResponse(400, ['error' => 'Student ID and Course ID are required']);
            return;
        }
        
        $studentId = (int)$input['student_id'];
        $courseId = (int)$input['course_id'];
        
        if (!$this->studentModel->getById($studentId)) {
            $this->jsonResponse(404, ['error' => 'Student not found']);
            return;
        }
        
        if (!$this->courseModel->getById($courseId)) {
            $this->jsonResponse(404, ['error' => 'Course not found']);
            return;
        }
        
        if ($this->model->exists($studentId, $courseId)) {
            $this->jsonResponse(409, ['error' => 'Student is already enrolled in this course']);
            return;
        }
        
        $enrollmentDate = $input['enrollment_date'] ?? null;
        $enrollment = $this->model->create($studentId, $courseId, $enrollmentDate);
        
        $this->jsonResponse(201, [
            'success' => true,
            'message' => 'Enrollment created successfully',
            'data' => $enrollment
        ]);
    }
    
    public function destroy() {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($input['student_id']) || !isset($input['course_id'])) {
            $this->jsonResponse(400, ['error' => 'Student ID and Course ID are required']);
            return;
        }
        
        $studentId = (int)$input['student_id'];
        $courseId = (int)$input['course_id'];
        
        if (!$this->model->exists($studentId, $courseId)) {
            $this->jsonResponse(404, ['error' => 'Enrollment not found']);
            return;
        }
        
        $this->model->delete($studentId, $courseId);
        
        $this->jsonResponse(200, [
            'success' => true,
            'message' => 'Enrollment deleted successfully'
        ]);
    }
    
    private function jsonResponse($code, $data) {
        http_response_code($code);
        echo json_encode($data);
    }
}
