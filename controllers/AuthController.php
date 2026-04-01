<?php

require_once 'BaseController.php';

class AuthController extends BaseController {
    
    public function login() {
        
        if ($this->auth->isLoggedIn()) {
            $this->redirect('dashboard');
        }
        
        $error = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            CSRFHelper::requireValidToken();
            
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if ($this->auth->login($username, $password)) {
                $this->redirect('dashboard');
            } else {
                $error = 'Invalid username or password';
            }
        }
        
        $this->render('auth/login', [
            'error' => $error,
            'csrf_field' => CSRFHelper::getHiddenField()
        ]);
    }
    
    public function loginPost() {
        CSRFHelper::requireValidToken();
        
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            $_SESSION['error'] = 'Username and password are required';
            $this->redirect('auth/login');
        }
        
        if ($this->auth->login($username, $password)) {
            $this->redirect('dashboard');
        } else {
            $_SESSION['error'] = 'Invalid username or password';
            $this->redirect('auth/login');
        }
    }
    
    public function logout() {
        $this->auth->logout();
        $this->redirect('auth/login');
    }
}
