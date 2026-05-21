<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Notification_model extends CI_Model {
    public function get_user_notifications($user_id) {
        $this->db->where('user_id', $user_id);
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get('notifications')->result();
    }

    public function insert($data) {
        return $this->db->insert('notifications', $data);
    }

    public function mark_as_read($id) {
        $this->db->where('id', $id);
        $this->db->update('notifications', ['is_read' => 1]);
    }
}
