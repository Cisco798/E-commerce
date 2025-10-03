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
    
    // Use the new method that includes password verification
    $user_data = $user->getCustomerByEmailAndPassword($email, $password);
    
    if ($user_data) {
        // Return user data if authentication successful
        return $user_data;
    }
    
    // Return false if login failed
    return false;
}
