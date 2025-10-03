<?php
header('Content-Type: application/json');
session_start();

$response = array();

// Check if already logged in
if (isset($_SESSION['user_id'])) {
    $response['status'] = 'error';
    $response['message'] = 'You are already logged in';
    echo json_encode($response);
    exit();
}

require_once '../controllers/user_controller.php';

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Basic validation
if (empty($email) || empty($password)) {
    $response['status'] = 'error';
    $response['message'] = 'Email and password are required';
    echo json_encode($response);
    exit();
}

// Email format validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['status'] = 'error';
    $response['message'] = 'Invalid email format';
    echo json_encode($response);
    exit();
}

// Use login function from controller
$login_result = login_user_ctr($email, $password);

if ($login_result) {
    // Set session variables
    $_SESSION['user_id'] = $login_result['user_id'];
    $_SESSION['user_role'] = $login_result['role'];
    $_SESSION['user_name'] = $login_result['name'];
    $_SESSION['user_email'] = $login_result['email'];
    $_SESSION['user_phone'] = $login_result['phone_number'];
    $_SESSION['login_time'] = date('Y-m-d H:i:s');
    $_SESSION['is_logged_in'] = true;
    
    // Regenerate session ID for security
    session_regenerate_id(true);
    
    $response['status'] = 'success';
    $response['message'] = 'Login successful';
    $response['user_id'] = $_SESSION['user_id'];
    $response['user_name'] = $_SESSION['user_name'];
    $response['user_role'] = $_SESSION['user_role'];
    
} else {
    $response['status'] = 'error';
    $response['message'] = 'Invalid email or password';
}

echo json_encode($response);
?>
