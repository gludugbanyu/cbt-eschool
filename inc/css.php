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
    border-bottom: 2px solid transparent !important;
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
/* khusus doughnut jangan flex */
.doughnut-card .card-body {
    display: block !important;
    position: relative;
}

/* tinggi terkontrol */
.doughnut-card .chart-wrapper {
    position: relative;
    width: 100%;
    height: 340px;
}
.chart-wrapper {
    position: relative;
    width: 100%;
    height: 340px;
}
/* =======================================================
   DATATABLES PAGINATION – LIGHT MODE (COMPACT & CLEAN)
======================================================= */

.pagination {

    --bs-pagination-padding-x: 10px;
    --bs-pagination-padding-y: 3px;
    --bs-pagination-font-size: 13px;

    --bs-pagination-bg: #ffffff;
    --bs-pagination-color: #495057;
    --bs-pagination-border-color: #dee2e6;

    --bs-pagination-hover-bg: #f8f9fa;
    --bs-pagination-hover-color: #212529;
    --bs-pagination-hover-border-color: #ced4da;

    --bs-pagination-active-bg: #0d6efd;
    --bs-pagination-active-color: #ffffff;
    --bs-pagination-active-border-color: #0d6efd;

    --bs-pagination-disabled-bg: #ffffff;
    --bs-pagination-disabled-color: #adb5bd;
    --bs-pagination-disabled-border-color: #dee2e6;
}
.pagination .page-link{
    margin:0 1px;
    min-width:28px;
    height:28px;
    padding:0;
    display:flex;
    align-items:center;
    justify-content:center;
    transition:0.12s ease;
}
.pagination .page-link:hover{
    transform:none;
    background:#f1f3f5;
}
.pagination .page-item.active .page-link{
    box-shadow:0 1px 3px rgba(0,0,0,.15);
}
/* ===============================
   TIMELINE SUBMENU SIDEBAR
=================================*/

.timeline-submenu {
    position: relative;
    padding-left: -20px;
}

/* GARIS VERTIKAL */
.timeline-submenu::before {
    content: "";
    position: absolute;
    top: 8px;
    bottom: 8px;
    left: 35px;
    width: 2px;
    background: rgba(255,255,255,0.25);
}

/* ITEM */
.timeline-submenu .sidebar-item {
    position: relative;
    left:49px;
    background: transparent !important;
}

/* TITIK */
.timeline-submenu .sidebar-link {
    position: relative;
    background: transparent !important;
}

.timeline-submenu .sidebar-link::before {
    content: "";
    position: absolute;
    left: -20px;
    top: 50%;
    transform: translateY(-50%);
    width: 10px;
    height: 10px;
    border-radius: 50%;
    border: 2px solid rgba(255,255,255,0.5);
    background: #1f2a37;
    z-index: 2;
}

/* ACTIVE TITIK */
.timeline-submenu .sidebar-item.active > .sidebar-link::before {
    background: #3b7ddd;
    border-color: #3b7ddd;
}
.timeline-submenu .sidebar-link.active::before {
    background: #3b7ddd !important;
    border-color: #3b7ddd !important;
}
.timeline-submenu .sidebar-item.active > .sidebar-link {
    border-left: 3px solid transparent !important;
}
.timeline-submenu .sidebar-item.active > .sidebar-link {
    background: transparent !important;
}
/* HOVER SUBMENU TIMELINE */
.timeline-submenu .sidebar-link:hover {
    border-left: 3px solid transparent !important;
    background: transparent !important;
}
/* ===============================
   FIX PARENT ACTIVE DROPDOWN
=================================*/

.sidebar-item.active > .sidebar-link[data-bs-toggle="collapse"] {
    background: transparent !important;
}

/* Dark mode juga */
.dark-mode .sidebar-item.active > .sidebar-link[data-bs-toggle="collapse"] {
    background: transparent !important;
}
/* ===============================
   TIMELINE DOT HOVER = FILLED
=================================*/

.timeline-submenu .sidebar-link:hover::before {
    background: #3b7ddd !important;
    border-color: #3b7ddd !important;
}
/* ===============================
   AUTO COLLAPSE ARROW ADMIN KIT
=================================*/

.sidebar-link[data-bs-toggle="collapse"]{
    display:flex;
    align-items:center;
}

/* default = RIGHT */
.sidebar-link[data-bs-toggle="collapse"]::after{
    content:"\f105";
    font-family:"Font Awesome 6 Free";
    font-weight:900;
    margin-left:auto;
    transition:.2s ease;
}

/* OPEN = DOWN */
.sidebar-link[data-bs-toggle="collapse"]:not(.collapsed)::after{
    transform:rotate(90deg);
}
/* ===============================
   FORCE ARROW COLOR FIX ADMIN KIT
=================================*/

.sidebar-link[data-bs-toggle="collapse"]::after,
.sidebar-link[data-bs-toggle="collapse"].collapsed::after{
    color:#cfd8e3 !important;
}

/* HOVER */
.sidebar-link[data-bs-toggle="collapse"]:hover::after{
    color:#ffffff !important;
}

/* ACTIVE / OPEN */
.sidebar-item.active > 
.sidebar-link[data-bs-toggle="collapse"]::after,
.sidebar-link[data-bs-toggle="collapse"]:not(.collapsed)::after{
    color:#ffffff !important;
}
.sidebar .sidebar-link i {
    width: 22px !important;
    text-align: center !important;
    margin-right:8px !important;
    font-size: 15px !important;
}
.timeline-submenu .sidebar-link::before{
    display:none !important;
}
.timeline-submenu .sidebar-item{
    position:relative;
    left:0 !important;
    padding-left:30px !important;
}
.timeline-submenu::before{
    left:38px !important;
    width:2px;
}

.timeline-submenu .sidebar-item::before{
    content:"";
    position:absolute;
    left:39px;           /* ⬅️ SAMA DENGAN TENGAH GARIS */
    top:50%;
    transform:translate(-50%,-50%);
    width:12px;
    height:12px;
    border-radius:50%;
    border:2px solid rgba(255,255,255,0.5);
    background:#1f2a37;
    z-index:3;
}

.timeline-submenu .sidebar-item.active::before{
    background:#3b7ddd;
    border-color:#3b7ddd;
}
/* HOVER DOT */
.timeline-submenu .sidebar-item:hover::before{
    background:#3b7ddd !important;
    border-color:#3b7ddd !important;
}
@media (max-width: 576px){
    .cta-ribbon{
        font-size:8px !important;
        padding:3px 8px !important;
        border-bottom-left-radius:6px !important;
    }
}
/* ===== Summernote Image Popover Dark Mode ===== */

.dark-mode .note-popover.popover {
    background-color: #1e1e2f !important;
    border: 1px solid #3a3b4f !important;
}

.dark-mode .note-popover .popover-body {
    background-color: #1e1e2f !important;
}

.dark-mode .note-popover .note-btn {
    background-color: #2b2c40 !important;
    border-color: #3a3b4f !important;
    color: #ddd !important;
}

.dark-mode .note-popover .note-btn:hover {
    background-color: #3a3b4f !important;
    color: #fff !important;
}

.dark-mode .note-popover .popover-arrow::before,
.dark-mode .note-popover .popover-arrow::after {
    border-bottom-color: #1e1e2f !important;
}

.dark-mode .note-popover .note-icon-trash,
.dark-mode .note-popover .note-icon-float-left,
.dark-mode .note-popover .note-icon-float-right,
.dark-mode .note-popover .note-icon-rollback {
    color: #ddd !important;
}
/* ============================
INSIGHT CARD BASE
============================ */

.stat-card{
height:100%;
min-height:95px;
padding:18px 22px;
border-radius:14px;
background:#ffffff;
border:1px solid #e5e7eb;
display:flex;
flex-direction:column;
justify-content:center;
position:relative;
overflow:hidden;
transition:.25s ease;

/* resting depth */
box-shadow:
0 1px 2px rgba(0,0,0,.04),
0 4px 10px rgba(0,0,0,.03);

/* inner light */
background-image:
linear-gradient(to bottom,
rgba(255,255,255,.7),
rgba(255,255,255,.4));
}

/* hover floating feel */
.stat-card:hover{
transform:translateY(-2px);
box-shadow:
0 8px 18px rgba(0,0,0,.08),
0 2px 6px rgba(0,0,0,.06);
border-color:#d1d5db;
}

/* title */
.stat-title{
font-size:12px;
font-weight:500;
color:#9ca3af;
margin-bottom:4px;
}

/* value */
.stat-value{
font-size:24px;
font-weight:600;
color:#111827;
}

.dark-mode .stat-card{
background:#202b38 !important;
border:1px solid #3a3f47;

box-shadow:
0 1px 1px rgba(0,0,0,.4),
0 6px 12px rgba(0,0,0,.25);

background-image:
linear-gradient(to bottom,
rgba(255,255,255,.02),
rgba(0,0,0,.15));
}

/* ============================
INDICATOR LEFT BORDER
============================ */

.indicator{
position:absolute;
left:0;
top:0;
width:4px;
height:100%;
border-radius:16px 0 0 16px;
transition:.3s ease;
}

/* P STATUS */
.p-sukar{ background:#ef4444; }
.p-sedang{ background:#3b82f6; }
.p-mudah{ background:#f59e0b; }

/* D STATUS */
.d-jelek{ background:#ef4444; }
.d-cukup{ background:#f59e0b; }
.d-baik{ background:#10b981; }

/* ============================
DECISION BADGE
============================ */

.decision-badge{
display:inline-flex;
align-items:center;
gap:10px;
padding:10px 20px;
border-radius:50px;
font-size:14px;
font-weight:600;
transition:.3s ease;
box-shadow:0 4px 12px rgba(0,0,0,.08);
}

/* Layak */
.badge-layak{
background:#d1fae5;
color:#065f46;
}

/* Revisi */
.badge-revisi{
background:#fef3c7;
color:#92400e;
}

/* Buang */
.badge-buang{
background:#fee2e2;
color:#7f1d1d;
}


/* ============================
DARK MODE SUPPORT
============================ */

.dark-mode .stat-card{
background:#262a31;
border:1px solid #3a3f47;
box-shadow:none;
}

.dark-mode .stat-title{
color:#9ca3af;
}

.dark-mode .stat-value{
color:#e4e6eb;
}

/* indikator tetap jelas */
.dark-mode .p-sukar{ background:#f87171; }
.dark-mode .p-sedang{ background:#60a5fa; }
.dark-mode .p-mudah{ background:#facc15; }

.dark-mode .d-jelek{ background:#f87171; }
.dark-mode .d-cukup{ background:#facc15; }
.dark-mode .d-baik{ background:#34d399; }

/* badge dark */
.dark-mode .decision-badge{
box-shadow:0 0 0 1px rgba(255,255,255,.05),
0 6px 18px rgba(0,0,0,.4);
}

.dark-mode .badge-layak{
background:#063b2e;
color:#34d399;
}

.dark-mode .badge-revisi{
background:#4b3700;
color:#facc15;
}

.dark-mode .badge-buang{
background:#4c0000;
color:#f87171;
}


/* ============================
RESPONSIVE
============================ */

@media(max-width:768px){

.stat-card{
min-height:75px;
padding:14px;
}

.stat-value{
font-size:20px;
}

}

/* BIKIN CARD NGISI COL */
.stat-row .stat-card{
flex:1;
}
@media(max-width:768px){
.stat-row > div{
margin-bottom:6px;
}
}
.stat-meta{
font-size:11px;
margin-top:2px;
opacity:.7;
}

/* subtle status */
.meta-sukar{ color:#ef4444; }
.meta-sedang{ color:#3b82f6; }
.meta-mudah{ color:#f59e0b; }

.meta-jelek{ color:#ef4444; }
.meta-cukup{ color:#f59e0b; }
.meta-baik{ color:#10b981; }

/* dark */
.dark-mode .meta-sukar{ color:#f87171; }
.dark-mode .meta-sedang{ color:#60a5fa; }
.dark-mode .meta-mudah{ color:#facc15; }

.dark-mode .meta-jelek{ color:#f87171; }
.dark-mode .meta-cukup{ color:#facc15; }
.dark-mode .meta-baik{ color:#34d399; }
.stat-row .col-md-3{
display:flex;
}

.stat-row .stat-card{
flex:1;
}
.stat-card{
padding:18px 22px;
}

.stat-card::before{
content:"";
position:absolute;
top:0;
left:0;
width:100%;
height:3px;
background:#e5e7eb;
opacity:.4;
}
.dark-mode .stat-card::before{
background:#444;
}

.metode-wrap{
padding:12px 16px;
border-radius:12px;
background:#f9fafb;
border:1px solid #e5e7eb;
}

.metode-title{
font-size:13px;
font-weight:600;
margin-bottom:8px;
color:#374151;
}

.metode-grid{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(140px,1fr));
gap:10px;
}

.metode-item{
padding:10px;
border-radius:10px;
background:#fff;
border:1px solid #eee;
transition:.2s;
}

.metode-item:hover{
box-shadow:0 4px 10px rgba(0,0,0,.05);
}

.metode-head{
font-weight:600;
font-size:13px;
margin-bottom:2px;
}

.metode-formula{
font-size:12px;
color:#6b7280;
}

.metode-note{
font-size:11px;
opacity:.75;
margin-top:4px;
line-height:1.3;
}

/* DARK MODE */
.dark-mode .metode-wrap{
background:#23272f;
border:1px solid #3a3f47;
}

.dark-mode .metode-item{
background:#2a2e34;
border:1px solid #444;
}

.dark-mode .metode-title{
color:#ddd;
}

.dark-mode .metode-formula{
color:#aaa;
}

/* =============================
REKOMENDASI BOX
============================= */
.rekom-box{
padding:12px 14px !important;
border-radius:10px !important;
font-size:13px;
line-height:1.5;
background:#f6f8fb;
border:1px solid #e4e8f0;
color:#344054;
box-shadow:0 1px 2px rgba(16,24,40,.04);
}

/* INFO (N sangat kecil) */
.rekom-info{
background:#f4f7ff !important;
border:1px solid #d9e3ff !important;
color:#3b5bcc !important;
}

/* WARNING (N belum stabil) */
.rekom-warn{
background:#fff9f2 !important;
border:1px solid #ffe2b5 !important;
color:#a66300 !important;
}

/* BAD (soal jelek) */
.rekom-bad{
background:#fff5f5 !important;
border:1px solid #ffd6d6 !important;
color:#c0392b !important;
}

/* DARK MODE */
.dark-mode .rekom-box{
background:#1f2937 !important;
border-color:#374151 !important;
color:#e5e7eb !important;
}

.dark-mode .rekom-info{
background:#1e2636 !important;
border-color:#2f3a55 !important;
color:#9bb6ff !important;
}

.dark-mode .rekom-warn{
background:#2b2518 !important;
border-color:#4a3a1c !important;
color:#ffcc66 !important;
}

.dark-mode .rekom-bad{
background:#2b1f1f !important;
border-color:#4a2a2a !important;
color:#ff8a8a !important;
}
/* LIGHT MODE */
.row-bad{background:#fff4f4;}
.row-good{background:#f2fff5;}

/* DARK MODE */
.dark-mode .row-bad{
background:#40292c !important;
}

.dark-mode .row-good{
background:#243a2c !important;
}

.info-sampel{
padding:10px 14px;
border-radius:8px;
background:#f8fafc;
border:1px solid #e2e8f0;
font-size:13px;
color:#475569;
}

.dark-mode .info-sampel{
background:#1e293b;
border:1px solid #334155;
color:#cbd5e1;
}
/* SELECT2 DARK MODE */
.dark-mode .select2-container--default .select2-selection--multiple{
    background-color:#1e293b;
    border:1px solid #334155;
    color:#f1f5f9;
}

.dark-mode .select2-selection__choice{
    background:#334155;
    border:none;
    color:#fff;
}

.dark-mode .select2-selection__choice__remove{
    color:#fff;
}

.dark-mode .select2-dropdown{
    background:#1e293b;
    border:1px solid #334155;
}

.dark-mode .select2-results__option{
    color:#f1f5f9;
}

.dark-mode .select2-results__option--highlighted{
    background:#3b82f6 !important;
    color:#fff !important;
}

.dark-mode .select2-search__field{
    background:#1e293b;
    color:#fff;
}
/* ===== SELECT2 TAG DARK FIX ===== */

.dark-mode 
.select2-container--default 
.select2-selection--multiple 
.select2-selection__choice{
    background-color:#334155 !important;
    color:#f8fafc !important;
    border:1px solid #475569 !important;
}

/* tombol x di tiap tag */
.dark-mode 
.select2-selection__choice__remove{
    color:#cbd5e1 !important;
}

.dark-mode 
.select2-selection__choice__remove:hover{
    color:#ef4444 !important;
}

/* tombol clear all (×) */
.dark-mode 
.select2-selection__clear{
    color:#f8fafc !important;
}

/* input search di dalam tag */
.dark-mode 
.select2-search__field{
    background:transparent !important;
    color:#f8fafc !important;
}
</style>
