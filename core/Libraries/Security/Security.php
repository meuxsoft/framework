<?php

namespace Core\Libraries\Security;

use Core\Libraries\Session\Session;
use Core\Libraries\Request\Request;
use RuntimeException;

class Security
{
    /**
     * Prevent direct instantiation.
     */
    private function __construct()
    {
    }

    /**
     * Escape HTML special characters for XSS prevention.
     *
     * @param mixed $value
     * @return string
     */
    public static function escape($value): string
    {
        if ($value === null) {
            return '';
        }
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * Alias for escape.
     *
     * @param mixed $value
     * @return string
     */
    public static function e($value): string
    {
        return self::escape($value);
    }

    /**
     * Hash a password securely using bcrypt.
     *
     * @param string $password
     * @param array $options
     * @return string
     */
    public static function hashPassword(string $password, array $options = []): string
    {
        return password_hash($password, PASSWORD_BCRYPT, $options);
    }

    /**
     * Verify a password against a hash.
     *
     * @param string $password
     * @param string $hash
     * @return bool
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Verify CSRF token from POST/HEADER data.
     *
     * @return bool
     */
    public static function validateCsrf(): bool
    {
        // Check POST parameter
        $token = Request::post('_csrf_token');

        // Check header (e.g. X-CSRF-TOKEN)
        if (empty($token)) {
            $token = Request::header('X-CSRF-TOKEN');
        }

        return Session::verifyCsrfToken($token);
    }

    /**
     * Enforce CSRF token or abort with 419 Page Expired.
     *
     * @return void
     */
    public static function checkCsrf(): void
    {
        if (in_array(Request::method(), ['POST', 'PUT', 'DELETE', 'PATCH'], true)) {
            if (!self::validateCsrf()) {
                http_response_code(419);
                echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>419 - CSRF Hatası</title><style>body{font-family:sans-serif;text-align:center;padding:50px;background:#f8fafc;color:#334155;}h1{font-size:36px;color:#ef4444;margin-bottom:10px;}</style></head><body><h1>419 - Sayfa Zaman Aşımına Uğradı</h1><p>CSRF güvenlik doğrulaması başarısız oldu. Lütfen formu yenileyip tekrar deneyin.</p><a href="javascript:history.back()" style="color:#3b82f6;">Geri Dön</a></body></html>';
                exit;
            }
        }
    }

    /**
     * Sanitize input recursively.
     *
     * @param mixed $input
     * @return mixed
     */
    public static function sanitize($input)
    {
        if (is_array($input)) {
            $clean = [];
            foreach ($input as $k => $v) {
                $cleanKey = is_string($k) ? strip_tags($k) : $k;
                $clean[$cleanKey] = self::sanitize($v);
            }
            return $clean;
        }

        if (is_string($input)) {
            return trim(strip_tags($input));
        }

        return $input;
    }

    /**
     * Validate data against simple validation rules.
     *
     * @param array $data
     * @param array $rules e.g. ['name' => 'required|min:3|max:100', 'email' => 'required|email', 'price' => 'required|numeric']
     * @return array Array of errors indexed by field name (empty if valid)
     */
    public static function validate(array $data, array $rules): array
    {
        $errors = [];

        foreach ($rules as $field => $ruleString) {
            $fieldRules = explode('|', $ruleString);
            $value = $data[$field] ?? null;

            foreach ($fieldRules as $rule) {
                $params = [];
                if (strpos($rule, ':') !== false) {
                    [$ruleName, $paramStr] = explode(':', $rule, 2);
                    $params = explode(',', $paramStr);
                } else {
                    $ruleName = $rule;
                }

                $ruleName = trim($ruleName);

                if ($ruleName === 'required') {
                    if ($value === null || (is_string($value) && trim($value) === '') || (is_array($value) && empty($value))) {
                        $errors[$field][] = "Bu alan zorunludur.";
                    }
                }

                // If not required and empty, skip remaining validations
                if ($value === null || (is_string($value) && trim($value) === '')) {
                    continue;
                }

                if ($ruleName === 'min' && isset($params[0])) {
                    $min = (int)$params[0];
                    if (is_numeric($value) && (float)$value < $min) {
                        $errors[$field][] = "Değer en az {$min} olmalıdır.";
                    } elseif (is_string($value) && mb_strlen($value) < $min) {
                        $errors[$field][] = "Bu alan en az {$min} karakter olmalıdır.";
                    }
                }

                if ($ruleName === 'max' && isset($params[0])) {
                    $max = (int)$params[0];
                    if (is_numeric($value) && (float)$value > $max) {
                        $errors[$field][] = "Değer en fazla {$max} olabilir.";
                    } elseif (is_string($value) && mb_strlen($value) > $max) {
                        $errors[$field][] = "Bu alan en fazla {$max} karakter olabilir.";
                    }
                }

                if ($ruleName === 'email') {
                    if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $errors[$field][] = "Geçerli bir e-posta adresi giriniz.";
                    }
                }

                if ($ruleName === 'numeric') {
                    if (!is_numeric($value)) {
                        $errors[$field][] = "Bu alan sayısal bir değer olmalıdır.";
                    }
                }

                if ($ruleName === 'integer') {
                    if (filter_var($value, FILTER_VALIDATE_INT) === false) {
                        $errors[$field][] = "Bu alan tam sayı olmalıdır.";
                    }
                }
            }
        }

        return $errors;
    }
}
