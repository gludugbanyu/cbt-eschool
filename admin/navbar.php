<nav class="navbar navbar-expand navbar-light navbar-bg">
    <a class="sidebar-toggle js-sidebar-toggle">
        <i class="hamburger align-self-center"></i>
    </a>

    <div class="navbar-collapse collapse">
        <ul class="navbar-nav navbar-align ms-auto">
            <li class="nav-item">
                <a href="#" class="nav-link px-3" id="darkModeToggle">
                    <i class="fas fa-moon"></i>
                </a>
            </li>
            <!-- User Info -->
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user-circle me-1"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li><span class="dropdown-item-text">Hai! <strong><?php echo $nama_admin; ?></strong></span></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                    <div class="btn-group dropdown-item-text" role="group" aria-label="Group tombol edit dan logout">
                        <a class="btn btn-outline-secondary" href="pass.php">
                        <i class="fas fa-user-circle me-1"></i> Edit
                        </a>
                        <a class="btn btn-outline-danger text-danger btnLogout" href="logout.php">
                        <i class="fas fa-sign-out-alt me-1"></i>
                        </a>
                    </div>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>
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