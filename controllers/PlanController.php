<?php

require_once 'BaseController.php';

class PlanController extends BaseController {
    
    public function list() {
        $this->auth->requireLogin();
        
        $user = $this->auth->getCurrentUser();
        
        try {
            $stmt = $this->db->query("SELECT * FROM tbl_plans ORDER BY name_plan");
            $plans = $stmt->fetchAll();
        } catch (PDOException $e) {
            $plans = [];
        }
        
        $this->render('plan/list', [
            'user' => $user,
            'plans' => $plans
        ]);
    }
    
    public function edit($planId = null) {
        $this->auth->requireLogin();
        
        $user = $this->auth->getCurrentUser();
        
        if (!$planId) {
            http_response_code(403);
            die('You do not have permission to access this page');
        }
        
        try {
            $stmt = $this->db->prepare("SELECT * FROM tbl_plans WHERE id = ?");
            $stmt->execute([$planId]);
            $plan = $stmt->fetch();
            
            if (!$plan) {
                http_response_code(403);
                die('You do not have permission to access this page');
            }
            
            // Check permission with exact denial message
            PermissionHelper::requirePlanEditPermission($user, $plan, $this->db);
            
            // Get associated recharge details for this plan
            if ($user['user_type'] === USER_TYPE_SALES) {
                // Sales users can only see recharges from their tree
                $stmt = $this->db->prepare("
                    SELECT r.*, u.fullname as customer_name, u.username as customer_username
                    FROM tbl_user_recharges r
                    LEFT JOIN tbl_users u ON r.customer_id = u.id
                    WHERE r.plan_id = ? AND r.customer_id IN (
                        SELECT id FROM tbl_users 
                        WHERE id = ? OR root = ? OR (root IN (SELECT id FROM tbl_users WHERE root = ?))
                    )
                    ORDER BY r.recharged_on DESC, r.recharged_time DESC
                    LIMIT 10
                ");
                $stmt->execute([$planId, $user['root'], $user['root'], $user['root']]);
            } else {
                // SuperAdmin, Admin, Agent can see all recharges
                $stmt = $this->db->prepare("
                    SELECT r.*, u.fullname as customer_name, u.username as customer_username
                    FROM tbl_user_recharges r
                    LEFT JOIN tbl_users u ON r.customer_id = u.id
                    WHERE r.plan_id = ?
                    ORDER BY r.recharged_on DESC, r.recharged_time DESC
                    LIMIT 10
                ");
                $stmt->execute([$planId]);
            }
            $recharges = $stmt->fetchAll();
            
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'permission') !== false) {
                die($e->getMessage());
            }
            die('You do not have permission to access this page');
        }
        
        $this->render('plan/edit', [
            'user' => $user,
            'plan' => $plan,
            'recharges' => $recharges,
            'csrf_field' => CSRFHelper::getHiddenField()
        ]);
    }
    
    public function editPost($planId = null) {
        $this->auth->requireLogin();
        CSRFHelper::requireValidToken();
        
        $user = $this->auth->getCurrentUser();
        
        if (!$planId) {
            http_response_code(403);
            die('You do not have permission to access this page');
        }
        
        try {
            $stmt = $this->db->prepare("SELECT * FROM tbl_plans WHERE id = ?");
            $stmt->execute([$planId]);
            $plan = $stmt->fetch();
            
            if (!$plan) {
                http_response_code(403);
                die('You do not have permission to access this page');
            }
            
            // Re-check permission on POST with exact denial message
            PermissionHelper::requirePlanEditPermission($user, $plan, $this->db);
            
            // Validate input
            $data = $this->sanitizeInput($_POST);
            $errors = [];
            
            if (empty($data['name_plan'])) {
                $errors['name_plan'] = 'Plan name is required';
            }
            
            if (!is_numeric($data['price']) || $data['price'] < 0) {
                $errors['price'] = 'Price must be a valid positive number';
            }
            
            if (!in_array($data['type'], ['prepaid', 'postpaid'])) {
                $errors['type'] = 'Invalid plan type';
            }
            
            if (!empty($errors)) {
                $this->render('plan/edit', [
                    'user' => $user,
                    'plan' => array_merge($plan, $data),
                    'errors' => $errors,
                    'csrf_field' => CSRFHelper::getHiddenField()
                ]);
                return;
            }
            
            // Update plan
            $stmt = $this->db->prepare("
                UPDATE tbl_plans 
                SET name_plan = ?, price = ?, type = ?, is_radius = ?, enabled = ?
                WHERE id = ?
            ");
            
            $stmt->execute([
                $data['name_plan'],
                $data['price'],
                $data['type'],
                isset($data['is_radius']) ? 1 : 0,
                isset($data['enabled']) ? 1 : 0,
                $planId
            ]);
            
            $_SESSION['success'] = 'Plan updated successfully';
            $this->redirect('plan/list');
            
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'permission') !== false) {
                die($e->getMessage());
            }
            die('You do not have permission to access this page');
        }
    }
}
