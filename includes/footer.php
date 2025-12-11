<?php
// includes/footer.php
?>
</main>

<script>
(function () {
    const toggle = document.getElementById('themeToggle');
    if (!toggle) return;

    const applyTheme = (mode) => {
        if (mode === 'dark') {
            document.body.classList.add('dark');
            toggle.src = 'img/toggle_on.png';
        } else {
            document.body.classList.remove('dark');
            toggle.src = 'img/toggle_off.png';
        }
    };

    const saved = localStorage.getItem('fitquest_theme') || 'light';
    applyTheme(saved);

    toggle.addEventListener('click', () => {
        const newMode = document.body.classList.contains('dark') ? 'light' : 'dark';
        localStorage.setItem('fitquest_theme', newMode);
        applyTheme(newMode);
    });
})();
</script>

<footer style="text-align:center;font-size:0.8rem;padding:10px;">
    © <?php echo date('Y'); ?> FitQuest
</footer>
</body>
</html>
