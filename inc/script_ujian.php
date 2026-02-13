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
function getSoalBelumLengkap() {
    let belumLengkap = [];

    document.querySelectorAll('.nav-btn[data-incomplete="true"]').forEach(btn => {
        belumLengkap.push({
            asli: btn.getAttribute('data-nomor'),
            urut: btn.getAttribute('data-urut')
        });
    });

    return belumLengkap;
}
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
    const soalBelumLengkap = getSoalBelumLengkap();

    let htmlKosong = '';

    if (soalKosong.length > 0) {
        htmlKosong += `
            <div style="text-align:center;margin-bottom:10px;">
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
    didOpen: () => {

        const adaBermasalah = document.querySelectorAll(
            '.nav-btn:not([data-answered="true"])'
        ).length > 0;

        if (adaBermasalah) {
            Swal.getConfirmButton().disabled = true;
            Swal.getConfirmButton().innerText = 'Lengkapi semua soal dulu';
        }

        // ⬇⬇ TAMBAHKAN DI SINI ⬇⬇
        const timerEl = document.getElementById('swalTimer');

        const interval = setInterval(() => {
            let menit = Math.floor(waktu / 60);
            let detik = waktu % 60;

            timerEl.innerText =
                `${menit.toString().padStart(2, '0')}:${detik.toString().padStart(2, '0')}`;

            if (waktu <= 0) clearInterval(interval);
        }, 1000);

    },
    title: 'Konfirmasi Selesai Ujian',
    width: 600,
   html: `
<div style="text-align:left">

    <div id="swalTimerBox" style="
        background:#e9f3ff;
        padding:12px;
        border-radius:8px;
        margin-bottom:15px;
        font-size:16px;
        text-align:center;">
        ⏱️ <b>Sisa Waktu:</b> <span id="swalTimer">--:--</span>
    </div>

    ${(soalKosong.length > 0 || soalBelumLengkap.length > 0) ? `
        <div style="
            background:#fff3cd;
            padding:12px;
            border-radius:8px;
            margin-bottom:10px;text-align:center">
            <b>⚠️ Soal belum dijawab / belum lengkap:</b><br><br>
            <div style="display:flex;flex-wrap:wrap;gap:6px;justify-content:center;">
                
                ${soalKosong.map(s => `
                    <button type="button"
                        onclick="tampilSoal(${s.urut - 1}); Swal.close();"
                        style="padding:6px 10px;border-radius:6px;border:1px solid #dc3545;background:#f8d7da;">
                        No ${s.urut}
                    </button>
                `).join('')}

                ${soalBelumLengkap.map(s => `
                    <button type="button"
                        onclick="tampilSoal(${s.urut - 1}); Swal.close();"
                        style="padding:6px 10px;border-radius:6px;border:1px solid #6c757d;background:#ced4da;">
                        No ${s.urut}
                    </button>
                `).join('')}

            </div>
        </div>
    ` : `

    `}

    <div style="margin-top:15px;text-align:center">
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

        let adaIsi = false;
        let lengkap = true;

        const groupRadio = {};

        inputs.forEach(input => {

            // RADIO / BENAR SALAH (harus semua group terisi)
            if (input.type === 'radio') {
                if (!groupRadio[input.name]) groupRadio[input.name] = false;
                if (input.checked) {
                    groupRadio[input.name] = true;
                    adaIsi = true;
                }
            }

            // CHECKBOX (minimal 1)
            else if (input.type === 'checkbox') {
                if (input.checked) {
                    adaIsi = true;
                }
            }

            // SELECT (menjodohkan wajib semua)
            else if (input.tagName === 'SELECT') {
                if (input.value !== '') {
                    adaIsi = true;
                } else {
                    lengkap = false;
                }
            }

            // TEXTAREA / URAIAN
            else if (input.tagName === 'TEXTAREA') {
                if (input.value.trim() !== '') {
                    adaIsi = true;
                } else {
                    lengkap = false;
                }
            }
        });

        // cek group radio
        Object.values(groupRadio).forEach(v => {
            if (!v) lengkap = false;
        });

        // RESET STATUS
        btn.removeAttribute('data-answered');
        btn.removeAttribute('data-incomplete');

        if (!adaIsi) return;

        if (lengkap) {
            btn.setAttribute('data-answered', 'true');   // hijau
        } else {
            btn.setAttribute('data-incomplete', 'true'); // merah
        }
    });
}

// Panggil saat halaman load dan setiap ada perubahan jawaban
document.addEventListener('DOMContentLoaded', function() {
    updateNavButtons();

    // Deteksi perubahan jawaban
    document.querySelectorAll('input, textarea, select').forEach(el => {
    el.addEventListener('change', updateNavButtons);
    el.addEventListener('keyup', updateNavButtons);
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
    <script>
document.addEventListener("DOMContentLoaded", function () {

    const toggleBtn = document.getElementById("darkModeToggle");
    const root = document.documentElement;

    // Set icon saat load
    if (root.classList.contains("dark-mode")) {
        toggleBtn.innerHTML = '<i class="fas fa-sun"></i>';
    }

    toggleBtn.addEventListener("click", function () {
        root.classList.toggle("dark-mode");

        const isDark = root.classList.contains("dark-mode");

        localStorage.setItem("admin-dark-mode", isDark ? "enabled" : "disabled");

        toggleBtn.innerHTML = isDark
            ? '<i class="fas fa-sun"></i>'
            : '<i class="fas fa-moon"></i>';

        applyChartTheme(isDark);
    });

});
</script>
<script>
function applyChartTheme(isDark) {

    const textColor = isDark ? '#e4e6eb' : '#666';
    const gridColor = isDark ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.05)';

    Chart.defaults.color = textColor;
    Chart.defaults.borderColor = gridColor;

    [chartRekapUjian, chartKodeSoal, chartTopSiswa].forEach(chart => {
        if (!chart) return;

        if (chart.options.scales) {
            Object.values(chart.options.scales).forEach(scale => {
                if (scale.ticks) scale.ticks.color = textColor;
                if (scale.grid) scale.grid.color = gridColor;
            });
        }

        if (chart.options.plugins?.legend) {
            chart.options.plugins.legend.labels = {
                color: textColor
            };
        }

        if (chart.options.plugins?.title) {
            chart.options.plugins.title.color = textColor;
        }

        chart.update();
    });
}

document.addEventListener("DOMContentLoaded", function() {

    const isDark = document.documentElement.classList.contains("dark-mode");
    applyChartTheme(isDark);

    document.getElementById("darkModeToggle")
        .addEventListener("click", function() {
            setTimeout(() => {
                const darkActive = document.body.classList.contains("dark-mode");
                applyChartTheme(darkActive);
            }, 100);
        });

});
</script>