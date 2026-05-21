<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {
    public function __construct() {
        parent::__construct();
        if (!$this->session->userdata('logged_in')) {
            redirect('auth');
        }
        $this->load->model('Task_model');
        $this->load->model('User_model');
        $this->load->model('Setting_model');
        $this->load->model('Notification_model');
    }

    public function index() {
        $role = $this->session->userdata('role');
        $data['role'] = $role;
        $data['notifications'] = $this->Notification_model->get_user_notifications($this->session->userdata('user_id'));
        
        if ($role == 'admin') {
            // Statistik Absensi
            $data['attendance_dosen'] = $this->db->join('users', 'users.id = attendance.user_id')
                                               ->where('users.role', 'dosen')
                                               ->count_all_results('attendance');
                                               
            $data['attendance_mhs'] = $this->db->join('users', 'users.id = attendance.user_id')
                                               ->where('users.role', 'mahasiswa')
                                               ->count_all_results('attendance');

            $data['settings'] = $this->Setting_model->get_all();
            $data['tasks'] = $this->Task_model->get_all_tasks();
            $this->db->order_by('created_at', 'DESC');
            $data['photobooth_images'] = $this->db->get('photobooth_images')->result();
            $this->load->view('dashboard/admin', $data);
        } elseif ($role == 'dosen') {
            $data['tasks'] = $this->Task_model->get_tasks_by_dosen($this->session->userdata('user_id'));
            $this->load->view('dashboard/dosen', $data);
        } elseif ($role == 'mahasiswa') {
            $tasks = $this->Task_model->get_all_tasks();
            foreach ($tasks as &$t) {
                $t->is_submitted = $this->db->get_where('submissions', [
                    'task_id' => $t->id,
                    'mahasiswa_id' => $this->session->userdata('user_id')
                ])->num_rows() > 0;
            }
            $data['tasks'] = $tasks;
            $this->load->view('dashboard/mahasiswa', $data);
        }
    }

    public function create_task() {
        if ($this->session->userdata('role') !== 'dosen') redirect('dashboard');
        $data = [
            'dosen_id' => $this->session->userdata('user_id'),
            'title' => $this->input->post('title'),
            'description' => $this->input->post('description'),
            'deadline' => $this->input->post('deadline')
        ];
        $this->Task_model->insert_task($data);
        
        // Notify all mahasiswa
        $mahasiswa = $this->User_model->get_users_by_role('mahasiswa');
        foreach ($mahasiswa as $m) {
            $this->Notification_model->insert([
                'user_id' => $m->id,
                'message' => 'Tugas baru: ' . $data['title']
            ]);
        }
        $this->session->set_flashdata('success', 'Tugas berhasil ditambahkan');
        redirect('dashboard');
    }

    public function view_task($task_id) {
        $data['submissions'] = $this->Task_model->get_submissions($task_id);
        $data['task_id'] = $task_id;
        $this->load->view('dashboard/view_task', $data);
    }

    public function submit_task() {
        if ($this->session->userdata('role') !== 'mahasiswa') redirect('dashboard');
        $task_id = $this->input->post('task_id');
        $user_id = $this->session->userdata('user_id');

        // CHECK IF ALREADY SUBMITTED
        $existing = $this->db->get_where('submissions', [
            'task_id' => $task_id, 
            'mahasiswa_id' => $user_id
        ])->row();

        if ($existing) {
            $this->session->set_flashdata('error', 'Anda sudah mengumpulkan tugas ini sebelumnya!');
            redirect('dashboard');
            return;
        }
        
        $config['upload_path'] = './uploads/';
        $config['allowed_types'] = 'pdf|doc|docx|zip';
        $this->load->library('upload', $config);
        
        if (!is_dir('uploads')) {
            mkdir('./uploads', 0777, true);
        }

        if ($this->upload->do_upload('file')) {
            $fileData = $this->upload->data();
            $data = [
                'task_id' => $task_id,
                'mahasiswa_id' => $user_id,
                'file_path' => $fileData['file_name']
            ];
            $this->Task_model->insert_submission($data);
            
            // Notify dosen
            $task = $this->db->get_where('tasks', ['id' => $task_id])->row();
            $this->Notification_model->insert([
                'user_id' => $task->dosen_id,
                'message' => 'Pengumpulan baru oleh ' . $this->session->userdata('name') . ' untuk tugas ' . $task->title
            ]);

            $this->session->set_flashdata('success', 'Tugas berhasil dikumpulkan');
        } else {
            $this->session->set_flashdata('error', $this->upload->display_errors());
        }
        redirect('dashboard');
    }

    public function grade_submission() {
        if ($this->session->userdata('role') !== 'dosen') redirect('dashboard');
        
        $submission_id = $this->input->post('submission_id');
        $task_id = $this->input->post('task_id');
        $data = [
            'grade' => $this->input->post('grade'),
            'feedback' => $this->input->post('feedback')
        ];
        $this->Task_model->update_submission($submission_id, $data);
        
        // Notify mahasiswa
        $submission = $this->Task_model->get_submission_by_id($submission_id);
        $this->Notification_model->insert([
            'user_id' => $submission->mahasiswa_id,
            'message' => 'Tugas kamu dinilai. Nilai: ' . $data['grade']
        ]);

        $this->session->set_flashdata('success', 'Nilai berhasil disimpan');
        redirect('dashboard/view_task/'.$task_id);
    }
    
    public function update_settings() {
        if ($this->session->userdata('role') !== 'admin') redirect('dashboard');
        
        $settings = $this->input->post();
        foreach($settings as $key => $val) {
            $this->Setting_model->update($key, $val);
        }
        $this->session->set_flashdata('success', 'Tampilan berhasil diupdate');
        redirect('dashboard');
    }

    public function read_notification($id) {
        $this->Notification_model->mark_as_read($id);
        redirect('dashboard');
    }
}
