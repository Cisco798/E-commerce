<?php

require_once '../classes/user_class.php';


function register_user_ctr($name, $email, $password, $phone_number, $role)
{
    $user = new User();
    $user_id = $user->createUser($name, $email, $password, $phone_number, $role);
    if ($user_id) {
        return $user_id;
    }
    return false;
}

function get_user_by_email_ctr($email)
{
    $user = new User();
    return $user->getUserByEmail($email);
}

function login_user_ctr($email, $password)
{
    $user = new User();
    
    // Get user by email
    $user_data = $user->getUserByEmail($email);
    
    if ($user_data && password_verify($password, $user_data['password'])) {
        // Return user data if login successful
        return $user_data;
    }
    
    // Return false if login failed
    return false;
}
