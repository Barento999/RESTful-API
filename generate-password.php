<?php
// Generate password hash for admin123
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Password: $password\n";
echo "Hash: $hash\n\n";

// Verify it works
if (password_verify($password, $hash)) {
    echo "✓ Verification successful!\n";
} else {
    echo "✗ Verification failed!\n";
}

// SQL to update admin password
echo "\n--- SQL to update admin password ---\n";
echo "UPDATE admins SET password = '$hash' WHERE email = 'admin@example.com';\n";
