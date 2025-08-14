<?php
class User extends ADODB_Active_Record {
    public $_table = 'users';
    public $_primarykey = 'id';
}

class UserModel {
    private $db;
    public function __construct($db) {
        $this->db = $db;
    }

    // REGISTER
    public function register($data) {
        $check = new User();
        if ($check->load('email = ?', [$email])) {
            return false; // User or email exists
        }
        if ($check->Load('username = ?', [$data['username']])) {
            return false; // Username already taken
        }
        $user = new User();
        $user->username = $data['username'];
        $user->email = $data['email'];
        $user->password = $password = password_hash($data['password'], PASSWORD_DEFAULT);
        return $user->Save();; // User registered successfully
    }

    // LOGIN
    public function login($username, $password) {
    $user = new User();
    // Try to load user by username
    if ($user->Load('username = ?', [$username])) {
        // Verify password
        if (password_verify($password, $user->password)) {
            return $user; // Return the loaded User object
        }
    }
    return false;
    }

    // GET USER BY ID
    public function getUserById($id) {
        $user = new User();
        if ($user->load('id = ?', [$id])) {
            return $user;
        }
        return false; // User not found
    }
}