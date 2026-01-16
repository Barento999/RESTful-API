# MVC Architecture Documentation

## Project Structure

```
student-api/
├── models/              # Data layer - Database interactions
│   ├── Student.php
│   ├── Course.php
│   ├── Enrollment.php
│   └── Admin.php
├── controllers/         # Business logic layer
│   ├── StudentController.php
│   ├── CourseController.php
│   ├── EnrollmentController.php
│   └── AuthController.php
├── routes/             # Routing layer
│   └── api.php
├── api/                # API endpoints (route to MVC)
│   ├── students.php
│   ├── courses.php
│   ├── enrollments.php
│   └── login.php
├── config.php          # Configuration
├── db.php              # Database connection
├── auth.php            # Authentication middleware
└── jwt.php             # JWT utilities
```

## Architecture Overview

### Models (Data Layer)

Models handle all database operations and data validation logic.

**Student.php**

- `getAll($search, $page, $limit)` - Get all students with pagination
- `getById($id)` - Get single student
- `create($data)` - Create new student
- `update($id, $data)` - Update student
- `delete($id)` - Delete student
- `emailExists($email, $excludeId)` - Check email uniqueness

**Course.php**

- `getAll($search, $page, $limit)` - Get all courses with pagination
- `getById($id)` - Get single course
- `create($data)` - Create new course
- `update($id, $data)` - Update course
- `delete($id)` - Delete course
- `codeExists($code, $excludeId)` - Check code uniqueness

**Enrollment.php**

- `getByStudent($studentId)` - Get enrollments for a student
- `getByCourse($courseId)` - Get enrollments for a course
- `create($studentId, $courseId, $enrollmentDate)` - Create enrollment
- `delete($studentId, $courseId)` - Delete enrollment
- `exists($studentId, $courseId)` - Check if enrollment exists

**Admin.php**

- `getByEmail($email)` - Get admin by email
- `verifyPassword($hashedPassword, $password)` - Verify password

### Controllers (Business Logic Layer)

Controllers handle request processing, validation, and response formatting.

**StudentController.php**

- `index()` - List all students (GET /api/students)
- `show($id)` - Get single student (GET /api/students/{id})
- `store()` - Create student (POST /api/students)
- `update($id)` - Update student (PUT /api/students/{id})
- `destroy($id)` - Delete student (DELETE /api/students/{id})

**CourseController.php**

- `index()` - List all courses (GET /api/courses)
- `show($id)` - Get single course (GET /api/courses/{id})
- `store()` - Create course (POST /api/courses)
- `update($id)` - Update course (PUT /api/courses/{id})
- `destroy($id)` - Delete course (DELETE /api/courses/{id})

**EnrollmentController.php**

- `getByStudent($studentId)` - Get student enrollments (GET /api/enrollments/student/{id})
- `getByCourse($courseId)` - Get course enrollments (GET /api/enrollments/course/{id})
- `store()` - Create enrollment (POST /api/enrollments)
- `destroy()` - Delete enrollment (DELETE /api/enrollments)

**AuthController.php**

- `login()` - Authenticate admin (POST /api/login)

### Routes (Routing Layer)

The `routes/api.php` file handles all routing logic:

- Parses incoming requests
- Routes to appropriate controllers
- Handles authentication
- Manages CORS headers
- Error handling

## Request Flow

1. **Request arrives** at `/api/students` (or any endpoint)
2. **API file** (`api/students.php`) includes `routes/api.php`
3. **Router** (`routes/api.php`):
   - Handles CORS preflight
   - Checks authentication (except login)
   - Parses URL path
   - Routes to appropriate controller
4. **Controller** processes request:
   - Validates input
   - Calls model methods
   - Formats response
5. **Model** interacts with database
6. **Response** sent back to client

## Benefits of MVC Architecture

1. **Separation of Concerns**: Each layer has a specific responsibility
2. **Reusability**: Models and controllers can be reused
3. **Maintainability**: Easy to locate and fix bugs
4. **Testability**: Each component can be tested independently
5. **Scalability**: Easy to add new features
6. **Clean Code**: Better organization and readability

## API Endpoints

All endpoints remain the same as before:

### Authentication

- `POST /api/login` - Login (no auth required)

### Students

- `GET /api/students` - List students
- `GET /api/students/{id}` - Get student
- `POST /api/students` - Create student
- `PUT /api/students/{id}` - Update student
- `DELETE /api/students/{id}` - Delete student

### Courses

- `GET /api/courses` - List courses
- `GET /api/courses/{id}` - Get course
- `POST /api/courses` - Create course
- `PUT /api/courses/{id}` - Update course
- `DELETE /api/courses/{id}` - Delete course

### Enrollments

- `GET /api/enrollments/student/{id}` - Get student enrollments
- `GET /api/enrollments/course/{id}` - Get course enrollments
- `POST /api/enrollments` - Create enrollment
- `DELETE /api/enrollments` - Delete enrollment

## Example Usage

### Creating a New Endpoint

1. **Create Model** (`models/NewModel.php`):

```php
<?php
class NewModel {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function getAll() {
        // Database logic
    }
}
```

2. **Create Controller** (`controllers/NewController.php`):

```php
<?php
require_once __DIR__ . '/../models/NewModel.php';

class NewController {
    private $model;

    public function __construct($pdo) {
        $this->model = new NewModel($pdo);
    }

    public function index() {
        $data = $this->model->getAll();
        $this->jsonResponse(200, ['data' => $data]);
    }

    private function jsonResponse($code, $data) {
        http_response_code($code);
        echo json_encode($data);
    }
}
```

3. **Add Route** (in `routes/api.php`):

```php
elseif ($pathParts[0] === 'api' && $pathParts[1] === 'newresource') {
    $controller = new NewController($pdo);

    if ($method === 'GET') {
        $controller->index();
    }
}
```

4. **Create API File** (`api/newresource.php`):

```php
<?php
require_once __DIR__ . '/../routes/api.php';
```

## Migration Notes

The API functionality remains identical to the previous version. All existing endpoints work the same way, but now with better code organization and maintainability.
