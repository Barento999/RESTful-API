<?php

class Enrollment {
    private $pdo;
    
    public function __construct($pdo) {
        $this->pdo = $pdo;
    }
    
    public function getByStudent($studentId) {
        $stmt = $this->pdo->prepare("
            SELECT e.id, e.enrollment_date, c.id as course_id, c.name, c.code, c.description, c.credits
            FROM enrollments e
            JOIN courses c ON e.course_id = c.id
            WHERE e.student_id = :student_id
            ORDER BY e.enrollment_date DESC
        ");
        $stmt->execute(['student_id' => $studentId]);
        return $stmt->fetchAll();
    }
    
    public function getByCourse($courseId) {
        $stmt = $this->pdo->prepare("
            SELECT e.id, e.enrollment_date, s.id as student_id, s.first_name, s.last_name, s.email, s.phone
            FROM enrollments e
            JOIN students s ON e.student_id = s.id
            WHERE e.course_id = :course_id
            ORDER BY e.enrollment_date DESC
        ");
        $stmt->execute(['course_id' => $courseId]);
        return $stmt->fetchAll();
    }
    
    public function create($studentId, $courseId, $enrollmentDate = null) {
        $enrollmentDate = $enrollmentDate ?? date('Y-m-d');
        
        $stmt = $this->pdo->prepare("
            INSERT INTO enrollments (student_id, course_id, enrollment_date) 
            VALUES (:student_id, :course_id, :enrollment_date)
        ");
        
        $stmt->execute([
            'student_id' => $studentId,
            'course_id' => $courseId,
            'enrollment_date' => $enrollmentDate
        ]);
        
        return [
            'id' => $this->pdo->lastInsertId(),
            'student_id' => $studentId,
            'course_id' => $courseId,
            'enrollment_date' => $enrollmentDate
        ];
    }
    
    public function delete($studentId, $courseId) {
        $stmt = $this->pdo->prepare("DELETE FROM enrollments WHERE student_id = :student_id AND course_id = :course_id");
        return $stmt->execute(['student_id' => $studentId, 'course_id' => $courseId]);
    }
    
    public function exists($studentId, $courseId) {
        $stmt = $this->pdo->prepare("SELECT id FROM enrollments WHERE student_id = :student_id AND course_id = :course_id");
        $stmt->execute(['student_id' => $studentId, 'course_id' => $courseId]);
        return $stmt->fetch() !== false;
    }
}
