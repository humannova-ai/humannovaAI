<?php
// ============================================
// BLOG MODULE - PUBLIC WEBSITE
// ============================================
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define base paths
define('ROOT_PATH', dirname(__DIR__));
define('BLOG_PATH', __DIR__);

// Autoload shared resources
require_once ROOT_PATH . '/shared/Core/Connection.php';
require_once ROOT_PATH . '/shared/Models/model.php';

// Compute a site base prefix so links work when hosted under a subpath (e.g. /our)
// Strip the trailing '/blog' or '/blog/index.php' from SCRIPT_NAME so base becomes '/our'
$siteBase = rtrim(preg_replace('#/blog(/index\.php)?$#', '', $_SERVER['SCRIPT_NAME']), '/');
if ($siteBase === '/' || $siteBase === '.') {
    $siteBase = '';
} else {
    if ($siteBase !== '' && $siteBase[0] !== '/') $siteBase = '/' . ltrim($siteBase, '/');
}
?>
<script>window.SITE_BASE = <?php echo json_encode($siteBase); ?>;</script>
<!-- Shared navbar (inlined from blog/Views/partials/_navbar.php) -->
<style>
    .header { position: fixed; top:0; left:0; right:0; background: rgba(0,0,0,0.8); backdrop-filter: blur(10px); z-index:999; padding:10px 0; }
    .nav-container { display:flex; justify-content:space-between; align-items:center; max-width:1200px; margin:0 auto; padding:0 20px; }
    .nav-menu { display:flex; list-style:none; gap:15px; margin:0; padding:0 }
    .nav-link { color:white; text-decoration:none; padding:10px 15px; border-radius:5px }
    .nav-link:hover, .nav-link.active { background: rgba(255,255,255,0.1); }
    .logo { display:flex; align-items:center; text-decoration:none }
    .logo-text { font-size:20px; font-weight:700; margin-left:8px }
    .prism { color:#6e45e2 } .flux { color:#88d3ce }
</style>
<header class="header">
    <nav class="nav-container">
        <a href="<?php echo ($siteBase ?: '') ?>/blog/index.php" class="logo"><div class="logo-icon"></div><span class="logo-text"><span class="prism">PRISM</span> <span class="flux">FLUX</span></span></a>
        <ul class="nav-menu">
            <li><a href="<?php echo ($siteBase ?: '') ?>/blog/index.php" class="nav-link">Home</a></li>
            <li><a href="<?php echo ($siteBase ?: '') ?>/blog/index.php?controller=article&action=index" class="nav-link">Articles</a></li>
            <li><a href="<?php echo ($siteBase ?: '') ?>/blog/index.php?controller=interaction&action=index" class="nav-link">Interactions</a></li>
            <li><a href="<?php echo ($siteBase ?: '') ?>/blog_admin/index.php" class="nav-link" style="background:#00dcB9;color:#000;border-radius:8px;">Admin</a></li>
        </ul>
    </nav>
</header>
<?php

// ============================================
// ROUTER
// ============================================
$controller = $_GET['controller'] ?? 'article';
$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

// Handle Reactions
if ($controller === 'reaction') {
    require_once BLOG_PATH . '/Controllers/ReactionController.php';
    $ctrl = new ReactionController();
    $ctrl->handle();
    exit;
}

// Handle Interactions
if ($controller === 'interaction') {
    require_once BLOG_PATH . '/Controllers/InteractionController.php';
    $ctrl = new InteractionController();
    $ctrl->handle();
    exit;
}

// Article Controller (default)
if ($controller === 'article' || $controller === null) {
    require_once BLOG_PATH . '/Controllers/ArticleController.php';
    $ctrl = new ArticleController();
    
    switch ($action) {
        case 'index':
            $ctrl->index();
            break;
        
        case 'show':
            if ($id) {
                $ctrl->show($id);
            } else {
                $ctrl->index();
            }
            break;
        
        case 'create':
            $ctrl->create();
            break;
        
        case 'edit':
            if ($id) {
                $ctrl->edit($id);
            } else {
                $ctrl->index();
            }
            break;
        
        case 'delete':
            if ($id) {
                $ctrl->delete($id);
            } else {
                $ctrl->index();
            }
            break;
        
        default:
            $ctrl->index();
            break;
    }
}
