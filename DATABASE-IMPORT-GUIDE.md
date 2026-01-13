# 📊 Database Import Guide - Step by Step

## Step 1: Open phpMyAdmin

1. Open your web browser (Chrome, Firefox, Edge, etc.)
2. In the address bar, type: `http://localhost/phpmyadmin`
3. Press Enter

**What you'll see:**

- A page with a login screen OR
- The phpMyAdmin dashboard (if no password is set)

---

## Step 2: Access the Database

**Look at the LEFT SIDEBAR:**

- You'll see a list of databases
- Find and click on `student_api`
- If you don't see `student_api`, that's okay - we'll create it

**If `student_api` doesn't exist:**

1. Click on "New" at the top of the left sidebar
2. Type `student_api` in the "Database name" field
3. Click "Create"

---

## Step 3: Click Import Tab

**At the TOP of the page, you'll see several tabs:**

- Structure
- SQL
- Search
- Query
- Export
- **Import** ← Click this one
- Operations
- etc.

Click on the **"Import"** tab

---

## Step 4: Choose File

**You'll see a section that says "File to import":**

1. Click the **"Choose File"** button (or "Browse..." button)
2. A file browser window will open
3. Navigate to: `C:\xampp\htdocs\rest-api\`
4. Find and select the file: `setup.sql`
5. Click "Open"

**You should now see:** `setup.sql` next to the Choose File button

---

## Step 5: Import the File

1. Scroll down to the bottom of the page
2. Click the **"Go"** button (usually blue or green)
3. Wait a few seconds...

**Success message:**
You should see a green box that says:

```
Import has been successfully finished, X queries executed.
```

---

## Step 6: Verify Import

**Check if tables were created:**

1. Look at the LEFT SIDEBAR
2. Click on `student_api` database
3. You should now see 4 tables:

   - ✓ admins
   - ✓ courses
   - ✓ enrollments
   - ✓ students

4. Click on `admins` table
5. Click "Browse" tab
6. You should see 1 row with email: `admin@example.com`

---

## ✅ Done! Now Test Your API

Open: `http://localhost/rest-api/test-api.html`

Click "Login" with:

- Email: `admin@example.com`
- Password: `admin123`

You should get a success message with a JWT token!

---

## 🚨 Troubleshooting

### Problem: "Can't access phpMyAdmin"

**Solution:**

- Make sure XAMPP is running
- In XAMPP Control Panel, check that "MySQL" is green/started
- Try: `http://127.0.0.1/phpmyadmin`

### Problem: "Access denied" or login required

**Solution:**

- Default username: `root`
- Default password: (leave empty)
- Click "Go"

### Problem: "Import file too large"

**Solution:**

- Our file is small, this shouldn't happen
- But if it does, use the SQL tab instead:
  1. Open `setup.sql` in Notepad
  2. Copy all the content (Ctrl+A, Ctrl+C)
  3. In phpMyAdmin, click "SQL" tab
  4. Paste the content
  5. Click "Go"

### Problem: "Table already exists" error

**Solution:**

- This is fine! The import worked
- The error just means some tables were already there
- Check if the `admins` table has the admin user

---

## Alternative: Import via SQL Tab

If the Import tab doesn't work:

1. Open `setup.sql` in Notepad or any text editor
2. Select all text (Ctrl+A)
3. Copy (Ctrl+C)
4. Go to phpMyAdmin
5. Click on `student_api` database
6. Click "SQL" tab at the top
7. Paste the SQL code in the text box
8. Click "Go" button

---

## Need More Help?

Check the XAMPP Apache error logs:

- Location: `C:\xampp\mysql\data\mysql_error.log`
- Or in XAMPP Control Panel, click "Logs" button next to MySQL
