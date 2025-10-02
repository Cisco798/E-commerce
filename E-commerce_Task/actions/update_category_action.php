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
    
    // Receive data from the category update form
    $category_id = trim($_POST['category_id'] ?? '');
    $category_name = trim($_POST['category_name'] ?? '');

    // Basic validation
    if (empty($category_id)) {
        $response['status'] = 'error';
        $response['message'] = 'Category ID is required';
        echo json_encode($response);
        exit();
    }

    if (empty($category_name)) {
        $response['status'] = 'error';
        $response['message'] = 'Category name is required';
        echo json_encode($response);
        exit();
    }

    // Validate category ID is numeric
    if (!is_numeric($category_id)) {
        $response['status'] = 'error';
        $response['message'] = 'Invalid category ID format';
        echo json_encode($response);
        exit();
    }

    $category_id = (int)$category_id;

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

    // Check if category exists and belongs to the user using controller function
    $existing_category = get_category_by_id_ctr($category_id, $user_id);
    if (!$existing_category) {
        $response['status'] = 'error';
        $response['message'] = 'Category not found or you do not have permission to edit it';
        echo json_encode($response);
        exit();
    }

    // Store original name for response
    $original_name = $existing_category['category_name'];

    // Validate category data using controller validation function
    $validation_errors = validate_category_data($category_name, $user_id, $category_id);
    if (!empty($validation_errors)) {
        $response['status'] = 'error';
        $response['message'] = implode(', ', $validation_errors);
        $response['validation_errors'] = $validation_errors;
        echo json_encode($response);
        exit();
    }

    // Check if the name is actually different (no need to update if same)
    if (strtolower(trim($original_name)) === strtolower(trim($category_name))) {
        $response['status'] = 'info';
        $response['message'] = 'No changes detected. Category name remains the same.';
        $response['category_id'] = $category_id;
        $response['category_name'] = htmlspecialchars($category_name, ENT_QUOTES, 'UTF-8');
        echo json_encode($response);
        exit();
    }

    // Invoke the relevant controller function to update the category
    $result = update_category_ctr($category_id, $user_id, $category_name);

    // Return message to the caller
    if ($result) {
        $response['status'] = 'success';
        $response['message'] = 'Category updated successfully';
        $response['category_id'] = $category_id;
        $response['category_name'] = htmlspecialchars($category_name, ENT_QUOTES, 'UTF-8');
        $response['original_name'] = htmlspecialchars($original_name, ENT_QUOTES, 'UTF-8');
        $response['updated_at'] = date('Y-m-d H:i:s');
        
        // Log successful update
        error_log("Category updated successfully: ID={$category_id}, User={$user_id}, Old='{$original_name}', New='{$category_name}'");
        
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Failed to update category. The category name might already exist or there was a database error.';
        $response['category_id'] = $category_id;
        
        // Log failed update
        error_log("Category update failed: ID={$category_id}, User={$user_id}, Name='{$category_name}'");
    }

} catch (Exception $e) {
    error_log("Update Category Action Error: " . $e->getMessage());
    $response['status'] = 'error';
    $response['message'] = 'An unexpected error occurred while updating the category. Please try again later.';
    
    // In development, you might want to include the actual error
    if (defined('DEBUG') && DEBUG === true) {
        $response['debug_error'] = $e->getMessage();
        $response['debug_trace'] = $e->getTraceAsString();
    }
}

echo json_encode($response);
?>
