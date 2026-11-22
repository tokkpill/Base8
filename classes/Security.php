<?php

declare(strict_types=1);

namespace Classes;

/**
 * Clase Security
 * 
 * Utilidades de seguridad para CSRF, XSS, y rate limiting.
 * Complementa la función s() existente en funciones.php.
 */
class Security
{
    /**
     * Genera un token CSRF y lo almacena en la sesión
     */
    public static function generateCSRFToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $token = bin2hex(random_bytes(32));
        $_SESSION['csrf_token'] = $token;
        $_SESSION['csrf_token_time'] = time();

        return $token;
    }

    /**
     * Obtiene el token CSRF actual o genera uno nuevo
     */
    public static function getCSRFToken(): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            return self::generateCSRFToken();
        }

        return $_SESSION['csrf_token'];
    }

    /**
     * Valida un token CSRF
     * 
     * @param string $token Token a validar
     * @param int $maxAge Tiempo máximo de validez en segundos (default: 1 hora)
     * @return bool True si el token es válido
     */
    public static function validateCSRFToken(string $token, int $maxAge = 3600): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token']) || empty($token)) {
            return false;
        }

        // Verificar que no haya expirado
        $tokenTime = $_SESSION['csrf_token_time'] ?? 0;
        if ((time() - $tokenTime) > $maxAge) {
            return false;
        }

        return hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Genera el campo HTML oculto con el token CSRF
     */
    public static function csrfField(): string
    {
        $token = self::getCSRFToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }

    /**
     * Sanitiza una cadena para prevenir XSS
     */
    public static function sanitize(string $input): string
    {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitiza un array completo
     */
    public static function sanitizeArray(array $data): array
    {
        $sanitized = [];
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = self::sanitize($value);
            } elseif (is_array($value)) {
                $sanitized[$key] = self::sanitizeArray($value);
            } else {
                $sanitized[$key] = $value;
            }
        }
        return $sanitized;
    }
}
