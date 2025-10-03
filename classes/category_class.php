<?php
require_once dirname(__FILE__) . '/../settings/db_class.php';

/**
 * Category Class - Handles all category database operations
 * Extends the database connection class
 */
class Category extends db_connection {
    
    /**
     * Add a new category to the database
     * @param int $user_id - The user ID who owns the category
     * @param string $category_name - The name of the category
     * @return bool - True on success, false on failure
     */
    public function addCategory($user_id, $category_name) {
        try {
            // Check if category name already exists for this user
            $check_sql = "SELECT category_id FROM categories WHERE category_name = ? AND user_id = ?";
            $check_stmt = $this->db_conn()->prepare($check_sql);
            $check_stmt->execute([$category_name, $user_id]);
            
            if ($check_stmt->rowCount() > 0) {
                return false; // Category already exists
            }
            
            // Insert new category
            $sql = "INSERT INTO categories (user_id, category_name, date_created) VALUES (?, ?, NOW())";
            $stmt = $this->db_conn()->prepare($sql);
            $result = $stmt->execute([$user_id, $category_name]);
            
            return $result;
        } catch (PDOException $e) {
            error_log("Add Category Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all categories for a specific user
     * @param int $user_id - The user ID
     * @return array - Array of categories or empty array on failure
     */
    public function getCategoriesByUser($user_id) {
        try {
            $sql = "SELECT category_id, category_name, date_created 
                    FROM categories 
                    WHERE user_id = ? 
                    ORDER BY date_created DESC";
            $stmt = $this->db_conn()->prepare($sql);
            $stmt->execute([$user_id]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get Categories Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get a single category by ID
     * @param int $category_id - The category ID
     * @param int $user_id - Optional user ID for ownership verification
     * @return array|false - Category data or false on failure
     */
    public function getCategoryById($category_id, $user_id = null) {
        try {
            $sql = "SELECT category_id, category_name, date_created, user_id 
                    FROM categories 
                    WHERE category_id = ?";
            $params = [$category_id];
            
            // If user_id is provided, add it to the query for security
            if ($user_id !== null) {
                $sql .= " AND user_id = ?";
                $params[] = $user_id;
            }
            
            $stmt = $this->db_conn()->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get Category Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update a category name
     * @param int $category_id - The category ID to update
     * @param int $user_id - The user ID who owns the category
     * @param string $category_name - The new category name
     * @return bool - True on success, false on failure
     */
    public function updateCategory($category_id, $user_id, $category_name) {
        try {
            // Check if the new category name already exists for this user (excluding current category)
            $check_sql = "SELECT category_id FROM categories 
                         WHERE category_name = ? AND user_id = ? AND category_id != ?";
            $check_stmt = $this->db_conn()->prepare($check_sql);
            $check_stmt->execute([$category_name, $user_id, $category_id]);
            
            if ($check_stmt->rowCount() > 0) {
                return false; // Category name already exists
            }
            
            // Update the category
            $sql = "UPDATE categories 
                    SET category_name = ? 
                    WHERE category_id = ? AND user_id = ?";
            $stmt = $this->db_conn()->prepare($sql);
            $result = $stmt->execute([$category_name, $category_id, $user_id]);
            
            return $result && $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Update Category Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete a category
     * @param int $category_id - The category ID to delete
     * @param int $user_id - The user ID who owns the category
     * @return bool - True on success, false on failure
     */
    public function deleteCategory($category_id, $user_id) {
        try {
            // First check if category exists and belongs to user
            $check_sql = "SELECT category_id FROM categories WHERE category_id = ? AND user_id = ?";
            $check_stmt = $this->db_conn()->prepare($check_sql);
            $check_stmt->execute([$category_id, $user_id]);
            
            if ($check_stmt->rowCount() === 0) {
                return false; // Category doesn't exist or doesn't belong to user
            }
            
            // Delete the category
            $sql = "DELETE FROM categories WHERE category_id = ? AND user_id = ?";
            $stmt = $this->db_conn()->prepare($sql);
            $result = $stmt->execute([$category_id, $user_id]);
            
            return $result && $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Delete Category Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Check if category name exists for a user
     * @param string $category_name - The category name to check
     * @param int $user_id - The user ID
     * @param int $exclude_id - Category ID to exclude from check (for updates)
     * @return bool - True if exists, false otherwise
     */
    public function categoryNameExists($category_name, $user_id, $exclude_id = null) {
        try {
            $sql = "SELECT category_id FROM categories WHERE category_name = ? AND user_id = ?";
            $params = [$category_name, $user_id];
            
            if ($exclude_id !== null) {
                $sql .= " AND category_id != ?";
                $params[] = $exclude_id;
            }
            
            $stmt = $this->db_conn()->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Category Name Check Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get category count for a user
     * @param int $user_id - The user ID
     * @return int - Number of categories
     */
    public function getCategoryCount($user_id) {
        try {
            $sql = "SELECT COUNT(*) as count FROM categories WHERE user_id = ?";
            $stmt = $this->db_conn()->prepare($sql);
            $stmt->execute([$user_id]);
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? (int)$result['count'] : 0;
        } catch (PDOException $e) {
            error_log("Get Category Count Error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get categories with pagination
     * @param int $user_id - The user ID
     * @param int $limit - Number of records per page
     * @param int $offset - Starting record number
     * @return array - Array of categories
     */
    public function getCategoriesWithPagination($user_id, $limit = 10, $offset = 0) {
        try {
            $sql = "SELECT category_id, category_name, date_created 
                    FROM categories 
                    WHERE user_id = ? 
                    ORDER BY date_created DESC 
                    LIMIT ? OFFSET ?";
            $stmt = $this->db_conn()->prepare($sql);
            $stmt->execute([$user_id, $limit, $offset]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get Categories with Pagination Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Search categories by name for a user
     * @param int $user_id - The user ID
     * @param string $search_term - The search term
     * @return array - Array of matching categories
     */
    public function searchCategories($user_id, $search_term) {
        try {
            $sql = "SELECT category_id, category_name, date_created 
                    FROM categories 
                    WHERE user_id = ? AND category_name LIKE ? 
                    ORDER BY category_name ASC";
            $stmt = $this->db_conn()->prepare($sql);
            $search_term = '%' . $search_term . '%';
            $stmt->execute([$user_id, $search_term]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Search Categories Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get categories sorted by name
     * @param int $user_id - The user ID
     * @param string $order - ASC or DESC
     * @return array - Sorted array of categories
     */
    public function getCategoriesSorted($user_id, $order = 'ASC') {
        try {
            $order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';
            $sql = "SELECT category_id, category_name, date_created 
                    FROM categories 
                    WHERE user_id = ? 
                    ORDER BY category_name $order";
            $stmt = $this->db_conn()->prepare($sql);
            $stmt->execute([$user_id]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get Categories Sorted Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get recently added categories
     * @param int $user_id - The user ID
     * @param int $limit - Number of recent categories to fetch
     * @return array - Array of recent categories
     */
    public function getRecentCategories($user_id, $limit = 5) {
        try {
            $sql = "SELECT category_id, category_name, date_created 
                    FROM categories 
                    WHERE user_id = ? 
                    ORDER BY date_created DESC 
                    LIMIT ?";
            $stmt = $this->db_conn()->prepare($sql);
            $stmt->execute([$user_id, $limit]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get Recent Categories Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Bulk delete categories
     * @param array $category_ids - Array of category IDs to delete
     * @param int $user_id - The user ID
     * @return bool - True if all deleted successfully
     */
    public function bulkDeleteCategories($category_ids, $user_id) {
        try {
            if (empty($category_ids) || !is_array($category_ids)) {
                return false;
            }
            
            // Create placeholders for the IN clause
            $placeholders = str_repeat('?,', count($category_ids) - 1) . '?';
            
            $sql = "DELETE FROM categories 
                    WHERE category_id IN ($placeholders) AND user_id = ?";
            
            $params = array_merge($category_ids, [$user_id]);
            $stmt = $this->db_conn()->prepare($sql);
            $result = $stmt->execute($params);
            
            return $result && $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Bulk Delete Categories Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get category statistics for a user
     * @param int $user_id - The user ID
     * @return array - Statistics about categories
     */
    public function getCategoryStats($user_id) {
        try {
            $sql = "SELECT 
                        COUNT(*) as total_categories,
                        MIN(date_created) as first_category_date,
                        MAX(date_created) as latest_category_date
                    FROM categories 
                    WHERE user_id = ?";
            $stmt = $this->db_conn()->prepare($sql);
            $stmt->execute([$user_id]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get Category Stats Error: " . $e->getMessage());
            return [
                'total_categories' => 0,
                'first_category_date' => null,
                'latest_category_date' => null
            ];
        }
    }
}
