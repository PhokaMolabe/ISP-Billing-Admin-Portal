<?php

class AuthHelper {
    private $db;
    
    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }
    
    public function login($username, $password) {
        $stmt = $this->db->prepare("SELECT * FROM tbl_users WHERE username = ? AND status = 'active'");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['user_type'] = $user['user_type'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['root'] = $user['root'];
            $_SESSION['logged_in'] = true;
            return true;
        }
        
        return false;
    }
    
    public function logout() {
        session_destroy();
        session_regenerate_id(true);
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return [
            'id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'user_type' => $_SESSION['user_type'],
            'fullname' => $_SESSION['fullname'],
            'root' => $_SESSION['root']
        ];
    }
    
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: ?_route=auth/login');
            exit;
        }
    }
    
    public function getUserById($userId) {
        $stmt = $this->db->prepare("SELECT * FROM tbl_users WHERE id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }
    
    public function getUserHierarchy($userId) {
        $user = $this->getUserById($userId);
        if (!$user) {
            return [];
        }
        
        $hierarchy = [$user];
        
        if ($user['root']) {
            $rootUser = $this->getUserById($user['root']);
            if ($rootUser) {
                $hierarchy[] = $rootUser;
            }
        }
    
        if ($user['user_type'] === 'Agent' || $user['user_type'] === 'SuperAdmin' || $user['user_type'] === 'Admin') {
            $stmt = $this->db->prepare("SELECT * FROM tbl_users WHERE root = ? ORDER BY user_type, fullname");
            $stmt->execute([$userId]);
            $subUsers = $stmt->fetchAll();
            $hierarchy = array_merge($hierarchy, $subUsers);
        }
        
        return $hierarchy;
    }
}
