-- Student Management System Database Setup

-- Create database
CREATE DATABASE IF NOT EXISTS student_api;
USE student_api;

-- Admins Table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Students Table
CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phone VARCHAR(15),
    address TEXT,
    dob DATE,
    gender ENUM('Male', 'Female', 'Other'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Courses Table
CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20) UNIQUE NOT NULL,
    description TEXT,
    credits INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Enrollments Table
CREATE TABLE IF NOT EXISTS enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    course_id INT NOT NULL,
    enrollment_date DATE DEFAULT (CURRENT_DATE),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE,
    UNIQUE KEY unique_enrollment (student_id, course_id)
);

-- Insert sample admin (password: admin123)
INSERT INTO admins (name, email, password) VALUES
('Admin User', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert sample students
INSERT INTO students (first_name, last_name, email, phone, address, dob, gender) VALUES
('John', 'Doe', 'john.doe@example.com', '555-0101', '123 Main St', '2000-05-15', 'Male'),
('Jane', 'Smith', 'jane.smith@example.com', '555-0102', '456 Oak Ave', '1999-08-22', 'Female'),
('Mike', 'Johnson', 'mike.johnson@example.com', '555-0103', '789 Pine Rd', '2001-03-10', 'Male');

-- Insert sample courses
INSERT INTO courses (name, code, description, credits) VALUES
('Introduction to Computer Science', 'CS101', 'Fundamentals of programming and computer science', 3),
('Data Structures', 'CS201', 'Advanced data structures and algorithms', 4),
('Database Systems', 'CS301', 'Relational databases and SQL', 3),
('Web Development', 'CS202', 'HTML, CSS, JavaScript and modern frameworks', 3);

-- Insert sample enrollments
INSERT INTO enrollments (student_id, course_id, enrollment_date) VALUES
(1, 1, '2026-01-10'),
(1, 2, '2026-01-10'),
(2, 1, '2026-01-11'),
(2, 3, '2026-01-11'),
(3, 2, '2026-01-12'),
(3, 4, '2026-01-12');
