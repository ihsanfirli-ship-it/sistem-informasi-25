<?php $this->load->view('layout/header'); ?>

<div class="container" style="display: flex; justify-content: center; align-items: center; min-height: 80vh;">
    <div class="glass-panel" style="width: 100%; max-width: 400px; text-align: center;">
        <img src="<?= base_url('assets/img/logo.png') ?>" alt="Logo" style="width: 80px; margin-bottom: 20px;">
        <h2 style="color: var(--primary); margin-bottom: 30px;">Member Login</h2>
        
        <?php if($this->session->flashdata('error')): ?>
            <div style="background: #FEE2E2; color: #DC2626; padding: 10px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
                <?= $this->session->flashdata('error') ?>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('auth/login') ?>" method="POST">
            <div class="form-group">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Username</label>
                <input type="text" name="username" class="form-control" required placeholder="Masukkan username">
            </div>
            <div class="form-group">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">Password</label>
                <input type="password" name="password" class="form-control" required placeholder="Masukkan password">
            </div>
            <button type="submit" class="btn" style="width: 100%; margin-top: 10px;">Login</button>
        </form>
    </div>
</div>

<?php $this->load->view('layout/footer'); ?>
