<?php

require_once 'BaseController.php';

class DashboardController extends BaseController {
    
    public function index() {
        $this->auth->requireLogin();
        
        $user = $this->auth->getCurrentUser();
        
        // Get dashboard statistics
        $stats = [];
        
        try {
            // Total users
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM tbl_users");
            $stats['total_users'] = $stmt->fetch()['total'];
            
            // Total plans
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM tbl_plans");
            $stats['total_plans'] = $stmt->fetch()['total'];
            
            // Active recharges
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM tbl_user_recharges WHERE status = 'active'");
            $stats['active_recharges'] = $stmt->fetch()['total'];
            
            // Total vouchers
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
