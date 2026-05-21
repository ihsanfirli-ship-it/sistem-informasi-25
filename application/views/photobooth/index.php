<?php $this->load->view('layout/header'); ?>

<style>
    .pb-container {
        max-width: 700px;
        margin: 30px auto;
        text-align: center;
    }
    
    #video {
        width: 100%;
        max-width: 400px;
        border-radius: 12px;
        border: 3px solid var(--primary);
        transform: scaleX(-1);
    }
    
    .countdown-overlay {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.7);
        display: none;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        font-size: 120px;
        color: white;
        font-weight: 800;
        text-shadow: 0 0 40px rgba(255,105,180,0.8);
    }

    .photo-counter {
        background: var(--primary);
        color: white;
        padding: 8px 20px;
        border-radius: 20px;
        display: inline-block;
        margin: 15px 0;
        font-weight: 600;
    }

    .mini-previews {
        display: flex;
        gap: 10px;
        justify-content: center;
        margin: 15px 0;
    }

    .mini-previews img {
        width: 100px;
        height: 133px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px solid #CBD5E1;
        transform: scaleX(-1);
    }

    .mini-previews .empty-slot {
        width: 100px;
        height: 133px;
        border-radius: 8px;
        border: 2px dashed #CBD5E1;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94A3B8;
        font-size: 0.8em;
    }

    #final-strip {
        display: none;
        margin: 20px auto;
    }

    #final-strip canvas {
        max-width: 100%;
        border-radius: 8px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    }

    .flash-effect {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: white;
        z-index: 9998;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.1s;
    }

    .flash-effect.active {
        opacity: 1;
        transition: opacity 0.05s;
    }

    .frame-selector {
        background: #f8fafc;
        padding: 15px;
        border-radius: 15px;
        margin-bottom: 20px;
        border: 1px solid #e2e8f0;
    }

    .frame-option {
        cursor: pointer;
        position: relative;
    }

    .frame-option input {
        position: absolute;
        opacity: 0;
    }

    .frame-card {
        padding: 10px 20px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        transition: all 0.2s;
        text-align: center;
        background: white;
    }

    .frame-option input:checked + .frame-card {
        border-color: var(--primary);
        background: rgba(255, 105, 180, 0.05);
        color: var(--primary);
        box-shadow: 0 4px 12px rgba(255,105,180,0.1);
    }

    .frame-card p {
        margin: 5px 0 0 0;
        font-size: 0.9em;
        font-weight: 600;
    }

    @media (max-width: 768px) {
        .pb-container {
            margin: 10px auto;
            padding: 0 10px;
        }
        
        #video {
            max-width: 100%;
        }

        .countdown-overlay {
            font-size: 80px;
        }

        .mini-previews img, .mini-previews .empty-slot {
            width: 80px;
            height: 106px;
        }
    }
</style>

<div class="pb-container">
    <h2 style="margin-bottom: 5px;"><i class="fas fa-camera-retro"></i> Photobooth</h2>
    <p style="color: #64748B;">Ambil 3 foto untuk membuat strip foto kenang-kenangan!</p>

    <div id="camera-status" style="background: #FEF3C7; color: #92400E; padding: 12px; border-radius: 8px; margin: 15px 0;">
        <i class="fas fa-spinner fa-spin"></i> Memuat kamera...
    </div>

    <div class="glass-panel" style="padding: 20px;">
        <!-- Camera View -->
        <div id="camera-section">
            <video id="video" autoplay playsinline muted></video>
            
            <div class="photo-counter" id="photo-counter">Foto: 0 / 3</div>

        <div class="frame-selector">
            <div class="d-flex justify-content-center gap-3">
                <label class="frame-option">
                    <input type="radio" name="frame_type" value="newspaper" checked>
                    <div class="frame-card">
                        <i class="fas fa-newspaper fa-xl"></i>
                        <p>Koran</p>
                    </div>
                </label>
                <label class="frame-option">
                    <input type="radio" name="frame_type" value="classic">
                    <div class="frame-card">
                        <i class="fas fa-film fa-xl"></i>
                        <p>Classic</p>
                    </div>
                </label>
            </div>
        </div>

        <div class="mini-previews" id="mini-previews">
                <div class="empty-slot">1</div>
                <div class="empty-slot">2</div>
                <div class="empty-slot">3</div>
            </div>

            <button id="snap-btn" class="btn" disabled style="padding: 15px 40px; font-size: 1.1rem;">
                <i class="fas fa-camera"></i> Ambil Foto (1/3)
            </button>
        </div>

        <!-- Final Result -->
        <div id="final-strip">
            <canvas id="strip-canvas"></canvas>
            
            <div style="margin-top: 20px; display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;">
                <button id="retake-all-btn" class="btn btn-secondary"><i class="fas fa-redo"></i> Ulang Semua</button>
            </div>

            <div style="margin-top: 20px; text-align: left;">
                <div class="form-group">
                    <label>Nomor WhatsApp (Cth: 628123456789)</label>
                    <input type="text" id="wa-number" class="form-control" placeholder="628xxx">
                </div>
                <button id="send-wa-btn" class="btn" style="width: 100%; background: #25D366;">
                    <i class="fab fa-whatsapp"></i> Kirim ke WhatsApp
                </button>
            </div>
        </div>
    </div>
</div>

<canvas id="capture-canvas" style="display:none;"></canvas>
<div class="countdown-overlay" id="countdown-overlay"></div>
<div class="flash-effect" id="flash-effect"></div>

<script>
const video = document.getElementById('video');
const snapBtn = document.getElementById('snap-btn');
const captureCanvas = document.getElementById('capture-canvas');
const stripCanvas = document.getElementById('strip-canvas');
const cameraStatus = document.getElementById('camera-status');
const countdownOverlay = document.getElementById('countdown-overlay');
const flashEffect = document.getElementById('flash-effect');
const photoCounter = document.getElementById('photo-counter');
const miniPreviews = document.getElementById('mini-previews');
const sendWaBtn = document.getElementById('send-wa-btn');

let photos = [];
let cameraReady = false;

// Start camera in portrait mode
function startCamera() {
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        cameraStatus.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Browser tidak mendukung kamera.';
        cameraStatus.style.background = '#FEE2E2'; cameraStatus.style.color = '#DC2626';
        return;
    }

    navigator.mediaDevices.getUserMedia({
        video: { facingMode: 'user', width: { ideal: 720 }, height: { ideal: 960 } },
        audio: false
    })
    .then(function(stream) {
        video.srcObject = stream;
        video.onloadedmetadata = function() {
            video.play();
            cameraReady = true;
            cameraStatus.innerHTML = '<i class="fas fa-check-circle"></i> Kamera siap!';
            cameraStatus.style.background = '#D1FAE5'; cameraStatus.style.color = '#059669';
            snapBtn.disabled = false;
            setTimeout(function() { cameraStatus.style.display = 'none'; }, 1500);
        };
    })
    .catch(function(err) {
        console.error(err);
        cameraStatus.innerHTML = '<i class="fas fa-lock"></i> Akses kamera ditolak. Izinkan di browser lalu refresh.';
        cameraStatus.style.background = '#FEE2E2'; cameraStatus.style.color = '#DC2626';
    });
}

startCamera();

// Countdown + capture
function doCountdown(seconds) {
    return new Promise(function(resolve) {
        countdownOverlay.style.display = 'flex';
        var count = seconds;
        countdownOverlay.textContent = count;
        var interval = setInterval(function() {
            count--;
            if (count <= 0) {
                clearInterval(interval);
                countdownOverlay.style.display = 'none';
                resolve();
            } else {
                countdownOverlay.textContent = count;
            }
        }, 1000);
    });
}

function flashScreen() {
    flashEffect.classList.add('active');
    setTimeout(function() { flashEffect.classList.remove('active'); }, 150);
}

function capturePhoto() {
    var ctx = captureCanvas.getContext('2d');
    // Capture in portrait ratio 3:4
    var w = video.videoWidth;
    var h = video.videoHeight;
    var targetRatio = 3 / 4;
    var currentRatio = w / h;
    var sx = 0, sy = 0, sw = w, sh = h;

    if (currentRatio > targetRatio) {
        sw = h * targetRatio;
        sx = (w - sw) / 2;
    } else {
        sh = w / targetRatio;
        sy = (h - sh) / 2;
    }

    captureCanvas.width = 600;
    captureCanvas.height = 800;
    // Mirror the image
    ctx.save();
    ctx.scale(-1, 1);
    ctx.drawImage(video, sx, sy, sw, sh, -600, 0, 600, 800);
    ctx.restore();

    return captureCanvas.toDataURL('image/png');
}

snapBtn.addEventListener('click', async function() {
    if (!cameraReady || photos.length >= 3) return;
    snapBtn.disabled = true;

    await doCountdown(3);
    flashScreen();

    var photoData = capturePhoto();
    photos.push(photoData);

    updateMiniPreviews();
    photoCounter.textContent = 'Foto: ' + photos.length + ' / 3';

    if (photos.length < 3) {
        snapBtn.innerHTML = '<i class="fas fa-camera"></i> Ambil Foto (' + (photos.length + 1) + '/3)';
        snapBtn.disabled = false;
    } else {
        // All 3 photos taken - generate strip
        snapBtn.style.display = 'none';
        generateStrip();
    }
});

function updateMiniPreviews() {
    var html = '';
    for (var i = 0; i < 3; i++) {
        if (photos[i]) {
            html += '<img src="' + photos[i] + '" alt="Foto ' + (i+1) + '">';
        } else {
            html += '<div class="empty-slot">' + (i+1) + '</div>';
        }
    }
    miniPreviews.innerHTML = html;
}

function generateStrip() {
    // Preload all 3 images first, then draw
    var loadedImages = [];
    var loadCount = 0;

    for (var i = 0; i < 3; i++) {
        (function(index) {
            var img = new Image();
            img.onload = function() {
                loadedImages[index] = img;
                loadCount++;
                if (loadCount === 3) {
                    drawStrip(loadedImages);
                }
            };
            img.src = photos[index];
        })(i);
    }
}

async function drawStrip(loadedImages) {
    // Wait for fonts to load properly
    await document.fonts.ready;

    var frameType = document.querySelector('input[name="frame_type"]:checked').value;
    
    if (frameType === 'newspaper') {
        await drawNewspaperFrame(loadedImages);
    } else {
        await drawClassicFrame(loadedImages);
    }

    // Show the strip
    document.getElementById('camera-section').style.display = 'none';
    document.getElementById('final-strip').style.display = 'block';
}

async function drawNewspaperFrame(loadedImages) {
    var ctx = stripCanvas.getContext('2d');
    var stripW = 1000;
    var stripH = 1500;
    stripCanvas.width = stripW;
    stripCanvas.height = stripH;

    // 1. Background - Premium Newsprint
    ctx.fillStyle = '#fdfbf7'; 
    ctx.fillRect(0, 0, stripW, stripH);
    
    // Subtle Paper Texture
    ctx.globalAlpha = 0.08;
    for (let i = 0; i < 8000; i++) {
        ctx.fillStyle = Math.random() > 0.5 ? '#000' : '#888';
        ctx.fillRect(Math.random() * stripW, Math.random() * stripH, 1, 1);
    }
    ctx.globalAlpha = 1.0;

    // 2. HEADER BLOCK
    ctx.fillStyle = '#000';
    ctx.textAlign = 'center';
    
    // Title
    ctx.font = '100px "UnifrakturMaguntia", serif';
    ctx.fillText('Sistem Informasi 25', stripW / 2, 130);

    // Double Lines
    ctx.lineWidth = 4;
    ctx.beginPath();
    ctx.moveTo(40, 160); ctx.lineTo(stripW - 40, 160);
    ctx.stroke();
    
    ctx.lineWidth = 1;
    ctx.beginPath();
    ctx.moveTo(40, 175); ctx.lineTo(stripW - 40, 175);
    ctx.stroke();

    // Secondary Info Bar
    ctx.font = 'bold 22px "Playfair Display", serif';
    ctx.textAlign = 'left';
    ctx.fillText('SPECIAL EDITION FRAME', 50, 205);
    ctx.textAlign = 'right';
    ctx.fillText('UNSADA PHOTOMATICS NEWSPAPER', stripW - 50, 205);

    ctx.lineWidth = 2;
    ctx.beginPath();
    ctx.moveTo(40, 220); ctx.lineTo(stripW - 40, 220);
    ctx.stroke();

    // 3. CONTENT PLACEMENT
    var margin = 60;
    var colW = (stripW - (margin * 3)) / 2;

    // Section 1: Intro + Large Photo 1
    var photo1H = 500;
    drawNewsImage(ctx, loadedImages[0], stripW - colW - margin, 275, colW, photo1H, "MOMEN UTAMA 2025");

    // Intro Text (Left)
    ctx.textAlign = 'left';
    ctx.fillStyle = '#1a1a1a';
    ctx.font = 'bold 36px "Playfair Display", serif';
    ctx.fillText('SI UNSADA HITS!', margin, 310);
    
    ctx.font = '16px "Playfair Display", serif';
    var cy = 350; // Current Y
    var intro = "Sistem Informasi Unsada sekarang bukan cuma tempat kuliah biasa tapi jadi spot wajib mahasiswa gaul Jakarta ngopi, ngonten, dan menunjukkan skill digital di tahun 2025. Dari lab komputer ke M-Bloc, semuanya menjadi arena eksplorasi kreativitas tanpa batas bagi para pengembang masa depan.";
    cy = wrapText(ctx, intro, margin, cy, colW, 24);
    cy += 15;

    var detailText = "Dengan kurikulum yang terus diperbarui, mahasiswa dibekali kemampuan analisa data dan manajemen bisnis yang kuat. Tidak heran jika lulusan SI Unsada banyak dilirik oleh perusahaan startup ternama di ibu kota.";
    cy = wrapText(ctx, detailText, margin, cy, colW, 24);
    cy += 20;

    ctx.font = 'italic bold 22px "Playfair Display", serif';
    var quote = "\"Dulu masuk SI buat belajar database, sekarang untuk bikin masa depan teknologi yang lebih inklusif!\"";
    cy = wrapText(ctx, quote, margin, cy, colW, 30);

    // Section 2: Big Headline (MOVED DOWN)
    ctx.textAlign = 'center';
    ctx.font = 'bold 76px "Playfair Display", serif';
    ctx.fillText('GAYA DAN INOVASI', stripW / 2, 880);

    // Section 2.5: Middle Sub-branding
    ctx.textAlign = 'center';
    ctx.font = 'bold 28px serif';
    ctx.fillText('• UNSADA SI EDITION •', stripW/2, 950);

    // Section 3: Two Photos at bottom
    var photoBottomY = 1010;
    var photoBottomH = 360;
    drawNewsImage(ctx, loadedImages[1], margin, photoBottomY, colW, photoBottomH, "NONGKRONG PRODUKTIF");
    drawNewsImage(ctx, loadedImages[2], stripW - colW - margin, photoBottomY, colW, photoBottomH, "KEBERSAMAAN SI");

    // FOOTER CONTENT
    ctx.textAlign = 'center';
    ctx.font = 'bold 20px serif';
    ctx.fillText('• BERITA TERKINI •', stripW/2, 1410);

    var footerColW = (stripW - (margin * 4)) / 3;
    ctx.textAlign = 'left';
    ctx.font = 'bold 14px serif';
    ctx.fillText('• TRENDING: AI in Campus', margin, 1445);
    ctx.fillText('• UPCOMING: SI-Fest 2025', margin, 1470);

    ctx.font = 'italic 13px serif';
    ctx.fillText('Laboratorium SI: Pusat inovasi', margin + footerColW + margin, 1445);
    ctx.fillText('mahasiswa Unsada 2025.', margin + footerColW + margin, 1465);

    ctx.font = '13px serif';
    ctx.fillText('Fokus integrasi AI untuk', stripW - footerColW - margin, 1445);
    ctx.fillText('solusi bisnis lokal modern.', stripW - footerColW - margin, 1465);

    ctx.lineWidth = 1;
    ctx.beginPath();
    ctx.moveTo(40, stripH - 50); ctx.lineTo(stripW - 40, stripH - 50);
    ctx.stroke();

    ctx.font = 'italic 14px "Playfair Display", serif';
    ctx.textAlign = 'center';
    ctx.fillText('© 2025 Perjalanan Sistem Informasi UNSADA - Dicetak melalui SiBot Virtual Assistant', stripW / 2, stripH - 25);

    var gradient = ctx.createRadialGradient(stripW/2, stripH/2, 0, stripW/2, stripH/2, stripW * 0.9);
    gradient.addColorStop(0, 'transparent');
    gradient.addColorStop(1, 'rgba(0,0,0,0.04)');
    ctx.fillStyle = gradient;
    ctx.fillRect(0, 0, stripW, stripH);
}

async function drawClassicFrame(loadedImages) {
    var ctx = stripCanvas.getContext('2d');
    var stripW = 600;
    var stripH = 1850; // Increased height to prevent overlap
    stripCanvas.width = stripW;
    stripCanvas.height = stripH;

    // 1. MAIN BACKGROUND
    ctx.fillStyle = '#0a0a1a';
    ctx.fillRect(0, 0, stripW, stripH);

    // 2. OUTER WHITE BORDER
    ctx.strokeStyle = '#fff';
    ctx.lineWidth = 20;
    ctx.strokeRect(0, 0, stripW, stripH);

    // 3. CHECKERED HEADERS
    function drawCheckers(y, rows) {
        var size = 30;
        var cols = Math.ceil(stripW / size);
        for (let r = 0; r < rows; r++) {
            for (let c = 0; c < cols; c++) {
                ctx.fillStyle = (r + c) % 2 === 0 ? '#ff69b4' : '#000';
                ctx.fillRect(c * size, y + (r * size), size, size);
            }
        }
    }
    
    drawCheckers(10, 3); // Top checker
    drawCheckers(stripH - 150, 5); // Bottom checker
    
    // 4. PHOTOS (3 vertical)
    var margin = 60;
    var photoW = stripW - (margin * 2);
    var photoH = 500; // Fixed height to fill space correctly
    
    loadedImages.forEach((img, i) => {
        var py = 130 + (i * (photoH + 35));
        
        // Photo White Frame
        ctx.fillStyle = '#fff';
        ctx.fillRect(margin - 8, py - 8, photoW + 16, photoH + 16);
        
        drawNewsImage(ctx, img, margin, py, photoW, photoH, "");

        // DIVIDER HEARTS (Between photos)
        if (i < 2) {
            var heartY = py + photoH + 15;
            for(let j=0; j<5; j++) {
                drawHeart(ctx, (stripW/2 - 50) + (j*25), heartY, 12, '#ff69b4');
            }
        }
    });

    // 5. BOTTOM TEXT (CENTERED IN CHECKERS)
    ctx.fillStyle = '#fff';
    ctx.textAlign = 'center';
    ctx.font = 'bold 36px "Playfair Display", serif';
    ctx.fillText('Sistem Informasi', stripW/2, stripH - 85);
    ctx.font = 'bold 44px "Playfair Display", serif';
    ctx.fillText('2025', stripW/2, stripH - 40);
}

function drawHeart(ctx, x, y, size, color) {
    ctx.save();
    ctx.beginPath();
    var topCurveHeight = size * 0.3;
    ctx.moveTo(x, y + topCurveHeight);
    // top left curve
    ctx.bezierCurveTo(x, y, x - size / 2, y, x - size / 2, y + topCurveHeight);
    // bottom left curve
    ctx.bezierCurveTo(x - size / 2, y + (size + topCurveHeight) / 2, x, y + (size + topCurveHeight) / 2, x, y + size);
    // bottom right curve
    ctx.bezierCurveTo(x, y + (size + topCurveHeight) / 2, x + size / 2, y + (size + topCurveHeight) / 2, x + size / 2, y + topCurveHeight);
    // top right curve
    ctx.bezierCurveTo(x + size / 2, y, x, y, x, y + topCurveHeight);
    ctx.closePath();
    ctx.fillStyle = color;
    ctx.fill();
    ctx.strokeStyle = '#fff';
    ctx.lineWidth = 3;
    ctx.stroke();
    ctx.restore();
}


function drawNewsImage(ctx, img, x, y, w, h, caption) {
    // Correct aspect ratio (object-fit: cover behavior)
    var imgW = img.width;
    var imgH = img.height;
    var targetRatio = w / h;
    var currentRatio = imgW / imgH;
    
    var sx, sy, sw, sh;
    if (currentRatio > targetRatio) {
        sw = imgH * targetRatio;
        sh = imgH;
        sx = (imgW - sw) / 2;
        sy = 0;
    } else {
        sw = imgW;
        sh = imgW / targetRatio;
        sx = 0;
        sy = (imgH - sh) / 2;
    }

    // Draw Image cropped to fit
    ctx.drawImage(img, sx, sy, sw, sh, x, y, w, h);
    
    // Caption
    ctx.fillStyle = '#1a1a1a';
    ctx.font = 'italic bold 15px serif';
    ctx.textAlign = 'center';
    ctx.fillText(caption, x + w/2, y + h + 25);
}

function wrapText(ctx, text, x, y, maxWidth, lineHeight) {
    var words = text.split(' ');
    var line = '';
    for (var n = 0; n < words.length; n++) {
        var testLine = line + words[n] + ' ';
        var metrics = ctx.measureText(testLine);
        var testWidth = metrics.width;
        if (testWidth > maxWidth && n > 0) {
            ctx.fillText(line, x, y);
            line = words[n] + ' ';
            y += lineHeight;
        } else {
            line = testLine;
        }
    }
    ctx.fillText(line, x, y);
    return y + lineHeight;
}

// Retake all
document.getElementById('retake-all-btn').addEventListener('click', function() {
    photos = [];
    photoCounter.textContent = 'Foto: 0 / 3';
    updateMiniPreviews();
    snapBtn.innerHTML = '<i class="fas fa-camera"></i> Ambil Foto (1/3)';
    snapBtn.style.display = 'inline-block';
    snapBtn.disabled = false;
    document.getElementById('camera-section').style.display = 'block';
    document.getElementById('final-strip').style.display = 'none';
});

// Send to WhatsApp
sendWaBtn.addEventListener('click', function() {
    var phone = document.getElementById('wa-number').value;
    if (!phone) { alert('Masukkan nomor WhatsApp'); return; }

    sendWaBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';
    sendWaBtn.disabled = true;

    var imageData = stripCanvas.toDataURL('image/png');

    $.ajax({
        url: "<?= base_url('photobooth/upload') ?>",
        type: 'POST',
        data: { image: imageData, phone: phone },
        timeout: 60000,
        success: function(response) {
            try {
                var data = (typeof response === 'object') ? response : JSON.parse(response);
                if (data.success) {
                    if (data.sent && !data.curl_error) {
                        alert('✅ Link download foto berhasil dikirim ke WhatsApp!');
                    } else if (data.curl_error) {
                        alert('⚠️ Fonnte error. Membuka WA manual...');
                        window.open("https://wa.me/" + phone + "?text=" + encodeURIComponent("Foto Photobooth Anda: " + data.url), '_blank');
                    } else {
                        alert('✅ Foto tersimpan! Membuka WA...');
                        window.open("https://wa.me/" + phone + "?text=" + encodeURIComponent("Foto Photobooth Anda: " + data.url), '_blank');
                    }
                } else {
                    alert('❌ Gagal: ' + (data.error || 'Server error'));
                }
            } catch (e) {
                console.error(e, response);
                alert('❌ Error parsing response');
            }
            sendWaBtn.innerHTML = '<i class="fab fa-whatsapp"></i> Kirim ke WhatsApp';
            sendWaBtn.disabled = false;
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', status, error);
            alert('❌ Gagal menghubungi server');
            sendWaBtn.innerHTML = '<i class="fab fa-whatsapp"></i> Kirim ke WhatsApp';
            sendWaBtn.disabled = false;
        }
    });
});
</script>

<?php $this->load->view('layout/footer'); ?>
