<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {
    public function login($username, $password) {
        $this->db->where('username', $username);
        $user = $this->db->get('users')->row();

        // Normally we use password_verify, but for simplicity here we check direct (or user might need generic hashing)
        // Adjust based on how we insert. Our SQL inserts plain text for demo.
        if ($user && $user->password === $password) {
            return $user;
        }
        return false;
    }

    public function get_users_by_role($role) {
        $this->db->where('role', $role);
        return $this->db->get('users')->result();
    }
}
