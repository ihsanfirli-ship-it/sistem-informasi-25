<?php $this->load->view('layout/header'); ?>

<div class="container" style="margin-top: 30px;">
    <h2>Dashboard Mahasiswa</h2>
    <p>Selamat datang, <?= $this->session->userdata('name') ?></p>
    
    <?php if($this->session->flashdata('success')): ?>
        <div style="background: #D1FAE5; color: #059669; padding: 10px; border-radius: 8px; margin: 20px 0;">
            <?= $this->session->flashdata('success') ?>
        </div>
    <?php endif; ?>

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

    <div style="margin-top: 30px;">
        <h3>Daftar Tugas</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; margin-top: 15px;">
            <?php foreach($tasks as $t): ?>
                <div class="glass-panel">
                    <h4 style="color: var(--primary)"><?= $t->title ?></h4>
                    <p style="font-size: 0.9em; margin: 10px 0; color: #475569;">Dosen: <?= $t->dosen_name ?></p>
                    <p style="font-size: 0.9em; margin: 10px 0; color: #EF4444;"><i class="fas fa-clock"></i> Deadline: <?= date('d M Y H:i', strtotime($t->deadline)) ?></p>
                    <p style="margin-bottom: 15px;"><?= $t->description ?></p>
                    
                    <?php if(!$t->is_submitted): ?>
                        <form action="<?= base_url('dashboard/submit_task') ?>" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="task_id" value="<?= $t->id ?>">
                            <div class="form-group">
                                <label style="font-size: 0.9em; display:block; margin-bottom:5px;">Upload Tugas (PDF/DOCX/ZIP)</label>
                                <input type="file" name="file" class="form-control" required style="padding: 5px;">
                            </div>
                            <button type="submit" class="btn" style="width: 100%;"><i class="fas fa-upload"></i> Kumpulkan</button>
                        </form>
                    <?php else: ?>
                        <div style="background: #ECFDF5; color: #059669; padding: 15px; border-radius: 12px; text-align: center; border: 1px solid #10B981;">
                            <i class="fas fa-check-circle fa-2x mb-2"></i>
                            <p class="mb-0 fw-bold">Tugas Sudah Dikumpulkan</p>
                            <small>Anda tidak dapat mengirim ulang tugas ini.</small>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php $this->load->view('layout/footer'); ?>
