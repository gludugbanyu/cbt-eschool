<link rel="icon" type="image/png" href="../assets/images/icon.png" />
<link href="../assets/bootstrap-5.3.6/css/bootstrap.min.css" rel="stylesheet">
<link href="../assets/fontawesome/css/all.min.css" rel="stylesheet">
<link href="../assets/adminkit/static/css/app.css" rel="stylesheet">
<link href="../assets/datatables/datatables.css" rel="stylesheet">
<style>
  #toast-container {
    position: fixed !important;
    bottom: 1rem;
    right: 1rem;
    left: auto !important;
    z-index: 9999;
  }
  .fa-beat, .fa-bounce, .fa-fade, .fa-beat-fade, .fa-flip, .fa-pulse, .fa-shake, .fa-spin, .fa-spin-pulse
{
  animation-duration: 2s;
  animation-iteration-count: infinite;
}
.table-wrapper {
            overflow-x: auto; /* Enable horizontal scrolling */
            -webkit-overflow-scrolling: touch; /* Smooth scrolling for mobile */
        }
        table th, table td {
    text-align: left !important;
}
.blinking {
  animation: blink 1s infinite;
}

@keyframes blink {
  0%, 100% { opacity: 1; }
  50% { opacity: 0; }
}
li.sidebar-item.submenu > a.sidebar-link {
  background: linear-gradient(to left, #222e3c, #3a4d63) !important;
  border-bottom:2px solid #222e3c;
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
    border: 1px solid rgba(255,255,255,0.08);
    box-shadow: 0 15px 35px rgba(0,0,0,0.25);
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
    box-shadow: 0 10px 20px rgba(0,0,0,0.08);
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
</style>
<!--<style>
#soal.sidebar-dropdown a {
    background-color: rgba(0, 0, 0, 0.15); /* Warna gelap dengan transparansi */
    padding: 10px 25px;
    margin-top: -1px; /* Untuk menghilangkan gap */
}
</style>-->
