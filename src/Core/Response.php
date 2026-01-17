<?php
namespace App\Core;

class Response
{
    /**
     * Send success response with data
     */
    public static function success($data, int $code = 200): void
    {
        http_response_code($code);
        echo json_encode($data);
        exit;
    }

    /**
     * Send success response with message
     */
    public static function message(string $message, int $code = 200): void
    {
        http_response_code($code);
        echo json_encode(['message' => $message]);
        exit;
    }

    /**
     * Send error response
     */
    public static function error(string $message, int $code = 400): void
    {
        http_response_code($code);
        echo json_encode(['message' => $message]);
        exit;
    }

    /**
     * Send 401 Unauthorized response
     */
    public static function unauthorized(string $message = 'Unauthorized'): void
    {
        self::error($message, 401);
    }

    /**
     * Send 403 Forbidden response
     */
    public static function forbidden(string $message = 'Forbidden'): void
    {
        self::error($message, 403);
    }

    /**
     * Send 404 Not Found response
     */
    public static function notFound(string $message = 'Not found'): void
    {
        self::error($message, 404);
    }

    /**
     * Send 500 Internal Server Error response
     */
    public static function serverError(string $message = 'Internal server error'): void
    {
        self::error($message, 500);
    }

    /**
     * Send paginated list response
     */
    public static function paginated(array $data, int $page, int $totalPages, int $totalRecords, int $limit): void
    {
        self::success([
            'data' => $data,
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_records' => $totalRecords,
                'limit' => $limit
            ]
        ]);
    }
}
