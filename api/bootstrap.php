<?php
/**
 * API Bootstrap File
 * Include this file at the top of every endpoint
 */

// Load Custom Autoloader (Moved from vendor/ to src/ to avoid gitignore issues)
require_once __DIR__ . '/../src/autoload.php';

// Classes are now available via autoloading:
// - App\Core\Response
// - App\Core\Request
// - App\Core\Middleware
// - App\Core\Bootstrap
// - Database (from config/database.php)
// - JWT (from config/jwt.php)
// - Captcha (from config/captcha.php)
