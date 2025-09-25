<?php
session_start();

//for header redirection
ob_start();

//function to check for login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../Login/login.php");
    exit;
}

//function to get user ID
function getUserId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

//function to check if user is logged in
function isLoggedIn() {
    // Check multiple possible session indicators for compatibility
    return (
        isset($_SESSION['user_id']) || 
        isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'] === true
    );
}

//function to check for role (admin, customer, etc)
function isAdmin() {
    // Check both possible role session variable names for compatibility
    $role = null;
    
    if (isset($_SESSION['user_role'])) {
        $role = $_SESSION['user_role'];
    } elseif (isset($_SESSION['role'])) {
        $role = $_SESSION['role'];
    }
    
    // Check if user has administrative privileges
    // Assuming admin role is represented by value 1 or string 'admin'
    return ($role == 1 || strtolower($role) === 'admin');
}

//function to check if user has elevated permissions (alias for isAdmin)
function hasElevatedPermissions() {
    return isAdmin();
}

//function to get user role
function getUserRole() {
    if (isset($_SESSION['user_role'])) {
        return $_SESSION['user_role'];
    } elseif (isset($_SESSION['role'])) {
        return $_SESSION['role'];
    }
    return null;
}

//function to get user name
function getUserName() {
    return isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 
           (isset($_SESSION['name']) ? $_SESSION['name'] : null);
}

?>
