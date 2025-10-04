<?php

require_once '../settings/db_class.php';

/**
 * 
 */
class User extends db_connection
{
    private $user_id;
    private $name;
    private $email;
    private $password;
    private $role;
    private $date_created;
    private $phone_number;
    private $country;
    private $city;

    public function __construct($user_id = null)
    {
        parent::db_connect();
        if ($user_id) {
            $this->user_id = $user_id;
            $this->loadUser();
        }
    }

    private function loadUser($user_id = null)
    {
        if ($user_id) {
            $this->user_id = $user_id;
        }
        if (!$this->user_id) {
            return false;
        }
        $stmt = $this->db->prepare("SELECT * FROM customer WHERE customer_id = ?");
        $stmt->bind_param("i", $this->user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        if ($result) {
            $this->name = $result['customer_name'];
            $this->email = $result['customer_email'];
            $this->role = $result['user_role'];
            $this->date_created = isset($result['date_created']) ? $result['date_created'] : null;
            $this->phone_number = $result['customer_contact'];
            $this->country = isset($result['customer_country']) ? $result['customer_country'] : null;
            $this->city = isset($result['customer_city']) ? $result['customer_city'] : null;
        }
    }

    public function createUser($full_name, $email, $hashed_password, $contact_number, $country, $city, $user_role)
    {
        // Password is already hashed from the action file, so use it directly
        $stmt = $this->db->prepare("INSERT INTO customer (customer_name, customer_email, customer_pass, customer_contact, customer_country, customer_city, user_role) VALUES (?, ?, ?, ?, ?, ?, ?)");
        
        // Ensure user_role is an integer
        $user_role = (int)$user_role;
        
        $stmt->bind_param("ssssssi", $full_name, $email, $hashed_password, $contact_number, $country, $city, $user_role);
        
        if ($stmt->execute()) {
            $insert_id = $this->db->insert_id;
            error_log("User created successfully with ID: " . $insert_id);
            return $insert_id;
        } else {
            error_log("Database error in createUser: " . $stmt->error);
            return false;
        }
    }

    public function getUserByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM customer WHERE customer_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Get customer by email and verify password
     * @param string $email Customer email address
     * @param string $password Plain text password to verify
     * @return array|false Returns customer data if authentication successful, false otherwise
     */
    public function getCustomerByEmailAndPassword($email, $password)
    {
        // Get customer by email
        $stmt = $this->db->prepare("SELECT customer_id, customer_name, customer_email, customer_pass, customer_contact, customer_country, customer_city, user_role, date_created FROM customer WHERE customer_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        
        // Check if customer exists and password matches
        if ($result && password_verify($password, $result['customer_pass'])) {
            // Return customer data with consistent field names for session
            return array(
                'user_id' => $result['customer_id'],
                'name' => $result['customer_name'],
                'email' => $result['customer_email'],
                'phone_number' => $result['customer_contact'],
                'country' => $result['customer_country'] ?? null,
                'city' => $result['customer_city'] ?? null,
                'role' => $result['user_role'],
                'date_created' => $result['date_created'] ?? null,
                'password' => $result['customer_pass'] // Keep for compatibility
            );
        }
        
        // Return false if customer not found or password doesn't match
        return false;
    }

}