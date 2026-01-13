@echo off
echo ========================================
echo   Database Import Script
echo ========================================
echo.
echo This will import the database automatically.
echo.
echo Make sure MySQL is running in XAMPP!
echo.
pause

echo.
echo Importing database...
echo.

"C:\xampp\mysql\bin\mysql.exe" -u root < setup.sql

if %errorlevel% equ 0 (
    echo.
    echo ========================================
    echo   SUCCESS! Database imported!
    echo ========================================
    echo.
    echo You can now test the API at:
    echo http://localhost/rest-api/test-api.html
    echo.
    echo Login credentials:
    echo Email: admin@example.com
    echo Password: admin123
    echo.
) else (
    echo.
    echo ========================================
    echo   ERROR! Import failed!
    echo ========================================
    echo.
    echo Please try manual import:
    echo 1. Open http://localhost/phpmyadmin
    echo 2. Click Import tab
    echo 3. Choose setup.sql file
    echo 4. Click Go
    echo.
)

pause
