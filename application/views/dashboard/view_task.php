<?php $this->load->view('layout/header'); ?>

<div class="container" style="margin-top: 30px;">
    <a href="<?= base_url('dashboard') ?>" style="color: var(--primary); text-decoration: none;"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a>
    <h2 style="margin-top: 15px;">Daftar Pengumpulan Tugas</h2>

    <?php if($this->session->flashdata('success')): ?>
        <div style="background: #D1FAE5; color: #059669; padding: 10px; border-radius: 8px; margin: 20px 0;">
            <?= $this->session->flashdata('success') ?>
        </div>
    <?php endif; ?>

    <div class="glass-panel" style="margin-top: 20px;">
        <?php if(empty($submissions)): ?>
            <p>Belum ada mahasiswa yang mengumpulkan tugas ini.</p>
        <?php else: ?>
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid #E2E8F0; text-align: left;">
                        <th style="padding: 10px;">Mahasiswa</th>
                        <th style="padding: 10px;">Waktu Kumpul</th>
                        <th style="padding: 10px;">File</th>
                        <th style="padding: 10px;">Nilai</th>
                        <th style="padding: 10px;">Koreksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($submissions as $s): ?>
                        <tr style="border-bottom: 1px solid #E2E8F0;">
                            <td style="padding: 10px;"><?= $s->mahasiswa_name ?></td>
                            <td style="padding: 10px;"><?= date('d/m/Y H:i', strtotime($s->submitted_at)) ?></td>
                            <td style="padding: 10px;">
                                <a href="<?= base_url('uploads/'.$s->file_path) ?>" target="_blank" style="color:var(--primary);"><i class="fas fa-download"></i> Unduh</a>
                            </td>
                            <td style="padding: 10px; font-weight: bold;">
                                <?= $s->grade !== null ? $s->grade : '<span style="color:#F59E0B">Belum dinilai</span>' ?>
                            </td>
                            <td style="padding: 10px;">
                                <form action="<?= base_url('dashboard/grade_submission') ?>" method="POST" style="display:flex; gap:10px; flex-wrap:wrap;">
                                    <input type="hidden" name="submission_id" value="<?= $s->id ?>">
                                    <input type="hidden" name="task_id" value="<?= $task_id ?>">
                                    <input type="number" name="grade" class="form-control" placeholder="Nilai (0-100)" style="width: 100px; padding: 5px;" value="<?= $s->grade ?>" required>
                                    <input type="text" name="feedback" class="form-control" placeholder="Feedback/Catatan" style="flex:1; padding: 5px;" value="<?= $s->feedback ?>">
                                    <button type="submit" class="btn"><i class="fas fa-check"></i> Simpan</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php $this->load->view('layout/footer'); ?>
