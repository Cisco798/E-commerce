<?php
header('Content-Type: application/json');
session_start();

$response = array();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $response['status'] = 'error';
    $response['message'] = 'You must be logged in to perform this action';
    echo json_encode($response);
    exit();
}

// Check if user is admin
$user_role = $_SESSION['role'] ?? $_SESSION['user_role'] ?? null;
if ($user_role != 1 && strtolower($user_role) !== 'admin') {
    $response['status'] = 'error';
    $response['message'] = 'Access denied. Admin privileges required';
    echo json_encode($response);
    exit();
}

// Check if request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['status'] = 'error';
    $response['message'] = 'Invalid request method. POST required';
    echo json_encode($response);
    exit();
}

require_once '../controllers/category_controller.php';

try {
    $user_id = $_SESSION['user_id'];
    
    // Receive data from the category creation form
    $category_name = trim($_POST['category_name'] ?? '');

    // Basic validation - check if category name is empty
    if (empty($category_name)) {
        $response['status'] = 'error';
        $response['message'] = 'Category name is required';
        echo json_encode($response);
        exit();
    }

    // Additional validation for category name length
    if (strlen($category_name) < 2) {
        $response['status'] = 'error';
        $response['message'] = 'Category name must be at least 2 characters long';
        echo json_encode($response);
        exit();
    }

    if (strlen($category_name) > 100) {
        $response['status'] = 'error';
        $response['message'] = 'Category name cannot exceed 100 characters';
        echo json_encode($response);
        exit();
    }

    // Validate category name format (only letters, numbers, spaces, hyphens, underscores)
    if (!preg_match('/^[a-zA-Z0-9\s\-_]+$/', $category_name)) {
        $response['status'] = 'error';
        $response['message'] = 'Category name can only contain letters, numbers, spaces, hyphens, and underscores';
        echo json_encode($response);
        exit();
    }

    // Validate category data using controller validation function
    $validation_errors = validate_category_data($category_name, $user_id);
    if (!empty($validation_errors)) {
        $response['status'] = 'error';
        $response['message'] = implode(', ', $validation_errors);
        $response['validation_errors'] = $validation_errors;
        echo json_encode($response);
        exit();
    }

    // Invoke the relevant controller function to add the category
    $result = add_category_ctr($user_id, $category_name);

    // Return message to the caller
    if ($result) {
        $response['status'] = 'success';
        $response['message'] = 'Category added successfully';
        $response['category_name'] = htmlspecialchars($category_name, ENT_QUOTES, 'UTF-8');
        $response['user_id'] = $user_id;
        $response['created_at'] = date('Y-m-d H:i:s');
        
        // Log successful addition
        error_log("Category added successfully: User={$user_id}, Name='{$category_name}'");
        
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Failed to add category. Category name might already exist or there was a database error.';
        
        // Log failed addition
        error_log("Category addition failed: User={$user_id}, Name='{$category_name}'");
    }

} catch (Exception $e) {
    error_log("Add Category Action Error: " . $e->getMessage());
    $response['status'] = 'error';
    $response['message'] = 'An unexpected error occurred while adding the category. Please try again later.';
    
    // In development, you might want to include the actual error
    if (defined('DEBUG') && DEBUG === true) {
        $response['debug_error'] = $e->getMessage();
        $response['debug_trace'] = $e->getTraceAsString();
    }
}

echo json_encode($response);
?>
