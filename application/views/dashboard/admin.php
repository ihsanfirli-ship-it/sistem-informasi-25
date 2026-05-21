<?php $this->load->view('layout/header'); ?>

<div class="container" style="margin-top: 30px;">
    <h2>Dashboard Admin</h2>
    <p>Selamat datang, <?= $this->session->userdata('name') ?></p>

    <?php if($this->session->flashdata('success')): ?>
        <div style="background: #D1FAE5; color: #059669; padding: 10px; border-radius: 8px; margin: 20px 0;">
            <?= $this->session->flashdata('success') ?>
        </div>
    <?php endif; ?>

    <div style="display: flex; gap: 30px; margin-top: 30px; flex-wrap: wrap;">
        
        <!-- Statistik Absensi -->
        <div class="glass-panel" style="flex: 1; min-width: 300px;">
            <h3><i class="fas fa-chart-pie" style="color:var(--primary)"></i> Statistik Absensi</h3>
            <div style="margin-top: 20px; height: 300px; display: flex; align-items: center; justify-content: center;">
                <canvas id="attendanceChart"></canvas>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                const ctx = document.getElementById('attendanceChart').getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Dosen', 'Mahasiswa'],
                        datasets: [{
                            data: [<?= $attendance_dosen ?>, <?= $attendance_mhs ?>],
                            backgroundColor: ['#6366F1', '#10B981'],
                            borderWidth: 0,
                            hoverOffset: 10
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20,
                                    font: { family: 'Inter', size: 12 }
                                }
                            }
                        },
                        cutout: '70%'
                    }
                });
            </script>
        </div>

        <!-- Setting Tampilan Front-End -->
        <div class="glass-panel" style="flex: 1; min-width: 300px;">
            <h3><i class="fas fa-desktop" style="color:var(--primary)"></i> Edit Tampilan Front-End</h3>
            <form action="<?= base_url('dashboard/update_settings') ?>" method="POST" style="margin-top: 20px;">
                <div class="form-group">
                    <label style="display:block; margin-bottom: 5px;">Judul Website (Title Bar)</label>
                    <input type="text" name="site_title" class="form-control" value="<?= isset($settings['site_title']) ? $settings['site_title'] : '' ?>">
                </div>
                <div class="form-group">
                    <label style="display:block; margin-bottom: 5px;">Judul Utama Beranda (Hero)</label>
                    <input type="text" name="hero_title" class="form-control" value="<?= isset($settings['hero_title']) ? $settings['hero_title'] : '' ?>">
                </div>
                <div class="form-group">
                    <label style="display:block; margin-bottom: 5px;">Sub Judul Beranda</label>
                    <textarea name="hero_subtitle" class="form-control" rows="3"><?= isset($settings['hero_subtitle']) ? $settings['hero_subtitle'] : '' ?></textarea>
                </div>
                <div class="form-group">
                    <label style="display:block; margin-bottom: 5px;">Token API Fonnte Whatsapp</label>
                    <input type="text" name="fonnte_token" class="form-control" value="<?= isset($settings['fonnte_token']) ? $settings['fonnte_token'] : '' ?>" placeholder="Masukkan Token API Fonnte">
                </div>
                <div class="form-group">
                    <label style="display:block; margin-bottom: 5px;">Gemini AI API Key</label>
                    <input type="text" name="gemini_api_key" class="form-control" value="<?= isset($settings['gemini_api_key']) ? $settings['gemini_api_key'] : '' ?>" placeholder="Masukkan Gemini API Key">
                    <small style="color: #666;">Dapatkan di <a href="https://aistudio.google.com/" target="_blank">Google AI Studio</a></small>
                </div>
                <button type="submit" class="btn"><i class="fas fa-save"></i> Simpan Perubahan</button>
            </form>
        </div>

        <!-- Pantau Perkembangan -->
        <div class="glass-panel" style="flex: 2; min-width: 300px;">
            <h3><i class="fas fa-chart-line" style="color:var(--secondary)"></i> Perkembangan Tugas Kuliah</h3>
            <div style="margin-top: 20px;">
                <p>Admin dapat memantau aktivitas dosen dan tugas yang sedang berlangsung.</p>
                <div style="margin-top: 15px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid #E2E8F0; text-align: left;">
                                <th style="padding: 10px;">Judul Tugas</th>
                                <th style="padding: 10px;">Dosen Pengampu</th>
                                <th style="padding: 10px;">Waktu Dibuat</th>
                                <th style="padding: 10px;">Deadline</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($tasks as $t): ?>
                                <tr style="border-bottom: 1px solid #E2E8F0;">
                                    <td style="padding: 10px;"><?= $t->title ?></td>
                                    <td style="padding: 10px;"><?= $t->dosen_name ?></td>
                                    <td style="padding: 10px;"><?= date('d/m/Y', strtotime($t->created_at)) ?></td>
                                    <td style="padding: 10px;"><?= date('d/m/Y', strtotime($t->deadline)) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

    <!-- Album Photobooth -->
    <div style="margin-top: 30px;">
        <div class="glass-panel">
            <h3><i class="fas fa-camera" style="color:var(--primary)"></i> Galeri Foto Photobooth</h3>
            <p style="margin-top: 10px; color:#475569;">Semua foto yang diambil lewat photobooth akan tampil di sini.</p>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; margin-top: 20px;">
                <?php if(empty($photobooth_images)): ?>
                    <p>Belum ada foto.</p>
                <?php else: ?>
                    <?php foreach($photobooth_images as $img): ?>
                        <div style="border-radius: 8px; overflow: hidden; border: 1px solid #E2E8F0; background: #fff; position: relative;">
                            <a href="<?= base_url('uploads/photobooth/'.$img->file_path) ?>" target="_blank">
                                <img src="<?= base_url('uploads/photobooth/'.$img->file_path) ?>" style="width: 100%; height: auto; display: block; filter: brightness(0.95);">
                            </a>
                            <a href="javascript:void(0);" 
                               onclick="event.stopPropagation(); if(confirm('Hapus foto ini dari galeri dan server?')) window.location.href='<?= site_url('photobooth/delete/'.$img->id) ?>';" 
                               style="position: absolute; top: 10px; right: 10px; background: rgba(239, 68, 68, 0.95); color: white; width: 34px; height: 34px; border-radius: 50%; display: flex; align-items: center; justify-content: center; text-decoration: none; box-shadow: 0 4px 10px rgba(0,0,0,0.3); z-index: 999; border: 2px solid white;" 
                               title="Hapus Foto">
                                <i class="fas fa-trash-alt" style="font-size: 1em;"></i>
                            </a>
                            <div style="padding: 10px; font-size: 0.8em; text-align: center; color: #64748B;">
                                <?= date('d M Y H:i', strtotime($img->created_at)) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php $this->load->view('layout/footer'); ?>
