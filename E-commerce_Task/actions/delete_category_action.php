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
    $response['message'] = 'Invalid request method';
    echo json_encode($response);
    exit();
}

require_once '../controllers/category_controller.php';

try {
    $user_id = $_SESSION['user_id'];
    $category_id = trim($_POST['category_id'] ?? '');

    // Basic validation
    if (empty($category_id)) {
        $response['status'] = 'error';
        $response['message'] = 'Category ID is required';
        echo json_encode($response);
        exit();
    }

    // Validate category ID is numeric
    if (!is_numeric($category_id)) {
        $response['status'] = 'error';
        $response['message'] = 'Invalid category ID';
        echo json_encode($response);
        exit();
    }

    $category_id = (int)$category_id;

    // Check if category exists and belongs to the user
    $existing_category = get_category_by_id_ctr($category_id, $user_id);
    if (!$existing_category) {
        $response['status'] = 'error';
        $response['message'] = 'Category not found or you do not have permission to delete it';
        echo json_encode($response);
        exit();
    }

    // Delete category
    $result = delete_category_ctr($category_id, $user_id);

    if ($result) {
        $response['status'] = 'success';
        $response['message'] = 'Category deleted successfully';
        $response['category_id'] = $category_id;
        $response['category_name'] = htmlspecialchars($existing_category['category_name']);
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Failed to delete category';
    }

} catch (Exception $e) {
    error_log("Delete Category Action Error: " . $e->getMessage());
    $response['status'] = 'error';
    $response['message'] = 'An error occurred while deleting the category';
}

echo json_encode($response);
?>
