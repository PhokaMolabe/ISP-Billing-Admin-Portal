<?php

class CSRFHelper {
    
    public static function generateToken() {
        if (!isset($_SESSION[CSRF_TOKEN_NAME]) || 
            !isset($_SESSION[CSRF_TOKEN_NAME . '_time']) || 
            time() - $_SESSION[CSRF_TOKEN_NAME . '_time'] > CSRF_TOKEN_EXPIRY) {
            
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
            $_SESSION[CSRF_TOKEN_NAME . '_time'] = time();
        }
        
        return $_SESSION[CSRF_TOKEN_NAME];
    }
    
    public static function validateToken($token) {
        if (!isset($_SESSION[CSRF_TOKEN_NAME]) || 
            !isset($_SESSION[CSRF_TOKEN_NAME . '_time'])) {
            return false;
        }
        
        if (time() - $_SESSION[CSRF_TOKEN_NAME . '_time'] > CSRF_TOKEN_EXPIRY) {
            self::clearToken();
            return false;
        }
        
        return hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
    }
    
    public static function clearToken() {
        unset($_SESSION[CSRF_TOKEN_NAME]);
        unset($_SESSION[CSRF_TOKEN_NAME . '_time']);
    }
    
    public static function getHiddenField() {
        $token = self::generateToken();
        return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . htmlspecialchars($token) . '">';
    }
    
    public static function requireValidToken() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_POST[CSRF_TOKEN_NAME] ?? '';
            if (!self::validateToken($token)) {
                http_response_code(403);
                die('You do not have permission to access this page');
            }
        }
    }
}
