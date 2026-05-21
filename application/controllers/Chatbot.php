<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Chatbot extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->model('Setting_model');
    }

    public function reply() {
        header('Content-Type: application/json');
        $msg = $this->input->post('message');
        
        // Ambil Gemini API Key dari database
        $settings = $this->Setting_model->get_all();
        $gemini_key = isset($settings['gemini_api_key']) ? $settings['gemini_api_key'] : '';

        if (!empty($gemini_key)) {
            $role = $this->session->userdata('role') ? $this->session->userdata('role') : 'guest';
            $reply = $this->_call_gemini($msg, $gemini_key, $role);
            if ($reply) {
                echo json_encode(['reply' => $reply]);
                return;
            }
        }

        // Fallback ke logika manual jika Gemini gagal atau key kosong
        $msg_lower = strtolower($msg);
        $reply = "Maaf, saya tidak mengerti. Silakan tanyakan seputar pendaftaran, dosen, atau pengumpulan tugas.";
        
        if (strpos($msg_lower, 'tugas') !== false || strpos($msg_lower, 'kumpul') !== false) {
            $reply = "Untuk pengumpulan tugas, silakan login sebagai Mahasiswa dan masuk ke menu Dashboard.";
        } elseif (strpos($msg_lower, 'dosen') !== false) {
            $reply = "Dosen dapat login untuk memberikan tugas, materi, dan melakukan penilaian mahasiswa.";
        } elseif (strpos($msg_lower, 'photobooth') !== false || strpos($msg_lower, 'foto') !== false) {
            $reply = "Fitur photobooth memungkinkan Anda mengambil foto langsung dan mengirimnya ke WhatsApp. Coba sekarang di menu Photobooth!";
        } elseif (strpos($msg_lower, 'halo') !== false || strpos($msg_lower, 'hai') !== false || strpos($msg_lower, 'p ') === 0) {
            $reply = "Halo! Saya Asisten - SI, asisten virtual Sistem Informasi. Ada yang bisa saya bantu?";
        } elseif (strpos($msg_lower, 'admin') !== false) {
            $reply = "Admin dapat mengelola konten website dan memantau aktivitas akademik melalui Dashboard Admin.";
        } elseif (strpos($msg_lower, 'sistem informasi') !== false || strpos($msg_lower, 'prodi') !== false || strpos($msg_lower, 'jurusan') !== false) {
            $reply = "Prodi Sistem Informasi berfokus pada integrasi teknologi informasi dengan proses bisnis. Di sini Anda akan belajar pemrograman, basis data, hingga manajemen proyek TI.";
        }

        echo json_encode(['reply' => $reply]);
    }

    private function _call_gemini($msg, $key, $role) {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-flash-latest:generateContent?key=" . $key;
        
        // Sesuaikan fitur berdasarkan Role
        $features = "1. portal login, 2. photobooth digital, 3. pengumpulantugas";
        if ($role == 'dosen') {
            $features = "1. portal login, 2. photobooth digital, 3. membuat tugas";
        } elseif ($role == 'admin') {
            $features = "1. portal login, 2. photobooth digital, 3. pengumpulantugas, 4. membuat tugas, 5. manajemen settings";
        }

        $system_instruction = "Anda adalah Asisten - SI, asisten virtual untuk Jurusan Sistem Informasi. 
        Tugas Anda adalah membantu pengguna (Role: $role) terkait informasi akademik. 
        Fitur yang tersedia: $features.
        Jawablah dengan sopan, informatif, dan sangat ringkas dalam Bahasa Indonesia.";

        $data = [
            "contents" => [
                [
                    "role" => "user",
                    "parts" => [
                        ["text" => "System Instruction: " . $system_instruction . "\n\nUser Question: " . $msg]
                    ]
                ]
            ],
            "generationConfig" => [
                "temperature" => 0.7,
                "topK" => 40,
                "topP" => 0.95,
                "maxOutputTokens" => 1024,
            ]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) return false;

        $result = json_decode($response, true);
        if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            return $result['candidates'][0]['content']['parts'][0]['text'];
        }

        return false;
    }
}
