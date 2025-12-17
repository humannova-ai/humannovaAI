<?php
// ============================================
// BLOG ADMIN - Requires existing user authentication
// ============================================
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Démarrer la session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- Admin protection: use a static placeholder user during local dev ---
require_once __DIR__ . '/user/static_user.php';

// If not connected or not admin, redirect to centralized login (production)
// During local dev we allow an env-gated bypass for troubleshooting.
if (!StaticUser::estConnecte() || !StaticUser::estAdmin()) {
    error_log('Admin access denied: connected=' . (StaticUser::estConnecte() ? '1' : '0') . ' admin=' . (StaticUser::estAdmin() ? '1' : '0'));
    // Dev-only bypass: only when APP_ENV=development and request comes from localhost and ?allow_admin_debug=1
    $appEnv = getenv('APP_ENV') ?: ($_SERVER['APP_ENV'] ?? '');
    $remote = $_SERVER['REMOTE_ADDR'] ?? '';
    $isLocalHost = in_array($remote, ['127.0.0.1', '::1', 'localhost']);
    if ($appEnv === 'development' && $isLocalHost && isset($_GET['allow_admin_debug']) && $_GET['allow_admin_debug'] == '1') {
        StaticUser::bootstrap();
        error_log('Admin bypass enabled (env gated)');
    } else {
        header('Location: user/view/login.php');
        exit;
    }
}

// (no debug banner in production)

// Define base paths
define('ROOT_PATH', dirname(__DIR__));
define('BLOG_ADMIN_PATH', __DIR__);

// Autoload shared resources
require_once ROOT_PATH . '/shared/Core/Connection.php';
require_once ROOT_PATH . '/shared/Models/model.php';

// Compute site base (two levels up) so admin links work under subpaths
// Compute site base (strip trailing '/blog_admin' so base becomes '/our' when hosted under a subpath)
$siteBase = rtrim(preg_replace('#/blog_admin(/index\.php)?$#', '', $_SERVER['SCRIPT_NAME']), '/');
if ($siteBase === '/' || $siteBase === '.') {
    $siteBase = '';
} else {
    if ($siteBase !== '' && $siteBase[0] !== '/') $siteBase = '/' . ltrim($siteBase, '/');
}
?>
<script>window.SITE_BASE = <?php echo json_encode($siteBase); ?>;</script>
<!-- Admin navbar (inlined) -->
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
        <a href="<?php echo ($siteBase ?: '') ?>/blog_admin/index.php" class="logo"><div class="logo-icon"></div><span class="logo-text"><span class="prism">PRISM</span> <span class="flux">FLUX</span></span></a>
        <ul class="nav-menu">
            <li><a href="<?php echo ($siteBase ?: '') ?>/blog_admin/index.php" class="nav-link">Dashboard</a></li>
            <li><a href="<?php echo ($siteBase ?: '') ?>/blog_admin/index.php?section=posts&action=index" class="nav-link">Posts</a></li>
            <li><a href="<?php echo ($siteBase ?: '') ?>/blog_admin/index.php?section=interactions&action=index" class="nav-link">Interactions</a></li>
            <li><a href="<?php echo ($siteBase ?: '') ?>/blog/index.php" class="nav-link" style="background:#00dcB9;color:#000;border-radius:8px;">Public site</a></li>
        </ul>
    </nav>
</header>
<?php

// ============================================
// ROUTER - Admin Dashboard
// ============================================
$section = $_GET['section'] ?? 'dashboard';
$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

// Dashboard
if ($section === 'dashboard') {
    // Load statistics
    require_once ROOT_PATH . '/shared/Models/Article.php';
    require_once ROOT_PATH . '/shared/Models/Interaction.php';
    require_once ROOT_PATH . '/shared/Models/Reaction.php';
    
    $articleModel = new Article();
    $interactionModel = new Interaction();
    $reactionModel = new Reaction();
    
    $totalPosts = count($articleModel->readAll());
    $publishedPosts = count(array_filter($articleModel->readAll(), function($a) { 
        return (($a['statut'] ?? '') === 'publie'); 
    }));
    $totalComments = count($interactionModel->readAll());
    $totalReactions = count($reactionModel->readAll());
    
    include BLOG_ADMIN_PATH . '/Views/dashboard.php';
    exit;
}

// Posts Management
if ($section === 'posts') {
    require_once ROOT_PATH . '/blog/Controllers/ArticleController.php';
    $ctrl = new ArticleController();
    
    // Override view path for admin (use table view instead of feed)
    define('USE_ADMIN_VIEW', true);
    
    switch ($action) {
        case 'index':
            $ctrl->index();
            break;
        
        case 'create':
            $ctrl->create();
            break;
    
        case 'store':
            $ctrl->store();
            break;
    
        case 'show':
            $ctrl->show($id);
            break;
    
        case 'edit':
            if ($id) {
                $ctrl->edit($id);
            } else {
                $ctrl->index();
            }
            break;
        
        case 'update':
            $ctrl->update($id);
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
    exit;
}

// Interactions Management
if ($section === 'interactions') {
    require_once ROOT_PATH . '/shared/Models/Interaction.php';
    require_once ROOT_PATH . '/shared/Models/Reaction.php';
    require_once ROOT_PATH . '/shared/Models/Article.php';
    
    $interactionModel = new Interaction();
    $reactionModel = new Reaction();
    $articleModel = new Article();
    
    // Handle delete action
    if ($action === 'delete') {
        $type = $_GET['type'] ?? '';
        if ($type === 'interaction' && $id) {
            $interactionModel->delete($id);
            header('Location: index.php?section=interactions&success=deleted');
            exit;
        } elseif ($type === 'reaction' && $id) {
            $reactionModel->delete($id);
            header('Location: index.php?section=interactions&success=deleted');
            exit;
        }
    }
    
    // Load interactions with article titles
    $interactions = $interactionModel->readAll();
    $articles = $articleModel->readAll();
    $articlesById = [];
    foreach ($articles as $article) {
        $articlesById[$article['id']] = $article['titre'];
    }
    
    // Add article titles to interactions
    foreach ($interactions as &$interaction) {
        $interaction['article_titre'] = $articlesById[$interaction['article_id']] ?? 'Article supprimé';
    }
    
    // Load reactions with article titles
    $reactions = $reactionModel->readAll();
    foreach ($reactions as &$reaction) {
        $reaction['article_titre'] = $articlesById[$reaction['article_id']] ?? 'Article supprimé';
    }
    
    include BLOG_ADMIN_PATH . '/Views/interactions/index.php';
    exit;
}

// Default: redirect to dashboard
header('Location: index.php?section=dashboard');
exit;
