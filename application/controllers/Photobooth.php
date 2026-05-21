<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Photobooth extends CI_Controller {
    public function index() {
        $this->load->view('photobooth/index');
    }

    public function upload() {
        header('Content-Type: application/json');
        
        $img = $this->input->post('image');
        if (empty($img)) {
            echo json_encode(['success' => false, 'error' => 'No image data received']);
            return;
        }

        // Clean base64
        $img_clean = $img;
        $img_clean = str_replace('data:image/png;base64,', '', $img_clean);
        $img_clean = str_replace('data:image/jpeg;base64,', '', $img_clean);
        $img_clean = str_replace(' ', '+', $img_clean);
        $data = base64_decode($img_clean);

        $phone = $this->input->post('phone');

        if (!is_dir('./uploads/photobooth')) {
            mkdir('./uploads/photobooth', 0777, true);
        }

        $fileName = 'photo_' . time() . '_' . rand(100,999) . '.png';
        $file = './uploads/photobooth/' . $fileName;
        $success = file_put_contents($file, $data);

        if (!$success) {
            echo json_encode(['success' => false, 'error' => 'Failed to save image file.']);
            return;
        }

        // Save to database
        $this->db->insert('photobooth_images', ['file_path' => $fileName]);
        
        // Generate download link (auto-download saat diklik)
        $download_url = base_url('photobooth/download/' . $fileName);

        if (!empty($phone)) {
            // Clean phone number (remove non-digits, replace leading 0 with 62)
            $phone = preg_replace('/[^0-9]/', '', $phone);
            if (str_starts_with($phone, '0')) {
                $phone = '62' . substr($phone, 1);
            } elseif (!str_starts_with($phone, '62')) {
                $phone = '62' . $phone;
            }

            // Ambil token fonnte dari database
            $this->db->where('setting_key', 'fonnte_token');
            $token_setting = $this->db->get('settings')->row();
            $fonnte_token = $token_setting ? $token_setting->setting_value : '';

            if (!empty($fonnte_token)) {
                $curl = curl_init();
                
                // Kirim pesan dengan link download foto
                $pesan  = "📸 *Photobooth Jurusan Sistem Informasi*\n\n";
                $pesan .= "Halo! Ini adalah foto kenang-kenangan Anda.\n\n";
                $pesan .= "📥 *Klik link di bawah untuk download foto Anda:*\n";
                $pesan .= $download_url . "\n\n";
                $pesan .= "Terima kasih! 🎓";

                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://api.fonnte.com/send',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => array(
                        'target' => $phone,
                        'message' => $pesan,
                        'countryCode' => '62',
                    ),
                    CURLOPT_HTTPHEADER => array(
                        "Authorization: $fonnte_token"
                    ),
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);
                $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
                curl_close($curl);
                
                if ($err) {
                    echo json_encode([
                        'success' => true, 
                        'sent' => false,
                        'curl_error' => $err, 
                        'url' => $download_url
                    ]);
                    return;
                }

                $decoded = json_decode($response, true);
                echo json_encode([
                    'success' => true, 
                    'sent' => (isset($decoded['status']) && $decoded['status'] == true),
                    'fonnte_response' => $decoded, 
                    'url' => $download_url,
                    'http_code' => $http_code
                ]);
                return;
            }
        }

        // Jika tidak ada nomor atau token kosong
        echo json_encode(['success' => true, 'sent' => false, 'url' => $download_url]);
    }

    // Auto-download foto saat link diklik
    public function download($filename) {
        $file_path = './uploads/photobooth/' . basename($filename);
        
        if (!file_exists($file_path)) {
            show_404();
            return;
        }

        // Force download
        header('Content-Description: File Transfer');
        header('Content-Type: image/png');
        header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
        header('Content-Length: ' . filesize($file_path));
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        
        readfile($file_path);
        exit;
    }

    public function delete($id) {
        // Cek login admin
        if ($this->session->userdata('role') !== 'admin') {
            redirect('auth');
        }

        $this->db->where('id', $id);
        $image = $this->db->get('photobooth_images')->row();

        if ($image) {
            $file_path = './uploads/photobooth/' . $image->file_path;
            if (file_exists($file_path)) {
                unlink($file_path);
            }
            $this->db->where('id', $id);
            $this->db->delete('photobooth_images');
            $this->session->set_flashdata('success', 'Foto berhasil dihapus.');
        }

        redirect('dashboard');
    }
}
