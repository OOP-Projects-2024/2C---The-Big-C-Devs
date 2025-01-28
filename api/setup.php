<?php
// Define the required directories
$directories = [
    __DIR__ . '/../src',
    __DIR__ . '/../src/config',
    __DIR__ . '/../src/models',
    __DIR__ . '/handlers'
];

// Create directories if they don't exist
foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
}

echo "Directory structure created successfully!\n";

// Verify database.php exists
$dbConfig = __DIR__ . '/../src/config/database.php';
if (!file_exists($dbConfig)) {
    echo "Warning: database.php not found at: $dbConfig\n";
    echo "Please ensure database.php is properly configured.\n";
}

