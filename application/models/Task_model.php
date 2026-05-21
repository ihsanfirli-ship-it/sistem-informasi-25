<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Task_model extends CI_Model {
    public function get_all_tasks() {
        $this->db->select('tasks.*, users.name as dosen_name');
        $this->db->from('tasks');
        $this->db->join('users', 'users.id = tasks.dosen_id');
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get()->result();
    }

    public function get_tasks_by_dosen($dosen_id) {
        $this->db->where('dosen_id', $dosen_id);
        $this->db->order_by('created_at', 'DESC');
        return $this->db->get('tasks')->result();
    }

    public function insert_task($data) {
        return $this->db->insert('tasks', $data);
    }

    public function get_submissions($task_id) {
        $this->db->select('submissions.*, users.name as mahasiswa_name');
        $this->db->from('submissions');
        $this->db->join('users', 'users.id = submissions.mahasiswa_id');
        $this->db->where('task_id', $task_id);
        return $this->db->get()->result();
    }

    public function insert_submission($data) {
        return $this->db->insert('submissions', $data);
    }
    
    public function update_submission($id, $data) {
        $this->db->where('id', $id);
        return $this->db->update('submissions', $data);
    }

    public function get_submission_by_id($id) {
        return $this->db->get_where('submissions', ['id' => $id])->row();
    }
}
