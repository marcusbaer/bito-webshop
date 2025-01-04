<?php
    echo "PHP Setup Test\n";
    echo "PHP Version: " . PHP_VERSION . "\n";
    echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
    
    if (function_exists('mysqli_connect')) {
        echo "MySQL support is enabled\n";
    }

    // phpinfo();
?>