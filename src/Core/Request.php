<?php
namespace App\Core;

class Request
{
    private static ?array $jsonBody = null;

    /**
     * Get JSON body (cached)
     */
    private static function getJsonBody(): array
    {
        if (self::$jsonBody === null) {
            $input = file_get_contents("php://input");
            self::$jsonBody = $input ? (json_decode($input, true) ?? []) : [];
        }
        return self::$jsonBody;
    }

    /**
     * Get a value from JSON body, POST, or GET
     */
    public static function get(string $key, $default = null)
    {
        $json = self::getJsonBody();
        if (isset($json[$key])) {
            return $json[$key];
        }
        if (isset($_POST[$key])) {
            return $_POST[$key];
        }
        if (isset($_GET[$key])) {
            return $_GET[$key];
        }
        return $default;
    }

    /**
     * Get all input data (JSON body merged with POST)
     */
    public static function all(): array
    {
        return array_merge($_POST, self::getJsonBody());
    }

    /**
     * Validate required fields exist
     * @param array $fields List of required field names
     * @return array The validated data
     */
    public static function require(array $fields): array
    {
        $data = self::all();
        $missing = [];
        foreach ($fields as $field) {
            if (!isset($data[$field]) || $data[$field] === '') {
                $missing[] = $field;
            }
        }
        if (!empty($missing)) {
            Response::error('Thiếu dữ liệu: ' . implode(', ', $missing), 400);
        }
        return $data;
    }

    /**
     * Get Bearer token from Authorization header
     */
    public static function getBearerToken(): ?string
    {
        $headers = getallheaders();
        $auth = $headers['Authorization'] ?? $headers['authorization'] ?? null;
        if ($auth && preg_match('/Bearer\s+(.+)/i', $auth, $matches)) {
            return $matches[1];
        }
        // Fallback to Apache/CGI
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            if (preg_match('/Bearer\s+(.+)/i', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
                return $matches[1];
            }
        }
        return null;
    }

    /**
     * Get client IP address
     */
    public static function ip(): ?string
    {
        return $_SERVER['REMOTE_ADDR'] ?? null;
    }

    /**
     * Get integer value
     */
    public static function getInt(string $key, ?int $default = null): ?int
    {
        $value = self::get($key);
        return $value !== null ? intval($value) : $default;
    }

    /**
     * Check if request method matches
     */
    public static function isMethod(string $method): bool
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET') === strtoupper($method);
    }
}
