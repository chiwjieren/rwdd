<?php
// Admin System Directory Setup
// Run this file once to create necessary directories

$directories = [
    '../media/events',
    '../media/items',
    '../media/profiles'
];

echo "<h2>GoGreen Admin System - Directory Setup</h2>";
echo "<ul>";

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0777, true)) {
            echo "<li style='color: green;'>✓ Created: $dir</li>";
        } else {
            echo "<li style='color: red;'>✗ Failed to create: $dir</li>";
        }
    } else {
        echo "<li style='color: blue;'>Already exists: $dir</li>";
    }
}

echo "</ul>";
echo "<p><strong>Setup complete!</strong> You can now use the admin system.</p>";
echo "<p><a href='login.php'>Go to Login Page</a></p>";
?>
