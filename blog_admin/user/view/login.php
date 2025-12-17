<?php
// Local admin login page (copied from userai/user/view/login.php)
// Adjusted to post to the central auth endpoint and use central assets.
session_start();

$logoutMessage = '';
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    $logoutMessage = 'Vous avez été déconnecté avec succès.';
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Connexion</title>
    <link rel="stylesheet" href="/userai/user/public/assets/css/templatemo-prism-flux.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* (Styles trimmed for brevity in file) */
        body { margin:0; font-family: Arial, sans-serif; }
        .auth-container { min-height:100vh; display:flex; align-items:center; justify-content:center; background: linear-gradient(135deg,#0f0c29,#302b63,#24243e); padding:2rem; }
        .auth-box { background: rgba(255,255,255,0.06); border-radius:16px; padding:2rem; width:100%; max-width:480px; color:#fff }
        .form-control { width:100%; padding:0.75rem; border-radius:8px; border:1px solid rgba(255,255,255,0.08); background:rgba(255,255,255,0.04); color:#fff }
        .btn { background:linear-gradient(90deg,#6e45e2,#88d3ce); color:#fff; border:none; padding:0.8rem 1rem; border-radius:30px; width:100%; }
        .message { display:none; margin-top:12px; }
    </style>
</head>
<body>
    <?php
    // include navbar if present (some setups inline navbar at top-level)
    if (file_exists(dirname(__DIR__, 3) . '/blog/Views/partials/_navbar.php')) {
        include dirname(__DIR__, 3) . '/blog/Views/partials/_navbar.php';
    } elseif (file_exists(__DIR__ . '/_navbar.php')) {
        include __DIR__ . '/_navbar.php';
    }
    ?>
    <div class="auth-container">
        <div class="auth-box">
            <div style="text-align:center;margin-bottom:1rem;">
                <img src="/userai/user/public/images/logo2.png" alt="Logo" style="max-width:160px; height:auto;">
            </div>
            <h2 style="text-align:center;margin-bottom:1rem;">Connexion administrateur</h2>

            <form id="loginForm" onsubmit="return loginUser(event)">
                <div class="form-group">
                    <label for="loginEmail">Email</label>
                    <input type="email" id="loginEmail" name="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="loginPassword">Mot de passe</label>
                    <input type="password" id="loginPassword" name="mdp" class="form-control" required>
                </div>
                <button type="submit" id="loginBtn" class="btn">Se connecter</button>
                <div id="loginMessage" class="message"></div>
            </form>
        </div>
    </div>

    <script>
    function showMessage(id, text, isError){
        const el = document.getElementById(id);
        el.textContent = text;
        el.style.display = 'block';
        el.style.color = isError ? '#ffdddd' : '#ddffdd';
        setTimeout(()=> el.style.display='none',5000);
    }

    function loginUser(event){
        event.preventDefault();
        const email = document.getElementById('loginEmail').value;
        const mdp = document.getElementById('loginPassword').value;
        const btn = document.getElementById('loginBtn');
        btn.disabled = true; btn.textContent = 'Connexion...';

        fetch('/userai/user/index.php?action=login', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email, mdp })
        })
        .then(resp => {
            if (resp.redirected) { window.location.href = resp.url; return; }
            return resp.json();
        })
        .then(data => {
            if (!data) return;
            if (data.success && data.redirect) {
                // central login sets session cookie; redirect to admin dashboard
                window.location.href = data.redirect.startsWith('..') ? '/blog_admin/index.php' : data.redirect;
            } else {
                showMessage('loginMessage', data.message || 'Erreur', true);
                btn.disabled = false; btn.textContent = 'Se connecter';
            }
        })
        .catch(err => {
            console.error(err);
            showMessage('loginMessage', 'Erreur serveur', true);
            btn.disabled = false; btn.textContent = 'Se connecter';
        });

        return false;
    }
    </script>
    <script src="/userai/user/public/assets/js/templatemo-prism-scripts.js"></script>
</body>
</html>
