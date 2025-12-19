<?php
// ============================================
// CONFIGURATION ET DÉBUT DE SESSION
// ============================================
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php_errors.log');

// Démarrer la session en TOUT PREMIER
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Définir le chemin de base du blog
$blogPath = $_SERVER['DOCUMENT_ROOT'] . "/blog";

// ============================================
// ROUTER PRINCIPAL
// ============================================
$controller = $_GET['controller'] ?? null;
$action = $_GET['action'] ?? null;
$id = $_GET['id'] ?? null;

// ===== ROUTING NOUVEAU POUR LES RÉACTIONS =====
if ($controller === 'reaction') {
    require_once $blogPath . "/Controllers/ReactionController.php";
    $ctrl = new ReactionController();
    $ctrl->handle();
    exit;
}

// ===== ROUTING ADMIN =====
if ($controller === 'admin') {
    require_once $blogPath . "/Controllers/AdminController.php";
    $ctrl = new AdminController();
    
    if ($action === 'login') {
        $ctrl->login();
        exit;
    } elseif ($action === 'index') {
        $ctrl->index();
        exit;
    } elseif ($action === 'logout') {
        $ctrl->logout();
        exit;
    }
}

// ===== ROUTING ARTICLES (pour l'admin) =====
if ($controller === 'article' && in_array($action, ['index', 'create', 'edit', 'delete'])) {
    require_once $blogPath . "/Controllers/ArticleController.php";
    $ctrl = new ArticleController();
    
    if ($action === 'index') {
        $ctrl->index();
        exit;
    } elseif ($action === 'create') {
        $ctrl->create();
        exit;
    } elseif ($action === 'edit' && $id) {
        $ctrl->edit($id);
        exit;
    } elseif ($action === 'delete' && $id) {
        $ctrl->delete($id);
        exit;
    }
}

// ===== GESTION DES INTERACTIONS (commentaires) =====
if (isset($_POST['article_id']) && isset($_POST['type'])) {
    require_once $blogPath . "/Models/Interaction.php";
    
    $article_id = (int)$_POST['article_id'];
    $type = trim($_POST['type']);
    $auteur = trim($_POST['auteur']);
    $email = trim($_POST['email']);
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    $interactionModel = new Interaction();
    $interactionModel->setArticleId($article_id);
    $interactionModel->setType($type);
    $interactionModel->setAuteur($auteur);
    $interactionModel->setEmail($email);
    $interactionModel->setMessage($message);
    $interactionModel->create();
    
    $_SESSION['success'] = "Votre interaction a été ajoutée avec succès!";
    header("Location: index.php?action=show&id=" . $article_id);
    exit;
}

// ===== SUPPRESSION D'INTERACTIONS =====
if (isset($_GET['deleteId']) && $id) {
    require_once $blogPath . "/Models/Interaction.php";
    
    $interactionModel = new Interaction();
    $interactionModel->delete($_GET['deleteId']);
    
    header("Location: index.php?action=show&id=" . $id);
    exit;
}

// ===== CHARGER LES MODÈLES POUR L'AFFICHAGE PUBLIC =====
require_once $blogPath . "/Models/Article.php";
require_once $blogPath . "/Models/Interaction.php";

// Déterminer quelle page afficher
$showArticleDetail = ($action === 'show' && $id);
$isBlog = $showArticleDetail;

// ============================================
// DÉBUT DU HTML
// ============================================
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>PRISM FLUX - Digital Innovation Studio</title>
    <link rel="stylesheet" href="templatemo-prism-flux.css" />
    <style>
        /* ============================================
           VARIABLES CSS POUR LES THÈMES
           ============================================ */
        :root {
            --bg-primary: #ffffff;
            --bg-secondary: #f8f9fa;
            --bg-tertiary: #e9ecef;
            --text-primary: #212529;
            --text-secondary: #6c757d;
            --text-tertiary: #adb5bd;
            --border-color: #dee2e6;
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --accent-color: #ff6b6b;
            --success-color: #51cf66;
            --warning-color: #ff922b;
            --info-color: #339af0;
            --shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 30px rgba(0, 0, 0, 0.15);
            --radius: 12px;
            --radius-sm: 6px;
            --transition: all 0.3s ease;
        }
        
        [data-theme="dark"] {
            --bg-primary: #1a1b1e;
            --bg-secondary: #25262b;
            --bg-tertiary: #2c2e33;
            --text-primary: #f8f9fa;
            --text-secondary: #adb5bd;
            --text-tertiary: #6c757d;
            --border-color: #373a40;
            --primary-color: #8a9eff;
            --secondary-color: #9d6bd9;
            --accent-color: #ff6b81;
            --success-color: #69db7c;
            --warning-color: #ffa94d;
            --info-color: #4dabf7;
            --shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
            --shadow-lg: 0 10px 30px rgba(0, 0, 0, 0.4);
        }
        
        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            font-family: 'Segoe UI', system-ui, sans-serif;
            line-height: 1.6;
            transition: background-color 0.3s ease, color 0.3s ease;
            margin: 0;
            padding: 0;
            min-height: 100vh;
        }
        
        /* ============================================
           BARRE DE PROGRESSION
           ============================================ */
        .reading-progress {
            position: fixed;
            top: 0;
            left: 0;
            width: 0%;
            height: 3px;
            background: linear-gradient(90deg, #667eea, #764ba2);
            z-index: 9999;
            transition: width 0.1s;
        }
        
        /* ============================================
           SWITCH THÈME
           ============================================ */
        .theme-switcher {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 999;
            background: var(--bg-secondary);
            padding: 10px;
            border-radius: 50px;
            box-shadow: var(--shadow-lg);
            border: 2px solid var(--border-color);
            transition: var(--transition);
        }
        
        .theme-switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 30px;
        }
        
        .theme-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .theme-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: var(--border-color);
            border-radius: 34px;
            transition: .4s;
        }
        
        .theme-slider:before {
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            border-radius: 50%;
            transition: .4s;
        }
        
        input:checked + .theme-slider {
            background-color: var(--primary-color);
        }
        
        input:checked + .theme-slider:before {
            transform: translateX(30px);
        }
        
        .theme-icon {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            font-size: 14px;
            pointer-events: none;
        }
        
        .theme-icon.sun {
            left: 8px;
        }
        
        .theme-icon.moon {
            right: 8px;
        }
        
        /* ============================================
           STYLES FORMULAIRES
           ============================================ */
        .form-group {
            position: relative;
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--text-primary);
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            background: var(--bg-tertiary);
            color: var(--text-primary);
            font-size: 16px;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }
        
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .form-hint {
            display: block;
            font-size: 12px;
            color: var(--text-tertiary);
            margin-top: 5px;
        }
        
        .char-counter {
            text-align: right;
            font-size: 12px;
            color: var(--text-secondary);
            margin-top: 5px;
        }
        
        .char-counter.warning {
            color: var(--warning-color);
        }
        
        .char-counter.error {
            color: var(--accent-color);
            font-weight: bold;
        }
        
        /* ============================================
           STYLES RÉACTIONS
           ============================================ */
        .emoji-btn {
            font-size: 28px;
            padding: 12px;
            background: var(--bg-primary);
            border: 2px solid var(--border-color);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.2s ease;
            min-width: 60px;
            min-height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .emoji-btn:hover {
            transform: scale(1.15);
            background: var(--bg-tertiary);
            border-color: var(--primary-color);
        }
        
        .emoji-btn.active {
            border-color: var(--primary-color);
            background: var(--bg-tertiary);
            transform: scale(1.1);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }
        
        .loading-spinner {
            width: 30px;
            height: 30px;
            border: 3px solid var(--border-color);
            border-top-color: var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* ============================================
           ANIMATIONS NOTIFICATIONS
           ============================================ */
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        @keyframes slideOutRight {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
        
        /* ============================================
           RESPONSIVE
           ============================================ */
        @media (max-width: 768px) {
            .theme-switcher {
                top: 70px;
                right: 10px;
                padding: 8px;
            }
            
            .theme-switch {
                width: 50px !important;
                height: 25px !important;
            }
            
            .theme-slider:before {
                width: 18px !important;
                height: 18px !important;
            }
            
            .form-row {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
</head>
<body data-theme="<?php echo isset($_COOKIE['theme']) ? htmlspecialchars($_COOKIE['theme']) : 'light'; ?>">
    <!-- Barre de progression de lecture -->
    <div class="reading-progress" id="reading-progress"></div>
    
    <!-- Switch thème -->
    <div class="theme-switcher">
    <label class="theme-switch" title="Changer le thème">
        <input type="checkbox" id="theme-toggle" <?php echo (isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark') ? 'checked' : ''; ?>>
        <span class="theme-slider">
            <span class="theme-icon sun">☀️</span>
            <span class="theme-icon moon">🌙</span>
        </span>
    </label>
</div>

    <!-- Loading Screen -->
    <div class="loader" id="loader">
      <div class="loader-content">
        <div class="loader-prism">
          <div class="prism-face"></div>
          <div class="prism-face"></div>
          <div class="prism-face"></div>
        </div>
        <div style="color: #9b59b6; font-size: 18px; text-transform: uppercase; letter-spacing: 3px;">Refracting Reality...</div>
      </div>
    </div>

    <script>
      setTimeout(function() {
        var loader = document.getElementById('loader');
        if (loader && loader.style.display !== 'none') {
          loader.style.display = 'none';
        }
      }, 3000);
    </script>

    <!-- Navigation Header -->
    <header class="header" id="header">
      <nav class="nav-container">
        <a href="index.php" class="logo">
          <div class="logo-icon">
            <div class="logo-prism">
              <div class="prism-shape"></div>
            </div>
          </div>
          <span class="logo-text">
            <span class="prism">PRO MANAGE</span>
            <span class="flux">AI</span>
          </span>
        </a>

        <ul class="nav-menu" id="navMenu">
          <li><a href="index.php" class="nav-link active">Home</a></li>
          <li><a href="#about" class="nav-link">About</a></li>
          <li><a href="#stats" class="nav-link">Metrics</a></li>
          <li><a href="#skills" class="nav-link">Arsenal</a></li>
          <li><a href="#blog" class="nav-link">Blog</a></li>
          <li><a href="#contact" class="nav-link">Contact</a></li>
        </ul>

        <div class="menu-toggle" id="menuToggle">
          <span></span>
          <span></span>
          <span></span>
        </div>
      </nav>
    </header>

    <?php if (!$isBlog): ?>
    <!-- ============================================
    SECTION ACCUEIL (QUAND PAS SUR UN ARTICLE)
    ============================================ -->
    
    <!-- Hero Section with 3D Carousel -->
    <section class="hero" id="home">
      <div class="carousel-container">
        <div class="carousel" id="carousel"></div>
        <div class="carousel-controls">
          <button class="carousel-btn" id="prevBtn">‹</button>
          <button class="carousel-btn" id="nextBtn">›</button>
        </div>
        <div class="carousel-indicators" id="indicators"></div>
      </div>
    </section>

    <!-- About Section -->
    <section class="philosophy-section" id="about">
      <div class="philosophy-container">
        <div class="prism-line"></div>
        <h2 class="philosophy-headline">Refracting Ideas<br />Into Reality</h2>
        <p class="philosophy-subheading">
          At PRISM FLUX, we transform complex challenges into elegant solutions
          through the convergence of cutting-edge technology and visionary
          design. Every project is a spectrum of possibilities waiting to be
          discovered.
        </p>
        <div class="philosophy-pillars">
          <div class="pillar">
            <div class="pillar-icon">💎</div>
            <h3 class="pillar-title">Innovation</h3>
            <p class="pillar-description">Breaking boundaries with revolutionary approaches that redefine industry standards and push the limits of what's possible.</p>
          </div>
          <div class="pillar">
            <div class="pillar-icon">🔬</div>
            <h3 class="pillar-title">Precision</h3>
            <p class="pillar-description">Meticulous attention to detail ensures every pixel, every line of code, and every interaction is perfectly crafted.</p>
          </div>
          <div class="pillar">
            <div class="pillar-icon">∞</div>
            <h3 class="pillar-title">Evolution</h3>
            <p class="pillar-description">Continuous adaptation and growth, staying ahead of trends while building timeless solutions for tomorrow.</p>
          </div>
        </div>
        <div class="philosophy-particles" id="particles"></div>
      </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section" id="stats">
      <div class="section-header">
        <h2 class="section-title">Performance Metrics</h2>
        <p class="section-subtitle">Real-time analytics and achievements powered by cutting-edge technology</p>
      </div>
      <div class="stats-grid">
        <div class="stat-card">
          <div class="stat-icon">🚀</div>
          <div class="stat-number" data-target="150">0</div>
          <div class="stat-label">Projects Completed</div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">⚡</div>
          <div class="stat-number" data-target="99">0</div>
          <div class="stat-label">Client Satisfaction %</div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">🏆</div>
          <div class="stat-number" data-target="25">0</div>
          <div class="stat-label">Industry Awards</div>
        </div>
        <div class="stat-card">
          <div class="stat-icon">💎</div>
          <div class="stat-number" data-target="500">0</div>
          <div class="stat-label">Code Commits Daily</div>
        </div>
      </div>
    </section>

    <!-- Skills Section -->
    <section class="skills-section" id="skills">
      <div class="skills-container">
        <div class="section-header">
          <h2 class="section-title">Technical Arsenal</h2>
          <p class="section-subtitle">Mastery of cutting-edge technologies and frameworks</p>
        </div>
        <div class="skill-categories">
          <div class="category-tab active" data-category="all">All Skills</div>
          <div class="category-tab" data-category="frontend">Frontend</div>
          <div class="category-tab" data-category="backend">Backend</div>
          <div class="category-tab" data-category="cloud">Cloud & DevOps</div>
          <div class="category-tab" data-category="emerging">Emerging Tech</div>
        </div>
        <div class="skills-hexagon-grid" id="skillsGrid"></div>
      </div>
    </section>

    <!-- Blog Section -->
    <section class="blog-section" id="blog">
      <div class="section-header">
        <h2 class="section-title">Blog</h2>
        <p class="section-subtitle">Donnez votre avis sur nos articles</p>
      </div>
      <div class="blog-container" style="max-width: 1200px; margin: 40px auto;">
        <?php
        try {
            $articleModel = new Article();
            
            if (method_exists($articleModel, 'readAll')) {
                $articles = $articleModel->readAll();
            } else {
                $stmt = $articleModel->getConnection()->prepare("SELECT * FROM articles ORDER BY date_creation DESC");
                $stmt->execute();
                $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
            }

            if (!empty($articles)) {
                echo '<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">';
                foreach ($articles as $article) {
                    echo '<div style="background: var(--bg-secondary); border: 1px solid var(--border-color); border-radius: 8px; padding: 20px; color: var(--text-primary); transition: all 0.3s ease;">';
                    echo '<h3 style="color: var(--primary-color); margin-top: 0;">' . htmlspecialchars($article['titre'] ?? 'Titre non disponible') . '</h3>';
                    
                    $excerpt = '';
                    if (isset($article['excerpt']) && !empty($article['excerpt'])) {
                        $excerpt = htmlspecialchars(substr($article['excerpt'], 0, 150)) . '...';
                    } elseif (isset($article['contenu']) && !empty($article['contenu'])) {
                        $excerpt = substr(strip_tags($article['contenu']), 0, 150) . '...';
                    } else {
                        $excerpt = 'Aucun contenu disponible...';
                    }
                    echo '<p style="color: var(--text-secondary); font-size: 0.95em; margin: 10px 0;">' . $excerpt . '</p>';
                    
                    echo '<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px;">';
                    echo '<span style="color: var(--text-tertiary); font-size: 0.85em;">👁️ ' . ($article['views'] ?? 0) . ' vues</span>';
                    echo '<span style="color: var(--text-tertiary); font-size: 0.85em;">📅 ' . date('d/m/Y', strtotime($article['date_creation'] ?? 'now')) . '</span>';
                    echo '</div>';
                    
                    echo '<a href="index.php?action=show&id=' . ($article['id'] ?? 0) . '" style="color: var(--primary-color); text-decoration: none; font-weight: bold; display: inline-block; margin-top: 10px;">Lire la suite →</a>';
                    echo '</div>';
                }
                echo '</div>';
            } else {
                echo '<p style="text-align: center; color: var(--text-secondary);">Aucun article disponible pour le moment.</p>';
            }
        } catch (Exception $e) {
            echo '<p style="text-align: center; color: var(--accent-color);">Erreur lors du chargement des articles: ' . htmlspecialchars($e->getMessage()) . '</p>';
            error_log("Erreur blog section: " . $e->getMessage());
        }
        ?>
      </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section" id="contact">
      <div class="section-header">
        <h2 class="section-title">Initialize Connection</h2>
        <p class="section-subtitle">Ready to transform your vision into reality? Let's connect.</p>
      </div>
      <div class="contact-container">
        <div class="contact-info">
          <a href="https://maps.google.com/?q=Silicon+Valley+CA+94025" target="_blank" class="info-item">
            <div class="info-icon">📍</div>
            <div class="info-text"><h4>Location</h4><p>Silicon Valley, CA 94025</p></div>
          </a>
          <a href="mailto:hello@prismflux.io" class="info-item">
            <div class="info-icon">📧</div>
            <div class="info-text"><h4>Email</h4><p>hello@prismflux.io</p></div>
          </a>
          <a href="tel:+15551234567" class="info-item">
            <div class="info-icon">📱</div>
            <div class="info-text"><h4>Phone</h4><p>+1 (555) 123-4567</p></div>
          </a>
        </div>
        <form class="contact-form" id="contactForm">
          <div class="form-group"><label for="name">Name</label><input type="text" id="name" name="name" required /></div>
          <div class="form-group"><label for="email">Email</label><input type="email" id="email" name="email" required /></div>
          <div class="form-group"><label for="subject">Subject</label><input type="text" id="subject" name="subject" required /></div>
          <div class="form-group"><label for="message">Message</label><textarea id="message" name="message" required></textarea></div>
          <button type="submit" class="submit-btn">Transmit Message</button>
        </form>
      </div>
    </section>
    <?php endif; ?>

    <?php if ($showArticleDetail): ?>
    <!-- ============================================
    PAGE DÉTAIL DE L'ARTICLE
    ============================================ -->
    <section class="article-detail-page" style="padding: 100px 20px; min-height: 100vh; background: var(--bg-primary); color: var(--text-primary);">
      <div class="article-container" style="max-width: 800px; margin: 0 auto;">
        <?php
          $articleId = (int)$id;
          $articleModel = new Article();
          $article = $articleModel->readById($articleId);
          
          if (!$article) {
            echo '<p style="text-align: center; color: var(--text-secondary);">Article non trouvé.</p>';
          } else {
            // Incrémenter les vues
            $articleModel->incrementViews($articleId);
            
            $success = $_SESSION['success'] ?? null;
            unset($_SESSION['success']);
            
            echo '<article>';
            
            // Titre et meta
            echo '<h1 style="color: var(--text-primary); margin-bottom: 20px; font-size: 2.5rem; line-height: 1.2;">' . htmlspecialchars($article['titre']) . '</h1>';
            
            echo '<div class="article-meta" style="display: flex; gap: 20px; margin-bottom: 30px; color: var(--text-secondary); font-size: 14px; flex-wrap: wrap;">';
            echo '<span>📅 ' . date('d/m/Y', strtotime($article['date_creation'])) . '</span>';
            echo '<span>👁️ ' . ($article['views'] ?? 0) . ' vues</span>';
            echo '<span>⏱️ ' . ($article['reading_time'] ?? 5) . ' min</span>';
            if(isset($article['tags']) && !empty($article['tags'])) {
                echo '<span>🏷️ ' . htmlspecialchars($article['tags']) . '</span>';
            }
            echo '</div>';
            
            // Image si disponible
            if(isset($article['image']) && !empty($article['image'])) {
                echo '<div class="article-image" style="margin: 30px 0; border-radius: 12px; overflow: hidden; box-shadow: var(--shadow);">';
                echo '<img src="' . htmlspecialchars($article['image']) . '" alt="' . htmlspecialchars($article['titre']) . '" style="width: 100%; height: auto; display: block;">';
                echo '</div>';
            }
            
            // Contenu
            echo '<div class="article-content" style="font-size: 18px; line-height: 1.8; margin: 40px 0;">';
            echo nl2br(htmlspecialchars($article['contenu'] ?? ''));
            echo '</div>';
            
            // Message de succès
            if ($success) {
              echo '<div style="background-color: rgba(76, 175, 80, 0.1); color: var(--success-color); padding: 15px; border: 1px solid var(--success-color); border-radius: 8px; margin-bottom: 30px;"><strong>Succès!</strong> ' . htmlspecialchars($success) . '</div>';
            }
            
            // Construire l'URL absolue pour le partage
            // $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
            // $host = $_SERVER['HTTP_HOST'];
            // $script = $_SERVER['SCRIPT_NAME'];
            // $base_url = $protocol . $host . dirname($script);
            // $article_url = $base_url . '/index.php?action=show&id=' . $article['id'];
            // $article_title = htmlspecialchars($article['titre'] ?? 'Article intéressant');
            // $encoded_url = urlencode($article_url);
            // $encoded_title = urlencode($article_title);
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$script_name = $_SERVER['SCRIPT_NAME']; // Ex: /blog/index.php
$script_dir = dirname($script_name); // Ex: /blog
$base_url = $protocol . $host . $script_dir;
$article_url = $base_url . '/index.php?action=show&id=' . $article['id'];
$article_title = $article['titre'] ?? 'Article intéressant';
$article_excerpt = substr(strip_tags($article['contenu'] ?? ''), 0, 150);
$encoded_url = urlencode($article_url);
$encoded_title = urlencode($article_title);
$encoded_excerpt = urlencode($article_excerpt);
error_log("=== URL Partage Debug ===");
error_log("Protocol: " . $protocol);
error_log("Host: " . $host);
error_log("Script Dir: " . $script_dir);
error_log("Article URL: " . $article_url);
            ?>
            
            <!-- ============================================
            SECTION RÉACTIONS (ÉMOJIS)
            ============================================ -->
            <div class="reactions-section" id="reactions-section" style="
                margin: 40px 0;
                padding: 25px;
                background: var(--bg-secondary);
                border-radius: 12px;
                border: 1px solid var(--border-color);
                box-shadow: var(--shadow);
            ">
                <h3 style="color: var(--text-primary); margin-top: 0;">Réagir à l'article</h3>
                <p style="color: var(--text-secondary); margin-bottom: 20px;">Choisissez un émoji pour exprimer votre réaction :</p>
                
                <div class="emoji-picker" id="emoji-picker" style="
                    display: flex;
                    gap: 15px;
                    margin: 25px 0;
                    flex-wrap: wrap;
                    min-height: 80px;
                    align-items: center;
                ">
                    <div class="emoji-loading" style="display: flex; align-items: center; gap: 15px; color: var(--text-secondary);">
                        <div class="loading-spinner"></div>
                        <p>Chargement des réactions...</p>
                    </div>
                </div>
                
                <div class="reactions-stats" id="reactions-stats" style="
                    margin-top: 25px;
                    padding-top: 20px;
                    border-top: 1px solid var(--border-color);
                ">
                    <p class="no-stats" style="color: var(--text-secondary); font-style: italic; text-align: center;">
                        Aucune statistique disponible pour le moment.
                    </p>
                </div>
            </div>

            <script>
            // ============================================
            // SYSTÈME DE RÉACTIONS COMPLET
            // ============================================
            const REACTIONS_ARTICLE_ID = <?= $article['id'] ?? 0 ?>;
            const REACTIONS_BASE_URL = 'index.php?controller=reaction&action=handle';
            
            console.log('Initialisation réactions - Article ID:', REACTIONS_ARTICLE_ID);
            
            // Charger les réactions
            async function loadReactions() {
                if (!REACTIONS_ARTICLE_ID || REACTIONS_ARTICLE_ID <= 0) {
                    console.error('ID article invalide');
                    showReactionsError('Article ID invalide');
                    return;
                }
                
                const emojiPicker = document.getElementById('emoji-picker');
                const statsDiv = document.getElementById('reactions-stats');
                
                if (!emojiPicker || !statsDiv) {
                    console.error('Éléments DOM non trouvés');
                    return;
                }
                
                try {
                    console.log('Fetching réactions...');
                    
                    const response = await fetch(`${REACTIONS_BASE_URL}&article_id=${REACTIONS_ARTICLE_ID}`, {
                        method: 'GET',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        cache: 'no-cache'
                    });
                    
                    console.log('Response status:', response.status);
                    
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    
                    const data = await response.json();
                    console.log('Data reçue:', data);
                    
                    if (data.success) {
                        updateReactionsUI(data);
                    } else {
                        showReactionsError('Erreur: ' + (data.error || 'Inconnue'));
                        loadDefaultEmojis();
                    }
                    
                } catch (error) {
                    console.error('Erreur chargement réactions:', error);
                    showReactionsError('Impossible de charger les réactions');
                    loadDefaultEmojis();
                }
            }
            
            // Mettre à jour l'interface
            function updateReactionsUI(data) {
                const emojiPicker = document.getElementById('emoji-picker');
                const statsDiv = document.getElementById('reactions-stats');
                
                // Mettre à jour les émojis
                if (data.available_emojis && data.available_emojis.length > 0) {
                    emojiPicker.innerHTML = '';
                    data.available_emojis.forEach(emoji => {
                        const button = document.createElement('button');
                        button.className = 'emoji-btn';
                        button.textContent = emoji;
                        button.title = `Réagir avec ${emoji}`;
                        button.dataset.emoji = emoji;
                        
                        // Marquer comme actif si c'est la réaction de l'utilisateur
                        if (data.user_reaction === emoji) {
                            button.classList.add('active');
                        }
                        
                        button.addEventListener('click', () => sendReaction(emoji));
                        emojiPicker.appendChild(button);
                    });
                } else {
                    loadDefaultEmojis();
                }
                
                // Mettre à jour les statistiques
                statsDiv.innerHTML = '';
                
                if (data.reactions && data.reactions.length > 0) {
                    const statsContainer = document.createElement('div');
                    statsContainer.style.cssText = 'display: flex; gap: 15px; flex-wrap: wrap;';
                    
                    data.reactions.forEach(reaction => {
                        const stat = document.createElement('div');
                        stat.innerHTML = `
                            <span style="font-size: 24px;">${reaction.emoji}</span>
                            <span style="margin-left: 8px; color: var(--text-primary); font-weight: 600;">${reaction.count}</span>
                        `;
                        stat.style.cssText = `
                            display: flex;
                            align-items: center;
                            padding: 8px 16px;
                            background: var(--bg-tertiary);
                            border-radius: 20px;
                            border: 1px solid var(--border-color);
                        `;
                        statsContainer.appendChild(stat);
                    });
                    
                    statsDiv.appendChild(statsContainer);
                    
                    if (data.stats && data.stats.total_reactions > 0) {
                        const total = document.createElement('p');
                        total.style.cssText = 'margin-top: 15px; text-align: center; color: var(--text-primary); font-weight: 600;';
                        total.innerHTML = `Total: ${data.stats.total_reactions} réactions`;
                        statsDiv.appendChild(total);
                    }
                } else {
                    statsDiv.innerHTML = '<p style="color: var(--text-secondary); font-style: italic; text-align: center;">Soyez le premier à réagir !</p>';
                }
            }
            
            // Charger les émojis par défaut
            function loadDefaultEmojis() {
                const emojiPicker = document.getElementById('emoji-picker');
                const defaultEmojis = ['👍', '❤️', '😮', '😄', '🔥', '👏', '🎉', '💡'];
                
                emojiPicker.innerHTML = '';
                defaultEmojis.forEach(emoji => {
                    const button = document.createElement('button');
                    button.className = 'emoji-btn';
                    button.textContent = emoji;
                    button.title = `Réagir avec ${emoji}`;
                    button.dataset.emoji = emoji;
                    button.addEventListener('click', () => sendReaction(emoji));
                    emojiPicker.appendChild(button);
                });
            }
            
            // Envoyer une réaction
            async function sendReaction(emoji) {
                console.log('Envoi réaction:', emoji);
                
                if (!REACTIONS_ARTICLE_ID || REACTIONS_ARTICLE_ID <= 0) {
                    showReactionsError('Article ID invalide');
                    return;
                }
                
                try {
                    const formData = new FormData();
                    formData.append('article_id', REACTIONS_ARTICLE_ID);
                    formData.append('emoji', emoji);
                    
                    const response = await fetch(REACTIONS_BASE_URL, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    if (!response.ok) {
                        throw new Error(`HTTP ${response.status}`);
                    }
                    
                    const result = await response.json();
                    console.log('Réponse réaction:', result);
                    
                    if (result.success) {
                        showReactionsMessage('✅ Réaction envoyée !', 'success');
                        loadReactions();
                    } else {
                        showReactionsError(result.error || 'Erreur lors de l\'envoi');
                    }
                    
                } catch (error) {
                    console.error('Erreur envoi réaction:', error);
                    showReactionsError('❌ Erreur de connexion au serveur');
                }
            }
            
            function showReactionsError(message) {
                showReactionsMessage(message, 'error');
            }
            
            function showReactionsMessage(message, type = 'info') {
                const oldMsg = document.querySelector('.reaction-message');
                if (oldMsg) oldMsg.remove();
                
                const msgDiv = document.createElement('div');
                msgDiv.className = 'reaction-message';
                msgDiv.textContent = message;
                
                const bgColors = {
                    error: 'linear-gradient(135deg, #e74c3c, #c0392b)',
                    success: 'linear-gradient(135deg, #27ae60, #229954)',
                    info: 'linear-gradient(135deg, #3498db, #2980b9)'
                };
                
                msgDiv.style.cssText = `
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    padding: 15px 25px;
                    border-radius: 10px;
                    z-index: 10000;
                    box-shadow: 0 6px 20px rgba(0,0,0,0.2);
                    animation: slideInRight 0.3s ease forwards;
                    max-width: 400px;
                    word-break: break-word;
                    color: white;
                    font-weight: 500;
                    font-size: 14px;
                    background: ${bgColors[type] || bgColors.info};
                `;
                
                document.body.appendChild(msgDiv);
                
                setTimeout(() => {
                    msgDiv.style.animation = 'slideOutRight 0.3s ease forwards';
                    setTimeout(() => msgDiv.remove(), 300);
                }, 4000);
            }
            
            // Initialiser
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    setTimeout(loadReactions, 500);
                });
            } else {
                setTimeout(loadReactions, 500);
            }
            </script>
            
            <?php
            // ============================================
            // SECTION PARTAGE SOCIAL
            // ============================================
            ?>
            <div class="social-share-section" style="
                margin: 40px 0;
                padding: 25px;
                background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-tertiary) 100%);
                border-radius: 12px;
                border: 2px solid var(--border-color);
                box-shadow: var(--shadow-lg);
            ">
                <h3 style="color: var(--text-primary); margin-top: 0; margin-bottom: 15px; display: flex; align-items: center; gap: 10px;">
                    <span style="font-size: 24px;">📢</span>
                    <span>Partager cet article</span>
                </h3>
                
                <div style="background: var(--bg-tertiary); padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid var(--primary-color);">
                    <strong style="color: var(--text-primary); display: block; margin-bottom: 8px;">Lien à partager :</strong>
                    <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                        <input type="text" 
                               id="share-url-input" 
                               value="<?= htmlspecialchars($article_url) ?>" 
                               readonly 
                               style="flex: 1; min-width: 200px; padding: 10px; background: var(--bg-primary); border: 1px solid var(--border-color); border-radius: 6px; color: var(--text-primary); font-family: monospace; font-size: 14px;">
                        <button onclick="copyShareLink()" 
                                style="padding: 10px 20px; background: var(--primary-color); color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; white-space: nowrap;">
                            📋 Copier
                        </button>
                    </div>
                </div>
                
                <div class="share-buttons" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px;">
    <!-- Facebook -->
    <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $encoded_url ?>&quote=<?= $encoded_title ?>" 
       target="_blank" 
       rel="noopener noreferrer"
       onclick="window.open(this.href, 'facebook-share', 'width=580,height=296'); return false;"
       class="share-btn"
       style="display: flex; align-items: center; justify-content: center; gap: 8px; padding: 14px 20px; background: linear-gradient(135deg, #1877f2, #0d5fd9); color: white; text-decoration: none; border-radius: 10px; font-weight: 600; transition: transform 0.2s, box-shadow 0.2s; box-shadow: 0 4px 8px rgba(0,0,0,0.15);">
        <span style="font-size: 20px;">📘</span>
        <span>Facebook</span>
    </a>
    
    <!-- Twitter/X -->
    <a href="https://twitter.com/intent/tweet?url=<?= $encoded_url ?>&text=<?= $encoded_title ?>&via=PrismFlux" 
       target="_blank" 
       rel="noopener noreferrer"
       onclick="window.open(this.href, 'twitter-share', 'width=550,height=420'); return false;"
       class="share-btn"
       style="display: flex; align-items: center; justify-content: center; gap: 8px; padding: 14px 20px; background: linear-gradient(135deg, #000000, #333333); color: white; text-decoration: none; border-radius: 10px; font-weight: 600; transition: transform 0.2s, box-shadow 0.2s; box-shadow: 0 4px 8px rgba(0,0,0,0.15);">
        <span style="font-size: 20px;">𝕏</span>
        <span>Twitter</span>
    </a>
    
    <!-- LinkedIn -->
    <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= $encoded_url ?>" 
       target="_blank" 
       rel="noopener noreferrer"
       onclick="window.open(this.href, 'linkedin-share', 'width=520,height=570'); return false;"
       class="share-btn"
       style="display: flex; align-items: center; justify-content: center; gap: 8px; padding: 14px 20px; background: linear-gradient(135deg, #0a66c2, #084a8e); color: white; text-decoration: none; border-radius: 10px; font-weight: 600; transition: transform 0.2s, box-shadow 0.2s; box-shadow: 0 4px 8px rgba(0,0,0,0.15);">
        <span style="font-size: 20px;">in</span>
        <span>LinkedIn</span>
    </a>
    
    <!-- WhatsApp -->
    <a href="https://api.whatsapp.com/send?text=<?= $encoded_title ?>%20-%20<?= $encoded_url ?>" 
       target="_blank" 
       rel="noopener noreferrer"
       data-action="share/whatsapp/share"
       class="share-btn"
       style="display: flex; align-items: center; justify-content: center; gap: 8px; padding: 14px 20px; background: linear-gradient(135deg, #25D366, #128C7E); color: white; text-decoration: none; border-radius: 10px; font-weight: 600; transition: transform 0.2s, box-shadow 0.2s; box-shadow: 0 4px 8px rgba(0,0,0,0.15);">
        <span style="font-size: 20px;">💬</span>
        <span>WhatsApp</span>
    </a>
    
    <!-- Email -->
    <a href="mailto:?subject=<?= $encoded_title ?>&body=<?= $encoded_excerpt ?>%0A%0ALire%20l%27article%20complet%20:%20<?= $encoded_url ?>" 
       class="share-btn"
       style="display: flex; align-items: center; justify-content: center; gap: 8px; padding: 14px 20px; background: linear-gradient(135deg, #ea4335, #d33b2c); color: white; text-decoration: none; border-radius: 10px; font-weight: 600; transition: transform 0.2s, box-shadow 0.2s; box-shadow: 0 4px 8px rgba(0,0,0,0.15);">
        <span style="font-size: 20px;">📧</span>
        <span>Email</span>
    </a>
    
    <!-- Telegram -->
    <a href="https://t.me/share/url?url=<?= $encoded_url ?>&text=<?= $encoded_title ?>" 
       target="_blank" 
       rel="noopener noreferrer"
       class="share-btn"
       style="display: flex; align-items: center; justify-content: center; gap: 8px; padding: 14px 20px; background: linear-gradient(135deg, #0088cc, #006699); color: white; text-decoration: none; border-radius: 10px; font-weight: 600; transition: transform 0.2s, box-shadow 0.2s; box-shadow: 0 4px 8px rgba(0,0,0,0.15);">
        <span style="font-size: 20px;">✈️</span>
        <span>Telegram</span>
    </a>
</div>
            <script>

            console.log('=== Test URL Partage ===');
console.log('URL actuelle:', window.location.href);
console.log('URL article:', '<?= htmlspecialchars($article_url, ENT_QUOTES, 'UTF-8') ?>');
console.log('Titre:', '<?= htmlspecialchars($article_title, ENT_QUOTES, 'UTF-8') ?>');

// Tester le bouton copier
function copyShareLink() {
    const input = document.getElementById('share-url-input');
    if (!input) {
        console.error('Input share-url-input not found');
        return;
    }
    
    const url = input.value;
    console.log('Copying URL:', url);
    
    input.select();
    input.setSelectionRange(0, 99999);
    
    // Méthode moderne
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(url).then(() => {
            console.log('Copied with clipboard API');
            showNotification('✅ Lien copié avec succès !', 'success');
        }).catch(err => {
            console.error('Clipboard error:', err);
            fallbackCopy(url);
        });
    } else {
        fallbackCopy(url);
    }
}

function fallbackCopy(text) {
    try {
        const successful = document.execCommand('copy');
        if (successful) {
            console.log('Copied with execCommand');
            showNotification('✅ Lien copié avec succès !', 'success');
        } else {
            console.error('execCommand failed');
            showNotification('❌ Erreur lors de la copie', 'error');
        }
    } catch (err) {
        console.error('Fallback copy error:', err);
        showNotification('❌ Impossible de copier: ' + text, 'error');
    }
}
            </script>
            
            <?php
            // ============================================
            // SECTION COMMENTAIRES
            // ============================================
            echo '<div class="comments-section" style="margin-top: 60px; padding-top: 40px; border-top: 1px solid var(--border-color);">';
            echo '<h2 style="color: var(--text-primary); margin-bottom: 30px;">Interactions</h2>';
            
            $interactionModel = new Interaction();
            $interactions = [];
            $likeCount = 0;
            try {
              $interactions = $interactionModel->readAllByArticle($article['id']);
              $likeCount = $interactionModel->countLikes($article['id']);
            } catch (Exception $e) {
              echo "<p style='color:var(--text-secondary);'>Erreur lors du chargement des interactions</p>";
            }
            
            echo '<p style="color: var(--text-primary); margin: 20px 0; font-size: 1.1rem;"><strong>👍 ' . $likeCount . ' J\'aime</strong></p>';
            
            // Formulaire LIKE
            echo '<div class="like-form-container" style="margin-bottom:30px; background: var(--bg-secondary); padding: 25px; border-radius: var(--radius); border: 1px solid var(--border-color);">';
            echo '<form method="post" class="like-form" id="likeForm">';
            echo '<h3 style="color: var(--text-primary); margin-top: 0; margin-bottom: 20px; font-size: 1.3rem;">Aimer cet article</h3>';
            echo '<input type="hidden" name="article_id" value="' . $article['id'] . '">';
            echo '<input type="hidden" name="type" value="like">';
            echo '<div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">';
            
            echo '<div class="form-group">';
            echo '<label for="like_name">Votre nom *</label>';
            echo '<input type="text" id="like_name" name="auteur" required placeholder="Votre nom">';
            echo '<small class="form-hint">Lettres, espaces et tirets seulement (2-50 caractères)</small>';
            echo '</div>';
            
            echo '<div class="form-group">';
            echo '<label for="like_email">Votre email *</label>';
            echo '<input type="email" id="like_email" name="email" required placeholder="votre@email.com">';
            echo '<small class="form-hint">Nous ne partagerons jamais votre email</small>';
            echo '</div>';
            
            echo '</div>';
            
            echo '<button type="submit" style="display: flex; align-items: center; gap: 10px; padding: 12px 25px; background: var(--primary-color); color: white; border: none; border-radius: var(--radius-sm); font-weight: 600; font-size: 16px; cursor: pointer; width: 100%; justify-content: center;">';
            echo '<span>👍</span><span>J\'aime cet article</span>';
            echo '</button>';
            
            echo '</form>';
            echo '</div>';
            
            // Liste des interactions
            if (!empty($interactions)) {
              echo '<div style="background: var(--bg-secondary); padding: 20px; border-radius: 8px; border: 1px solid var(--border-color); margin-bottom: 30px;">';
              echo '<h3 style="color: var(--text-primary); margin-top: 0;">Activités récentes</h3>';
              echo '<ul style="list-style: none; padding: 0; margin: 0;">';
              foreach ($interactions as $i) {
                echo '<li style="padding: 12px 0; border-bottom: 1px solid var(--border-color); color: var(--text-secondary);">';
                if ($i['type'] === 'like') {
                  echo '👍 <strong>' . htmlspecialchars($i['auteur']) . '</strong> a aimé cet article';
                } else {
                  echo '💬 <strong>' . htmlspecialchars($i['auteur']) . '</strong>: ' . htmlspecialchars($i['message']);
                }
                echo ' <a href="index.php?action=show&id=' . $article['id'] . '&deleteId=' . $i['id'] . '" style="color: var(--accent-color); text-decoration: none; margin-left: 10px; font-size: 0.9em;" onclick="return confirm(\'Supprimer cette interaction ?\')">Supprimer</a>';
                echo '</li>';
              }
              echo '</ul>';
              echo '</div>';
            }
            
            // Formulaire COMMENTAIRE
            echo '<div style="background: var(--bg-secondary); padding: 25px; border-radius: 8px; border: 1px solid var(--border-color);">';
            echo '<h3 style="color: var(--text-primary); margin-top: 0;">Ajouter un commentaire</h3>';
            echo '<form method="post" id="commentForm">';
            echo '<input type="hidden" name="article_id" value="' . $article['id'] . '">';
            echo '<input type="hidden" name="type" value="comment">';
            
            echo '<div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">';
            
            echo '<div class="form-group">';
            echo '<label for="comment_name">Votre nom *</label>';
            echo '<input type="text" id="comment_name" name="auteur" required placeholder="Votre nom">';
            echo '</div>';
            
            echo '<div class="form-group">';
            echo '<label for="comment_email">Votre email *</label>';
            echo '<input type="email" id="comment_email" name="email" required placeholder="votre@email.com">';
            echo '</div>';
            
            echo '</div>';
            
            echo '<div class="form-group">';
            echo '<label for="comment_message">Votre commentaire *</label>';
            echo '<textarea id="comment_message" name="message" required placeholder="Votre commentaire (5-1000 caractères)" rows="5" minlength="5" maxlength="1000"></textarea>';
            echo '<div class="char-counter"><span id="char-count">0</span> / 1000 caractères</div>';
            echo '</div>';
            
            echo '<button type="submit" style="padding: 12px 25px; background: var(--primary-color); color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 1rem; width: 100%;">💬 Publier le commentaire</button>';
            echo '</form>';
            echo '</div>';
            
            echo '</div>'; // Fin section commentaires
            
            echo '</article>';
            
            // Navigation
            echo '<div style="margin-top: 40px; padding-top: 30px; border-top: 1px solid var(--border-color); text-align: center;">';
            echo '<a href="index.php#blog" style="color: var(--primary-color); text-decoration: none; font-weight: bold; font-size: 1.1rem; padding: 10px 20px; border: 1px solid var(--primary-color); border-radius: 6px; display: inline-block;">← Retour au blog</a>';
            echo '</div>';
          }
        ?>
      </div>
    </section>
    <?php endif; ?>

    <!-- Footer -->
    <footer class="footer" style="background: var(--bg-secondary); color: var(--text-primary); border-top: 1px solid var(--border-color);">
      <div class="footer-content">
        <div class="footer-brand">
          <div class="footer-logo">
            <div class="logo-icon">
              <div class="logo-prism">
                <div class="prism-shape"></div>
              </div>
            </div>
            <span class="logo-text">
              <span class="prism">PRISM</span>
              <span class="flux">FLUX</span>
            </span>
          </div>
          <p class="footer-description">
            Refracting complex challenges into brilliant solutions through the
            convergence of art, science, and technology.
          </p>
          <div class="footer-social">
            <a href="#" class="social-icon">f</a>
            <a href="#" class="social-icon">t</a>
            <a href="#" class="social-icon">in</a>
            <a href="#" class="social-icon">ig</a>
          </div>
        </div>

        <div class="footer-section">
          <h4>Services</h4>
          <div class="footer-links">
            <a href="#">Web Development</a>
            <a href="#">App Development</a>
            <a href="#">Cloud Solutions</a>
            <a href="#">AI Integration</a>
          </div>
        </div>

        <div class="footer-section">
          <h4>Company</h4>
          <div class="footer-links">
            <a href="#">About Us</a>
            <a href="#">Our Team</a>
            <a href="#">Careers</a>
            <a href="#">Press Kit</a>
          </div>
        </div>

        <div class="footer-section">
          <h4>Resources</h4>
          <div class="footer-links">
            <a href="#">Documentation</a>
            <a href="#">API Reference</a>
            <a href="#">Blog</a>
            <a href="#">Support</a>
          </div>
        </div>

        <div class="footer-section">
          <h4>Admin</h4>
          <div class="footer-links">
            <a href="index.php?controller=admin&action=login">Admin Login</a>
          </div>
        </div>
      </div>

      <div class="footer-bottom">
        <div class="copyright">© 2026 PRISM FLUX. All rights reserved.</div>
        <div class="footer-credits">
          Designed by <a href="https://templatemo.com" rel="nofollow" target="_blank">TemplateMo</a>
        </div>
      </div>
    </footer>
    
    <script src="templatemo-prism-scripts.js"></script>
    
    <!-- ============================================
    SCRIPTS JAVASCRIPT POUR VALIDATION ET THÈME
    ============================================ -->
<script>
function setupThemeSwitcher() {
    const themeToggle = document.getElementById('theme-toggle');
    const body = document.body;
    
    if (!themeToggle) {
        console.error('Theme toggle not found');
        return;
    }
    
    console.log('Setting up theme switcher...');
    
    // Fonction pour obtenir le thème actuel
    function getCurrentTheme() {
        // Priorité: localStorage > cookie > attribut data-theme > défaut
        let theme = localStorage.getItem('theme');
        
        if (!theme) {
            // Essayer de lire le cookie
            const cookieMatch = document.cookie.match(/theme=([^;]+)/);
            theme = cookieMatch ? cookieMatch[1] : null;
        }
        
        if (!theme) {
            // Lire l'attribut data-theme
            theme = body.getAttribute('data-theme');
        }
        
        return theme || 'light';
    }
    
    // Fonction pour appliquer le thème
    function applyTheme(theme) {
        console.log('Applying theme:', theme);
        body.setAttribute('data-theme', theme);
        themeToggle.checked = (theme === 'dark');
        
        // Sauvegarder dans localStorage
        localStorage.setItem('theme', theme);
        
        // Sauvegarder dans cookie (expire dans 1 an)
        const expirationDate = new Date();
        expirationDate.setFullYear(expirationDate.getFullYear() + 1);
        document.cookie = `theme=${theme}; path=/; expires=${expirationDate.toUTCString()}`;
        
        console.log('Theme applied and saved:', theme);
    }
    
    // Charger le thème au démarrage
    const savedTheme = getCurrentTheme();
    console.log('Initial theme:', savedTheme);
    applyTheme(savedTheme);
    
    // Écouter les changements
    themeToggle.addEventListener('change', function() {
        const newTheme = this.checked ? 'dark' : 'light';
        console.log('Theme changed to:', newTheme);
        applyTheme(newTheme);
        
        // Animation de transition
        body.style.transition = 'background-color 0.3s ease, color 0.3s ease';
        
        // Afficher une notification
        showNotification(`Thème ${newTheme === 'dark' ? 'sombre' : 'clair'} activé`, 'success');
    });
    
    console.log('Theme switcher ready');
}

// Fonction pour tester le thème
function testTheme() {
    const body = document.body;
    const currentTheme = body.getAttribute('data-theme');
    console.log('Current theme:', currentTheme);
    console.log('localStorage theme:', localStorage.getItem('theme'));
    console.log('Cookie theme:', document.cookie.match(/theme=([^;]+)/)?.[1]);
    
    const toggle = document.getElementById('theme-toggle');
    console.log('Toggle checked:', toggle?.checked);
}

// Appeler testTheme() dans la console pour déboguer
</script>

// Fonction pour tester le thème
function testTheme() {
    const body = document.body;
    const currentTheme = body.getAttribute('data-theme');
    console.log('Current theme:', currentTheme);
    console.log('localStorage theme:', localStorage.getItem('theme'));
    console.log('Cookie theme:', document.cookie.match(/theme=([^;]+)/)?.[1]);
    
    const toggle = document.getElementById('theme-toggle');
    console.log('Toggle checked:', toggle?.checked);
}
        
        // Barre de progression
        setupReadingProgress();
    });
    
    // ============================================
    // VALIDATION DES FORMULAIRES
    // ============================================
    function validateAllInteractionForms() {
        const likeForm = document.getElementById('likeForm');
        const commentForm = document.getElementById('commentForm');
        
        if (likeForm) setupFormValidation(likeForm);
        if (commentForm) setupFormValidation(commentForm);