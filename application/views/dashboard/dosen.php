<?php $this->load->view('layout/header'); ?>

<div class="container" style="margin-top: 30px;">
    <h2>Dashboard Dosen</h2>
    <p>Selamat datang, <?= $this->session->userdata('name') ?></p>

    <?php if($this->session->flashdata('success')): ?>
        <div style="background: #D1FAE5; color: #059669; padding: 10px; border-radius: 8px; margin: 20px 0;">
            <?= $this->session->flashdata('success') ?>
        </div>
    <?php endif; ?>

    <div style="display: flex; gap: 30px; margin-top: 30px; flex-wrap: wrap;">
        
        <!-- Form Tambah Tugas -->
        <div class="glass-panel" style="flex: 1; min-width: 300px;">
            <h3><i class="fas fa-plus-circle" style="color:var(--primary)"></i> Tambah Tugas Baru</h3>
            <form action="<?= base_url('dashboard/create_task') ?>" method="POST" style="margin-top: 20px;">
                <div class="form-group">
                    <label style="display:block; margin-bottom: 5px;">Judul Tugas</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="form-group">
                    <label style="display:block; margin-bottom: 5px;">Deskripsi / Instruksi</label>
                    <textarea name="description" class="form-control" rows="4" required></textarea>
                </div>
                <div class="form-group">
                    <label style="display:block; margin-bottom: 5px;">Deadline</label>
                    <input type="datetime-local" name="deadline" class="form-control" required>
                </div>
                <button type="submit" class="btn"><i class="fas fa-save"></i> Simpan Tugas</button>
            </form>
        </div>

        <!-- Daftar Tugas -->
        <div class="glass-panel" style="flex: 2; min-width: 300px;">
            <h3>Tugas yang Diberikan</h3>
            <div style="margin-top: 20px;">
                <?php if(empty($tasks)): ?>
                    <p>Belum ada tugas.</p>
                <?php else: ?>
                    <table style="width: 100%; border-collapse: collapse; margin-top:10px;">
                        <thead>
                            <tr style="border-bottom: 2px solid #E2E8F0; text-align: left;">
                                <th style="padding: 10px;">Judul</th>
                                <th style="padding: 10px;">Deadline</th>
                                <th style="padding: 10px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($tasks as $t): ?>
                                <tr style="border-bottom: 1px solid #E2E8F0;">
                                    <td style="padding: 10px;"><?= $t->title ?></td>
                                    <td style="padding: 10px;"><?= date('d/m/Y H:i', strtotime($t->deadline)) ?></td>
                                    <td style="padding: 10px;">
                                        <a href="<?= base_url('dashboard/view_task/'.$t->id) ?>" class="btn btn-secondary" style="padding: 5px 10px; font-size:0.9em;"><i class="fas fa-eye"></i> Lihat Pengumpulan</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- Notifications Section -->
    <div style="margin-top: 30px;">
        <h3>Notifikasi</h3>
        <div class="glass-panel" style="margin-top: 15px; padding: 15px;">
            <?php if(empty($notifications)): ?>
                <p>Tidak ada notifikasi.</p>
            <?php else: ?>
                <?php foreach($notifications as $notif): ?>
                    <div style="padding: 10px; border-bottom: 1px solid #E2E8F0; <?= $notif->is_read ? 'opacity:0.6' : 'font-weight:bold' ?>">
                        <p><?= $notif->message ?></p>
                        <small style="color:#64748B"><?= date('d M Y H:i', strtotime($notif->created_at)) ?></small>
                        <?php if(!$notif->is_read): ?>
                            <a href="<?= base_url('dashboard/read_notification/'.$notif->id) ?>" style="font-size:0.8em; color:var(--primary); text-decoration:none; margin-left:10px;">Tandai Dibaca</a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $this->load->view('layout/footer'); ?>
