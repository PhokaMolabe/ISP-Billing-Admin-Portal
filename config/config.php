<?php

define('APP_URL', 'http://localhost/ISP-Billing-Engine');
define('APP_NAME', 'ISP Billing Engine');
define('SESSION_NAME', 'isp_billing_session');

// CSRF Token settings
define('CSRF_TOKEN_NAME', 'csrf_token');
define('CSRF_TOKEN_EXPIRY', 3600); // 1 hour

// Password settings
define('BCRYPT_COST', 12);

// Memory settings
ini_set('memory_limit', '256M');
ini_set('max_execution_time', 300);

// Error reporting (for debugging)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// User types
define('USER_TYPE_SUPERADMIN', 'SuperAdmin');
define('USER_TYPE_ADMIN', 'Admin');
define('USER_TYPE_AGENT', 'Agent');
define('USER_TYPE_SALES', 'Sales');

// Plan types
define('PLAN_TYPE_PREPAID', 'prepaid');
define('PLAN_TYPE_POSTPAID', 'postpaid');

// Status constants
define('STATUS_ACTIVE', 'active');
define('STATUS_INACTIVE', 'inactive');
define('STATUS_ENABLED', 'enabled');
define('STATUS_DISABLED', 'disabled');
