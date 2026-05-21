<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Materials extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('user_id')) {
            redirect('auth');
        }
        $this->load->helper('text');
    }

    public function index() {
        $data['materials'] = $this->db->select('materials.*, users.name as dosen_name')
                                      ->from('materials')
                                      ->join('users', 'users.id = materials.dosen_id')
                                      ->order_by('materials.created_at', 'DESC')
                                      ->get()->result();
        
        $this->load->view('materials/index', $data);
    }

    public function upload() {
        if ($this->session->userdata('role') != 'dosen') {
            redirect('materials');
        }

        $config['upload_path']          = './uploads/materials/';
        $config['allowed_types']        = 'pdf|doc|docx|ppt|pptx|zip';
        $config['max_size']             = 10000; // 10MB
        $config['encrypt_name']         = TRUE;

        if (!is_dir('./uploads/materials/')) {
            mkdir('./uploads/materials/', 0777, TRUE);
        }

        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('material_file')) {
            $this->session->set_flashdata('error', $this->upload->display_errors());
        } else {
            $data = $this->upload->data();
            $insert = [
                'title' => $this->input->post('title'),
                'description' => $this->input->post('description'),
                'file_path' => $data['file_name'],
                'dosen_id' => $this->session->userdata('user_id')
            ];
            $this->db->insert('materials', $insert);
            $this->session->set_flashdata('success', 'Materi berhasil diunggah!');
        }
        redirect('materials');
    }

    public function delete($id) {
        if ($this->session->userdata('role') != 'dosen') {
            redirect('materials');
        }
        
        $material = $this->db->get_where('materials', ['id' => $id, 'dosen_id' => $this->session->userdata('user_id')])->row();
        if ($material) {
            unlink('./uploads/materials/' . $material->file_path);
            $this->db->delete('materials', ['id' => $id]);
            $this->session->set_flashdata('success', 'Materi berhasil dihapus!');
        }
        redirect('materials');
    }
}
