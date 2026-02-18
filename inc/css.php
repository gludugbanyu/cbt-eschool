<link rel="icon" type="image/png" href="../assets/images/icon.png" />
<script>
if (localStorage.getItem("admin-dark-mode") === "enabled") {
    document.documentElement.classList.add("dark-mode");
}
</script>
<link href="../assets/bootstrap-5.3.6/css/bootstrap.min.css" rel="stylesheet">
<link href="../assets/fontawesome/css/all.min.css" rel="stylesheet">
<link href="../assets/adminkit/static/css/app.css" rel="stylesheet">
<link href="../assets/datatables/datatables.css" rel="stylesheet">
<style>
html.dark-mode,
html.dark-mode body {
    background-color: #1c2530 !important;
    color: #e4e6eb !important;
}

#toast-container {
    position: fixed !important;
    bottom: 1rem;
    right: 1rem;
    left: auto !important;
    z-index: 9999;
}

.fa-beat,
.fa-bounce,
.fa-fade,
.fa-beat-fade,
.fa-flip,
.fa-pulse,
.fa-shake,
.fa-spin,
.fa-spin-pulse {
    animation-duration: 2s;
    animation-iteration-count: infinite;
}

.table-wrapper {
    overflow-x: auto;
    /* Enable horizontal scrolling */
    -webkit-overflow-scrolling: touch;
    /* Smooth scrolling for mobile */
}

table th,
table td {
    text-align: left !important;
}

.blinking {
    animation: blink 1s infinite;
}

@keyframes blink {

    0%,
    100% {
        opacity: 1;
    }

    50% {
        opacity: 0;
    }
}

li.sidebar-item.submenu>a.sidebar-link {
    background: linear-gradient(to left, #222e3c, #3a4d63) !important;
    border-bottom: 2px solid #222e3c;
}

/* === FIX CARD DASHBOARD MOBILE === */
@media (max-width: 768px) {

    .card[style*="height: 180px"],
    .card[style*="height: 150px"] {
        height: auto !important;
        min-height: unset !important;
    }

    .card-body {
        padding: 14px !important;
    }

    .card-body ul {
        padding-left: 18px;
        font-size: 13px;
    }

    .card-title {
        font-size: 15px !important;
    }

    .card-text {
        font-size: 13px !important;
    }

    /* icon besar di belakang jangan ganggu teks */
    .card i.position-absolute {
        font-size: 70px !important;
        bottom: -10px !important;
        right: -10px !important;
        opacity: 0.06 !important;
    }
}

/* Modern Toast */
#toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
}

#toast-container .toast {
    min-width: 320px;
    border-radius: 18px;
    backdrop-filter: blur(12px);
    background: rgba(30, 41, 59, 0.85);
    border: 1px solid rgba(255, 255, 255, 0.08);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.25);
    transform: translateX(100%);
    opacity: 0;
    transition: all 0.4s ease;
}

#toast-container .toast.showing,
#toast-container .toast.show {
    transform: translateX(0);
    opacity: 1;
}

#toast-container .toast-body {
    font-size: 14px;
    line-height: 1.5;
}

#toast-container .toast i {
    font-size: 18px;
}

#toast-container .btn-close {
    filter: invert(1);
    opacity: 0.8;
}

.support-cta {
    border-left: 4px solid var(--bs-primary) !important;
    position: relative;
    overflow: hidden;
    transition: 0.2s ease;
}

.support-cta:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
}

.bg-icon {
    font-size: 120px;
    bottom: -25px;
    right: -25px;
    opacity: 0.05;
    color: #6c757d;
    pointer-events: none;
}

.cta-badge {
    position: absolute;
    top: 0;
    right: 0;
    background: var(--bs-primary);
    color: #fff;
    font-size: 10px;
    padding: 6px 12px;
    border-bottom-left-radius: 8px;
}

/* Ribbon Sudut */
.cta-ribbon {
    position: absolute;
    top: 0;
    right: 0;
    background: grey;
    color: #fff;
    font-size: 10px;
    font-weight: 600;
    padding: 6px 14px;
    border-bottom-left-radius: 10px;
    z-index: 5;
}

/* Icon Background */
.bg-icon {
    font-size: 120px;
    bottom: -25px;
    right: -25px;
    opacity: 0.05;
    color: #6c757d;
    pointer-events: none;
}

/* =======================================================
   DARK MODE – DATATABLES FIX
======================================================= */

.dark-mode .dataTables_wrapper {
    color: #e4e6eb !important;
}

.dark-mode .dataTables_wrapper .dataTables_info,
.dark-mode .dataTables_wrapper .dataTables_length,
.dark-mode .dataTables_wrapper .dataTables_filter,
.dark-mode .dataTables_wrapper label {
    color: #cfd8e3 !important;
}

.dark-mode .dataTables_wrapper input,
.dark-mode .dataTables_wrapper select {
    background-color: #2b394a !important;
    color: #fff !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
}

.dark-mode .dataTables_wrapper .paginate_button {
    color: #cfd8e3 !important;
}

.dark-mode .dataTables_wrapper .paginate_button.current {
    background-color: #3a4d63 !important;
    color: #fff !important;
}

/* =======================================================
   FULL DARK MODE – ADMIN CBT (STABLE VERSION)
   Base: #222e3c
======================================================= */

.dark-mode {
    background-color: #1c2530 !important;
    color: #e4e6eb !important;
}

/* =======================================================
   MAIN CONTENT
======================================================= */

.dark-mode main.content {
    background-color: #222e3c !important;
    color: #e4e6eb !important;
}

/* =======================================================
   NAVBAR
======================================================= */

.dark-mode .navbar,
.dark-mode .navbar-bg {
    background-color: #222e3c !important;
    color: #ffffff !important;
}

.dark-mode .nav-link,
.dark-mode .navbar .dropdown-toggle {
    color: #e4e6eb !important;
}

/* =======================================================
   CARD
======================================================= */

.dark-mode .card {
    background-color: #263445 !important;
    color: #e4e6eb !important;
    border: 1px solid rgba(255, 255, 255, 0.06) !important;
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.25);
}

.dark-mode .card-header {
    background-color: #2b394a !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
    color: #ffffff !important;
}

/* =======================================================
   TABLE (FIX TOTAL)
======================================================= */

.dark-mode .table {
    background-color: transparent !important;
    color: #e4e6eb !important;
}

.dark-mode .table th,
.dark-mode .table td {
    color: #e4e6eb !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
}

.dark-mode .table thead,
.dark-mode .table thead th {
    background-color: #2f3f52 !important;
    color: #ffffff !important;
}

.dark-mode .table-secondary,
.dark-mode .table-secondary th {
    background-color: #2f3f52 !important;
    color: #ffffff !important;
}

.dark-mode .table-striped>tbody>tr:nth-of-type(odd) {
    background-color: rgba(255, 255, 255, 0.03) !important;
}

.dark-mode .table-hover tbody tr:hover {
    background-color: rgba(255, 255, 255, 0.06) !important;
}

/* Override semua text bootstrap */
.dark-mode .text-dark,
.dark-mode .text-body,
.dark-mode .text-muted {
    color: #cfd8e3 !important;
}

/* =======================================================
   DATATABLES FIX TOTAL
======================================================= */

.dark-mode .dataTables_wrapper {
    color: #e4e6eb !important;
}

.dark-mode .dataTables_wrapper .dataTables_info,
.dark-mode .dataTables_wrapper .dataTables_length,
.dark-mode .dataTables_wrapper .dataTables_filter,
.dark-mode .dataTables_wrapper label {
    color: #cfd8e3 !important;
}

.dark-mode .dataTables_wrapper input,
.dark-mode .dataTables_wrapper select {
    background-color: #2b394a !important;
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
}

.dark-mode .dataTables_wrapper .paginate_button {
    color: #cfd8e3 !important;
}

.dark-mode .dataTables_wrapper .paginate_button.current {
    background-color: #3a4d63 !important;
    color: #ffffff !important;
    border: none !important;
}

/* =======================================================
   FORM
======================================================= */

.dark-mode .form-control,
.dark-mode .form-select {
    background-color: #2b394a !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    color: #ffffff !important;
}

.dark-mode .form-control::placeholder {
    color: #aab4c3 !important;
}

.dark-mode .form-control:focus {
    background-color: #2b394a !important;
    border-color: #3d5168 !important;
    box-shadow: none !important;
}

/* =======================================================
   DROPDOWN
======================================================= */

.dark-mode .dropdown-menu {
    background-color: #2b394a !important;
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
}

.dark-mode .dropdown-item {
    color: #e4e6eb !important;
}

.dark-mode .dropdown-item:hover {
    background-color: rgba(255, 255, 255, 0.05) !important;
}

/* =======================================================
   MODAL
======================================================= */

.dark-mode .modal-content {
    background-color: #263445 !important;
    color: #ffffff !important;
}

.dark-mode .modal-header,
.dark-mode .modal-footer {
    border-color: rgba(255, 255, 255, 0.08) !important;
}

/* =======================================================
   BUTTON
======================================================= */

.dark-mode .btn-outline-secondary {
    color: #cfd8e3 !important;
    border-color: rgba(255, 255, 255, 0.2) !important;
}

.dark-mode .btn-outline-secondary:hover {
    background-color: #2b394a !important;
}

.dark-mode .btn-light {
    background-color: #2b394a !important;
    color: #ffffff !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
}

/* =======================================================
   TOAST
======================================================= */

.dark-mode #toast-container .toast {
    background: rgba(34, 46, 60, 0.95) !important;
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
    color: #ffffff !important;
}

/* =======================================================
   FOOTER
======================================================= */

.dark-mode footer {
    background-color: #1b2530 !important;
    color: #cfd8e3 !important;
}

/* =======================================================
   TRANSITION SMOOTH
======================================================= */

body,
main.content,
.card,
.navbar,
.table,
.form-control,
.dropdown-menu {
    transition: background-color 0.25s ease, color 0.25s ease;
}

.dark-mode .text-primary {
    color: #6ea8fe !important;
}

.dark-mode .btn-primary {
    background-color: #3a4d63 !important;
    border-color: #3a4d63 !important;
}

.dark-mode .bg-primary {
    background-color: #3a4d63 !important;
}

/* =======================================================
   DARK MODE – FIX HEADING COLOR
======================================================= */

.dark-mode h1,
.dark-mode h2,
.dark-mode h3,
.dark-mode h4,
.dark-mode h5,
.dark-mode h6 {
    color: #ffffff !important;
}

.dark-mode .card h1,
.dark-mode .card h2,
.dark-mode .card h3,
.dark-mode .card h4,
.dark-mode .card h5,
.dark-mode .card h6 {
    color: #ffffff !important;
}

/* =======================================================
   DARK MODE – FIX btn-outline-dark
======================================================= */

.dark-mode .btn-outline-dark {
    color: #e4e6eb !important;
    border-color: #e4e6eb !important;
}

.dark-mode .btn-outline-dark:hover {
    background-color: #e4e6eb !important;
    color: #222e3c !important;
}

.dark-mode .text-secondary {
    color: #94a3b8 !important;
}

/* =======================================================
   DARK MODE – CTA RIBBON FIX
======================================================= */

.dark-mode .cta-ribbon {
    background: #3a4d63 !important;
    color: #e4e6eb !important;
}

/* =======================================================
   DARK MODE – SWEETALERT SAFE FIX
   (TIDAK MERUSAK ICON ANIMASI)
======================================================= */

.dark-mode .swal2-popup {
    background: #263445 !important;
    color: #ffffff !important;
}

.dark-mode .swal2-title {
    color: #ffffff !important;
}

.dark-mode .swal2-html-container {
    color: #cfd8e3 !important;
}

.dark-mode .swal2-container {
    background: rgba(0, 0, 0, 0.65) !important;
}

.dark-mode .swal2-input,
.dark-mode .swal2-textarea,
.dark-mode .swal2-select {
    background: #2b394a !important;
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
}

.dark-mode .swal2-confirm {
    background-color: #3a4d63 !important;
    color: #ffffff !important;
    border: none !important;
}

.dark-mode .swal2-cancel {
    background-color: #495057 !important;
    color: #ffffff !important;
    border: none !important;
}

/* =======================================================
   DARK MODE – SWEETALERT FINAL STABLE
   (TIDAK MENYENTUH ICON)
======================================================= */

.dark-mode .swal2-popup {
    background-color: #263445 !important;
    color: #ffffff !important;
}

.dark-mode .swal2-title {
    color: #ffffff !important;
}

.dark-mode .swal2-html-container {
    color: #cfd8e3 !important;
}

.dark-mode .swal2-container {
    background: rgba(0, 0, 0, 0.6) !important;
}

.dark-mode .swal2-input,
.dark-mode .swal2-textarea,
.dark-mode .swal2-select {
    background-color: #2b394a !important;
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
}

.dark-mode .swal2-confirm {
    background-color: #3a4d63 !important;
    color: #ffffff !important;
    border: none !important;
}

.dark-mode .swal2-cancel {
    background-color: #495057 !important;
    color: #ffffff !important;
    border: none !important;
}

/* =======================================================
   FIX SWEETALERT ICON WARNA TERWARISI
======================================================= */

.dark-mode .swal2-icon {
    color: initial !important;
}

.dark-mode .swal2-icon * {
    color: initial !important;
}

.dark-mode .swal2-success-circular-line-left,
.dark-mode .swal2-success-circular-line-right,
.dark-mode .swal2-success-fix {
    background-color: transparent !important;
}

/* =======================================================
   DARK MODE – DATATABLES 2 PAGINATION FIX
======================================================= */

/* Default button */
.dark-mode .dataTables_wrapper .dt-paging-button .page-link {
    background-color: #2b394a !important;
    color: #e4e6eb !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
}

/* Hover */
.dark-mode .dataTables_wrapper .dt-paging-button .page-link:hover {
    background-color: #3a4d63 !important;
    color: #ffffff !important;
}

/* Active */
.dark-mode .dataTables_wrapper .dt-paging-button.active .page-link {
    background-color: #3a4d63 !important;
    border-color: #3a4d63 !important;
    color: #ffffff !important;
}

/* Disabled */
.dark-mode .dataTables_wrapper .dt-paging-button.disabled .page-link {
    background-color: #1f2a38 !important;
    color: #6c757d !important;
    border-color: rgba(255, 255, 255, 0.05) !important;
}

/* =======================================================
   DARK MODE – DATATABLES PAGINATION (VARIABLE FIX)
======================================================= */

.dark-mode .pagination {
    --bs-pagination-bg: #2b394a;
    --bs-pagination-color: #e4e6eb;
    --bs-pagination-border-color: rgba(255, 255, 255, 0.1);

    --bs-pagination-hover-bg: #3a4d63;
    --bs-pagination-hover-color: #ffffff;
    --bs-pagination-hover-border-color: #3a4d63;

    --bs-pagination-active-bg: #3a4d63;
    --bs-pagination-active-color: #ffffff;
    --bs-pagination-active-border-color: #3a4d63;

    --bs-pagination-disabled-bg: #1f2a38;
    --bs-pagination-disabled-color: #6c757d;
    --bs-pagination-disabled-border-color: rgba(255, 255, 255, 0.05);
}

/* =======================================================
   DARK MODE – FILE INPUT FIX
======================================================= */

.dark-mode input[type="file"].form-control {
    background-color: #2b394a !important;
    color: #e4e6eb !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
}

/* Tombol "Browse" */
.dark-mode input[type="file"].form-control::file-selector-button {
    background-color: #3a4d63 !important;
    color: #ffffff !important;
    border: none !important;
}

/* Hover tombol */
.dark-mode input[type="file"].form-control::file-selector-button:hover {
    background-color: #4a617d !important;
}

/* =======================================================
   DARK MODE – BOOTSTRAP ALERT FIX
======================================================= */

.dark-mode .alert {
    --bs-alert-bg: #2b394a;
    --bs-alert-color: #e4e6eb;
    --bs-alert-border-color: rgba(255, 255, 255, 0.08);
}

/* Info */
.dark-mode .alert-info {
    --bs-alert-bg: #1f3447;
    --bs-alert-color: #8ecdf5;
    --bs-alert-border-color: #2c4e6b;
}

/* Success */
.dark-mode .alert-success {
    --bs-alert-bg: #1e3a32;
    --bs-alert-color: #4dd4ac;
    --bs-alert-border-color: #275a4e;
}

/* Warning */
.dark-mode .alert-warning {
    --bs-alert-bg: #3a321d;
    --bs-alert-color: #ffc107;
    --bs-alert-border-color: #5a4a27;
}

/* Danger */
.dark-mode .alert-danger {
    --bs-alert-bg: #3a1f24;
    --bs-alert-color: #ff6b6b;
    --bs-alert-border-color: #5a2b32;
}

/* =======================================================
   DARK MODE – CHAT FIX
======================================================= */

/* Chat container */
.dark-mode #chat-box {
    background: #1f2a38 !important;
}

/* Default bubble */
.dark-mode .chat-message {
    background-color: #2b394a !important;
    color: #e4e6eb !important;
    box-shadow: none !important;
}

/* Right bubble */
.dark-mode .chat-line.right .chat-message {
    background-color: #3a4d63 !important;
}

/* Sender name */
.dark-mode .chat-sender {
    color: #94a3b8 !important;
}

/* Timestamp */
.dark-mode .chat-timestamp {
    color: rgba(255, 255, 255, 0.5) !important;
}

/* Admin message */
.dark-mode .chat-line.admin .chat-message {
    background-color: #3a1f24 !important;
    color: #ff6b6b !important;
    border: 1px solid #5a2b32 !important;
}

/* Siswa message */
.dark-mode .chat-line.siswa .chat-message {
    background-color: #1f3447 !important;
    color: #8ecdf5 !important;
    border: 1px solid #2c4e6b !important;
}

/* Form input */
.dark-mode #pesan {
    background-color: #2b394a !important;
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
}

/* Emoji button */
.dark-mode .emoji-button {
    background: #2b394a !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    color: #ffffff !important;
}

.dark-mode .emoji-button:hover {
    background: #3a4d63 !important;
}

/* Emoji dropdown */
.dark-mode .emoji-dropdown {
    background: #263445 !important;
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
}

.dark-mode .emoji-item:hover {
    background: rgba(255, 255, 255, 0.08) !important;
}

/* Card body gradient override */
.dark-mode .card-body {
    background: #222e3c !important;
}

/* =======================================================
   DARK MODE – BG-LIGHT FIX
======================================================= */

.dark-mode .bg-light {
    background-color: #2b394a !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
}

.dark-mode .border {
    border-color: rgba(255, 255, 255, 0.08) !important;
}

/* =======================================================
   DARK MODE – STRIPED TABLE FIX (NO FLICKER)
======================================================= */

.dark-mode .table {
    --bs-table-bg: transparent;
    --bs-table-striped-bg: rgba(255, 255, 255, 0.03);
    --bs-table-hover-bg: rgba(255, 255, 255, 0.06);
    --bs-table-border-color: rgba(255, 255, 255, 0.08);
    --bs-table-color: #e4e6eb;
}

/* Dark mode toggle style */
#darkModeToggle {
    font-size: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.navbar .nav-link {
    height: 40px;
    display: flex;
    align-items: center;
}

#darkModeToggle:hover {
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;
}

.navbar .nav-item {
    display: flex;
    align-items: center;
}

/* =======================================================
   DARK MODE – BOXCHAT FINAL FIX
======================================================= */
.dark-mode .boxchatnya {
    background: #1f2a38 !important;
    border-radius: 10px;
    padding: 12px;
    border: 1px solid rgba(255, 255, 255, 0.06);
    color: #e4e6eb !important;
}

/* ==========================================
   DARK MODE – UJIAN CARD FIX
========================================== */

.dark-mode .ujian-card-hover {
    background: linear-gradient(to bottom right, #1f2a38, #222e3c) !important;
    border: 1px solid rgba(255, 255, 255, 0.06) !important;
    color: #e4e6eb !important;
}

.dark-mode .ujian-card-hover:hover {
    background: linear-gradient(to bottom right, #263445, #2b394a) !important;
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.35) !important;
}

.dark-mode .icon-wrapper {
    background-color: #2b394a !important;
}

.dark-mode #last-updated {
    color: #e4e6eb !important;
}

.dark-mode .row-alarm {
    border-left: 8px solid #dc3545 !important;
    background-color: #151c24 !important;
}

/* ==========================================
   DARK MODE – DASHBOARD ICON
========================================== */

.dark-mode .dashboard-icon {
    color: #94a3b8 !important;
}

/* ==========================================
   DARK MODE – CARD MINIMAL
========================================== */

.dark-mode .card-minimal {
    background: #222e3c !important;
    border: 1px solid rgba(255, 255, 255, 0.06) !important;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.35) !important;
    color: #e4e6eb !important;
}

.dark-mode .card-minimal:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.45) !important;
}

/* ==========================================
   DARK MODE – CARD TITLE
========================================== */

.dark-mode .card-title {
    color: #ffffff !important;
}

/* ==========================================
   DARK MODE – TABLE WRAPPER
========================================== */

.dark-mode .table-wrapper {
    background: transparent !important;
}

/* ==========================================
   DARK MODE – GAME CARD
========================================== */

.dark-mode .game-card {
    background: #222e3c !important;
    border: 1px solid rgba(255, 255, 255, 0.06) !important;
    color: #e4e6eb !important;
}

.dark-mode .game-card:hover {
    background: linear-gradient(to bottom right, #263445, #2b394a) !important;
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.45) !important;
}

/* ==========================================
   DARK MODE – ICON WRAPPER
========================================== */

.dark-mode .icon-wrapper {
    background-color: #2b394a !important;
}

/* ==========================================
   DARK MODE – HEADER INFO
========================================== */

.dark-mode .header-info {
    border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
}

.dark-mode .header-left,
.dark-mode .header-right {
    color: #e4e6eb !important;
}

.dark-mode .header-right {
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    background-color: #2b394a !important;
}

/* ==========================================
   DARK MODE – RAW TABLE FIX
========================================== */

.dark-mode table {
    background-color: transparent !important;
    color: #e4e6eb !important;
}

.dark-mode table th,
.dark-mode table td {
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
    color: #e4e6eb !important;
}

/* ==========================================
   DARK MODE – PEMBAHASAN
========================================== */

.dark-mode .pembahasan {
    background-color: #1f2a38 !important;
    background-image: none !important;
    color: #cfd8e3 !important;
    border: 1px solid rgba(255, 255, 255, 0.06);
}

/* ==========================================
   DARK MODE – SKOR SOAL
========================================== */

.dark-mode .skor-soal {
    background-color: #263445 !important;
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.06);
}

.dark-mode ul {
    color: #e4e6eb;
}

/* ==========================================
   DARK MODE – INLINE WHITE FIX
========================================== */

.dark-mode div[style*="background-color: white"] {
    background-color: #222e3c !important;
    color: #e4e6eb !important;
}

.dark-mode div[style*="color: black"] {
    color: #e4e6eb !important;
}

/* ==========================================
   DARK MODE – INLINE RESULT BOX FIX
========================================== */

/* Wrapper luar */
.dark-mode .row[style*="#444"] {
    background-color: #222e3c !important;
    color: #e4e6eb !important;
}

/* Box nilai putih */
.dark-mode div[style*="background-color: white"] {
    background-color: #2b394a !important;
    color: #ffffff !important;
}

/* Inline color black */
.dark-mode div[style*="color: black"] {
    color: #e4e6eb !important;
}

/* ==========================================
   DARK MODE – CARD UTAMA
========================================== */

.dark-mode .card-utama {
    background-color: #222e3c !important;
    color: #e4e6eb !important;
}

.dark-mode .mb-4.p-3.border.rounded.bg-white {
    background-color: #2b394a !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
}

/* ==========================================
   DARK MODE – CUSTOM CARD
========================================== */

.dark-mode .custom-card {
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    background-color: #222e3c !important;
}

.dark-mode .custom-card-header {
    background-color: #2b394a !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
    color: #ffffff !important;
}

/* =====================================================
   DARK MODE – ANALISA TABLE + PROGRESS FIX
===================================================== */

.dark-mode #tabelAnalisa {
    color: #e4e6eb !important;
}

.dark-mode #tabelAnalisa th,
.dark-mode #tabelAnalisa td {
    border: 1px solid rgba(255, 255, 255, 0.15) !important;
    background-color: #222e3c !important;
    color: #ffffff !important;
}

/* Progress background */
.dark-mode .progress {
    background: #2b394a !important;
}

/* Progress bar */
.dark-mode .progress-bar {
    color: #ffffff !important;
}

/* Table responsive background */
.dark-mode .table-responsive {
    background-color: transparent !important;
}

/* =====================================================
   PRINT MODE – FORCE LIGHT VERSION
===================================================== */

@media print {

    body {
        background: #ffffff !important;
        color: #000000 !important;
    }

    #tabelAnalisa th,
    #tabelAnalisa td {
        border: 1px solid #333 !important;
        color: #000 !important;
        background: #ffffff !important;
    }

    .progress {
        background: #eee !important;
    }

    .progress-bar {
        color: #fff !important;
    }
}

/* =====================================================
   DARK MODE – TABLE HEADER GENERIC FIX
===================================================== */

.dark-mode table th {
    background-color: #2f3f52 !important;
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.15) !important;
}

/* ==========================================
   DARK MODE – FORCE ALL TEXT COLOR
========================================== */

/* Semua text umum */
.dark-mode,
.dark-mode p,
.dark-mode span,
.dark-mode div,
.dark-mode label,
.dark-mode small,
.dark-mode strong,
.dark-mode b,
.dark-mode li {
    color: #e4e6eb !important;
}

/* Heading */
.dark-mode h1,
.dark-mode h2,
.dark-mode h3,
.dark-mode h4,
.dark-mode h5,
.dark-mode h6 {
    color: #ffffff !important;
}

/* Bootstrap text utilities */
.dark-mode .text-dark,
.dark-mode .text-black,
.dark-mode .text-body {
    color: #e4e6eb !important;
}

.dark-mode .text-muted {
    color: #94a3b8 !important;
}

.dark-mode .text-secondary {
    color: #94a3b8 !important;
}

/* Link */
.dark-mode a {
    color: #6ea8fe !important;
}

.dark-mode a:hover {
    color: #9ec5fe !important;
}

/* Form label */
.dark-mode .form-check-label {
    color: #e4e6eb !important;
}

/* =========================================
   DARK MODE – FORCE LOADING OVERLAY FIX
========================================= */

.dark-mode #loadingOverlay {
    background-color: rgba(34, 46, 60, 0.95) !important;
    color: #e4e6eb !important;
}

.dark-mode #loadingOverlay p {
    color: #e4e6eb !important;
}

/* Loader border fix */
.dark-mode #loadingOverlay .loader {
    border: 5px solid rgba(255, 255, 255, 0.15) !important;
    border-top: 5px solid #6ea8fe !important;
}

/* =========================================
   DARK MODE – INPUT GROUP FIX TOTAL
========================================= */

.dark-mode .input-group-text {
    background-color: #2b394a !important;
    color: #cfd8e3 !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
}

.dark-mode .form-control {
    background-color: #2b394a !important;
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
}

.dark-mode .form-control::placeholder {
    color: #94a3b8 !important;
}

/* Focus state */
.dark-mode .form-control:focus {
    background-color: #2b394a !important;
    color: #ffffff !important;
    border-color: #3a4d63 !important;
    box-shadow: none !important;
}

/* Input group wrapper biar nyatu */
.dark-mode .input-group>.form-control,
.dark-mode .input-group>.input-group-text {
    border-color: rgba(255, 255, 255, 0.1) !important;
}

.dark-mode .simplebar-offset {
    border-right: 1px solid #3a4b61 !important;
}

.dark-mode .navbar {
    border-bottom: 1px solid #3a4b61 !important;
}

.dark-mode .btn-success,
.dark-mode .btn-info,
.dark-mode .btn-warning,
.dark-mode .btn-secondary {
    color: white !important;
}

/* =========================================
   DARK MODE – CARD FOOTER PANDUAN
========================================= */

.dark-mode .card-footer.bg-white {
    background-color: #263445 !important;
    border-top: 1px solid rgba(255, 255, 255, 0.08) !important;
    color: #e4e6eb !important;
}

/* Heading */
.dark-mode .card-footer h6 {
    color: #ffffff !important;
}

/* text-dark override */
.dark-mode .card-footer .text-dark {
    color: #ffffff !important;
}

/* text-muted override */
.dark-mode .card-footer .text-muted {
    color: #94a3b8 !important;
}

/* Badge putih */
.dark-mode .card-footer .badge.bg-white {
    background-color: #2b394a !important;
    color: #e4e6eb !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
}

/* Badge text-danger */
.dark-mode .card-footer .badge.text-danger {
    color: #ff6b6b !important;
}

/* CODE block */
.dark-mode .card-footer code {
    background-color: #1f2a38 !important;
    color: #8ecdf5 !important;
    padding: 2px 6px;
    border-radius: 6px;
}

/* border class */
.dark-mode .card-footer .border {
    border-color: rgba(255, 255, 255, 0.08) !important;
}

/* =====================================================
   DARK MODE – PREVIEW IMPORT DOCX
===================================================== */

/* Background halaman */
.dark-mode body {
    background-color: #1c2530 !important;
}

/* Card preview */
.dark-mode .card-preview {
    background-color: #263445 !important;
    border-left-color: #3a4d63 !important;
    color: #e4e6eb !important;
}

.dark-mode .card-danger {
    background-color: #3a1f24 !important;
    border-left-color: #ff6b6b !important;
}

/* Card header */
.dark-mode .card-header.bg-white {
    background-color: #263445 !important;
    color: #ffffff !important;
}

/* Card body text */
.dark-mode .pertanyaan-area p,
.dark-mode .fs-6,
.dark-mode .text-dark {
    color: #e4e6eb !important;
}

/* Badge tipe */
.dark-mode .badge-tipe {
    background: #1f3447 !important;
    color: #8ecdf5 !important;
    border: 1px solid #2c4e6b !important;
}

/* Opsi container */
.dark-mode .opsi-container {
    background-color: #2b394a !important;
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
    color: #e4e6eb !important;
}

.dark-mode .opsi-error {
    background-color: #3a1f24 !important;
    border: 1px dashed #ff6b6b !important;
}

.dark-mode .opsi-error-text {
    color: #ff6b6b !important;
}

/* Border start light */
.dark-mode .border-light {
    border-color: rgba(255, 255, 255, 0.08) !important;
}

/* Area MJD (bg-light override) */
.dark-mode .bg-light {
    background-color: #2b394a !important;
    color: #e4e6eb !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
}

/* Kunci jawaban box */
.dark-mode .kunci-box {
    background-color: #1f3447 !important;
    border: 1px solid #2c4e6b !important;
    color: #8ecdf5 !important;
}

/* Alert custom */
.dark-mode .alert-custom,
.dark-mode .alert-danger {
    background-color: #3a1f24 !important;
    color: #ff6b6b !important;
}

/* Border warning */
.dark-mode .card.border-warning {
    background-color: #3a321d !important;
    border-color: #ffc107 !important;
    color: #ffc107 !important;
}

/* Badge system */
.dark-mode .badge.bg-dark {
    background-color: #2b394a !important;
}

.dark-mode .badge.bg-primary {
    background-color: #3a4d63 !important;
}

/* Button outline danger */
.dark-mode .btn-outline-danger {
    color: #ff6b6b !important;
    border-color: #ff6b6b !important;
}

.dark-mode .btn-outline-danger:hover {
    background-color: #ff6b6b !important;
    color: #222e3c !important;
}

/* Button secondary */
.dark-mode .btn-secondary {
    background-color: #2b394a !important;
    border-color: #2b394a !important;
}

/* Shadow fix */
.dark-mode .shadow-sm,
.dark-mode .shadow,
.dark-mode .shadow-lg {
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.35) !important;
}

/* ============================= */
/* GITHUB CARD DARK MODE FIX */
/* ============================= */

/* Badge default (light mode tetap normal) */
.badge.github-badge {
    background: #f8f9fa;
    color: #212529;
    border: 1px solid #dee2e6;
}

/* Dark mode override */
.dark-mode .badge.github-badge {
    background: #2a3441 !important;
    color: #e4e6eb !important;
    border: 1px solid #3a4b61 !important;
}

/* Icon warna */
.github-badge i {
    font-size: 14px;
}

.github-badge i.fa-star {
    color: #ffc107 !important;
}

/* Dark mode icon tweak */
.dark-mode .github-badge i.fa-star {
    color: #ffc107 !important;
}

.dark-mode .github-badge i.fa-code-branch {
    color: #9aa4b2 !important;
}

/* Button GitHub dark mode */
.dark-mode .btn-outline-dark {
    color: #e4e6eb !important;
    border-color: #3a4b61 !important;
}

.dark-mode .btn-outline-dark:hover {
    background: #3a4b61 !important;
    color: #fff !important;
}

/* Icon background besar GitHub */
.dark-mode .fa-github.position-absolute {
    opacity: 0.05 !important;
    color: #8b949e !important;
}

.dark-mode .support-cta {
    border-left: 4px solid var(--bs-danger) !important;
    position: relative;
    overflow: hidden;
    transition: 0.2s ease;
}

.status-online {
    color: #28a745;
    font-weight: 600;
}

.status-offline {
    color: #dc3545;
    font-weight: 600;
}

.server-box {
    background: #fff;
    border-radius: 10px;
    padding: 15px;
    border: solid 1px rgba(121, 121, 121, 0.78);
    transition: .2s;
    height: 100%;
}

.server-box:hover {
    transform: translateY(-3px);
}

.server-title {
    font-size: 13px;
    color: #666;
}

.server-value {
    font-weight: 600;
    font-size: 16px;
}

.progress-bar {
    height: 8px;
    background: #28a745;
    border-radius: 10px;
}

.progress-wrap {
    background: #e9ecef;
    border-radius: 10px;
    overflow: hidden;
}

/* =========================
   DARK MODE – SERVER BOX
========================= */

.dark-mode .server-box {
    background: #263445 !important;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.35) !important;
}

.dark-mode .server-box:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.45) !important;
}

.dark-mode .server-title {
    color: #aab4c3 !important;
}

.dark-mode .server-value {
    color: #ffffff !important;
}

/* Progress background */
.dark-mode .progress-wrap {
    background: #1f2a38 !important;
}

/* Progress bar online */
.dark-mode .progress-bar {
    background: #22c55e !important;
    /* hijau modern */
}

/* Status text */
.dark-mode .status-online {
    color: #4ade80 !important;
}

.dark-mode .status-offline {
    color: #f87171 !important;
}

.dark-mode .progress-bar {
    box-shadow: 0 0 8px rgba(34, 197, 94, 0.6);
}

/* =========================
   DARK MODE – SUBMENU ACTIVE
========================= */

.dark-mode li.sidebar-item.submenu>a.sidebar-link {
    background: linear-gradient(to left, #222e3c, #1b222d) !important;
    color: #e4e6eb !important;
    border-bottom: 2px solid #222e3c;
}

.dark-mode .overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(13, 13, 13, 0.9);
    /* lebih terang, jelas */
    display: flex;
    align-items: center;
    justify-content: center;
}

.dark-mode .card-meta-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 18px;
    padding-top: 10px;
    font-size: 12px;
    color: #878a8d;
}

.dark-mode .admin-meta {
    display: flex;
    align-items: center;
    gap: 6px;
    color: #878a8d;
    text-decoration: none;
    font-weight: 500;
    letter-spacing: .3px;
    transition: .2s ease;
}

.app-version {
    margin-top: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    opacity: .6;
    transition: .3s ease;
}

.app-version:hover {
    opacity: 1;
}

.version-line {
    flex: 1;
    height: 1px;
    background: #606060;
}

.version-text {
    font-size: 11px;
    letter-spacing: 2px;
    font-weight: 600;
    color: #888;
}

.dark-mode .version-line {
    flex: 1;
    height: 1px;
    background: #dcdcdc;
}

.dark-mode .version-text {
    font-size: 11px;
    letter-spacing: 2px;
    font-weight: 600;
    color: #888;
}
.dark-mode a.btn-primary {
    color:#c4c4c4 !important;
}
.dark-mode a.btn-danger {
    color:#c4c4c4 !important;
}
.dark-mode .btn-outline-primary {
    color:#c4c4c4 !important;
}
.dark-mode .preview-header{
    background-color:#1c1c1c;
}
.chart-card {
    height: 450px;
    display: flex;
    flex-direction: column;
}

.chart-card .card-body {
    flex: 1;
}
.chart-card .card-body {
    display: flex;
    align-items: center;
    justify-content: center;
}
.dark-mode .buttons-excel span{
    color:white !important;
}
</style>