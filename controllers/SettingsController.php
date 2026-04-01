<?php

require_once 'BaseController.php';

class SettingsController extends BaseController {
    
    public function editUser($userId = null) {
        $this->auth->requireLogin();
        
        $currentUser = $this->auth->getCurrentUser();
        
        if (!$userId) {
            http_response_code(403);
            die('You do not have permission to access this page');
        }
        
        try {
            $stmt = $this->db->prepare("SELECT * FROM tbl_users WHERE id = ?");
            $stmt->execute([$userId]);
            $targetUser = $stmt->fetch();
            
            if (!$targetUser) {
                http_response_code(403);
                die('You do not have permission to access this page');
            }
            
            PermissionHelper::requireUserEditPermission($currentUser, $targetUser);
          
            $availableAgents = [];
            if ($currentUser['user_type'] === USER_TYPE_SUPERADMIN || $currentUser['user_type'] === USER_TYPE_ADMIN) {
                $stmt = $this->db->prepare("SELECT id, fullname FROM tbl_users WHERE user_type = ? AND status = 'active' ORDER BY fullname");
                $stmt->execute([USER_TYPE_AGENT]);
                $availableAgents = $stmt->fetchAll();
            }
            
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'permission') !== false) {
                die($e->getMessage());
            }
            die('You do not have permission to access this page');
        }
        
        $this->render('settings/edit_user', [
            'user' => $currentUser,
            'targetUser' => $targetUser,
            'availableAgents' => $availableAgents,
            'csrf_field' => CSRFHelper::getHiddenField()
        ]);
    }
    
    public function editUserPost($userId = null) {
        $this->auth->requireLogin();
        CSRFHelper::requireValidToken();
        
        $currentUser = $this->auth->getCurrentUser();
        
        if (!$userId) {
            http_response_code(403);
            die('You do not have permission to access this page');
        }
        
        try {
            $stmt = $this->db->prepare("SELECT * FROM tbl_users WHERE id = ?");
            $stmt->execute([$userId]);
            $targetUser = $stmt->fetch();
            
            if (!$targetUser) {
                http_response_code(403);
                die('You do not have permission to access this page');
            }
            
            PermissionHelper::requireUserEditPermission($currentUser, $targetUser);
            
            $data = $this->sanitizeInput($_POST);
            $errors = [];
            
            if (empty($data['fullname'])) {
                $errors['fullname'] = 'Full name is required';
            }
            
            if (!empty($data['email']) && !$this->validateEmail($data['email'])) {
                $errors['email'] = 'Invalid email address';
            }
            
            if (!empty($data['phone']) && !$this->validatePhone($data['phone'])) {
                $errors['phone'] = 'Invalid phone number';
            }
   
            if (!empty($data['new_password'])) {
                if (strlen($data['new_password']) < 6) {
                    $errors['new_password'] = 'Password must be at least 6 characters';
                }
                
                if ($data['new_password'] !== $data['confirm_password']) {
                    $errors['confirm_password'] = 'Passwords do not match';
                }
            }
            
            if (!empty($errors)) {
            
                $availableAgents = [];
                if ($currentUser['user_type'] === USER_TYPE_SUPERADMIN || $currentUser['user_type'] === USER_TYPE_ADMIN) {
                    $stmt = $this->db->prepare("SELECT id, fullname FROM tbl_users WHERE user_type = ? AND status = 'active' ORDER BY fullname");
                    $stmt->execute([USER_TYPE_AGENT]);
                    $availableAgents = $stmt->fetchAll();
                }
                
                $this->render('settings/edit_user', [
                    'user' => $currentUser,
                    'targetUser' => array_merge($targetUser, $data),
                    'availableAgents' => $availableAgents,
                    'errors' => $errors,
                    'csrf_field' => CSRFHelper::getHiddenField()
                ]);
                return;
            }
            
            $stmt = $this->db->prepare("
                UPDATE tbl_users 
                SET fullname = ?, phone = ?, email = ?, status = ?
                WHERE id = ?
            ");
            
            $stmt->execute([
                $data['fullname'],
                $data['phone'] ?? null,
                $data['email'] ?? null,
                $data['status'] ?? 'active',
                $userId
            ]);

            if (!empty($data['new_password'])) {
                $passwordHash = password_hash($data['new_password'], PASSWORD_BCRYPT);
                $stmt = $this->db->prepare("UPDATE tbl_users SET password_hash = ? WHERE id = ?");
                $stmt->execute([$passwordHash, $userId]);
            }
     
            if (($currentUser['user_type'] === USER_TYPE_SUPERADMIN || $currentUser['user_type'] === USER_TYPE_ADMIN) && 
                isset($data['root'])) {
                $stmt = $this->db->prepare("UPDATE tbl_users SET root = ? WHERE id = ?");
                $stmt->execute([$data['root'] ?: null, $userId]);
            }
            
            $_SESSION['success'] = 'User updated successfully';
            $this->redirect('dashboard');
            
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'permission') !== false) {
                die($e->getMessage());
            }
            die('You do not have permission to access this page');
        }
    }
    
    public function usersList() {
        $this->auth->requireLogin();
        
        $currentUser = $this->auth->getCurrentUser();
        
        // Only SuperAdmin, Admin, Agent, and Sales can access user list
        if ($currentUser['user_type'] !== USER_TYPE_SUPERADMIN && 
            $currentUser['user_type'] !== USER_TYPE_ADMIN && 
            $currentUser['user_type'] !== USER_TYPE_AGENT) {
            http_response_code(403);
            die('You do not have permission to access this page');
        }
        
        try {
           
            $stmt = $this->db->query("SELECT * FROM tbl_users ORDER BY user_type, fullname");
            $users = $stmt->fetchAll();
            
            $stmt = $this->db->prepare("SELECT id, fullname FROM tbl_users WHERE user_type = ? ORDER BY fullname");
            $stmt->execute([USER_TYPE_AGENT]);
            $agents = $stmt->fetchAll();
            
        } catch (Exception $e) {
            $users = [];
            $agents = [];
        }
        
        $this->render('settings/users_list', [
            'user' => $currentUser,
            'users' => $users,
            'agents' => $agents
        ]);
    }
}
