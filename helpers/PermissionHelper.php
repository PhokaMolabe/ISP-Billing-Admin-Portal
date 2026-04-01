<?php

require_once 'config/config.php';

class PermissionHelper {

    public static function canEditPlan($user, $plan, $db = null) {
        
        if ($user['user_type'] === USER_TYPE_SUPERADMIN || 
            $user['user_type'] === USER_TYPE_ADMIN || 
            $user['user_type'] === USER_TYPE_SALES) {
            return true;
        }
        
        if ($user['user_type'] === USER_TYPE_AGENT) {
            return $plan['enabled'];
        }
        
        return false;
    }
    
 
    public static function canEditUser($currentUser, $targetUser) {
        
        if ($currentUser['user_type'] === USER_TYPE_SUPERADMIN) {
            return true;
        }
        
        if ($currentUser['user_type'] === USER_TYPE_ADMIN) {
            return $targetUser['user_type'] !== USER_TYPE_SUPERADMIN;
        }
        
      
        if ($currentUser['user_type'] === USER_TYPE_AGENT) {
            return ($targetUser['user_type'] === USER_TYPE_SALES &&
                   $targetUser['root'] == $currentUser['id']) ||
                   $targetUser['id'] == $currentUser['id'];
        }

        if ($currentUser['user_type'] === USER_TYPE_SALES) {
           return  $targetUser['id'] == $currentUser['id'];
        }
        
        return false;
    }
    
    public static function requirePermission($condition, $message = 'You do not have permission to access this page') {
        if (!$condition) {
            http_response_code(403);
            die($message);
        }
    }
    
    public static function requirePlanEditPermission($user, $plan, $db = null) {
        self::requirePermission(
            self::canEditPlan($user, $plan, $db),
            'You do not have permission to access this page'
        );
    }
    
    public static function requireUserEditPermission($currentUser, $targetUser) {
        self::requirePermission(
            self::canEditUser($currentUser, $targetUser),
            'You do not have permission to access this page'
        );
    }
    
    
    public static function isInUserTree($userId, $resource, $db = null) {
        
        if (!$db) {
            $database = new Database();
            $db = $database->connect();
        }
        
        $stmt = $db->prepare("SELECT root FROM tbl_users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        if (!$user || !$user['root']) {
            return false;
        }
        
        $stmt = $db->prepare("
            SELECT id FROM tbl_users 
            WHERE id = ? OR root = ? OR (root IN (SELECT id FROM tbl_users WHERE root = ?))
        ");
        $stmt->execute([$user['root'], $user['root'], $user['root']]);
        $treeUsers = $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
        
        if (empty($treeUsers)) {
            return false;
        }
        
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
