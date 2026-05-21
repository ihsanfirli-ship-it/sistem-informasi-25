<?php $this->load->view('layout/header'); ?>

<div class="container py-4">
    <!-- Header Section -->
    <div class="glass-panel mb-5 d-flex justify-content-between align-items-center" style="padding: 2.5rem; border-left: 8px solid var(--primary);">
        <div>
            <h1 class="fw-bold mb-2" style="font-size: 2.5rem; letter-spacing: -1px;">E-Learning <span style="color: var(--primary);">Center</span></h1>
            <p class="text-muted mb-0"><i class="fas fa-book-reader me-2"></i> Akses materi kuliah, modul, dan referensi akademik secara digital.</p>
        </div>
        <?php if($this->session->userdata('role') == 'dosen'): ?>
        <button class="btn shadow-lg px-4 py-3 rounded-pill" onclick="toggleUploadForm()" id="btn-toggle-upload">
            <i class="fas fa-plus-circle me-2"></i>Tambah Materi Baru
        </button>
        <?php endif; ?>
    </div>

    <?php if($this->session->flashdata('success')): ?>
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4 fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> <?= $this->session->flashdata('success') ?>
        </div>
    <?php endif; ?>

    <!-- Lecturer Upload Form (Hidden by default) -->
    <?php if($this->session->userdata('role') == 'dosen'): ?>
    <div id="upload-section" class="glass-panel mb-5" style="display: none; border-top: 5px solid var(--primary);">
        <h3 class="fw-bold mb-4"><i class="fas fa-file-upload text-primary me-2"></i>Unggah Materi Baru</h3>
        <form action="<?= base_url('materials/upload') ?>" method="POST" enctype="multipart/form-data" class="row g-4">
            <div class="col-md-6">
                <div class="form-group mb-0">
                    <label class="fw-bold small text-uppercase text-muted mb-2">Judul Materi</label>
                    <input type="text" name="title" class="form-control" required placeholder="Contoh: Dasar Algoritma Pertemuan 1">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-0">
                    <label class="fw-bold small text-uppercase text-muted mb-2">Pilih File (PDF/PPT/DOC/ZIP)</label>
                    <input type="file" name="material_file" class="form-control" required>
                </div>
            </div>
            <div class="col-12">
                <div class="form-group mb-0">
                    <label class="fw-bold small text-uppercase text-muted mb-2">Deskripsi Ringkas</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Jelaskan isi materi ini agar mahasiswa mudah mengerti..."></textarea>
                </div>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary px-5 py-3 rounded-pill fw-bold">
                    <i class="fas fa-paper-plane me-2"></i>Mulai Publikasikan Materi
                </button>
                <button type="button" class="btn btn-secondary px-4 py-3 rounded-pill ms-2" onclick="toggleUploadForm()" style="background: transparent; color: var(--dark); border: 1px solid #CBD5E1;">Batal</button>
            </div>
        </form>
    </div>
    <?php endif; ?>

    <!-- Materials Grid -->
    <div class="row g-4">
        <?php if(empty($materials)): ?>
            <div class="col-12 text-center py-5 glass-panel">
                <div class="mb-4">
                    <i class="fas fa-box-open fa-5x" style="color: #E2E8F0;"></i>
                </div>
                <h4 class="fw-bold text-muted">Belum ada materi yang tersedia</h4>
                <p class="text-muted">Materi yang diunggah dosen akan muncul di sini secara otomatis.</p>
            </div>
        <?php else: ?>
            <?php foreach($materials as $row): ?>
            <div class="col-md-6 col-lg-4">
                <div class="glass-panel h-100 material-card" style="padding: 1.5rem; transition: 0.3s; position: relative; overflow: hidden;">
                    <!-- Floating File Icon Decoration -->
                    <i class="fas <?= (strpos($row->file_path, '.pdf') !== false) ? 'fa-file-pdf' : 'fa-file-powerpoint' ?> position-absolute" style="font-size: 8rem; right: -20px; bottom: -20px; opacity: 0.05; transform: rotate(-15deg);"></i>
                    
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="badge rounded-pill bg-primary px-3 py-2" style="font-size: 0.75rem;">
                            <i class="far fa-clock me-1"></i> <?= date('d M Y', strtotime($row->created_at)) ?>
                        </span>
                        <?php if($this->session->userdata('role') == 'dosen' && $row->dosen_id == $this->session->userdata('user_id')): ?>
                            <a href="<?= base_url('materials/delete/'.$row->id) ?>" class="text-danger" onclick="return confirm('Hapus materi ini?')">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        <?php endif; ?>
                    </div>

                    <h4 class="fw-bold mb-2 text-dark" style="line-height: 1.3;"><?= $row->title ?></h4>
                    <p class="text-muted small mb-4" style="min-height: 40px;"><?= character_limiter($row->description, 80) ?></p>
                    
                    <div class="d-flex align-items-center p-3 rounded-4 bg-white bg-opacity-50 border border-white mb-4">
                        <div class="avatar bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; font-weight: bold;">
                            <?= substr($row->dosen_name, 0, 1) ?>
                        </div>
                        <div>
                            <p class="mb-0 fw-bold small text-dark"><?= $row->dosen_name ?></p>
                            <p class="mb-0 text-muted small">Dosen Pengampu</p>
                        </div>
                    </div>

                    <a href="<?= base_url('uploads/materials/'.$row->file_path) ?>" class="btn w-100 py-3 rounded-4 shadow-sm" target="_blank">
                        <i class="fas fa-download me-2"></i>Download Materi
                    </a>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleUploadForm() {
    var section = document.getElementById('upload-section');
    var btn = document.getElementById('btn-toggle-upload');
    if (section.style.display === 'none') {
        $(section).fadeIn();
        btn.innerHTML = '<i class="fas fa-times-circle me-2"></i>Tutup Form';
        btn.classList.add('btn-secondary');
    } else {
        $(section).fadeOut();
        btn.innerHTML = '<i class="fas fa-plus-circle me-2"></i>Tambah Materi Baru';
        btn.classList.remove('btn-secondary');
    }
}
</script>

<style>
.material-card:hover { 
    transform: translateY(-10px); 
    box-shadow: 0 15px 40px rgba(79, 70, 229, 0.15) !important;
    border-color: var(--primary) !important;
}
.btn-secondary:hover {
    background: #e2e8f0 !important;
}
</style>

<?php $this->load->view('layout/footer'); ?>
