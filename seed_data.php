<?php
require_once 'config/database.php';
require_once 'config/config.php';

try {
    $database = new Database();
    $conn = $database->connect();
    
    if (!$conn) {
        throw new Exception("Database connection failed");
    }
    
    $conn->beginTransaction();
    
    // Clear existing data
    $conn->exec("DELETE FROM tbl_voucher");
    $conn->exec("DELETE FROM tbl_user_recharges");
    $conn->exec("DELETE FROM tbl_plans");
    $conn->exec("DELETE FROM tbl_users");
    
    // Insert users as per requirements
    $users = [
        [
            'username' => 'superadmin',
            'password_hash' => password_hash('admin123', PASSWORD_BCRYPT),
            'fullname' => 'Super Administrator',
            'phone' => '+1234567890',
            'email' => 'superadmin@isp.com',
            'user_type' => USER_TYPE_SUPERADMIN,
            'root' => null,
            'status' => 'active'
        ],
        [
            'username' => 'admin',
            'password_hash' => password_hash('admin123', PASSWORD_BCRYPT),
            'fullname' => 'System Administrator',
            'phone' => '+1234567891',
            'email' => 'admin@isp.com',
            'user_type' => USER_TYPE_ADMIN,
            'root' => null,
            'status' => 'active'
        ],
        [
            'username' => 'agent1',
            'password_hash' => password_hash('agent123', PASSWORD_BCRYPT),
            'fullname' => 'Agent One',
            'phone' => '+1234567892',
            'email' => 'agent1@isp.com',
            'user_type' => USER_TYPE_AGENT,
            'root' => null,
            'status' => 'active'
        ],
        [
            'username' => 'agent2',
            'password_hash' => password_hash('agent123', PASSWORD_BCRYPT),
            'fullname' => 'Agent Two',
            'phone' => '+1234567893',
            'email' => 'agent2@isp.com',
            'user_type' => USER_TYPE_AGENT,
            'root' => null,
            'status' => 'active'
        ],
        [
            'username' => 'sales1',
            'password_hash' => password_hash('sales123', PASSWORD_BCRYPT),
            'fullname' => 'Sales User One',
            'phone' => '+1234567893',
            'email' => 'sales1@isp.com',
            'user_type' => USER_TYPE_SALES,
            'root' => null, // Will be updated after agent1 gets ID
            'status' => 'active'
        ],
        [
            'username' => 'sales2',
            'password_hash' => password_hash('sales123', PASSWORD_BCRYPT),
            'fullname' => 'Sales User Two',
            'phone' => '+1234567894',
            'email' => 'sales2@isp.com',
            'user_type' => USER_TYPE_SALES,
            'root' => null, // Will be updated after agent1 gets ID
            'status' => 'active'
        ],
        [
            'username' => 'sales3',
            'password_hash' => password_hash('sales123', PASSWORD_BCRYPT),
            'fullname' => 'Sales User Three',
            'phone' => '+1234567895',
            'email' => 'sales3@isp.com',
            'user_type' => USER_TYPE_SALES,
            'root' => null, // Will be updated after agent2 gets ID
            'status' => 'active'
        ]
    ];
    
    $userIds = [];
    foreach ($users as $user) {
        $stmt = $conn->prepare("
            INSERT INTO tbl_users (username, password_hash, fullname, phone, email, user_type, root, status)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $user['username'],
            $user['password_hash'],
            $user['fullname'],
            $user['phone'],
            $user['email'],
            $user['user_type'],
            $user['root'],
            $user['status']
        ]);
        $userIds[$user['username']] = $conn->lastInsertId();
    }
    
    // Update sales users with correct agent ID
    $agent1Id = $userIds['agent1'];
    $agent2Id = $userIds['agent2'];
    
    // Assign sales1 and sales2 to agent1
    $stmt = $conn->prepare("UPDATE tbl_users SET root = ? WHERE username IN ('sales1', 'sales2')");
    $stmt->execute([$agent1Id]);
    
    // Assign sales3 to agent2
    $stmt = $conn->prepare("UPDATE tbl_users SET root = ? WHERE username = 'sales3'");
    $stmt->execute([$agent2Id]);
    
    // Insert 3 plans with mixed enabled and type values as per requirements
    $plans = [
        [
            'name_plan' => 'Basic Prepaid Plan',
            'price' => 29.99,
            'type' => 'prepaid',
            'is_radius' => false,
            'enabled' => true
        ],
        [
            'name_plan' => 'Premium Postpaid Plan',
            'price' => 79.99,
            'type' => 'postpaid',
            'is_radius' => true,
            'enabled' => true
        ],
        [
            'name_plan' => 'Disabled Trial Plan',
            'price' => 0.00,
            'type' => 'prepaid',
            'is_radius' => false,
            'enabled' => false
        ]
    ];
    
    $planIds = [];
    foreach ($plans as $plan) {
        $stmt = $conn->prepare("
            INSERT INTO tbl_plans (name_plan, price, type, is_radius, enabled)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $plan['name_plan'],
            $plan['price'],
            $plan['type'],
            $plan['is_radius'],
            $plan['enabled']
        ]);
        $planIds[] = $conn->lastInsertId();
    }
    
    // Insert sample recharges for testing permissions
    $recharges = [
        [
            'customer_id' => $userIds['sales1'],
            'plan_id' => $planIds[0], // Basic Prepaid Plan (enabled)
            'status' => 'active',
            'recharged_on' => date('Y-m-d'),
            'recharged_time' => date('H:i:s'),
            'expiration' => date('Y-m-d', strtotime('+30 days')),
            'time' => date('H:i:s')
        ],
        [
            'customer_id' => $userIds['sales2'],
            'plan_id' => $planIds[1], // Premium Postpaid Plan (enabled)
            'status' => 'active',
            'recharged_on' => date('Y-m-d', strtotime('-7 days')),
            'recharged_time' => date('H:i:s'),
            'expiration' => date('Y-m-d', strtotime('+23 days')),
            'time' => date('H:i:s')
        ],
        [
            'customer_id' => $userIds['sales3'],
            'plan_id' => $planIds[0], // Basic Prepaid Plan (enabled) - for testing agent2 tree
            'status' => 'active',
            'recharged_on' => date('Y-m-d', strtotime('-3 days')),
            'recharged_time' => date('H:i:s'),
            'expiration' => date('Y-m-d', strtotime('+27 days')),
            'time' => date('H:i:s')
        ],
        [
            'customer_id' => $userIds['sales1'],
            'plan_id' => $planIds[2], // Disabled Trial Plan (disabled) - for testing restrictions
            'status' => 'expired',
            'recharged_on' => date('Y-m-d', strtotime('-30 days')),
            'recharged_time' => date('H:i:s'),
            'expiration' => date('Y-m-d', strtotime('-1 days')),
            'time' => date('H:i:s')
        ]
    ];
    
    foreach ($recharges as $recharge) {
        $stmt = $conn->prepare("
            INSERT INTO tbl_user_recharges (customer_id, plan_id, status, recharged_on, recharged_time, expiration, time)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $recharge['customer_id'],
            $recharge['plan_id'],
            $recharge['status'],
            $recharge['recharged_on'],
            $recharge['recharged_time'],
            $recharge['expiration'],
            $recharge['time']
        ]);
    }
    
    // Insert sample vouchers
    $vouchers = [
        [
            'id_plan' => $planIds[0],
            'code' => 'BASIC2024',
            'status' => 'active',
            'generated_by' => $userIds['admin']
        ],
        [
            'id_plan' => $planIds[1],
            'code' => 'PREMIUM2024',
            'status' => 'active',
            'generated_by' => $userIds['admin']
        ]
    ];
    
    foreach ($vouchers as $voucher) {
        $stmt = $conn->prepare("
            INSERT INTO tbl_voucher (id_plan, code, status, generated_by)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $voucher['id_plan'],
            $voucher['code'],
            $voucher['status'],
            $voucher['generated_by']
        ]);
    }
    
    $conn->commit();
    echo "Database seeded successfully!\n";
    echo "Login credentials:\n";
    echo "SuperAdmin: superadmin / admin123\n";
    echo "Admin: admin / admin123\n";
    echo "Agent1: agent1 / agent123\n";
    echo "Agent2: agent2 / agent123\n";
    echo "Sales1: sales1 / sales123 (under agent1)\n";
    echo "Sales2: sales2 / sales123 (under agent1)\n";
    echo "Sales3: sales3 / sales123 (under agent2)\n";
    echo "\nPermission Testing Scenarios:\n";
    echo "- SuperAdmin and Admin can edit any plan\n";
    echo "- Agent can only edit enabled plans (plans 1 and 2)\n";
    echo "- Sales users can only edit enabled plans they have recharges for\n";
    echo "- Plan 3 (Disabled Trial Plan) should be blocked for Agent and Sales users\n";
    
} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollBack();
    }
    echo "Error seeding database: " . $e->getMessage() . "\n";
}
