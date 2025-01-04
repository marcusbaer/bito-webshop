<?php
$setupDir = __DIR__ . '/setup';

// Check if the setup directory exists
if (!is_dir($setupDir)) {
    die("Setup directory not found.");
}

// Open the directory
$dirHandle = opendir($setupDir);

if ($dirHandle) {
    // Iterate over the files in the directory
    while (($file = readdir($dirHandle)) !== false) {
        $filePath = $setupDir . '/' . $file;

        // Check if the file is a PHP file
        if (is_file($filePath) && pathinfo($filePath, PATHINFO_EXTENSION) === 'php') {
            echo "Executing $file...\n";
            include $filePath; // Execute the PHP file
        }
    }
    closedir($dirHandle);
} else {
    die("Failed to open setup directory.");
}

echo "All setup scripts executed.\n";
?>