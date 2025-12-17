<?php
// Views/layout/_theme_switcher.php
?>
<div class="theme-switcher">
    <label class="theme-switch" title="Changer le thème">
        <input type="checkbox" id="theme-toggle" <?= ($_COOKIE['theme'] ?? 'light') === 'dark' ? 'checked' : '' ?>>
        <span class="theme-slider">
            <span class="theme-icon sun">☀️</span>
            <span class="theme-icon moon">🌙</span>
        </span>
    </label>
</div>

<script>
const themeToggle = document.getElementById('theme-toggle');
let currentTheme = localStorage.getItem('theme') || 'light';

document.documentElement.setAttribute('data-theme', currentTheme);

themeToggle?.addEventListener('change', function() {
    const newTheme = this.checked ? 'dark' : 'light';
    document.documentElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    
    document.cookie = `theme=${newTheme}; path=/; max-age=${30 * 24 * 60 * 60}`;
    
    document.dispatchEvent(new CustomEvent('themeChanged', { 
        detail: { theme: newTheme } 
    }));
});

if (window.matchMedia && !localStorage.getItem('theme')) {
    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)');
    const systemTheme = prefersDark.matches ? 'dark' : 'light';
    
    document.documentElement.setAttribute('data-theme', systemTheme);
    if (themeToggle) themeToggle.checked = (systemTheme === 'dark');
    localStorage.setItem('theme', systemTheme);
}
</script>