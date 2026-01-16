# ✅ Final Setup Checklist

## Step 1: Start XAMPP Services ⚙️

1. Open **XAMPP Control Panel**
2. Click **Start** next to **Apache** (should turn green)
3. Click **Start** next to **MySQL** (should turn green)

**Verify:**

- Apache shows: `Running` with green background
- MySQL shows: `Running` with green background

---

## Step 2: Import Database 📊

**Option A - Automatic (Easiest):**

1. Double-click `import-database.bat`
2. Press any key when prompted
3. Wait for success message

**Option B - Manual:**

1. Open browser: `http://localhost/phpmyadmin`
2. Click `student_api` in left sidebar (or create it if missing)
3. Click `Import` tab at top
4. Click `Choose File` button
5. Select `setup.sql` from your project folder
6. Click `Go` button
7. Wait for green success message

**Verify:**

- You should see 4 tables: admins, students, courses, enrollments
- Click on `admins` table → Browse → You should see 1 admin user

---

## Step 3: Test API Connection 🔌

Open browser: `http://localhost/rest-api/test-connection.php`

**Expected result:**

```json
{
  "status": "success",
  "message": "API is working!",
  "php_version": "8.x.x",
  "timestamp": "2026-01-16 ..."
}
```

**If you see this ✅** → API is accessible!

**If you see error ❌** → Check:

- Apache is running in XAMPP
- Files are in `C:\xampp\htdocs\rest-api\`

---

## Step 4: Test Login 🔐

Open browser: `http://localhost/rest-api/test-api.html`

**What happens:**

1. Page loads and auto-tests connection
2. If connection fails, you'll see an alert

**Login Test:**

1. Email: `admin@example.com`
2. Password: `admin123`
3. Click **Login** button

**Expected result:**

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

**If you see this ✅** → Authentication works!

---

## Step 5: Test Students API 👨‍🎓

After successful login, the token is saved automatically.

**Click these buttons:**

1. **Get All Students** → Should show 3 students (John, Jane, Mike)
2. **Search Students** → Type "john" → Click Search → Shows John Doe
3. **Create Student** → Click Create → Should create Alice Johnson
4. **Export CSV** → Should download students.csv file

**Expected results:**

- All buttons return JSON responses
- No errors
- Data is displayed in the response box

---

## Step 6: Test Courses API 📚

**Click these buttons:**

1. **Get All Courses** → Should show 4 courses (CS101, CS201, etc.)
2. **Search Courses** → Type "computer" → Shows matching courses
3. **Create Course** → Should create Machine Learning course
4. **Export CSV** → Should download courses.csv file

---

## Step 7: Test Enrollments API 📝

**Test enrollment:**

1. Student ID: `1` (John Doe)
2. Course ID: `3` (Database Systems)
3. Click **Create Enrollment** → Should enroll John in Database Systems

**Test queries:**

1. Click **Get Student's Courses** → Shows all courses for student 1
2. Click **Get Course's Students** → Shows all students in course 1

---

## 🎉 Success Criteria

You've successfully completed the setup if:

- ✅ Login returns a JWT token
- ✅ Get All Students shows data
- ✅ Get All Courses shows data
- ✅ Create operations work
- ✅ Search functionality works
- ✅ CSV export downloads files
- ✅ Enrollments can be created and queried

---

## 🚨 Troubleshooting

### Problem: "Failed to fetch"

**Cause:** API not accessible
**Fix:**

- Check Apache is running
- Verify URL: `http://localhost/rest-api/test-connection.php`
- Check files are in correct folder

### Problem: "Invalid credentials"

**Cause:** Database not imported or wrong password
**Fix:**

- Re-import `setup.sql` in phpMyAdmin
- Make sure you see the admin user in `admins` table

### Problem: "Invalid or expired token"

**Cause:** Token expired (1 hour) or invalid
**Fix:**

- Click Login again to get a new token
- Check JWT_SECRET in config.php hasn't changed

### Problem: "Database connection failed"

**Cause:** MySQL not running or wrong credentials
**Fix:**

- Start MySQL in XAMPP
- Check `config.php` has correct database credentials

### Problem: 404 Not Found

**Cause:** .htaccess not working
**Fix:**

- Check `.htaccess` file exists
- Enable mod_rewrite in Apache
- Restart Apache

---

## 📋 Quick Test Commands (cURL)

If you prefer command line testing:

```bash
# Test connection
curl http://localhost/rest-api/test-connection.php

# Login
curl -X POST http://localhost/rest-api/login ^
  -H "Content-Type: application/json" ^
  -d "{\"email\":\"admin@example.com\",\"password\":\"admin123\"}"

# Get students (replace TOKEN with your actual token)
curl http://localhost/rest-api/students ^
  -H "Authorization: Bearer TOKEN"
```

---

## 📚 API Documentation

Full API documentation is in: `README.md`

Includes:

- All endpoints with examples
- Request/response formats
- Error codes
- Security features
- Database schema

---

## 🎯 What You've Built

A complete RESTful API with:

- **Authentication:** JWT-based security
- **Students Management:** Full CRUD + search + export
- **Courses Management:** Full CRUD + search + export
- **Enrollments:** Link students to courses
- **Security:** Password hashing, SQL injection protection
- **Features:** Pagination, filtering, CSV export
- **Documentation:** Complete guides and examples

**Perfect for your portfolio!** 🚀

---

## Next Steps (Optional Enhancements)

Want to add more features? Consider:

1. **Frontend:** Build a React/Vue dashboard
2. **Email:** Send welcome emails to new students
3. **Reports:** Generate PDF reports
4. **Grades:** Add grades to enrollments
5. **Attendance:** Track student attendance
6. **API Versioning:** Add /v1/ to endpoints
7. **Rate Limiting:** Prevent API abuse
8. **Logging:** Track all API requests
9. **Testing:** Add PHPUnit tests
10. **Docker:** Containerize the application

But for now, you have a **complete, production-ready API!** ✨
