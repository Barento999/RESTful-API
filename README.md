# Student Management System - RESTful API

A complete RESTful API built with pure PHP and MySQL for managing students, courses, and enrollments with JWT authentication.

## Features

- JWT-based authentication
- Complete CRUD operations for Students, Courses, and Enrollments
- Search and filtering
- Pagination support
- CSV export functionality
- Secure password hashing
- SQL injection protection with prepared statements
- Proper HTTP status codes
- JSON responses

## Requirements

- PHP 7.4+
- MySQL 5.7+
- Apache with mod_rewrite enabled
- XAMPP (recommended for local development)

## Installation

### 1. Start XAMPP

- Start Apache and MySQL services

### 2. Create Database

- Open phpMyAdmin: `http://localhost/phpmyadmin`
- Import `setup.sql` file or run the SQL commands manually

### 3. Configure Database

- Edit `config.php` if your MySQL credentials differ from defaults
- Default: username=`root`, password=`` (empty)

### 4. Copy Files

- Place the project folder in `C:\xampp\htdocs\`

### 5. Test the API

- Open browser: `http://localhost/student-api/`

## Default Admin Credentials

```
Email: admin@example.com
Password: admin123
```

## API Endpoints

Base URL: `http://localhost/student-api`

### Authentication

#### Login

```
POST /login
Content-Type: application/json

{
  "email": "admin@example.com",
  "password": "admin123"
}

Response (200):
{
  "success": true,
  "token": "eyJ0eXAiOiJKV1QiLCJhbGc...",
  "admin": {
    "id": 1,
    "name": "Admin User",
    "email": "admin@example.com"
  }
}
```

**Note:** All endpoints below require authentication. Include the token in the Authorization header:

```
Authorization: Bearer <your-token>
```

### Students

#### Get All Students (with pagination)

```
GET /students?page=1&limit=10
Authorization: Bearer <token>

Response (200):
{
  "success": true,
  "data": [...],
  "pagination": {
    "total": 50,
    "page": 1,
    "limit": 10,
    "pages": 5
  }
}
```

#### Search Students

```
GET /students?search=john
Authorization: Bearer <token>
```

#### Export Students to CSV

```
GET /students?export=csv
Authorization: Bearer <token>
```

#### Get Single Student

```
GET /students/1
Authorization: Bearer <token>

Response (200):
{
  "success": true,
  "data": {
    "id": 1,
    "first_name": "John",
    "last_name": "Doe",
    "email": "john.doe@example.com",
    "phone": "555-0101",
    "address": "123 Main St",
    "dob": "2000-05-15",
    "gender": "Male",
    "created_at": "2026-01-13 10:00:00"
  }
}
```

#### Create Student

```
POST /students
Authorization: Bearer <token>
Content-Type: application/json

{
  "first_name": "John",
  "last_name": "Doe",
  "email": "john.doe@example.com",
  "phone": "555-0101",
  "address": "123 Main St",
  "dob": "2000-05-15",
  "gender": "Male"
}

Response (201):
{
  "success": true,
  "message": "Student created successfully",
  "data": {...}
}
```

#### Update Student

```
PUT /students/1
Authorization: Bearer <token>
Content-Type: application/json

{
  "first_name": "Jane",
  "last_name": "Doe",
  "email": "jane.doe@example.com",
  "phone": "555-0199",
  "address": "456 Oak Ave",
  "dob": "2000-05-15",
  "gender": "Female"
}

Response (200):
{
  "success": true,
  "message": "Student updated successfully",
  "data": {...}
}
```

#### Delete Student

```
DELETE /students/1
Authorization: Bearer <token>

Response (200):
{
  "success": true,
  "message": "Student deleted successfully"
}
```

### Courses

#### Get All Courses (with pagination)

```
GET /courses?page=1&limit=10
Authorization: Bearer <token>
```

#### Search Courses

```
GET /courses?search=computer
Authorization: Bearer <token>
```

#### Export Courses to CSV

```
GET /courses?export=csv
Authorization: Bearer <token>
```

#### Get Single Course

```
GET /courses/1
Authorization: Bearer <token>

Response (200):
{
  "success": true,
  "data": {
    "id": 1,
    "name": "Introduction to Computer Science",
    "code": "CS101",
    "description": "Fundamentals of programming",
    "credits": 3,
    "created_at": "2026-01-13 10:00:00"
  }
}
```

#### Create Course

```
POST /courses
Authorization: Bearer <token>
Content-Type: application/json

{
  "name": "Introduction to Computer Science",
  "code": "CS101",
  "description": "Fundamentals of programming",
  "credits": 3
}

Response (201):
{
  "success": true,
  "message": "Course created successfully",
  "data": {...}
}
```

#### Update Course

```
PUT /courses/1
Authorization: Bearer <token>
Content-Type: application/json

{
  "name": "Advanced Computer Science",
  "code": "CS101",
  "description": "Advanced programming concepts",
  "credits": 4
}

Response (200):
{
  "success": true,
  "message": "Course updated successfully",
  "data": {...}
}
```

#### Delete Course

```
DELETE /courses/1
Authorization: Bearer <token>

Response (200):
{
  "success": true,
  "message": "Course deleted successfully"
}
```

### Enrollments

#### Enroll Student in Course

```
POST /enrollments
Authorization: Bearer <token>
Content-Type: application/json

{
  "student_id": 1,
  "course_id": 1,
  "enrollment_date": "2026-01-13"
}

Response (201):
{
  "success": true,
  "message": "Enrollment created successfully",
  "data": {
    "id": 1,
    "student_id": 1,
    "course_id": 1,
    "enrollment_date": "2026-01-13"
  }
}
```

#### Get Student's Courses

```
GET /enrollments/student/1
Authorization: Bearer <token>

Response (200):
{
  "success": true,
  "student_id": 1,
  "data": [
    {
      "id": 1,
      "enrollment_date": "2026-01-13",
      "course_id": 1,
      "name": "Introduction to Computer Science",
      "code": "CS101",
      "description": "Fundamentals of programming",
      "credits": 3
    }
  ],
  "count": 1
}
```

#### Get Course's Students

```
GET /enrollments/course/1
Authorization: Bearer <token>

Response (200):
{
  "success": true,
  "course_id": 1,
  "data": [
    {
      "id": 1,
      "enrollment_date": "2026-01-13",
      "student_id": 1,
      "first_name": "John",
      "last_name": "Doe",
      "email": "john.doe@example.com",
      "phone": "555-0101"
    }
  ],
  "count": 1
}
```

#### Remove Enrollment

```
DELETE /enrollments
Authorization: Bearer <token>
Content-Type: application/json

{
  "student_id": 1,
  "course_id": 1
}

Response (200):
{
  "success": true,
  "message": "Enrollment deleted successfully"
}
```

## HTTP Status Codes

- `200 OK` - Successful GET, PUT, DELETE
- `201 Created` - Successful POST
- `400 Bad Request` - Invalid request data
- `401 Unauthorized` - Missing or invalid authentication
- `404 Not Found` - Resource not found
- `409 Conflict` - Duplicate entry (email, code, enrollment)
- `500 Internal Server Error` - Server error

## Testing with cURL

### Login

```bash
curl -X POST http://localhost/student-api/login ^
  -H "Content-Type: application/json" ^
  -d "{\"email\":\"admin@example.com\",\"password\":\"admin123\"}"
```

### Get All Students (with token)

```bash
curl http://localhost/student-api/students ^
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Create Student

```bash
curl -X POST http://localhost/student-api/students ^
  -H "Content-Type: application/json" ^
  -H "Authorization: Bearer YOUR_TOKEN_HERE" ^
  -d "{\"first_name\":\"John\",\"last_name\":\"Doe\",\"email\":\"john@example.com\",\"phone\":\"555-0101\",\"address\":\"123 Main St\",\"dob\":\"2000-05-15\",\"gender\":\"Male\"}"
```

### Search Students

```bash
curl "http://localhost/student-api/students?search=john" ^
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

### Export Students to CSV

```bash
curl "http://localhost/student-api/students?export=csv" ^
  -H "Authorization: Bearer YOUR_TOKEN_HERE" ^
  -o students.csv
```

## Security Features

- JWT token-based authentication
- Password hashing with `password_hash()`
- Prepared statements to prevent SQL injection
- Input validation and sanitization
- Email format validation
- Duplicate prevention (unique constraints)
- Token expiration (1 hour)
- CORS headers for cross-origin requests

## Project Structure

```
student-api/
├── api/
│   ├── login.php          # Authentication endpoint
│   ├── students.php       # Students CRUD operations
│   ├── courses.php        # Courses CRUD operations
│   └── enrollments.php    # Enrollments management
├── config.php             # Configuration settings
├── db.php                 # Database connection
├── jwt.php                # JWT encoding/decoding
├── auth.php               # Authentication middleware
├── index.php              # API router
├── .htaccess              # URL rewriting
├── setup.sql              # Database schema
└── README.md              # Documentation
```

## Database Schema

### Students Table

- id (INT, PRIMARY KEY, AUTO_INCREMENT)
- first_name (VARCHAR 50)
- last_name (VARCHAR 50)
- email (VARCHAR 100, UNIQUE)
- phone (VARCHAR 15)
- address (TEXT)
- dob (DATE)
- gender (ENUM: Male, Female, Other)
- created_at (TIMESTAMP)

### Courses Table

- id (INT, PRIMARY KEY, AUTO_INCREMENT)
- name (VARCHAR 100)
- code (VARCHAR 20, UNIQUE)
- description (TEXT)
- credits (INT)
- created_at (TIMESTAMP)

### Enrollments Table

- id (INT, PRIMARY KEY, AUTO_INCREMENT)
- student_id (INT, FOREIGN KEY)
- course_id (INT, FOREIGN KEY)
- enrollment_date (DATE)
- UNIQUE constraint on (student_id, course_id)

### Admins Table

- id (INT, PRIMARY KEY, AUTO_INCREMENT)
- name (VARCHAR 100)
- email (VARCHAR 100, UNIQUE)
- password (VARCHAR 255)
- created_at (TIMESTAMP)

## Configuration

Edit `config.php` to customize:

- JWT secret key
- JWT expiration time
- Database credentials
- Pagination settings

## Portfolio Highlights

This project demonstrates:

- RESTful API design principles
- JWT authentication implementation
- Pure PHP without frameworks
- MySQL with PDO
- Security best practices
- Clean code architecture
- Proper error handling
- Database relationships (foreign keys)
- Pagination and filtering
- CSV export functionality
- Professional documentation

## License

Free to use for personal and portfolio projects.
#   R E S T f u l - A P I  
 