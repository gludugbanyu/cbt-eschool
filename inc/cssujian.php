<style>
.question-container {
    font-size: 16px;
}

.question-container * {
    font-size: inherit !important;
    /* Paksa semua elemen dalam container ikut ukuran induknya */
}

.modal-img {
    display: none;
    position: fixed;
    z-index: 2000;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.85);
    justify-content: center;
    align-items: center;
    padding: 20px;
}

.modal-img.active {
    display: flex;
}

.modal-content-img {
    max-width: 100%;
    max-height: 90vh;
    border-radius: 10px;
    box-shadow: 0 0 20px rgba(255, 255, 255, 0.3);
    object-fit: contain;
}

.close-btn {
    position: absolute;
    top: 20px;
    right: 25px;
    color: white;
    font-size: 30px;
    font-weight: bold;
    background: rgba(0, 0, 0, 0.5);
    border-radius: 50%;
    width: 40px;
    height: 40px;
    text-align: center;
    line-height: 40px;
    cursor: pointer;
    z-index: 2100;
}

.question-container img {
    height: 250px;
    width: 100%;
    object-fit: contain;
    max-width: 700px !important;
    max-height: 300px !important;
    display: block;
}

.question-container {
    display: none;
    margin-bottom: 20px;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.question-container.active {
    display: block;
    min-height: 500px;
}

.navigation-buttons {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
}

#autoSaveStatus {
    display: none;
    background-color: #28a745;
    color: white;
    padding: 5px 10px;
    border-radius: 4px;
    margin-bottom: 10px;
    text-align: center;
}

#timer {
    font-size: 15px;
    color: red;
    background-color: rgba(255, 255, 255, 0.2);
    padding: 5px 10px;
    border-radius: 20px;
}

#texttimer {
    font-size: 15px;
    border: solid 1px red;
    color: black;
    background-color: rgb(255, 255, 255);
    padding: 5px 10px;
    border-radius: 20px;
}

.question-text {
    font-weight: bold;
    margin-bottom: 15px;
}

.answer-option {
    margin-bottom: 8px;
}

.matching-table {
    width: 100%;
    margin-bottom: 15px;
}

.matching-table td {
    padding: 8px;
    vertical-align: middle;
}

.essay-textarea {
    width: 100%;
    min-height: 150px;
    padding: 10px;
    border-radius: 5px;
    border: 1px solid #ced4da;
}

.submit-btn {
    margin-top: 20px;
}

.spinner-container {
    text-align: center;
    color: white;
}

.spinner-border {
    width: 3rem;
    height: 3rem;
}

/* Scrollbar custom */
.question-nav-container::-webkit-scrollbar {
    width: 8px;
}

.question-nav-container::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.question-nav-container::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.question-nav-container::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Tombol sudah diisi */
.nav-btn[data-answered="true"] {
    background-color: #198754;
    /* Warna hijau */
    border-color: #198754 !important;
    color: white !important;
}
.nav-btn[data-incomplete="true"] {
    background-color: #484848;
    /* Warna hijau */
    border-color: #484848 !important;
    color: white !important;
}
/* Indicator dot untuk soal terjawab */
/* DOT KUNING = lengkap */
.nav-btn[data-answered="true"]::after{
    content:'';
    position:absolute;
    top:-3px;
    right:-3px;
    width:10px;
    height:10px;
    background:#ffc107;
    border-radius:50%;
    border:1px solid white;
}

/* DOT MERAH = belum lengkap */
.nav-btn[data-incomplete="true"]::after{
    content:'';
    position:absolute;
    top:-3px;
    right:-3px;
    width:10px;
    height:10px;
    background:#dc3545;
    border-radius:50%;
    border:1px solid white;
}

/* Hover Effect */
.nav-btn:hover {
    transform: scale(1.1);
    box-shadow: 0 0 5px rgba(0, 0, 0, 0.2);
}

.option-circle {
    display: flex;
    align-items: center;
    margin: 10px 0;
    cursor: pointer;
    font-size: 14px;
}

.option-circle input[type="radio"],
.option-circle input[type="checkbox"] {
    display: none;
}

.option-circle span {
    display: inline-flex;
    justify-content: center;
    align-items: center;
    width: 25px;
    height: 25px;
    border-radius: 50%;
    border: 1px solid grey !important;
    color: black !important;
    font-weight: bold;
    margin-right: 12px;
    transition: all 0.3s ease;
}

/* Saat dipilih */
.option-circle input[type="radio"]:checked+span,
.option-circle input[type="checkbox"]:checked+span {
    background-color: rgb(20, 158, 100);
    color: white !important;
    border-color: rgb(20, 158, 100) !important;
}

.custom-card-header {
    border-bottom: 1px solid #343a40;
    /* Garis bawah header card */
    background-color: #f8f9fa;
    /* Warna latar belakang header */
    padding: 10px;
    /* Padding tambahan untuk header */
    font-weight: bold;
    /* Agar teks header lebih tebal */
}

.custom-radio-spacing {
    margin-right: 100px;
    /* Menambah jarak kanan antar radio button */
}

input[type="radio"]:not(:checked) {
    border-color: black;
    /* Warna border hitam */
}

input[type="radio"]:checked {
    background-color: green;
    /* Warna latar belakang hijau */
    border-color: green;
    /* Warna border hijau */
}

input[type="checkbox"]:not(:checked) {
    border-color: black;
    /* Warna border hitam */
}

input[type="checkbox"]:checked {
    background-color: green;
    /* Warna latar belakang hijau */
    border-color: green;
    /* Warna border hijau */
}

.question-container table,
.question-container td,
.question-container th {
    border: 1px solid black !important;
}

.table {
    border-collapse: collapse !important;
}

table {
    border-collapse: collapse;
    width: 100%;
}

td {
    border: 1px solid black;
    text-align: center;
    vertical-align: middle;
    height: 50px;
    /* supaya kelihatan vertical center */
}

.navigation-buttons {
    display: flex;
    justify-content: space-between;
    margin-top: 5px;
}

.dropdown-wide {
    min-width: 220px;
}

.navbar-bg.sticky-top {
    position: sticky;
    top: 0;
    z-index: 1030;
    background-color: var(--adminkit-body-bg) !important;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}

html,
body {
    height: 100%;
    margin: 0;
    overflow: hidden;
}

.wrapper {
    display: flex;
    flex-direction: column;
    height: 100vh;
}

main.content {
    flex: 1;
    overflow-y: auto;
    padding-bottom: 220px; /* sebelumnya 20px */
    margin-bottom: 0; /* hilangkan margin agar tidak dobel spacing */
}

@media (max-width: 768px) {
    main.content {
        padding-bottom: 260px; /* extra space di HP agar tombol tidak ketutupan */
    }
}


/* Loading Spinner Styles */
#loadingOverlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(255, 255, 255, 0.8);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    display: none;
}

.spinner-container {
    text-align: center;
}

.spinner-border {
    width: 3rem;
    height: 3rem;
}

#soal.sidebar-dropdown a {
    background-color: rgba(0, 0, 0, 0.15);
    /* Warna gelap dengan transparansi */
    padding: 10px 25px;
    margin-top: -1px;
    /* Untuk menghilangkan gap */
}

/* ===== NAVIGASI SOAL FINAL BERSIH ===== */

.question-nav-container{
    position: fixed;
    bottom: 90px;
    right: 15px;
    z-index: 1100;
    width: min(92vw, 420px);
    max-height: 65vh;
    overflow-y: auto;
    display: none;
}

/* GRID RESPONSIVE */
.question-nav{
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(45px, 1fr));
    gap: 6px;
    justify-items: center;
}

/* TOMBOL BULAT STABIL */
.nav-btn {
    width: 40px;
    height: 40px;
    border: 2px solid grey !important;
    color: grey !important;
    background: transparent;
    border-radius: 50%;
    margin: 5px;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    font-size: 14px;
}

/* RESPONSIVE */
@media (max-width: 768px){
    .question-nav-container{
        width: 92vw;
        right: 4vw;
        bottom: 80px;
    }
}

@media (max-width: 480px){
    .question-nav-container{
        width: 94vw;
        right: 3vw;
        bottom: 75px;
        max-height: 70vh;
    }

    .nav-btn{
        width: 40px;
        height: 40px;
        font-size: 14px;
    }
}
/* =======================================================
   DARK MODE – QUESTION / UJIAN PAGE FIX TOTAL
======================================================= */

/* Container soal */
.dark-mode .question-container {
    background: #222e3c !important;
    border-color: rgba(255,255,255,0.1) !important;
    color: #e4e6eb !important;
}

/* Semua text di dalam soal */
.dark-mode .question-container * {
    color: #e4e6eb !important;
}

/* Table dalam soal */
.dark-mode .question-container table,
.dark-mode .question-container th,
.dark-mode .question-container td {
    border: 1px solid rgba(255,255,255,0.15) !important;
    color: #e4e6eb !important;
}

/* Table umum */
.dark-mode table,
.dark-mode td,
.dark-mode th {
    border-color: rgba(255,255,255,0.15) !important;
}

/* Timer */
.dark-mode #timer {
    color: #ff6b6b !important;
    background-color: rgba(255,255,255,0.08) !important;
}

.dark-mode #texttimer {
    background-color: #2b394a !important;
    color: #e4e6eb !important;
    border-color: #ff6b6b !important;
}

/* Text soal */
.dark-mode .question-text {
    color: #ffffff !important;
}

/* Option circle */
.dark-mode .option-circle span {
    border-color: #94a3b8 !important;
    color: #e4e6eb !important;
}

/* Tombol navigasi soal */
.dark-mode .nav-btn {
    border: 2px solid #94a3b8 !important;
    color: #e4e6eb !important;
    background: transparent !important;
}

.dark-mode .nav-btn:hover {
    background: #3a4d63 !important;
    color: #ffffff !important;
}

/* Dropdown nav container */
.dark-mode .question-nav-container {
    background: transparent !important;
}

/* Header custom */
.dark-mode .custom-card-header {
    background-color: #2b394a !important;
    border-color: rgba(255,255,255,0.1) !important;
    color: #ffffff !important;
}

/* Essay textarea */
.dark-mode .essay-textarea {
    background-color: #2b394a !important;
    border: 1px solid rgba(255,255,255,0.1) !important;
    color: #ffffff !important;
}

/* Loading overlay */
.dark-mode #loadingOverlay {
    background-color: rgba(0,0,0,0.85) !important;
}

/* Scrollbar */
.dark-mode .question-nav-container::-webkit-scrollbar-track {
    background: #1f2a38 !important;
}

.dark-mode .question-nav-container::-webkit-scrollbar-thumb {
    background: #3a4d63 !important;
}

/* Spinner text */
.dark-mode .spinner-container {
    color: #e4e6eb !important;
}
/* ==========================================
   DARK MODE – INLINE WHITE / BLACK FIX
========================================== */

.dark-mode b[style*="background-color:#ffffff"],
.dark-mode b[style*="background-color: #ffffff"],
.dark-mode b[style*="background-color: rgb(255, 255, 255)"] {
    background-color: #2b394a !important;
    color: #e4e6eb !important;
}

.dark-mode b[style*="color:black"],
.dark-mode b[style*="color: black"],
.dark-mode b[style*="color: rgb(0, 0, 0)"] {
    color: #e4e6eb !important;
}
/* =========================================================
   🔥 FORCE DARK MODE – OVERRIDE INLINE STYLE
   TANPA UBAH HTML / JS
========================================================= */

/* Semua elemen di dalam SweetAlert */
.dark-mode .swal2-popup * {
    background-color: transparent !important;
    color: #e4e6eb !important;
    border-color: rgba(255,255,255,0.15) !important;
}

/* Popup utama */
.dark-mode .swal2-popup {
    background: #263445 !important;
    color: #ffffff !important;
}

/* Box biru timer (#e9f3ff) */
.dark-mode .swal2-popup [style*="#e9f3ff"],
.dark-mode .swal2-popup [style*="rgb(233, 243, 255)"] {
    background: #2b394a !important;
    color: #e4e6eb !important;
}

/* Box kuning warning (#fff3cd) */
.dark-mode .swal2-popup [style*="#fff3cd"] {
    background: #3a321d !important;
    color: #ffc107 !important;
    border-color: #5a4a27 !important;
}

/* Tombol merah (#f8d7da) */
.dark-mode .swal2-popup [style*="#f8d7da"] {
    background: #3a1f24 !important;
    border-color: #ff6b6b !important;
    color: #ff6b6b !important;
}

/* Tombol abu (#ced4da) */
.dark-mode .swal2-popup [style*="#ced4da"] {
    background: #2b394a !important;
    border-color: #94a3b8 !important;
    color: #e4e6eb !important;
}

/* Border hitam */
.dark-mode .swal2-popup [style*="#000"],
.dark-mode .swal2-popup [style*="black"] {
    border-color: rgba(255,255,255,0.2) !important;
    color: #e4e6eb !important;
}

/* Background putih inline */
.dark-mode .swal2-popup [style*="background:#fff"],
.dark-mode .swal2-popup [style*="background-color: white"] {
    background: #2b394a !important;
    color: #e4e6eb !important;
}
/* ======================================================
   DARK MODE – FORCE CLOSE BUTTON FIX (FINAL)
====================================================== */

/* Semua tombol close di dark mode */
.dark-mode button.close,
.dark-mode .close {
    background-color: #2b394a !important;
    color: #e4e6eb !important;
    border: 1px solid rgba(255,255,255,0.2) !important;
    box-shadow: none !important;
}

/* Hover */
.dark-mode button.close:hover,
.dark-mode .close:hover {
    background-color: #3a4d63 !important;
    color: #ffffff !important;
}

/* Span X di dalamnya */
.dark-mode button.close span {
    color: #e4e6eb !important;
}
/* =========================================
   DARK MODE – KEMBALIKAN BUTTON SWEETALERT
========================================= */

.dark-mode .swal2-confirm,
.dark-mode .swal2-cancel {
    background-color: revert !important;
    color: revert !important;
    border: revert !important;
    box-shadow: revert !important;
}
</style>