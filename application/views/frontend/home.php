<?php $this->load->view('layout/header'); ?>

<div class="container" style="text-align: center; margin-top: 50px;">
    <div class="glass-panel" style="padding: 60px 40px; display: inline-block; max-width: 800px; width: 100%;">
        <img src="<?= base_url('assets/img/logo.png') ?>" alt="Logo UNSADA" style="width: 100px; margin-bottom: 20px;">
        <h1 style="font-size: 3rem; color: var(--primary); margin-bottom: 20px; text-shadow: 2px 2px 4px rgba(0,0,0,0.1);">
            <?= isset($settings['hero_title']) ? $settings['hero_title'] : 'Sistem Informasi 2025' ?>
        </h1>
        <p style="font-size: 1.2rem; color: #475569; margin-bottom: 40px;">
            <?= isset($settings['hero_subtitle']) ? $settings['hero_subtitle'] : 'Portal terpadu untuk kebutuhan akademik mahasiswa dan dosen.' ?>
        </p>
        
        <div style="display: flex; justify-content: center; gap: 20px; flex-wrap: wrap;">
            <a href="<?= base_url('auth') ?>" class="btn" style="padding: 15px 30px; font-size: 1.1rem; min-width: 200px;">
                <i class="fas fa-sign-in-alt"></i> Login Portal
            </a>
            <a href="<?= base_url('photobooth') ?>" class="btn btn-secondary" style="padding: 15px 30px; font-size: 1.1rem; min-width: 200px;">
                <i class="fas fa-camera-retro"></i> Photobooth
            </a>
        </div>
    </div>
</div>

<div class="container" style="margin-top: 50px; display: flex; gap: 30px; flex-wrap: wrap;">
    <div class="glass-panel" style="flex: 1; min-width: 300px; text-align: center;">
        <i class="fas fa-book-open" style="font-size: 40px; color: var(--primary); margin-bottom: 15px;"></i>
        <h3>Pengumpulan Tugas</h3>
        <p style="margin-top: 10px; color: #64748B;">Mahasiswa dapat mengumpulkan tugas secara online dengan mudah dan cepat.</p>
    </div>
    <div class="glass-panel" style="flex: 1; min-width: 300px; text-align: center;">
        <i class="fas fa-chalkboard-teacher" style="font-size: 40px; color: var(--secondary); margin-bottom: 15px;"></i>
        <h3>Panel Dosen</h3>
        <p style="margin-top: 10px; color: #64748B;">Dosen dapat memberikan materi, tugas, dan langsung melakukan penilaian.</p>
    </div>
    <div class="glass-panel" style="flex: 1; min-width: 300px; text-align: center;">
        <i class="fas fa-camera" style="font-size: 40px; color: #F59E0B; margin-bottom: 15px;"></i>
        <h3>Photobooth WA</h3>
        <p style="margin-top: 10px; color: #64748B;">Abadikan momen jurusan dan kirim langsung ke WhatsApp Anda.</p>
    </div>
</div>

<?php $this->load->view('layout/footer'); ?>
