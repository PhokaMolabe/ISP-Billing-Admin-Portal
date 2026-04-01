<?php

require_once 'config/database.php';
require_once 'helpers/AuthHelper.php';
require_once 'helpers/CSRFHelper.php';

class BaseController {
    
    protected $db;
    protected $auth;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
        $this->auth = new AuthHelper();
    }
    
    protected function render($view, $data = []) {
        extract($data);
        
        include 'views/layout/header.php';
       
        include "views/{$view}.php";
        
        include 'views/layout/footer.php';
    }
    
    protected function redirect($route) {
        header("Location: ?_route={$route}");
        exit;
    }
    
    protected function validateRequired($data, $requiredFields) {
        $errors = [];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || trim($data[$field]) === '') {
                $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }
        
        return $errors;
    }
    
    protected function sanitizeInput($data) {
        $sanitized = [];
        
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $sanitized[$key] = trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
            } else {
                $sanitized[$key] = $value;
            }
        }
        
        return $sanitized;
    }
    
    protected function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    protected function validatePhone($phone) {
        
        return preg_match('/^[\d\s\-\+\(\)]+$/', $phone);
    }
}
