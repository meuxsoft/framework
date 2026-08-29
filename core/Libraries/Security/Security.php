<?php

namespace Core\Libraries\Security;

use Core\Libraries\Session\Session;
use Core\Libraries\Request\Request;

class Security
{
    private function __construct()
    {
    }

    public static function escape($value)
    {
        if ($value === null) {
            return '';
        }
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public static function e($value)
    {
        return self::escape($value);
    }

    public static function hashPassword($password, $options = [])
    {
        return password_hash($password, PASSWORD_BCRYPT, $options);
    }

    public static function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    public static function validateCsrf()
    {
        $token = Request::post('_csrf_token');

        if (empty($token)) {
            $token = Request::header('X-CSRF-TOKEN');
        }

        return Session::verifyCsrfToken($token);
    }

    public static function checkCsrf()
    {
        if (in_array(Request::method(), ['POST', 'PUT', 'DELETE', 'PATCH'], true)) {
            if (!self::validateCsrf()) {
                http_response_code(419);
                echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>419 - CSRF Hatası</title><style>body{font-family:sans-serif;text-align:center;padding:50px;background:#f8fafc;color:#334155;}h1{font-size:36px;color:#ef4444;margin-bottom:10px;}</style></head><body><h1>419 - Sayfa Zaman Aşımına Uğradı</h1><p>CSRF güvenlik doğrulaması başarısız oldu. Lütfen formu yenileyip tekrar deneyin.</p><a href="javascript:history.back()" style="color:#3b82f6;">Geri Dön</a></body></html>';
                exit;
            }
        }
    }

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

    public static function validate($data, $rules)
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

                if ($value === null || (is_string($value) && trim($value) === '')) {
                    continue;
                }

                if ($ruleName === 'min' && isset($params[0])) {
                    $min = $params[0];
                    if (is_numeric($value) && $value < $min) {
                        $errors[$field][] = "Değer en az {$min} olmalıdır.";
                    } elseif (is_string($value) && mb_strlen($value) < $min) {
                        $errors[$field][] = "Bu alan en az {$min} karakter olmalıdır.";
                    }
                }

                if ($ruleName === 'max' && isset($params[0])) {
                    $max = $params[0];
                    if (is_numeric($value) && $value > $max) {
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
