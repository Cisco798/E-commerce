<?php
header('Content-Type: application/json');
session_start();

$response = array();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $response['status'] = 'error';
    $response['message'] = 'You must be logged in to perform this action';
    $response['categories'] = [];
    $response['count'] = 0;
    echo json_encode($response);
    exit();
}

// Check if user is admin
$user_role = $_SESSION['role'] ?? $_SESSION['user_role'] ?? null;
if ($user_role != 1 && strtolower($user_role) !== 'admin') {
    $response['status'] = 'error';
    $response['message'] = 'Access denied. Admin privileges required';
    $response['categories'] = [];
    $response['count'] = 0;
    echo json_encode($response);
    exit();
}

require_once '../controllers/category_controller.php';

try {
    $user_id = $_SESSION['user_id'];
    
    // Fetch categories for the logged-in user using the controller function
    $categories = get_categories_by_user_ctr($user_id);
    
    if ($categories !== false) {
        // Format the categories data for frontend consumption
        $formatted_categories = [];
        foreach ($categories as $index => $category) {
            $formatted_categories[] = [
                'category_id' => (int)$category['category_id'],
                'category_name' => htmlspecialchars($category['category_name'], ENT_QUOTES, 'UTF-8'),
                'date_created' => $category['date_created'],
                'formatted_date' => date('M d, Y', strtotime($category['date_created'])),
                'formatted_time' => date('M d, Y g:i A', strtotime($category['date_created'])),
                'time_ago' => timeAgoHelper($category['date_created']),
                'row_number' => $index + 1
            ];
        }
        
        // Get category count using controller function
        $total_count = get_category_count_ctr($user_id);
        
        $response['status'] = 'success';
        $response['message'] = 'Categories fetched successfully';
        $response['categories'] = $formatted_categories;
        $response['count'] = count($formatted_categories);
        $response['total_count'] = $total_count;
        $response['user_id'] = $user_id;
        
        // Add some metadata
        $response['metadata'] = [
            'fetch_time' => date('Y-m-d H:i:s'),
            'has_categories' => count($formatted_categories) > 0,
            'is_empty' => count($formatted_categories) === 0
        ];
        
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Failed to fetch categories from database';
        $response['categories'] = [];
        $response['count'] = 0;
        $response['total_count'] = 0;
    }

} catch (Exception $e) {
    error_log("Fetch Categories Action Error: " . $e->getMessage());
    $response['status'] = 'error';
    $response['message'] = 'An error occurred while fetching categories. Please try again later.';
    $response['categories'] = [];
    $response['count'] = 0;
    $response['total_count'] = 0;
    
    // In development, you might want to include the actual error
    if (defined('DEBUG') && DEBUG === true) {
        $response['debug_error'] = $e->getMessage();
    }
}

/**
 * Helper function to calculate time ago
 * @param string $datetime - The datetime string
 * @return string - Human readable time difference
 */
function timeAgoHelper($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'just now';
    if ($time < 3600) return floor($time/60) . ' min ago';
    if ($time < 86400) return floor($time/3600) . ' hr ago';
    if ($time < 2592000) return floor($time/86400) . ' days ago';
    if ($time < 31536000) return floor($time/2592000) . ' months ago';
    
    return floor($time/31536000) . ' years ago';
}

echo json_encode($response);
?>
