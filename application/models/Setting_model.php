<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting_model extends CI_Model {
    public function get_all() {
        $query = $this->db->get('settings');
        $result = [];
        foreach($query->result() as $row) {
            $result[$row->setting_key] = $row->setting_value;
        }
        return $result;
    }

    public function update($key, $value) {
        $this->db->where('setting_key', $key);
        $this->db->update('settings', ['setting_value' => $value]);
    }
}
