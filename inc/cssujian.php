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

.question-nav {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    margin-top: 15px;
}

.question-nav button {
    min-width: 40px;
}

#loadingOverlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    color: white;
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

#loadingOverlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    transition: opacity 0.3s ease;
}

.spinner-container {
    text-align: center;
    color: white;
}

.spinner-border {
    width: 3rem;
    height: 3rem;
}

* Animasi smooth */ .question-nav-container {
    transition: all 0.3s ease;
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

.nav-btn {
    width: 40px;
    height: 40px;
    border: 2px solid grey !important;
    /* Warna outline biru */
    color: grey !important;
    background: transparent;
    border-radius: 50%;
    margin: 5px;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    font-size: 14px;
}

/* Tombol sudah diisi */
.nav-btn[data-answered="true"] {
    background-color: #198754;
    /* Warna hijau */
    border-color: #198754 !important;
    color: white !important;
}

/* Indicator dot untuk soal terjawab */
.nav-btn[data-answered="true"]::after {
    content: '';
    position: absolute;
    top: -3px;
    right: -3px;
    width: 10px;
    height: 10px;
    background: #ffc107;
    /* Warna kuning */
    border-radius: 50%;
    border: 1px solid white;
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

.table,
.table th,
.table td {
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

.question-nav-container {
    margin-top: 20px;
}

.question-nav-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
    cursor: pointer;
    padding: 10px;
    border-radius: 5px;
    background-color: #f8f9fa;
}

.question-nav {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
    max-height: 200px;
    overflow: hidden;
    transition: max-height 0.3s ease-out;
    margin-bottom: 10px;
}

.question-nav.collapsed {
    max-height: 0;
}

.question-nav-btn {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #ddd;
    border-radius: 4px;
    cursor: pointer;
    transition: all 0.3s;
}

.question-nav-btn:hover {
    background-color: #f0f0f0;
}

.question-nav-btn.active {
    background-color: <?=$warna_tema ?>;
    color: white;
    border-color: <?=$warna_tema ?>;
}

.question-nav-btn.answered {
    background-color: #d4edda;
    border-color: #c3e6cb;
}

.navigation-buttons {
    display: flex;
    justify-content: space-between;
    margin-top: 5px;
}

.tempatsoal {
    min-height: 380px;
    height: 100%;
    display: flex;
    flex-wrap: wrap;
    gap: 2px;
    max-height: 200px;
    overflow-y: auto;
    transition: max-height 0.3s ease-out;
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

.question-nav-container {
    display: block;
    margin-top: 10px;
}

.question-nav-container.show {
    display: none;
}
</style>