<?php
// ============================================
// BLOG MODULE - PUBLIC WEBSITE
// ============================================
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRISM Blog</title>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;700;900&family=Rajdhani:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/nour/blog/public/templatemo-prism-flux.css">
</head>
<body>
    <!-- Loading Screen -->
    <div class="loader" id="loader">
        <div class="loader-content">
            <div class="loader-prism">
                <div class="prism-face"></div>
                <div class="prism-face"></div>
                <div class="prism-face"></div>
            </div>
            <div style="color: var(--accent-purple); font-size: 18px; text-transform: uppercase; letter-spacing: 3px;">Refracting Reality...</div>
        </div>
    </div>

    <!-- Navigation Header -->
    <header class="header" id="header">
        <nav class="nav-container">
            <a href="/" class="logo">
                <div class="logo-icon">
                    <div class="logo-prism">
                        <div class="prism-shape"></div>
                    </div>
                </div>
                <span class="logo-text">
                    <span class="prism">PRISM</span>
                    <span class="flux">BLOG</span>
                </span>
            </a>
            <ul class="nav-menu" id="navMenu">
                <li><a href="/" class="nav-link">Home</a></li>
                <li><a href="/" class="nav-link">Articles</a></li>
            </ul>
            <div class="menu-toggle" id="menuToggle">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </nav>
    </header>

    <!-- Show articles only (home removed) -->
    <main>
        <section class="posts" style="max-width:1200px;margin:40px auto;padding:0 20px;">
            <!-- Articles will be rendered by the controller router below -->
        </section>
    </main>

    <script src="/nour/blog/public/templatemo-prism-scripts.js"></script>
</body>
</html>

<?php
// ============================================
// ROUTER
// ============================================
define('BLOG_PATH', __DIR__);
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
