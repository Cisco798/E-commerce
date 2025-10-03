<?php
require_once dirname(__FILE__) . '/../classes/category_class.php';

/**
 * Add a new category
 * @param int $user_id - The user ID
 * @param string $category_name - The category name
 * @return bool - True on success, false on failure
 */
function add_category_ctr($user_id, $category_name) {
    // Validate inputs
    if (empty($user_id) || empty($category_name)) {
        return false;
    }
    
    // Sanitize category name
    $category_name = trim($category_name);
    
    if (strlen($category_name) === 0 || strlen($category_name) > 100) {
        return false;
    }
    
    // Create category instance and add category
    $category = new Category();
    return $category->addCategory($user_id, $category_name);
}

/**
 * Get all categories for a user
 * @param int $user_id - The user ID
 * @return array - Array of categories or empty array
 */
function get_categories_by_user_ctr($user_id) {
    if (empty($user_id)) {
        return [];
    }
    
    $category = new Category();
    return $category->getCategoriesByUser($user_id);
}

/**
 * Get a single category by ID
 * @param int $category_id - The category ID
 * @param int $user_id - The user ID (optional for security)
 * @return array|bool - Category data or false
 */
function get_category_by_id_ctr($category_id, $user_id = null) {
    if (empty($category_id)) {
        return false;
    }
    
    $category = new Category();
    return $category->getCategoryById($category_id, $user_id);
}

/**
 * Update a category
 * @param int $category_id - The category ID
 * @param int $user_id - The user ID
 * @param string $category_name - The new category name
 * @return bool - True on success, false on failure
 */
function update_category_ctr($category_id, $user_id, $category_name) {
    // Validate inputs
    if (empty($category_id) || empty($user_id) || empty($category_name)) {
        return false;
    }
    
    // Sanitize category name
    $category_name = trim($category_name);
    
    if (strlen($category_name) === 0 || strlen($category_name) > 100) {
        return false;
    }
    
    // Create category instance and update category
    $category = new Category();
    return $category->updateCategory($category_id, $user_id, $category_name);
}

/**
 * Delete a category
 * @param int $category_id - The category ID
 * @param int $user_id - The user ID
 * @return bool - True on success, false on failure
 */
function delete_category_ctr($category_id, $user_id) {
    // Validate inputs
    if (empty($category_id) || empty($user_id)) {
        return false;
    }
    
    // Create category instance and delete category
    $category = new Category();
    return $category->deleteCategory($category_id, $user_id);
}

/**
 * Check if category name exists for a user
 * @param string $category_name - The category name
 * @param int $user_id - The user ID
 * @param int $exclude_id - Category ID to exclude (for updates)
 * @return bool - True if exists, false otherwise
 */
function category_name_exists_ctr($category_name, $user_id, $exclude_id = null) {
    if (empty($category_name) || empty($user_id)) {
        return false;
    }
    
    $category_name = trim($category_name);
    
    $category = new Category();
    return $category->categoryNameExists($category_name, $user_id, $exclude_id);
}

/**
 * Get category count for a user
 * @param int $user_id - The user ID
 * @return int - Number of categories
 */
function get_category_count_ctr($user_id) {
    if (empty($user_id)) {
        return 0;
    }
    
    $category = new Category();
    return $category->getCategoryCount($user_id);
}

/**
 * Validate category data
 * @param string $category_name - The category name
 * @param int $user_id - The user ID
 * @param int $category_id - Category ID (for updates)
 * @return array - Array of error messages
 */
function validate_category_data($category_name, $user_id, $category_id = null) {
    $errors = [];
    
    // Validate category name
    if (empty($category_name)) {
        $errors[] = "Category name is required";
    } elseif (strlen(trim($category_name)) === 0) {
        $errors[] = "Category name cannot be empty";
    } elseif (strlen($category_name) > 100) {
        $errors[] = "Category name cannot exceed 100 characters";
    } elseif (category_name_exists_ctr($category_name, $user_id, $category_id)) {
        $errors[] = "Category name already exists";
    }
    
    // Validate user ID
    if (empty($user_id)) {
        $errors[] = "User ID is required";
    }
    
    return $errors;
}

/**
 * Get categories with additional metadata
 * @param int $user_id - The user ID
 * @return array - Enhanced category data
 */
function get_categories_with_metadata_ctr($user_id) {
    if (empty($user_id)) {
        return [];
    }
    
    $categories = get_categories_by_user_ctr($user_id);
    
    // Add metadata to each category
    foreach ($categories as &$category) {
        $category['formatted_date'] = date('M d, Y', strtotime($category['date_created']));
        $category['time_ago'] = timeAgo($category['date_created']);
    }
    
    return $categories;
}

/**
 * Helper function to calculate time ago
 * @param string $datetime - The datetime string
 * @return string - Human readable time difference
 */
function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'just now';
    if ($time < 3600) return floor($time/60) . ' min ago';
    if ($time < 86400) return floor($time/3600) . ' hr ago';
    if ($time < 2592000) return floor($time/86400) . ' days ago';
    if ($time < 31536000) return floor($time/2592000) . ' months ago';
    
    return floor($time/31536000) . ' years ago';
}

/**
 * Search categories by name
 * @param int $user_id - The user ID
 * @param string $search_term - The search term
 * @return array - Array of matching categories
 */
function search_categories_ctr($user_id, $search_term) {
    if (empty($user_id) || empty($search_term)) {
        return [];
    }
    
    $category = new Category();
    $all_categories = $category->getCategoriesByUser($user_id);
    
    // Filter categories by search term
    $filtered_categories = array_filter($all_categories, function($cat) use ($search_term) {
        return stripos($cat['category_name'], $search_term) !== false;
    });
    
    return array_values($filtered_categories); // Re-index array
}
