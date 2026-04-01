<?php

require_once 'BaseController.php';

class DashboardController extends BaseController {
    
    public function index() {
        $this->auth->requireLogin();
        
        $user = $this->auth->getCurrentUser();
        
        $stats = [];
        
        try {
            
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM tbl_users");
            $stats['total_users'] = $stmt->fetch()['total'];
            
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM tbl_plans");
            $stats['total_plans'] = $stmt->fetch()['total'];
            
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM tbl_user_recharges WHERE status = 'active'");
            $stats['active_recharges'] = $stmt->fetch()['total'];
            
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM tbl_voucher");
            $stats['total_vouchers'] = $stmt->fetch()['total'];
            
        } catch (PDOException $e) {
            $stats = [
                'total_users' => 0,
                'total_plans' => 0,
                'active_recharges' => 0,
                'total_vouchers' => 0
            ];
        }
        
        $this->render('dashboard/index', [
            'user' => $user,
            'stats' => $stats
        ]);
    }
}
