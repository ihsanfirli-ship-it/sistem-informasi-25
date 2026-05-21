<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($settings['site_title']) ? $settings['site_title'] : 'Sistem Informasi 2025' ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/style.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=UnifrakturMaguntia&family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <nav class="navbar">
        <a href="<?= base_url() ?>" class="logo"><img src="<?= base_url('assets/img/logo.png') ?>" alt="Logo" style="height: 36px; vertical-align: middle; margin-right: 8px;"> <?= isset($settings['site_title']) ? $settings['site_title'] : 'Sistem Informasi 2025' ?></a>
        <div class="nav-links">
            <a href="<?= base_url() ?>">Home</a>
            <a href="<?= base_url('photobooth') ?>">Photobooth</a>
            <?php if($this->session->userdata('logged_in')): ?>
                <?php 
                    $role = $this->session->userdata('role');
                    $menu_text = 'Dashboard';
                    if($role == 'mahasiswa') $menu_text = 'Pengumpulan Tugas';
                    if($role == 'dosen') $menu_text = 'Tugas';
                ?>
                <a href="<?= base_url('materials') ?>">Materi</a>
                <a href="<?= base_url('attendance') ?>">Absensi</a>
                <a href="<?= base_url('dashboard') ?>"><?= $menu_text ?></a>
                <a href="<?= base_url('auth/logout') ?>" class="btn">Logout</a>
            <?php else: ?>
                <a href="<?= base_url('auth') ?>" class="btn">Login</a>
            <?php endif; ?>
        </div>
    </nav>
