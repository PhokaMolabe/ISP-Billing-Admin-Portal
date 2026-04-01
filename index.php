<?php
require_once 'config/config.php';
require_once 'config/database.php';
require_once 'helpers/AuthHelper.php';
require_once 'helpers/CSRFHelper.php';
require_once 'helpers/PermissionHelper.php';


session_name(SESSION_NAME);
session_start();


$route = $_GET['_route'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];


if (empty($route)) {
    $route = 'auth/login';
}

$route_parts = explode('/', $route);
$controller_name = $route_parts[0] ?? '';
$action = $route_parts[1] ?? '';
$params = array_slice($route_parts, 2);

$routes = [
    'plan' => [
        'list' => ['PlanController', 'list', 'GET'],
        'edit' => ['PlanController', 'edit', 'GET'],
        'edit-post' => ['PlanController', 'editPost', 'POST']
    ],
    'settings' => [
        'users-list' => ['SettingsController', 'usersList', 'GET'],
        'users-edit' => ['SettingsController', 'editUser', 'GET'],
        'users-edit-post' => ['SettingsController', 'editUserPost', 'POST']
    ],
    'auth' => [
        'login' => ['AuthController', 'login', 'GET'],
        'login-post' => ['AuthController', 'loginPost', 'POST'],
        'logout' => ['AuthController', 'logout', 'GET']
    ],
    'dashboard' => [
        '' => ['DashboardController', 'index', 'GET']
    ]
];

if (!isset($routes[$controller_name])) {
    http_response_code(403);
    die('You do not have permission to access this page');
}

if (!isset($routes[$controller_name][$action])) {
    http_response_code(403);
    die('You do not have permission to access this page');
}

$route_info = $routes[$controller_name][$action];
list($controller_class, $controller_method, $expected_method) = $route_info;

if ($method !== $expected_method) {
    http_response_code(403);
    die('You do not have permission to access this page');
}

if (!empty($params)) {
    foreach ($params as $param) {
        if (!is_numeric($param)) {
            http_response_code(403);
            die('You do not have permission to access this page');
        }
    }
}


$controller_file = "controllers/{$controller_class}.php";
if (!file_exists($controller_file)) {
    http_response_code(403);
    die('You do not have permission to access this page');
}

require_once $controller_file;

if (!class_exists($controller_class)) {
    http_response_code(403);
    die('You do not have permission to access this page');
}

$controller = new $controller_class();

if (!method_exists($controller, $controller_method)) {
    http_response_code(403);
    die('You do not have permission to access this page');
}

call_user_func_array([$controller, $controller_method], $params);
