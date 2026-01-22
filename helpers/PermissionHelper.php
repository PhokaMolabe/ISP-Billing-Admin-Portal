<?php

require_once 'config/config.php';

class PermissionHelper {
    
    /**
     * Check if user can edit a plan
     */
    public static function canEditPlan($user, $plan, $db = null) {
        // SuperAdmin, Admin, and Sales can edit any plan
        if ($user['user_type'] === USER_TYPE_SUPERADMIN || 
            $user['user_type'] === USER_TYPE_ADMIN || 
            $user['user_type'] === USER_TYPE_SALES) {
            return true;
        }
        
        // Agent can only edit enabled plans
        if ($user['user_type'] === USER_TYPE_AGENT) {
            return $plan['enabled'];
        }
        
        return false;
    }
    
    /**
     * Check if user can edit another user
     */
    public static function canEditUser($currentUser, $targetUser) {
        // SuperAdmin can edit any user
        if ($currentUser['user_type'] === USER_TYPE_SUPERADMIN) {
            return true;
        }
        
        // Admin and Sales can edit Admin, Agent, and Sales users (but not SuperAdmin)
        if ($currentUser['user_type'] === USER_TYPE_ADMIN) {
            return $targetUser['user_type'] !== USER_TYPE_SUPERADMIN;
        }
        
        // Agent can edit Sales users under their tree OR themselves
        if ($currentUser['user_type'] === USER_TYPE_AGENT) {
            return ($targetUser['user_type'] === USER_TYPE_SALES &&
                   $targetUser['root'] == $currentUser['id']) ||
                   $targetUser['id'] == $currentUser['id'];
        }

        // Sales can edit only themselves
        if ($currentUser['user_type'] === USER_TYPE_SALES) {
           return  $targetUser['id'] == $currentUser['id'];
        }
        
        return false;
    }
    
    /**
     * Generic permission requirement
     */
    public static function requirePermission($condition, $message = 'You do not have permission to access this page') {
        if (!$condition) {
            http_response_code(403);
            die($message);
        }
    }
    
    /**
     * Require plan edit permission
     */
    public static function requirePlanEditPermission($user, $plan, $db = null) {
        self::requirePermission(
            self::canEditPlan($user, $plan, $db),
            'You do not have permission to access this page'
        );
    }
    
    /**
     * Require user edit permission
     */
    public static function requireUserEditPermission($currentUser, $targetUser) {
        self::requirePermission(
            self::canEditUser($currentUser, $targetUser),
            'You do not have permission to access this page'
        );
    }
    
    /**
     * Check if a resource belongs to the user's tree
     */
    public static function isInUserTree($userId, $resource, $db = null) {
        // Check if the resource belongs to the user's tree
        // For plans, we need to check if any recharge records link to users in the tree
        
        if (!$db) {
            $database = new Database();
            $db = $database->connect();
        }
        
        // Get the user's agent root
        $stmt = $db->prepare("SELECT root FROM tbl_users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        if (!$user || !$user['root']) {
            return false;
        }
        
        // Get all users in the agent's tree (including the agent)
        $stmt = $db->prepare("
            SELECT id FROM tbl_users 
            WHERE id = ? OR root = ? OR (root IN (SELECT id FROM tbl_users WHERE root = ?))
        ");
        $stmt->execute([$user['root'], $user['root'], $user['root']]);
        $treeUsers = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        
        if (empty($treeUsers)) {
            return false;
        }
        
        // Check if there are any recharge records for users in this tree for this plan
        $placeholders = str_repeat('?,', count($treeUsers) - 1) . '?';
        $stmt = $db->prepare("
            SELECT COUNT(*) as count 
            FROM tbl_user_recharges
            WHERE plan_id = ? AND customer_id IN ($placeholders)
        ");
        
        $params = array_merge([$resource['id']], $treeUsers);
        $stmt->execute($params);
        $result = $stmt->fetch();
        
        return $result['count'] > 0;
    }
}
