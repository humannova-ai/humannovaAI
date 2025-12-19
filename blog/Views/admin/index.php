<?php
// Démarrer la session si elle n'est pas déjà démarrée
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vérification de sécurité (au cas où quelqu'un accède directement à la vue)
if (!isset($_SESSION['admin'])) {
    header('Location: index.php?controller=admin&action=login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - PRO MANAGE AI</title>
    <style>
        :root {
            --bg-primary: #0a0a0f;
            --bg-secondary: #12121a;
            --bg-tertiary: #1a1a25;
            --bg-card: #15151f;
            --bg-hover: #1f1f2e;
            --accent-cyan: #00d4ff;
            --accent-purple: #a855f7;
            --accent-green: #22c55e;
            --accent-orange: #f59e0b;
            --accent-red: #ef4444;
            --accent-blue: #3b82f6;
            --text-primary: #ffffff;
            --text-secondary: #94a3b8;
            --text-dim: #64748b;
            --border-color: rgba(255,255,255,0.08);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            background: var(--bg-primary);
            color: var(--text-primary);
            font-family: 'Segoe UI', system-ui, sans-serif;
            min-height: 100vh;
            display: flex;
        }

        /* Animated Background */
        .animated-bg {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            pointer-events: none;
            z-index: 0;
        }

        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.3;
            animation: orbFloat 20s ease-in-out infinite;
        }

        .orb-1 {
            width: 500px; height: 500px;
            background: linear-gradient(135deg, var(--accent-cyan), var(--accent-blue));
            top: -150px; left: -100px;
        }

        .orb-2 {
            width: 400px; height: 400px;
            background: linear-gradient(135deg, var(--accent-purple), #ec4899);
            bottom: -100px; right: -100px;
            animation-delay: -5s;
        }

        @keyframes orbFloat {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(50px, 50px) scale(1.1); }
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: var(--bg-secondary);
            border-right: 1px solid var(--border-color);
            position: fixed;
            height: 100vh;
            z-index: 100;
            display: flex;
            flex-direction: column;
        }

        .sidebar-header {
            padding: 30px 25px;
            border-bottom: 1px solid var(--border-color);
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }

        .logo-icon {
            width: 48px; height: 48px;
            background: linear-gradient(135deg, var(--accent-cyan), var(--accent-purple));
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
            font-weight: 900;
            color: #000;
        }

        .logo-text {
            font-size: 18px;
            font-weight: 800;
            line-height: 1.2;
        }

        .logo-text span:first-child { color: var(--accent-cyan); display: block; }
        .logo-text span:last-child { color: var(--accent-purple); display: block; }

        .sidebar-nav {
            flex: 1;
            padding: 25px 15px;
        }

        .nav-section { margin-bottom: 25px; }

        .nav-section-title {
            color: var(--text-dim);
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            padding: 0 15px;
            margin-bottom: 15px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 18px;
            color: var(--text-secondary);
            text-decoration: none;
            border-radius: 12px;
            transition: all 0.3s;
            margin-bottom: 6px;
            position: relative;
        }

        .nav-link::before {
            content: '';
            position: absolute;
            left: 0; top: 50%;
            transform: translateY(-50%);
            width: 4px; height: 0;
            background: linear-gradient(180deg, var(--accent-cyan), var(--accent-purple));
            border-radius: 0 4px 4px 0;
            transition: height 0.3s;
        }

        .nav-link:hover { background: var(--bg-hover); color: var(--text-primary); }
        .nav-link:hover::before { height: 60%; }
        .nav-link.active { background: linear-gradient(135deg, rgba(0,212,255,0.15), rgba(168,85,247,0.1)); color: var(--text-primary); }
        .nav-link.active::before { height: 70%; }

        .nav-icon {
            width: 42px; height: 42px;
            background: var(--bg-tertiary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .nav-link:hover .nav-icon, .nav-link.active .nav-icon {
            background: linear-gradient(135deg, rgba(0,212,255,0.2), rgba(168,85,247,0.2));
        }

        .nav-label { font-size: 14px; font-weight: 600; }

        .sidebar-footer {
            padding: 20px 25px;
            border-top: 1px solid var(--border-color);
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background: var(--bg-tertiary);
            border-radius: 12px;
            margin-bottom: 15px;
        }

        .user-avatar {
            width: 40px; height: 40px;
            background: linear-gradient(135deg, var(--accent-purple), var(--accent-blue));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 14px;
        }

        .user-details { flex: 1; }
        .user-name { font-size: 14px; font-weight: 600; margin-bottom: 2px; }
        .user-role { font-size: 11px; color: var(--text-dim); }

        .logout-btn {
            width: 100%;
            padding: 12px;
            background: rgba(239,68,68,0.15);
            color: var(--accent-red);
            border: 1px solid var(--accent-red);
            border-radius: 10px;
            font-weight: 700;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
            text-decoration: none;
            display: block;
        }

        .logout-btn:hover {
            background: var(--accent-red);
            color: #fff;
            transform: translateY(-2px);
        }

        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 30px;
            position: relative;
            z-index: 10;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 35px;
            animation: fadeIn 0.6s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .header-content h1 {
            font-size: 36px;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .header-date {
            color: var(--text-dim);
            font-size: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .header-date::before {
            content: '';
            width: 8px; height: 8px;
            background: var(--accent-green);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.5; transform: scale(1.2); }
        }

        /* Dashboard Cards */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
            margin-top: 30px;
        }

        .dashboard-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 20px;
            padding: 40px;
            transition: all 0.4s;
            animation: fadeUp 0.6s ease backwards;
            position: relative;
            overflow: hidden;
        }

        .dashboard-card:nth-child(1) { animation-delay: 0.1s; }
        .dashboard-card:nth-child(2) { animation-delay: 0.2s; }

        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .dashboard-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 4px;
            background: linear-gradient(90deg, var(--accent-cyan), var(--accent-purple));
        }

        .dashboard-card:hover {
            transform: translateY(-8px);
            border-color: rgba(0,212,255,0.3);
            box-shadow: 0 20px 50px rgba(0,0,0,0.4);
        }

        .card-icon {
            width: 80px; height: 80px;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
            margin-bottom: 25px;
        }

        .card-icon.articles { background: linear-gradient(135deg, rgba(168,85,247,0.2), rgba(168,85,247,0.1)); }
        .card-icon.interactions { background: linear-gradient(135deg, rgba(59,130,246,0.2), rgba(59,130,246,0.1)); }

        .dashboard-card h3 {
            font-size: 24px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .dashboard-card p {
            color: var(--text-secondary);
            line-height: 1.6;
            margin-bottom: 30px;
            font-size: 15px;
        }

        .card-link {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 28px;
            background: linear-gradient(135deg, var(--accent-cyan), var(--accent-blue));
            color: #000;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 700;
            font-size: 14px;
            transition: all 0.3s;
            box-shadow: 0 8px 25px rgba(0,212,255,0.3);
        }

        .card-link:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(0,212,255,0.4);
        }

        /* Back to site */
        .back-to-site {
            margin-top: 50px;
            text-align: center;
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 14px 28px;
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            color: var(--accent-cyan);
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            transition: all 0.3s;
        }

        .back-link:hover {
            background: var(--bg-hover);
            border-color: var(--accent-cyan);
            transform: translateX(-5px);
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .dashboard-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 768px) {
            .sidebar { display: none; }
            .main-content { margin-left: 0; padding: 20px; }
            .dashboard-header { flex-direction: column; gap: 20px; }
            .header-content h1 { font-size: 28px; }
        }
    </style>
</head>
<body>
    <div class="animated-bg">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
    </div>

    <aside class="sidebar">
        <div class="sidebar-header">
            <a href="index.php" class="logo">
                <div class="logo-icon">P</div>
                <div class="logo-text">
                    <span>PRO</span>
                    <span>MANAGE AI</span>
                </div>
            </a>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section">
                <div class="nav-section-title">Menu</div>
                <a href="index.php" class="nav-link active">
                    <div class="nav-icon">📊</div>
                    <span class="nav-label">Dashboard</span>
                </a>
                <a href="index.php?controller=article&action=index" class="nav-link">
                    <div class="nav-icon">📝</div>
                    <span class="nav-label">Articles</span>
                </a>
                <a href="index.php?controller=interaction&action=index" class="nav-link">
                    <div class="nav-icon">💬</div>
                    <span class="nav-label">Interactions</span>
                </a>
            </div>

            <div class="nav-section">
                <div class="nav-section-title">Système</div>
                <a href="index.php" class="nav-link">
                    <div class="nav-icon">🌐</div>
                    <span class="nav-label">Site Principal</span>
                </a>
            </div>
        </nav>

        <div class="sidebar-footer">
            <div class="user-info">
                <div class="user-avatar"><?= strtoupper(substr($_SESSION['admin'], 0, 2)) ?></div>
                <div class="user-details">
                    <div class="user-name"><?= htmlspecialchars($_SESSION['admin']) ?></div>
                    <div class="user-role">Administrateur</div>
                </div>
            </div>
            <a href="index.php?controller=admin&action=logout" class="logout-btn">🚪 Déconnexion</a>
        </div>
    </aside>

    <main class="main-content">
        <div class="dashboard-header">
            <div class="header-content">
                <h1>Dashboard Admin</h1>
                <div class="header-date"><?php echo date('l, d F Y'); ?></div>
            </div>
        </div>

        <div class="dashboard-grid">
            <!-- Card Articles -->
            <div class="dashboard-card">
                <div class="card-icon articles">📝</div>
                <h3>Manage Articles</h3>
                <p>Create, edit, and delete blog articles. Manage your content effectively with our powerful content management system.</p>
                <a href="index.php?controller=article&action=index" class="card-link">
                    <span>Go to Articles</span>
                    <span>→</span>
                </a>
            </div>

            <!-- Card Interactions -->
            <div class="dashboard-card">
                <div class="card-icon interactions">💬</div>
                <h3>Manage Interactions</h3>
                <p>View and moderate comments, likes, and user interactions on your articles. Keep your community engaged and safe.</p>
                <a href="index.php?controller=interaction&action=index" class="card-link">
                    <span>Go to Interactions</span>
                    <span>→</span>
                </a>
            </div>
        </div>

        <div class="back-to-site">
            <a href="index.php" class="back-link">
                <span>←</span>
                <span>Back to Main Site</span>
            </a>
        </div>
    </main>
</body>
</html>