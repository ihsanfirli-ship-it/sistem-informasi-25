<?php $this->load->view('layout/header'); ?>

<div class="container py-4">
    <?php if($this->session->userdata('role') == 'admin'): ?>
    <!-- ADMIN DASHBOARD VIEW -->
    <div class="row g-4 mb-4">
        <!-- Summary Cards -->
        <div class="col-md-3">
            <div class="glass-panel p-3 text-center border-0 shadow-sm">
                <div class="text-muted x-small fw-bold text-uppercase mb-1">Total Mahasiswa</div>
                <div class="h3 fw-bold mb-0 text-primary"><?= $total_mhs ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-panel p-3 text-center border-0 shadow-sm">
                <div class="text-muted x-small fw-bold text-uppercase mb-1">Total Dosen</div>
                <div class="h3 fw-bold mb-0 text-secondary"><?= $total_dosen ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-panel p-3 text-center border-0 shadow-sm">
                <div class="text-muted x-small fw-bold text-uppercase mb-1">Hadir Hari Ini</div>
                <div class="h3 fw-bold mb-0 text-success"><?= $today_hadir ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="glass-panel p-3 text-center border-0 shadow-sm">
                <div class="text-muted x-small fw-bold text-uppercase mb-1">Izin/Sakit Hari Ini</div>
                <div class="h3 fw-bold mb-0 text-warning"><?= $today_izin + $today_sakit ?></div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Panel: Chart & Summary -->
        <div class="col-md-8">
            <div class="glass-panel p-4 h-100 border-0 shadow-sm">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-chart-pie text-primary me-2"></i>Rekap Absensi Hari Ini</h5>
                    <span class="text-muted small"><?= date('l, d F Y') ?></span>
                </div>
                
                <div class="row align-items-center">
                    <div class="col-md-6 text-center">
                        <div style="position: relative; height: 250px;">
                            <canvas id="adminTodayChart"></canvas>
                            <div style="position: absolute; top:50%; left:50%; transform: translate(-50%, -50%); text-align: center;">
                                <div class="h2 fw-bold mb-0"><?= $today_hadir + $today_izin + $today_sakit ?></div>
                                <div class="x-small text-muted fw-bold text-uppercase">Total Masuk</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item bg-transparent d-flex justify-content-between align-items-center border-0 px-0">
                                <span class="small fw-medium"><i class="fas fa-circle text-success me-2" style="font-size: 8px;"></i>Hadir</span>
                                <span class="badge bg-light text-dark rounded-pill"><?= $today_hadir ?></span>
                            </div>
                            <div class="list-group-item bg-transparent d-flex justify-content-between align-items-center border-0 px-0">
                                <span class="small fw-medium"><i class="fas fa-circle text-warning me-2" style="font-size: 8px;"></i>Izin</span>
                                <span class="badge bg-light text-dark rounded-pill"><?= $today_izin ?></span>
                            </div>
                            <div class="list-group-item bg-transparent d-flex justify-content-between align-items-center border-0 px-0">
                                <span class="small fw-medium"><i class="fas fa-circle text-danger me-2" style="font-size: 8px;"></i>Sakit</span>
                                <span class="badge bg-light text-dark rounded-pill"><?= $today_sakit ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel: Recent Submissions -->
        <div class="col-md-4">
            <div class="glass-panel p-4 h-100 border-0 shadow-sm overflow-hidden">
                <h5 class="fw-bold mb-4 text-dark"><i class="fas fa-file-signature text-secondary me-2"></i>Daftar Pengajuan</h5>
                <div class="submission-list">
                    <?php if(empty($recent_submissions)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-inbox text-muted display-4 mb-3"></i>
                            <p class="text-muted small">Belum ada pengajuan izin/sakit terbaru</p>
                        </div>
                    <?php else: ?>
                        <?php foreach($recent_submissions as $rs): ?>
                        <div class="p-3 bg-white rounded-4 border border-light shadow-sm mb-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge bg-<?= $rs->status == 'Izin' ? 'warning' : 'danger' ?> x-small fw-bold"><?= strtoupper($rs->status) ?></span>
                                <span class="x-small text-muted"><?= date('d M', strtotime($rs->created_at)) ?></span>
                            </div>
                            <div class="fw-bold small mb-1 text-dark"><?= $rs->user_name ?></div>
                            <div class="text-muted x-small italic text-truncate mb-2"><?= $rs->reason ?></div>
                            <a href="<?= base_url('uploads/attendance/'.$rs->photo) ?>" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill w-100 x-small py-1">Lihat Bukti</a>
                        </div>
                        <?php endforeach; ?>
                        <div class="text-center">
                            <button class="btn btn-link text-primary x-small fw-bold text-decoration-none">LIHAT SEMUA</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Main List at Bottom for Admin -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="glass-panel p-0 border-0 shadow-sm overflow-hidden">
                <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
                    <h6 class="mb-0 fw-bold">Daftar Kehadiran Lengkap</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Berkas</th>
                                <th>Nama & Role</th>
                                <th>Keterangan</th>
                                <th>Status</th>
                                <th>Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($attendance as $row): ?>
                            <tr>
                                <td class="ps-4">
                                    <a href="<?= base_url('uploads/attendance/'.$row->photo) ?>" target="_blank">
                                        <img src="<?= base_url('uploads/attendance/'.$row->photo) ?>" class="rounded-3 shadow-sm" width="45" alt="Bukti">
                                    </a>
                                </td>
                                <td>
                                    <div class="fw-bold small text-dark"><?= $row->user_name ?></div>
                                    <span class="badge bg-<?= $row->user_role == 'mahasiswa' ? 'info' : 'primary' ?> x-small" style="font-size: 0.65em;"><?= strtoupper($row->user_role) ?></span>
                                </td>
                                <td>
                                    <div class="text-dark x-small fw-bold"><?= $row->class_info ?></div>
                                    <?php if($row->reason): ?>
                                        <div class="text-muted x-small text-truncate" style="max-width: 150px;"><?= $row->reason ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge rounded-pill bg-<?= $row->status == 'Hadir' ? 'success' : ($row->status == 'Izin' ? 'warning' : 'danger') ?> x-small">
                                        <?= $row->status ?>
                                    </span>
                                </td>
                                <td class="x-small text-muted"><?= date('H:i, d M Y', strtotime($row->created_at)) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const adminCtx = document.getElementById('adminTodayChart').getContext('2d');
        new Chart(adminCtx, {
            type: 'doughnut',
            data: {
                labels: ['Hadir', 'Izin', 'Sakit'],
                datasets: [{
                    data: [<?= $today_hadir ?>, <?= $today_izin ?>, <?= $today_sakit ?>],
                    backgroundColor: ['#10B981', '#F59E0B', '#EF4444'],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                cutout: '80%'
            }
        });
    </script>

    <?php else: ?>
    <!-- USER (DOSEN/MHS) COMPACT CAPTURE VIEW -->
    <div class="row <?= $this->session->userdata('role') == 'mahasiswa' ? 'justify-content-center' : '' ?>">
        <div class="<?= $this->session->userdata('role') == 'mahasiswa' ? 'col-md-6' : 'col-md-5' ?>">
            <div class="glass-panel p-3 shadow-lg border-0 rounded-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary rounded-circle p-2 me-2 text-white">
                        <i class="fas fa-camera"></i>
                    </div>
                    <h5 class="mb-0 fw-bold">Presensi Kehadiran</h5>
                </div>
                
                <div id="camera-box" class="position-relative bg-dark rounded-4 overflow-hidden mb-3 shadow-sm mx-auto" style="aspect-ratio: 4/3; width: 100%;">
                    <video id="video" autoplay playsinline class="w-100 h-100 object-fit-cover" style="transform: scaleX(-1);"></video>
                    <canvas id="canvas" style="display:none;"></canvas>
                    <div id="countdown" class="position-absolute top-50 start-50 translate-middle display-1 fw-bold text-white d-none" style="text-shadow: 0 0 20px rgba(0,0,0,0.5);">3</div>
                </div>
                
                <div class="glass-panel p-3 border-0 bg-light rounded-4 shadow-none text-start">
                    <div class="mb-3">
                        <label class="form-label x-small fw-bold text-muted mb-1 ms-1">Mata Kuliah & Jam</label>
                        <input type="text" id="class-info" class="form-control border-0 bg-white rounded-3 py-2 px-3 shadow-sm" placeholder="Contoh: Algoritma (14:00-15:00)">
                    </div>

                    <div class="mb-3">
                        <label class="form-label x-small fw-bold text-muted mb-1 ms-1">Status Kehadiran</label>
                        <select id="attendance-status" class="form-select border-0 bg-white rounded-3 py-2 px-3 shadow-sm" onchange="toggleAttendanceMode()">
                            <option value="Hadir">Status: Hadir (Selfie)</option>
                            <option value="Izin">Status: Izin (Upload Surat)</option>
                            <option value="Sakit">Status: Sakit (Upload Surat)</option>
                        </select>
                    </div>

                    <div id="extra-fields" class="d-none animate__animated animate__fadeIn">
                        <div class="mb-3">
                            <label class="form-label x-small fw-bold text-muted mb-1 ms-1">Alasan</label>
                            <textarea id="reason" class="form-control border-0 bg-white rounded-3 py-2 px-3 shadow-sm" rows="2" placeholder="Tuliskan alasan Anda..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label x-small fw-bold text-muted mb-1 ms-1">Foto Surat / Bukti</label>
                            <input type="file" id="attachment" class="form-control border-0 bg-white rounded-3 py-2 px-3 shadow-sm">
                        </div>
                    </div>

                    <button id="snap-btn" class="btn btn-primary btn-lg w-100 rounded-3 py-3 mt-2 fw-bold shadow-sm ripple">
                        <i class="fas fa-check-circle me-2"></i>Konfirmasi Kehadiran
                    </button>
                </div>
            </div>
        </div>

        <?php if($this->session->userdata('role') == 'dosen'): ?>
        <div class="col-md-7">
            <div class="glass-panel h-100 p-0 border-0 shadow-sm overflow-hidden">
                <div class="card-header bg-white py-3 px-4 border-bottom">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-users-viewfinder text-primary me-2"></i>Monitor Mahasiswa</h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 small">Foto</th>
                                <th class="small">Nama</th>
                                <th class="small">Keterangan</th>
                                <th class="small">Waktu</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($attendance as $row): ?>
                            <tr>
                                <td class="ps-4"><img src="<?= base_url('uploads/attendance/'.$row->photo) ?>" class="rounded-3" width="40"></td>
                                <td class="small fw-bold"><?= $row->user_name ?></td>
                                <td class="x-small text-muted"><?= $row->class_info ?></td>
                                <td class="x-small text-muted"><?= date('H:i', strtotime($row->created_at)) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- SCRIPTS FOR CAMERA (NON-ADMIN) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script>
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const snapBtn = document.getElementById('snap-btn');
        const countdown = document.getElementById('countdown');
        const cameraBox = document.getElementById('camera-box');
        const extraFields = document.getElementById('extra-fields');
        let cameraStream = null;

        async function startCamera() {
            try { cameraStream = await navigator.mediaDevices.getUserMedia({ video: true }); if(video) video.srcObject = cameraStream; }
            catch(err) { console.log('Kamera tidak aktif'); }
        }
        function stopCamera() { if (cameraStream) { cameraStream.getTracks().forEach(track => track.stop()); if(video) video.srcObject = null; cameraStream = null; } }
        function toggleAttendanceMode() {
            const status = document.getElementById('attendance-status').value;
            if (status === 'Hadir') { cameraBox.classList.remove('d-none'); extraFields.classList.add('d-none'); startCamera(); }
            else { cameraBox.classList.add('d-none'); extraFields.classList.remove('d-none'); stopCamera(); }
        }
        startCamera();

        snapBtn.addEventListener('click', () => {
            const status = document.getElementById('attendance-status').value;
            const classInfo = document.getElementById('class-info').value.trim();
            if(!classInfo) { alert('Mohon isi Matakuliah & Jam!'); return; }
            
            if (status === 'Hadir') {
                snapBtn.disabled = true;
                let count = 3; countdown.classList.remove('d-none'); countdown.innerText = count;
                const timer = setInterval(() => {
                    count--;
                    if (count > 0) { countdown.innerText = count; }
                    else {
                        clearInterval(timer); countdown.classList.add('d-none');
                        canvas.width = video.videoWidth; canvas.height = video.videoHeight;
                        canvas.getContext('2d').translate(canvas.width, 0); canvas.getContext('2d').scale(-1, 1);
                        canvas.getContext('2d').drawImage(video, 0, 0);
                        submitAttendance(status, classInfo, canvas.toDataURL('image/png'));
                    }
                }, 800);
            } else {
                const reason = document.getElementById('reason').value;
                const attachment = document.getElementById('attachment').files[0];
                if (!attachment) { alert('Sertakan bukti surat!'); return; }
                submitAttendance(status, classInfo, null, reason, attachment);
            }
        });

        function submitAttendance(status, classInfo, imageData, reason = '', attachment = null) {
            const formData = new FormData();
            formData.append('status', status);
            formData.append('class_info', classInfo);
            if(imageData) formData.append('image', imageData);
            if(reason) formData.append('reason', reason);
            if(attachment) formData.append('attachment', attachment);
            $.ajax({
                url: '<?= base_url('attendance/clock_in') ?>', type: 'POST', data: formData, processData: false, contentType: false,
                success: function(response) {
                    const res = JSON.parse(response);
                    if (res.success) { alert('✅ Absen berhasil!'); location.reload(); }
                    else { alert('❌ Gagal: ' + res.error); snapBtn.disabled = false; }
                }
            });
        }
    </script>
    <?php endif; ?>
</div>

<?php $this->load->view('layout/footer'); ?>

<?php $this->load->view('layout/footer'); ?>
