<?php

require_once '../classes/user_class.php';


function register_user_ctr($full_name, $email, $hashed_password, $contact_number, $country, $city, $user_role)
{
    // Log the parameters received for debugging
    error_log("register_user_ctr called with 7 parameters");
    error_log("Parameters: full_name={$full_name}, email={$email}, contact={$contact_number}, country={$country}, city={$city}, role={$user_role}");
    
    try {
        $user = new User();
        $user_id = $user->createUser($full_name, $email, $hashed_password, $contact_number, $country, $city, $user_role);
        
        if ($user_id) {
            error_log("User created successfully with ID: " . $user_id);
            return $user_id;
        }
        
        error_log("User creation failed - createUser returned false");
        return false;
        
    } catch (Exception $e) {
        error_log("Error in register_user_ctr: " . $e->getMessage());
        return false;
    }
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
