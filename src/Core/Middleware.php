<?php
namespace App\Core;

class Middleware
{
    /**
     * Set CORS headers and handle OPTIONS preflight
     * @param string $methods Allowed HTTP methods (e.g., 'GET, POST')
     */
    public static function cors(string $methods = 'GET, POST, OPTIONS'): void
    {
        header("Access-Control-Allow-Origin: *");
        header("Content-Type: application/json; charset=UTF-8");
        header("Access-Control-Allow-Methods: " . $methods);
        header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit;
        }
    }

    /**
     * Require valid JWT authentication
     * @return object User data from token (id, role, username)
     */
    public static function auth(): object
    {
        $token = Request::getBearerToken();
        if (!$token) {
            Response::unauthorized();
        }

        $decoded = validateJWT($token);
        if (!$decoded) {
            Response::unauthorized();
        }

        return $decoded['data'];
    }

    /**
     * Require admin role
     * @return object User data from token
     */
    public static function requireAdmin(): object
    {
        $user = self::auth();
        if ($user->role !== 'admin') {
            Response::forbidden();
        }
        return $user;
    }

    /**
     * Require one of the specified roles
     * @param string ...$roles Allowed roles
     * @return object User data from token
     */
    public static function requireRole(string ...$roles): object
    {
        $user = self::auth();
        if (!in_array($user->role, $roles)) {
            Response::forbidden();
        }
        return $user;
    }

    /**
     * Optional auth - returns user data if authenticated, null otherwise
     * @return object|null User data or null
     */
    public static function optionalAuth(): ?object
    {
        $token = Request::getBearerToken();
        if (!$token) {
            return null;
        }

        $decoded = validateJWT($token);
        if (!$decoded) {
            return null;
        }

        return $decoded['data'];
    }
}
