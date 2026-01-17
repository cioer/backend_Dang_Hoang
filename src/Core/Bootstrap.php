<?php
namespace App\Core;

use Database;
use PDO;

class Bootstrap
{
    private static ?PDO $dbConnection = null;

    /**
     * Get database connection (singleton)
     */
    public static function db(): PDO
    {
        if (self::$dbConnection === null) {
            $database = new Database();
            self::$dbConnection = $database->getConnection();
            if (self::$dbConnection === null) {
                Response::serverError('Database connection failed');
            }
        }
        return self::$dbConnection;
    }

    /**
     * Get JWT instance
     */
    public static function jwt(): \JWT
    {
        return new \JWT();
    }

    /**
     * Get Captcha instance
     */
    public static function captcha(): \Captcha
    {
        return new \Captcha();
    }
}
