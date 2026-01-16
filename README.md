<div align="center">

# 🎓 Student Management System

### RESTful API with JWT Authentication

[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-5.7%2B-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)](LICENSE)
[![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen?style=for-the-badge)](http://makeapullrequest.com)

**A complete, production-ready RESTful API built with pure PHP and MySQL**

[Features](#-features) • [Quick Start](#-quick-start) • [API Docs](#-api-endpoints) • [Testing](#-testing) • [Security](#-security)

![Divider](https://user-images.githubusercontent.com/73097560/115834477-dbab4500-a447-11eb-908a-139a6edaec5c.gif)

</div>

## ✨ Features

<table>
<tr>
<td>

### 🔐 Authentication

- JWT token-based auth
- Secure password hashing
- Token expiration (1 hour)
- Protected endpoints

</td>
<td>

### 👨‍🎓 Students Management

- Full CRUD operations
- Search by name/email
- Pagination support
- CSV export

</td>
</tr>
<tr>
<td>

### 📚 Courses Management

- Complete CRUD
- Search by name/code
- Pagination
- CSV export

</td>
<td>

### 📝 Enrollments

- Link students to courses
- View student's courses
- View course's students
- Manage enrollments

</td>
</tr>
</table>

### 🛡️ Security Features

```
✓ JWT Authentication          ✓ SQL Injection Protection
✓ Password Hashing (bcrypt)   ✓ Input Validation
✓ Prepared Statements         ✓ CORS Support
✓ Email Validation            ✓ Duplicate Prevention
```

![Divider](https://user-images.githubusercontent.com/73097560/115834477-dbab4500-a447-11eb-908a-139a6edaec5c.gif)

## 📋 Requirements

| Technology                                                                                   | Version | Purpose                 |
| -------------------------------------------------------------------------------------------- | ------- | ----------------------- |
| ![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat&logo=php&logoColor=white)          | 7.4+    | Backend Language        |
| ![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat&logo=mysql&logoColor=white)    | 5.7+    | Database                |
| ![Apache](https://img.shields.io/badge/Apache-D22128?style=flat&logo=apache&logoColor=white) | 2.4+    | Web Server              |
| ![XAMPP](https://img.shields.io/badge/XAMPP-FB7A24?style=flat&logo=xampp&logoColor=white)    | Latest  | Development Environment |

![Divider](https://user-images.githubusercontent.com/73097560/115834477-dbab4500-a447-11eb-908a-139a6edaec5c.gif)

## 🚀 Quick Start

### 1️⃣ Clone or Download

```bash
# Clone the repository
git clone https://github.com/yourusername/student-api.git

# Or download and extract to C:\xampp\htdocs\rest-api\
```

### 2️⃣ Start XAMPP

```
✓ Open XAMPP Control Panel
✓ Start Apache
✓ Start MySQL
```

### 3️⃣ Import Database

**Option A - Automatic (Easiest):**

```bash
# Double-click this file
import-database.bat
```

**Option B - Manual:**

1. Open `http://localhost/phpmyadmin`
2. Click `Import` tab
3. Choose `setup.sql`
4. Click `Go`

### 4️⃣ Test API

Open in browser:

```
http://localhost/rest-api/test-api.html
```

**Default Login:**

```
Email: admin@example.com
Password: admin123
```

> 🎉 **That's it!** Your API is now running!

![Divider](https://user-images.githubusercontent.com/73097560/115834477-dbab4500-a447-11eb-908a-139a6edaec5c.gif)

## 📡 API Endpoints

### Base URL

```
http://localhost/rest-api
```

### 🔐 Authentication

<details>
<summary><b>POST /login</b> - Admin Login</summary>

**Request:**

```json
{
  "email": "admin@example.com",
  "password": "admin123"
}
```

**Response (200):**

```json
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

</details>

> **Note:** All endpoints below require authentication. Include token in header:
>
> ```
> Authorization: Bearer <your-token>
> ```

---

### 👨‍🎓 Students

<details>
<summary><b>GET /students</b> - Get All Students (with pagination)</summary>

**Query Parameters:**

- `page` - Page number (default: 1)
- `limit` - Items per page (default: 10, max: 100)
- `search` - Search by name or email

**Example:**

```
GET /students?page=1&limit=10&search=john
```

**Response (200):**

```json
{
  "success": true,
  "data": [
    {
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
  ],
  "pagination": {
    "total": 50,
    "page": 1,
    "limit": 10,
    "pages": 5
  }
}
```

</details>

<details>
<summary><b>GET /students/{id}</b> - Get Single Student</summary>

**Example:**

```
GET /students/1
```

**Response (200):**

```json
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

</details>

<details>
<summary><b>POST /students</b> - Create Student</summary>

**Request:**

```json
{
  "first_name": "John",
  "last_name": "Doe",
  "email": "john.doe@example.com",
  "phone": "555-0101",
  "address": "123 Main St",
  "dob": "2000-05-15",
  "gender": "Male"
}
```

**Response (201):**

```json
{
  "success": true,
  "message": "Student created successfully",
  "data": { ... }
}
```

</details>

<details>
<summary><b>PUT /students/{id}</b> - Update Student</summary>

**Request:**

```json
{
  "first_name": "Jane",
  "last_name": "Doe",
  "email": "jane.doe@example.com",
  "phone": "555-0199",
  "address": "456 Oak Ave",
  "dob": "2000-05-15",
  "gender": "Female"
}
```

**Response (200):**

```json
{
  "success": true,
  "message": "Student updated successfully",
  "data": { ... }
}
```

</details>

<details>
<summary><b>DELETE /students/{id}</b> - Delete Student</summary>

**Response (200):**

```json
{
  "success": true,
  "message": "Student deleted successfully"
}
```

</details>

<details>
<summary><b>GET /students?export=csv</b> - Export Students to CSV</summary>

Downloads a CSV file with all students data.

</details>

---

### 📚 Courses

<details>
<summary><b>GET /courses</b> - Get All Courses</summary>

**Query Parameters:**

- `page` - Page number
- `limit` - Items per page
- `search` - Search by name or code

**Response (200):**

```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "name": "Introduction to Computer Science",
      "code": "CS101",
      "description": "Fundamentals of programming",
      "credits": 3,
      "created_at": "2026-01-13 10:00:00"
    }
  ],
  "pagination": { ... }
}
```

</details>

<details>
<summary><b>GET /courses/{id}</b> - Get Single Course</summary>

Returns details of a specific course.

</details>

<details>
<summary><b>POST /courses</b> - Create Course</summary>

**Request:**

```json
{
  "name": "Introduction to Computer Science",
  "code": "CS101",
  "description": "Fundamentals of programming",
  "credits": 3
}
```

</details>

<details>
<summary><b>PUT /courses/{id}</b> - Update Course</summary>

Update course details.

</details>

<details>
<summary><b>DELETE /courses/{id}</b> - Delete Course</summary>

Remove a course from the system.

</details>

<details>
<summary><b>GET /courses?export=csv</b> - Export Courses to CSV</summary>

Downloads a CSV file with all courses data.

</details>

---

### 📝 Enrollments

<details>
<summary><b>POST /enrollments</b> - Enroll Student in Course</summary>

**Request:**

```json
{
  "student_id": 1,
  "course_id": 1,
  "enrollment_date": "2026-01-13"
}
```

**Response (201):**

```json
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

</details>

<details>
<summary><b>GET /enrollments/student/{id}</b> - Get Student's Courses</summary>

**Response (200):**

```json
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

</details>

<details>
<summary><b>GET /enrollments/course/{id}</b> - Get Course's Students</summary>

**Response (200):**

```json
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

</details>

<details>
<summary><b>DELETE /enrollments</b> - Remove Enrollment</summary>

**Request:**

```json
{
  "student_id": 1,
  "course_id": 1
}
```

**Response (200):**

```json
{
  "success": true,
  "message": "Enrollment deleted successfully"
}
```

</details>

![Divider](https://user-images.githubusercontent.com/73097560/115834477-dbab4500-a447-11eb-908a-139a6edaec5c.gif)

## 📊 HTTP Status Codes

| Code                                                        | Description  | Usage                       |
| ----------------------------------------------------------- | ------------ | --------------------------- |
| ![200](https://img.shields.io/badge/200-OK-success)         | Success      | GET, PUT, DELETE successful |
| ![201](https://img.shields.io/badge/201-Created-success)    | Created      | POST successful             |
| ![400](https://img.shields.io/badge/400-Bad_Request-orange) | Bad Request  | Invalid input data          |
| ![401](https://img.shields.io/badge/401-Unauthorized-red)   | Unauthorized | Missing/invalid token       |
| ![404](https://img.shields.io/badge/404-Not_Found-red)      | Not Found    | Resource doesn't exist      |
| ![409](https://img.shields.io/badge/409-Conflict-orange)    | Conflict     | Duplicate entry             |
| ![500](https://img.shields.io/badge/500-Server_Error-red)   | Server Error | Internal error              |

![Divider](https://user-images.githubusercontent.com/73097560/115834477-dbab4500-a447-11eb-908a-139a6edaec5c.gif)

## 🧪 Testing

### Interactive Test Page

Open in browser:

```
http://localhost/rest-api/test-api.html
```

### Automated Test Suite

Run all tests automatically:

```
http://localhost/rest-api/TEST-ALL-ENDPOINTS.html
```

### cURL Examples

**Login:**

```bash
curl -X POST http://localhost/rest-api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"admin123"}'
```

**Get Students:**

```bash
curl http://localhost/rest-api/students \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Create Student:**

```bash
curl -X POST http://localhost/rest-api/students \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -d '{
    "first_name":"John",
    "last_name":"Doe",
    "email":"john@example.com",
    "phone":"555-0101",
    "address":"123 Main St",
    "dob":"2000-05-15",
    "gender":"Male"
  }'
```

![Divider](https://user-images.githubusercontent.com/73097560/115834477-dbab4500-a447-11eb-908a-139a6edaec5c.gif)

## 🗄️ Database Schema

```sql
┌─────────────────────────────────────────────────────────────┐
│                         ADMINS                              │
├─────────────────────────────────────────────────────────────┤
│ id (PK)          │ INT AUTO_INCREMENT                       │
│ name             │ VARCHAR(100)                             │
│ email            │ VARCHAR(100) UNIQUE                      │
│ password         │ VARCHAR(255)                             │
│ created_at       │ TIMESTAMP                                │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                        STUDENTS                             │
├─────────────────────────────────────────────────────────────┤
│ id (PK)          │ INT AUTO_INCREMENT                       │
│ first_name       │ VARCHAR(50)                              │
│ last_name        │ VARCHAR(50)                              │
│ email            │ VARCHAR(100) UNIQUE                      │
│ phone            │ VARCHAR(15)                              │
│ address          │ TEXT                                     │
│ dob              │ DATE                                     │
│ gender           │ ENUM('Male','Female','Other')            │
│ created_at       │ TIMESTAMP                                │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                         COURSES                             │
├─────────────────────────────────────────────────────────────┤
│ id (PK)          │ INT AUTO_INCREMENT                       │
│ name             │ VARCHAR(100)                             │
│ code             │ VARCHAR(20) UNIQUE                       │
│ description      │ TEXT                                     │
│ credits          │ INT                                      │
│ created_at       │ TIMESTAMP                                │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                      ENROLLMENTS                            │
├─────────────────────────────────────────────────────────────┤
│ id (PK)          │ INT AUTO_INCREMENT                       │
│ student_id (FK)  │ INT → students.id                        │
│ course_id (FK)   │ INT → courses.id                         │
│ enrollment_date  │ DATE                                     │
│ UNIQUE (student_id, course_id)                              │
└─────────────────────────────────────────────────────────────┘
```

![Divider](https://user-images.githubusercontent.com/73097560/115834477-dbab4500-a447-11eb-908a-139a6edaec5c.gif)

## 🛡️ Security

### Implemented Security Measures

| Feature                  | Implementation                    |
| ------------------------ | --------------------------------- |
| **Authentication**       | JWT tokens with expiration        |
| **Password Storage**     | bcrypt hashing (PASSWORD_DEFAULT) |
| **SQL Injection**        | PDO prepared statements           |
| **Input Validation**     | Server-side validation            |
| **Email Validation**     | FILTER_VALIDATE_EMAIL             |
| **Duplicate Prevention** | UNIQUE constraints                |
| **CORS**                 | Configurable headers              |
| **Error Handling**       | Proper HTTP status codes          |

### Configuration

Edit `config.php` to customize:

```php
// JWT Settings
define('JWT_SECRET', 'your-secret-key-change-in-production');
define('JWT_EXPIRATION', 3600); // 1 hour

// Database
define('DB_HOST', 'localhost');
define('DB_NAME', 'student_api');
define('DB_USER', 'root');
define('DB_PASS', '');

// Pagination
define('DEFAULT_PAGE_SIZE', 10);
define('MAX_PAGE_SIZE', 100);
```

![Divider](https://user-images.githubusercontent.com/73097560/115834477-dbab4500-a447-11eb-908a-139a6edaec5c.gif)

## 📁 Project Structure

```
rest-api/
├── 📂 api/
│   ├── 📄 login.php          # Authentication endpoint
│   ├── 📄 students.php       # Students CRUD
│   ├── 📄 courses.php        # Courses CRUD
│   └── 📄 enrollments.php    # Enrollments management
├── 📄 config.php             # Configuration
├── 📄 db.php                 # Database connection
├── 📄 jwt.php                # JWT implementation
├── 📄 auth.php               # Auth middleware
├── 📄 index.php              # Main router
├── 📄 .htaccess              # URL rewriting
├── 📄 setup.sql              # Database schema
├── 📄 test-api.html          # Interactive tester
├── 📄 TEST-ALL-ENDPOINTS.html # Automated tests
└── 📄 README.md              # Documentation
```

![Divider](https://user-images.githubusercontent.com/73097560/115834477-dbab4500-a447-11eb-908a-139a6edaec5c.gif)

## 🎯 Use Cases

Perfect for:

- 🎓 **Educational Institutions** - Manage student records
- 💼 **Portfolio Projects** - Showcase API development skills
- 📚 **Learning** - Study RESTful API design
- 🔧 **Prototyping** - Quick backend for student apps
- 📊 **Data Management** - Course and enrollment tracking

![Divider](https://user-images.githubusercontent.com/73097560/115834477-dbab4500-a447-11eb-908a-139a6edaec5c.gif)

## 🚀 Deployment

### Production Checklist

- [ ] Change `JWT_SECRET` in `config.php`
- [ ] Update database credentials
- [ ] Enable HTTPS
- [ ] Set proper file permissions
- [ ] Configure CORS for your domain
- [ ] Enable error logging
- [ ] Disable display_errors in php.ini
- [ ] Set up database backups
- [ ] Configure rate limiting
- [ ] Add API versioning

![Divider](https://user-images.githubusercontent.com/73097560/115834477-dbab4500-a447-11eb-908a-139a6edaec5c.gif)

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the project
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

![Divider](https://user-images.githubusercontent.com/73097560/115834477-dbab4500-a447-11eb-908a-139a6edaec5c.gif)

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

![Divider](https://user-images.githubusercontent.com/73097560/115834477-dbab4500-a447-11eb-908a-139a6edaec5c.gif)

## 👨‍💻 Author

**Your Name**

- GitHub: [@yourusername](https://github.com/yourusername)
- LinkedIn: [Your Name](https://linkedin.com/in/yourprofile)
- Email: your.email@example.com

![Divider](https://user-images.githubusercontent.com/73097560/115834477-dbab4500-a447-11eb-908a-139a6edaec5c.gif)

## 🙏 Acknowledgments

- Built with ❤️ using pure PHP
- Inspired by modern RESTful API design principles
- Thanks to the open-source community

![Divider](https://user-images.githubusercontent.com/73097560/115834477-dbab4500-a447-11eb-908a-139a6edaec5c.gif)

<div align="center">

### ⭐ Star this repo if you find it helpful!

**Made with ❤️ and PHP**

[⬆ Back to Top](#-student-management-system)

</div>
