<?php
/**
 * Simple PSR-4 Autoloader
 * This file mimics composer's autoload functionality
 */

// Autoload config files
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/jwt.php';
require_once __DIR__ . '/../config/captcha.php';

// Autoload lib files
require_once __DIR__ . '/../lib/UserRepository.php';

// PSR-4 Autoloader for App\ namespace
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    // Base directory for the namespace prefix
    $base_dir = __DIR__ . '/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});
