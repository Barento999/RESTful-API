<?php
// JWT Configuration
define('JWT_SECRET', 'your-secret-key-change-this-in-production');
define('JWT_ALGORITHM', 'HS256');
define('JWT_EXPIRATION', 3600); // 1 hour

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'student_api');
define('DB_USER', 'root');
define('DB_PASS', '');

// Pagination
define('DEFAULT_PAGE_SIZE', 10);
define('MAX_PAGE_SIZE', 100);
