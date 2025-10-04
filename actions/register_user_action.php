<?php
// Enable error reporting for debugging (comment out in production)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in JSON response
ini_set('log_errors', 1);

header('Content-Type: application/json');
session_start();

$response = array();

try {
    // Check if the user is already logged in
    if (isset($_SESSION['user_id'])) {
        $response['status'] = 'error';
        $response['message'] = 'You are already logged in';
        echo json_encode($response);
        exit();
    }

    // Check if controller file exists
    $controller_path = '../controllers/user_controller.php';
    if (!file_exists($controller_path)) {
        $response['status'] = 'error';
        $response['message'] = 'System error: Controller file not found';
        error_log("Error: user_controller.php not found at path: " . $controller_path);
        echo json_encode($response);
        exit();
    }

    require_once $controller_path;

    // Check if request method is POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $response['status'] = 'error';
        $response['message'] = 'Invalid request method';
        echo json_encode($response);
        exit();
    }

    // Log incoming POST data for debugging
    error_log("Registration attempt with POST data: " . json_encode(array_keys($_POST)));

    // Get form data with correct field names
    $full_name = isset($_POST['full_name']) ? trim($_POST['full_name']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    $contact_number = isset($_POST['contact_number']) ? trim($_POST['contact_number']) : '';
    $country = isset($_POST['country']) ? trim($_POST['country']) : '';
    $city = isset($_POST['city']) ? trim($_POST['city']) : '';
    $user_role = isset($_POST['user_role']) ? trim($_POST['user_role']) : '';

    // Validate required fields
    if (empty($full_name) || empty($email) || empty($password) || empty($contact_number) || empty($country) || empty($city) || empty($user_role)) {
        $response['status'] = 'error';
        $response['message'] = 'All required fields must be filled';
        
        // Debug: Show which fields are empty
        $empty_fields = array();
        if (empty($full_name)) $empty_fields[] = 'full_name';
        if (empty($email)) $empty_fields[] = 'email';
        if (empty($password)) $empty_fields[] = 'password';
        if (empty($contact_number)) $empty_fields[] = 'contact_number';
        if (empty($country)) $empty_fields[] = 'country';
        if (empty($city)) $empty_fields[] = 'city';
        if (empty($user_role)) $empty_fields[] = 'user_role';
        
        error_log("Registration failed: Empty fields - " . implode(', ', $empty_fields));
        $response['empty_fields'] = $empty_fields; // For debugging
        
        echo json_encode($response);
        exit();
    }

    // Validate full name (only letters and spaces)
    if (!preg_match('/^[a-zA-Z\s]+$/', $full_name)) {
        $response['status'] = 'error';
        $response['message'] = 'Full name can only contain letters and spaces';
        echo json_encode($response);
        exit();
    }

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['status'] = 'error';
        $response['message'] = 'Invalid email format';
        echo json_encode($response);
        exit();
    }

    // Validate password strength
    if (strlen($password) < 6 || !preg_match('/[a-z]/', $password) || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $response['status'] = 'error';
        $response['message'] = 'Password must be at least 6 characters long and contain at least one lowercase letter, one uppercase letter, and one number';
        echo json_encode($response);
        exit();
    }

    // Validate contact number format
    if (!preg_match('/^[\d\s\-\+\(\)]+$/', $contact_number)) {
        $response['status'] = 'error';
        $response['message'] = 'Invalid contact number format';
        echo json_encode($response);
        exit();
    }

    // Validate user role (should be 1 or 2)
    if (!in_array($user_role, array('1', '2', 1, 2))) {
        $response['status'] = 'error';
        $response['message'] = 'Invalid user role';
        error_log("Invalid user role provided: " . var_export($user_role, true));
        echo json_encode($response);
        exit();
    }

    // Hash password before storing
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    if (!$hashed_password) {
        $response['status'] = 'error';
        $response['message'] = 'Failed to hash password';
        error_log("Password hashing failed for email: " . $email);
        echo json_encode($response);
        exit();
    }

    // Check if register_user_ctr function exists
    if (!function_exists('register_user_ctr')) {
        $response['status'] = 'error';
        $response['message'] = 'System error: Registration function not found';
        error_log("Error: register_user_ctr function not found in user_controller.php");
        echo json_encode($response);
        exit();
    }

    // Log registration attempt
    error_log("Attempting to register user: email={$email}, role={$user_role}");

    // Register user with all fields
    $user_id = register_user_ctr($full_name, $email, $hashed_password, $contact_number, $country, $city, $user_role);

    if ($user_id && $user_id > 0) {
        $response['status'] = 'success';
        $response['message'] = 'Registration successful! Please login to continue.';
        $response['user_id'] = $user_id;
        
        // Log successful registration
        error_log("User registered successfully: user_id={$user_id}, email={$email}");
        
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Failed to register. Email may already exist or there was a database error.';
        
        // Log failed registration
        error_log("Registration failed for email: {$email}. Returned user_id: " . var_export($user_id, true));
    }

} catch (Exception $e) {
    // Catch any unexpected errors
    error_log("Registration Error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    $response['status'] = 'error';
    $response['message'] = 'An unexpected error occurred during registration. Please try again later.';
    
    // Include error details for debugging (comment out in production)
    $response['debug_error'] = $e->getMessage();
    $response['debug_file'] = $e->getFile();
    $response['debug_line'] = $e->getLine();
}

echo json_encode($response);
?>