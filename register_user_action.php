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

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$phone_number = trim($_POST['phone_number'] ?? '');
$role = $_POST['role'] ?? '2'; // default role

// Basic validation
if (empty($name) || empty($email) || empty($password) || empty($phone_number) || empty($role)) {
    $response['status'] = 'error';
    $response['message'] = 'All fields are required';
    echo json_encode($response);
    exit();
}

// Email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $response['status'] = 'error';
    $response['message'] = 'Invalid email format';
    echo json_encode($response);
    exit();
}

// Phone number format (7–15 digits)
if (!preg_match("/^[0-9]{7,15}$/", $phone_number)) {
    $response['status'] = 'error';
    $response['message'] = 'Invalid phone number format';
    echo json_encode($response);
    exit();
}

// Password policy: at least 6 chars, one lowercase, one uppercase, one number
if (
    strlen($password) < 6 ||
    !preg_match("/[a-z]/", $password) ||
    !preg_match("/[A-Z]/", $password) ||
    !preg_match("/[0-9]/", $password)
) {
    $response['status'] = 'error';
    $response['message'] = 'Password must be at least 6 characters and contain lowercase, uppercase, and a number';
    echo json_encode($response);
    exit();
}

// Hash password
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// Register user
$user_id = register_user_ctr($name, $email, $hashed_password, $phone_number, $role);

if ($user_id) {
    $response['status'] = 'success';
    $response['message'] = 'Registered successfully';
    $response['user_id'] = $user_id;
} else {
    $response['status'] = 'error';
    $response['message'] = 'Failed to register. Email may already exist.';
}

echo json_encode($response);
