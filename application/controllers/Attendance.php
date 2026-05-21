<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Attendance extends CI_Controller {

    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('user_id')) {
            redirect('auth');
        }
    }

    public function index() {
        $user_id = $this->session->userdata('user_id');
        $role = $this->session->userdata('role');
        $today = date('Y-m-d');
        
        $data = [];

        if ($role == 'admin') {
            // Admin Data: Statistics for Dashboard (Use independent queries)
            $data['total_mhs'] = $this->db->where('role', 'mahasiswa')->count_all_results('users');
            $data['total_dosen'] = $this->db->where('role', 'dosen')->count_all_results('users');
            
            $data['today_hadir'] = $this->db->where('status', 'Hadir')->like('created_at', $today, 'after')->count_all_results('attendance');
            $data['today_izin'] = $this->db->where('status', 'Izin')->like('created_at', $today, 'after')->count_all_results('attendance');
            $data['today_sakit'] = $this->db->where('status', 'Sakit')->like('created_at', $today, 'after')->count_all_results('attendance');
            
            // Recent Submissions (Izin/Sakit) - Fetch first to avoid state issues
            $this->db->select('attendance.*, users.name as user_name');
            $this->db->from('attendance');
            $this->db->join('users', 'users.id = attendance.user_id');
            $this->db->where_in('attendance.status', ['Izin', 'Sakit']);
            $this->db->order_by('attendance.created_at', 'DESC');
            $this->db->limit(5);
            $data['recent_submissions'] = $this->db->get()->result();
        }

        // Build the main attendance list
        $this->db->select('attendance.*, users.name as user_name, users.role as user_role');
        $this->db->from('attendance');
        $this->db->join('users', 'users.id = attendance.user_id');

        if ($role == 'mahasiswa') {
            $this->db->where('attendance.user_id', $user_id);
        } elseif ($role == 'dosen') {
            $this->db->where_in('users.role', ['mahasiswa']);
        } elseif ($role == 'admin') {
            $this->db->where_in('users.role', ['dosen', 'mahasiswa']);
        }

        $data['attendance'] = $this->db->order_by('attendance.created_at', 'DESC')->get()->result();
        $this->load->view('attendance/index', $data);
    }

    public function clock_in() {
        $status = $this->input->post('status');
        $class_info = $this->input->post('class_info');
        $reason = $this->input->post('reason');
        $photo_name = '';

        if (empty($class_info)) {
            echo json_encode(['success' => false, 'error' => 'Nama Matakuliah & Jam wajib diisi']);
            return;
        }

        // Cek apakah sudah absen hari ini untuk matakuliah yang sama
        $today = date('Y-m-d');
        $check = $this->db->get_where('attendance', [
            'user_id' => $this->session->userdata('user_id'),
            'class_info' => $class_info,
            'DATE(created_at)' => $today
        ])->row();

        if ($check) {
            echo json_encode(['success' => false, 'error' => 'Anda sudah melakukan absensi untuk matakuliah dan jam ini hari ini.']);
            return;
        }

        if ($status == 'Hadir') {
            $img = $this->input->post('image');
            if (!$img) {
                echo json_encode(['success' => false, 'error' => 'Foto selfie tidak ditemukan']);
                return;
            }
            $img = str_replace('data:image/png;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $data = base64_decode($img);
            $photo_name = 'selfie_' . time() . '_' . $this->session->userdata('user_id') . '.png';
            $path = './uploads/attendance/' . $photo_name;
            file_put_contents($path, $data);
        } else {
            // Izin atau Sakit - Upload File Surat
            $config['upload_path']   = './uploads/attendance/';
            $config['allowed_types'] = 'gif|jpg|png|jpeg|pdf|doc|docx';
            $config['encrypt_name']  = TRUE;
            $this->load->library('upload', $config);

            if ($this->upload->do_upload('attachment')) {
                $photo_name = $this->upload->data('file_name');
            } else {
                echo json_encode(['success' => false, 'error' => $this->upload->display_errors()]);
                return;
            }
        }
        
        if (!is_dir('./uploads/attendance/')) {
            mkdir('./uploads/attendance/', 0777, TRUE);
        }

        $insert = [
            'user_id' => $this->session->userdata('user_id'),
            'photo' => $photo_name,
            'status' => $status,
            'class_info' => $class_info,
            'reason' => $reason
        ];
        $this->db->insert('attendance', $insert);
        echo json_encode(['success' => true]);
    }
}
