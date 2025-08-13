<?php
class UserModel {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    // REGISTER
    public function register($data) {
        if ($this->db->GetOne("SELECT id FROM users WHERE username = ? OR email = ?", [$data['username'], $data['email']])) {
            return false; // User or email exists
        }
        $sql = "INSERT INTO users (username, password, email, role) VALUES (?, ?, ?, 'attendee')";
        $password = password_hash($data['password'], PASSWORD_DEFAULT);
        return $this->db->Execute($sql, [$data['username'], $password, $data['email']]);
    }

    // LOGIN
    public function login($username, $password) {
        $sql = "SELECT * FROM users WHERE username = ?";
        $user = $this->db->GetRow($sql, [$username]);
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];
            return $user;
        }
        return false;
    }

    // GET USER BY ID
    public function getUserById($id) {
        $sql = "SELECT id, username, email, role FROM users WHERE id = ?";
        return $this->db->GetRow($sql, [$id]);
    }
}