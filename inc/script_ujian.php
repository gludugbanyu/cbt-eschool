    <script>
// Timer Logic
let waktu = <?= max(1, (int)$waktu_sisa) ?> * 60; // waktu_sisa menit -> detik
let soalAktif = 0;
const totalSoal = <?= count($soal) ?>;

// Tampilkan loading overlay saat pertama kali load
document.getElementById('loadingOverlay').style.display = 'flex';
setTimeout(() => {
    document.getElementById('loadingOverlay').style.display = 'none';
}, 500);

function updateTimer() {
    let menit = Math.floor(waktu / 60);
    let detik = waktu % 60;

    document.getElementById('timer').innerText =
        `${menit.toString().padStart(2, '0')}:${detik.toString().padStart(2, '0')}`;

    cekTombolSelesai(); // SATU PINTU LOGIKA

    waktu--;

    if (waktu < 0) {
        document.getElementById('formUjian').submit();
    }
}


function cekTombolSelesai() {
    const submitBtn = document.getElementById('submitBtn');
    const menit = Math.floor(waktu / 60);

    const diSoalTerakhir = (soalAktif === totalSoal - 1);
    const waktuMemenuhi  = (batasMenitSelesai === 0) || (menit <= batasMenitSelesai);

    if (diSoalTerakhir && waktuMemenuhi) {
        submitBtn.style.display = 'inline-block';
    } else {
        submitBtn.style.display = 'none';
    }
}


function updateNavigationButtons() {
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');

    prevBtn.style.display = soalAktif > 0 ? 'block' : 'none';

    if (soalAktif < totalSoal - 1) {
        nextBtn.style.display = 'block';
    } else {
        nextBtn.style.display = 'none';
    }
}


function tampilSoal(index) {
    document.querySelectorAll('.question-container').forEach(s => s.classList.remove('active'));
    const soal = document.getElementById('soal-' + index);
    if (soal) {
        soal.classList.add('active');
        soalAktif = index;

        // Tampilkan nomor urut (1, 2, 3, dst.)
        const currentNo = index + 1;
        document.getElementById('currentQuestionNumber').textContent = currentNo.toString().padStart(2, '0');

        updateNavigationButtons();
        cekTombolSelesai()

        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }
}

function nextSoal() {
    if (soalAktif < totalSoal - 1) {
        tampilSoal(soalAktif + 1);
    }
}

function prevSoal() {
    if (soalAktif > 0) {
        tampilSoal(soalAktif - 1);
    }
}

// Auto save setiap interval tertentu
setInterval(() => {
    const form = document.getElementById('formUjian');
    const data = new FormData(form);
    data.append('waktu_sisa', Math.ceil(waktu / 60));

    fetch('autosave_jawaban.php', {
        method: 'POST',
        body: data
    })
    .then(res => res.json())
    .then(response => {
        if (response.status === 'already_done') {
            Swal.fire({
                icon: 'warning',
                title: 'Ujian Sudah Dikerjakan',
                text: response.message
            }).then(() => {
                window.location.href = response.redirect_url;
            });
        } else if (response.status === 'success') {
            console.log('Auto-saved:', response.debug?.final_jawaban ?? 'ok');
        } else {
            console.warn('Auto-save error:', response.message);
        }
    })
    .catch(err => console.error('Auto-save fetch error:', err));
}, syncInterval);


document.addEventListener("DOMContentLoaded", function() {
    var base64Text = "<?php echo $encryptedText; ?>";
    if (base64Text) {
        var decodedText = atob(base64Text);
        document.getElementById("enc").innerHTML = decodedText;
    }
});

function checkIfEncDeleted() {
    var encElement = document.getElementById("enc");

    if (!encElement) {
        window.location.href = "../error_page.php";
    }
}
setInterval(checkIfEncDeleted, 500);

// Fungsi toggle navigasi
function toggleNav() {
    const navContainer = document.querySelector('.question-nav-container');
    if (navContainer.style.display === 'none') {
        navContainer.style.display = 'block';
    } else {
        navContainer.style.display = 'none';
    }
}

function hideNav() {
    document.querySelector('.question-nav-container').style.display = 'none';
}

// Event listeners
document.getElementById('navToggle').addEventListener('click', toggleNav);
document.querySelector('.card-header button.close').addEventListener('click', hideNav);

// Update waktu_sisa setiap detik
setInterval(() => {
    document.getElementById('waktu_sisa').value = waktu;
}, 1000);
function getSoalBelumDijawab() {
    let kosong = [];

    document.querySelectorAll('.nav-btn').forEach(btn => {
        const nomorAsli = btn.getAttribute('data-nomor');
        const nomorUrut = btn.getAttribute('data-urut');

        const inputs = document.querySelectorAll(`[name^="jawaban[${nomorAsli}]"]`);

        let terisi = false;

        inputs.forEach(input => {
            if ((input.type === 'radio' || input.type === 'checkbox') && input.checked) {
                terisi = true;
            }
            else if ((input.tagName === 'TEXTAREA' || input.tagName === 'SELECT' || input.type === 'text')
                && input.value.trim() !== '') {
                terisi = true;
            }
        });

        if (!terisi) {
            kosong.push({
                asli: nomorAsli,
                urut: nomorUrut
            });
        }
    });

    return kosong;
}

// Tangani klik tombol "Selesai"
document.getElementById('submitBtn').addEventListener('click', function(e) {
    e.preventDefault();

    const soalKosong = getSoalBelumDijawab();

    let htmlKosong = '';

    if (soalKosong.length > 0) {
        htmlKosong += `
            <div style="text-align:left;margin-bottom:10px;">
            <b>Soal berikut belum dijawab:</b><br><br>
        `;

        soalKosong.forEach(s => {
            htmlKosong += `
                <button type="button" 
                        onclick="tampilSoal(${s.urut - 1}); Swal.close();" 
                        style="margin:3px;padding:5px 10px;border-radius:5px;border:1px solid #ccc;background:#f8d7da;">
                    No ${s.urut}
                </button>
            `;
        });

        htmlKosong += `</div><hr>`;
    }

    const sisaDetik = parseInt(waktu) || 0;
const menit = Math.floor(sisaDetik / 60);
const detik = sisaDetik % 60;
const formatWaktu =
    `${menit.toString().padStart(2, '0')}:${detik.toString().padStart(2, '0')}`;

Swal.fire({
    title: 'Konfirmasi Selesai Ujian',
    width: 600,
    html: `
        <div style="text-align:left">

            <div style="
                background:#e9f3ff;
                padding:12px;
                border-radius:8px;
                margin-bottom:15px;
                font-size:16px;">
                ⏱️ <b>Sisa Waktu:</b> ${formatWaktu}
            </div>

            ${soalKosong.length > 0 ? `
                <div style="
                    background:#fff3cd;
                    padding:12px;
                    border-radius:8px;
                    margin-bottom:10px;">
                    <b>⚠️ Soal belum dijawab:</b><br><br>
                    <div style="display:flex;flex-wrap:wrap;gap:6px;">
                        ${soalKosong.map(s => `
                            <button type="button"
                                onclick="tampilSoal(${s.urut - 1}); Swal.close();"
                                style="
                                    padding:6px 10px;
                                    border-radius:6px;
                                    border:1px solid #dc3545;
                                    background:#f8d7da;
                                    cursor:pointer;">
                                No ${s.urut}
                            </button>
                        `).join('')}
                    </div>
                </div>
            ` : `
                <div style="
                    background:#e6ffed;
                    padding:12px;
                    border-radius:8px;
                    margin-bottom:10px;">
                    ✅ Semua soal sudah terjawab
                </div>
            `}

            <div style="margin-top:15px">
                <input type="checkbox" id="konfirmasiCek">
                <label for="konfirmasiCek">
                    Saya yakin ingin mengakhiri ujian ini
                </label>
            </div>

        </div>
    `,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Selesai Ujian',
    cancelButtonText: 'Kembali',
    preConfirm: () => {
        const checkbox = document.getElementById('konfirmasiCek');
        if (!checkbox.checked) {
            Swal.showValidationMessage('Centang konfirmasi terlebih dahulu.');
            return false;
        }
        return true;
    }
}).then((result) => {
    if (result.isConfirmed) {
        document.getElementById('formUjian').submit();
    }
});

});



// Panggil pertama kali untuk inisialisasi
updateNavigationButtons();
// Tampilkan soal pertama saat halaman dimuat
window.onload = function() {
    tampilSoal(0);
    cekTombolSelesai();
    setInterval(updateTimer, 1000);
    updateTimer();
};

// Fungsi untuk update status tombol navigasi
function updateNavButtons() {
    document.querySelectorAll('.nav-btn').forEach(btn => {
        const nomor = btn.getAttribute('data-nomor');
        const inputs = document.querySelectorAll(`[name^="jawaban[${nomor}]"]`);
        let answered = false;

        inputs.forEach(input => {
            if ((input.type === 'radio' || input.type === 'checkbox') && input.checked) {
                answered = true;
            } else if ((input.type === 'text' || input.tagName === 'TEXTAREA' || input.tagName ===
                    'SELECT') && input.value.trim() !== '') {
                answered = true;
            }
        });

        if (answered) {
            btn.classList.add('answered');
            btn.setAttribute('data-answered', 'true');
        } else {
            btn.classList.remove('answered');
            btn.setAttribute('data-answered', 'false');
        }
    });
}

// Panggil saat halaman load dan setiap ada perubahan jawaban
document.addEventListener('DOMContentLoaded', function() {
    updateNavButtons();

    // Deteksi perubahan jawaban
    document.querySelectorAll('input, textarea, select').forEach(el => {
        el.addEventListener('change', updateNavButtons);
    });
});

document.addEventListener("DOMContentLoaded", function() {
    const images = document.querySelectorAll(".question-text img");

    images.forEach(img => {
        img.style.cursor = 'zoom-in';
        img.addEventListener("click", function() {
            const modal = document.getElementById("imageModal");
            const modalImg = document.getElementById("modalImage");
            modalImg.src = this.src;
            modal.classList.add("active");
        });
    });
});

function closeModal(event) {
    const modal = document.getElementById("imageModal");
    const modalImg = document.getElementById("modalImage");

    // Supaya klik gambar tidak menutup modal
    if (event.target === modal || event.target.classList.contains("close-btn")) {
        modal.classList.remove("active");
        modalImg.src = "";
    }
}

const defaultFontSize = 16;
let currentFontSize = defaultFontSize;

function changeFontSize(delta) {
    currentFontSize += delta;
    if (currentFontSize < 10) currentFontSize = 10;
    if (currentFontSize > 30) currentFontSize = 30;

    document.querySelectorAll('.question-container').forEach(container => {
        container.style.fontSize = currentFontSize + 'px';
    });
}

function resetFontSize() {
    currentFontSize = defaultFontSize;
    document.querySelectorAll('.question-container').forEach(container => {
        container.style.fontSize = defaultFontSize + 'px';
    });
}



document.addEventListener("DOMContentLoaded", function() {
    var base64Text = "<?php echo $encryptedText; ?>";
    var versiSaya = "<?= $data['versi_aplikasi'] ?? '1.0.0' ?>"; // ambil dari database

    if (base64Text) {
        var decodedText = atob(base64Text);
        document.getElementById("enc").innerHTML = decodedText + " v." + versiSaya;
    } else {
        document.getElementById("enc").innerHTML = "v." + versiSaya;
    }
});


function checkIfEncDeleted() {
    var encElement = document.getElementById("enc");

    if (!encElement) {
        window.location.href = "../error_page.php";
    }
}
setInterval(checkIfEncDeleted, 500);
    </script>