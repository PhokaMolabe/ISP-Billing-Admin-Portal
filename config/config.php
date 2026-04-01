<?php

define('APP_URL', 'http://localhost/ISP-Billing-Engine');
define('APP_NAME', 'ISP Billing Engine');
define('SESSION_NAME', 'isp_billing_session');

define('CSRF_TOKEN_NAME', 'csrf_token');
define('CSRF_TOKEN_EXPIRY', 3600); // 1 hour

define('BCRYPT_COST', 12);

ini_set('memory_limit', '256M');
ini_set('max_execution_time', 300);

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('USER_TYPE_SUPERADMIN', 'SuperAdmin');
define('USER_TYPE_ADMIN', 'Admin');
define('USER_TYPE_AGENT', 'Agent');
define('USER_TYPE_SALES', 'Sales');

define('PLAN_TYPE_PREPAID', 'prepaid');
define('PLAN_TYPE_POSTPAID', 'postpaid');

define('STATUS_ACTIVE', 'active');
define('STATUS_INACTIVE', 'inactive');
define('STATUS_ENABLED', 'enabled');
define('STATUS_DISABLED', 'disabled');
