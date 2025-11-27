<?php
/**
 * Dashboard Admin - لوحة التحكم الرئيسية للمستثمرين
 */

require_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Récupérer les statistiques
function getStats($db) {
    $stats = array();
    
    // Total utilisateurs
    $stmt = $db->query("SELECT COUNT(*) as total FROM utilisateurs");
    $stats['total_users'] = $stmt->fetch()['total'];
    
    // Total événements
    $stmt = $db->query("SELECT COUNT(*) as total FROM evenements");
    $stats['total_events'] = $stmt->fetch()['total'];
    
    // Événements par type
    $stmt = $db->query("SELECT type, COUNT(*) as count FROM evenements GROUP BY type");
    $stats['events_by_type'] = $stmt->fetchAll();
    
    // Total participations
    $stmt = $db->query("SELECT COUNT(*) as total FROM participations");
    $stats['total_participations'] = $stmt->fetch()['total'];
    
    // Total quiz passés
    $stmt = $db->query("SELECT COUNT(*) as total FROM resultats_quiz");
    $stats['total_quiz_results'] = $stmt->fetch()['total'];
    
    // Score moyen des quiz
    $stmt = $db->query("SELECT AVG(pourcentage) as avg FROM resultats_quiz");
    $avg = $stmt->fetch()['avg'];
    $stats['avg_quiz_score'] = $avg ? round($avg, 1) : 0;
    
    // Participations par statut
    $stmt = $db->query("SELECT statut, COUNT(*) as count FROM participations GROUP BY statut");
    $stats['participations_by_status'] = $stmt->fetchAll();
    
    // Taux de conversion (approuvés / total)
    $stmt = $db->query("SELECT COUNT(*) as total FROM participations WHERE statut = 'approuve'");
    $approved = $stmt->fetch()['total'];
    $stats['conversion_rate'] = $stats['total_participations'] > 0 ? round(($approved / $stats['total_participations']) * 100, 1) : 0;
    
    // Participations cette semaine
    $stmt = $db->query("SELECT COUNT(*) as total FROM participations WHERE date_participation >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
    $stats['weekly_participations'] = $stmt->fetch()['total'];
    
    // Croissance (comparaison avec semaine précédente)
    $stmt = $db->query("SELECT COUNT(*) as total FROM participations WHERE date_participation >= DATE_SUB(NOW(), INTERVAL 14 DAY) AND date_participation < DATE_SUB(NOW(), INTERVAL 7 DAY)");
    $lastWeek = $stmt->fetch()['total'];
    $stats['growth_rate'] = $lastWeek > 0 ? round((($stats['weekly_participations'] - $lastWeek) / $lastWeek) * 100, 1) : 0;
    
    // Dernières participations avec jointure
    $stmt = $db->query("
        SELECT u.nom, u.prenom, u.email, e.titre, p.date_participation, p.statut
        FROM participations p
        INNER JOIN utilisateurs u ON p.utilisateur_id = u.id
        INNER JOIN evenements e ON p.evenement_id = e.id
        ORDER BY p.date_participation DESC
        LIMIT 5
    ");
    $stats['recent_participations'] = $stmt->fetchAll();
    
    // Derniers résultats quiz avec jointure
    $stmt = $db->query("
        SELECT u.nom, u.prenom, e.titre, rq.score, rq.total_questions, rq.pourcentage, rq.date_passage
        FROM resultats_quiz rq
        INNER JOIN utilisateurs u ON rq.utilisateur_id = u.id
        INNER JOIN evenements e ON rq.evenement_id = e.id
        ORDER BY rq.date_passage DESC
        LIMIT 5
    ");
    $stats['recent_quiz_results'] = $stmt->fetchAll();
    
    // Top événements
    $stmt = $db->query("
        SELECT e.titre, e.type, 
               (SELECT COUNT(*) FROM participations WHERE evenement_id = e.id) as participations,
               (SELECT COUNT(*) FROM resultats_quiz WHERE evenement_id = e.id) as quiz_results
        FROM evenements e
        ORDER BY (participations + quiz_results) DESC
        LIMIT 5
    ");
    $stats['top_events'] = $stmt->fetchAll();
    
    // Participations par jour (7 derniers jours)
    $stmt = $db->query("
        SELECT DATE(date_participation) as date, COUNT(*) as count 
        FROM participations 
        WHERE date_participation >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        GROUP BY DATE(date_participation)
        ORDER BY date
    ");
    $stats['daily_participations'] = $stmt->fetchAll();
    
    return $stats;
}

$stats = getStats($db);

// Préparer les données pour les graphiques
$eventTypes = [];
$eventCounts = [];
foreach($stats['events_by_type'] as $row) {
    $eventTypes[] = $row['type'] === 'quiz' ? 'Quiz' : 'Normal';
    $eventCounts[] = $row['count'];
}

$statusLabels = [];
$statusCounts = [];
foreach($stats['participations_by_status'] as $row) {
    $statusLabels[] = ucfirst(str_replace('_', ' ', $row['statut']));
    $statusCounts[] = $row['count'];
}

// Données pour graphique linéaire
$dailyLabels = [];
$dailyCounts = [];
foreach($stats['daily_participations'] as $row) {
    $dailyLabels[] = date('d/m', strtotime($row['date']));
    $dailyCounts[] = $row['count'];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Human Nova AI</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --carbon-dark: #0a0a0a;
            --carbon-medium: #141414;
            --carbon-light: #1e1e1e;
            --metal-dark: #2a2a2a;
            --metal-light: #3a3a3a;
            --accent-cyan: #00ffff;
            --accent-purple: #9945ff;
            --accent-green: #00ff88;
            --accent-orange: #ff9500;
            --accent-red: #ff3333;
            --accent-blue: #00a8ff;
            --text-primary: #ffffff;
            --text-secondary: #b0b0b0;
            --text-dim: #606060;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: var(--carbon-dark);
            color: var(--text-primary);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }
        
        /* Header with Left-aligned Navigation */
        .header {
            background: linear-gradient(180deg, var(--carbon-medium) 0%, var(--carbon-dark) 100%);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            padding: 15px 30px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }
        
        .nav-container {
            display: flex;
            align-items: center;
            gap: 40px;
            max-width: 1600px;
            margin: 0 auto;
        }
        
        .logo {
            text-decoration: none;
        }
        
        .logo-text {
            font-size: 20px;
            font-weight: 900;
        }
        
        .logo-text .prism {
            color: var(--accent-cyan);
        }
        
        .logo-text .flux {
            color: var(--accent-purple);
        }
        
        .nav-menu {
            display: flex;
            list-style: none;
            gap: 5px;
        }
        
        .nav-link {
            color: var(--text-secondary);
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 600;
            font-size: 14px;
        }
        
        .nav-link:hover {
            color: var(--accent-cyan);
            background: rgba(0,255,255,0.1);
        }
        
        .nav-link.active {
            color: #000;
            background: var(--accent-cyan);
        }
        
        .dashboard-container {
            padding: 100px 30px 50px;
            max-width: 1600px;
            margin: 0 auto;
        }
        
        .section-header {
            margin-bottom: 40px;
            animation: fadeInUp 0.6s ease;
        }
        
        .section-title {
            font-size: 32px;
            font-weight: 900;
            background: linear-gradient(135deg, var(--accent-cyan), var(--accent-purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .section-subtitle {
            color: var(--text-secondary);
            margin-top: 10px;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--carbon-medium), var(--carbon-dark));
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 16px;
            padding: 25px;
            text-align: center;
            transition: all 0.4s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.6s ease backwards;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            transition: left 0.5s ease;
        }
        
        .stat-card:hover::before {
            left: 100%;
        }

        .stat-card:hover {
            border-color: var(--accent-cyan);
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(0, 255, 255, 0.2);
        }
        
        .stat-card:nth-child(1) { animation-delay: 0.1s; }
        .stat-card:nth-child(2) { animation-delay: 0.2s; }
        .stat-card:nth-child(3) { animation-delay: 0.3s; }
        .stat-card:nth-child(4) { animation-delay: 0.4s; }
        .stat-card:nth-child(5) { animation-delay: 0.5s; }
        .stat-card:nth-child(6) { animation-delay: 0.6s; }

        .stat-icon {
            font-size: 40px;
            margin-bottom: 15px;
            animation: bounce 2s infinite;
        }

        .stat-value {
            font-size: 36px;
            font-weight: 900;
            color: var(--accent-cyan);
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 12px;
            margin-top: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .stat-change {
            font-size: 12px;
            margin-top: 8px;
            padding: 4px 10px;
            border-radius: 12px;
            display: inline-block;
        }
        
        .stat-change.positive {
            background: rgba(0,255,136,0.2);
            color: var(--accent-green);
        }
        
        .stat-change.negative {
            background: rgba(255,51,51,0.2);
            color: var(--accent-red);
        }

        /* Charts Grid */
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .chart-card {
            background: var(--carbon-medium);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 16px;
            padding: 25px;
            animation: fadeInUp 0.6s ease backwards;
            animation-delay: 0.3s;
            transition: all 0.3s ease;
        }
        
        .chart-card:hover {
            border-color: rgba(0,255,255,0.3);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }

        .chart-title {
            color: var(--accent-cyan);
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .chart-container {
            height: 250px;
        }

        /* Tables Grid */
        .tables-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(450px, 1fr));
            gap: 30px;
        }

        .table-card {
            background: var(--carbon-medium);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 16px;
            padding: 25px;
            overflow: hidden;
            animation: fadeInUp 0.6s ease backwards;
            animation-delay: 0.5s;
        }

        .table-title {
            color: var(--accent-cyan);
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background: var(--carbon-dark);
            color: var(--accent-cyan);
            padding: 12px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        td {
            padding: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            color: var(--text-secondary);
        }
        
        tr:hover td {
            background: rgba(0,255,255,0.02);
        }

        .score-badge, .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 700;
        }

        .score-high { background: rgba(0, 255, 136, 0.2); color: var(--accent-green); }
        .score-medium { background: rgba(255, 149, 0, 0.2); color: var(--accent-orange); }
        .score-low { background: rgba(255, 51, 51, 0.2); color: var(--accent-red); }
        
        .status-badge.en_attente { background: rgba(255,149,0,0.2); color: var(--accent-orange); }
        .status-badge.approuve { background: rgba(0,255,136,0.2); color: var(--accent-green); }
        .status-badge.rejete { background: rgba(255,51,51,0.2); color: var(--accent-red); }
        
        /* Investor Metrics */
        .investor-section {
            margin-top: 40px;
            animation: fadeInUp 0.6s ease backwards;
            animation-delay: 0.7s;
        }
        
        .investor-title {
            color: var(--accent-purple);
            font-size: 22px;
            font-weight: 700;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .metrics-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        .metric-card {
            background: linear-gradient(135deg, rgba(153,69,255,0.1), var(--carbon-dark));
            border: 1px solid rgba(153,69,255,0.3);
            border-radius: 16px;
            padding: 25px;
            transition: all 0.3s ease;
        }
        
        .metric-card:hover {
            border-color: var(--accent-purple);
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(153,69,255,0.2);
        }
        
        .metric-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .metric-icon { font-size: 30px; }
        
        .metric-trend {
            font-size: 12px;
            padding: 4px 10px;
            border-radius: 12px;
        }
        
        .metric-trend.up { background: rgba(0,255,136,0.2); color: var(--accent-green); }
        .metric-trend.down { background: rgba(255,51,51,0.2); color: var(--accent-red); }
        
        .metric-value {
            font-size: 32px;
            font-weight: 900;
            color: var(--accent-purple);
        }
        
        .metric-label {
            color: var(--text-secondary);
            font-size: 13px;
            margin-top: 5px;
        }
        
        .metric-bar {
            height: 6px;
            background: var(--carbon-dark);
            border-radius: 3px;
            margin-top: 15px;
            overflow: hidden;
        }
        
        .metric-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--accent-purple), var(--accent-cyan));
            border-radius: 3px;
            transition: width 1s ease;
        }

        /* Animations */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        @media (max-width: 768px) {
            .charts-grid, .tables-grid { grid-template-columns: 1fr; }
            .nav-menu { display: none; }
        }
    </style>
</head>
<body>
    <header class="header">
        <nav class="nav-container">
            <a href="../../index.php" class="logo">
                <span class="logo-text">
                    <span class="prism">HUMAN</span>
                    <span class="flux">NOVA AI</span>
                </span>
            </a>
            <ul class="nav-menu">
                <li><a href="dashboard.php" class="nav-link active">📊 Dashboard</a></li>
                <li><a href="manage-events.php" class="nav-link">📅 Événements</a></li>
                <li><a href="manage-participations.php" class="nav-link">👥 Participations</a></li>
                <li><a href="../front/events.php" class="nav-link">🌐 Front Office</a></li>
            </ul>
        </nav>
    </header>

    <main class="dashboard-container">
        <div class="section-header">
            <h2 class="section-title">📊 Dashboard Investisseur</h2>
            <p class="section-subtitle">Vue d'ensemble complète de votre plateforme</p>
        </div>

        <!-- Statistiques principales cliquables -->
        <div class="stats-grid">
            <div class="stat-card" onclick="window.location.href='#'" title="Voir tous les utilisateurs">
                <div class="stat-icon">👥</div>
                <div class="stat-value"><?php echo $stats['total_users']; ?></div>
                <div class="stat-label">Utilisateurs</div>
            </div>
            <div class="stat-card" onclick="window.location.href='manage-events.php'" title="Gérer les événements">
                <div class="stat-icon">📅</div>
                <div class="stat-value"><?php echo $stats['total_events']; ?></div>
                <div class="stat-label">Événements</div>
            </div>
            <div class="stat-card" onclick="window.location.href='manage-participations.php'" title="Gérer les participations">
                <div class="stat-icon">✍️</div>
                <div class="stat-value"><?php echo $stats['total_participations']; ?></div>
                <div class="stat-label">Participations</div>
                <div class="stat-change <?php echo $stats['growth_rate'] >= 0 ? 'positive' : 'negative'; ?>">
                    <?php echo $stats['growth_rate'] >= 0 ? '↑' : '↓'; ?> <?php echo abs($stats['growth_rate']); ?>% cette semaine
                </div>
            </div>
            <div class="stat-card" onclick="window.location.href='manage-events.php?type=quiz'" title="Voir les quiz">
                <div class="stat-icon">🎯</div>
                <div class="stat-value"><?php echo $stats['total_quiz_results']; ?></div>
                <div class="stat-label">Quiz complétés</div>
            </div>
            <div class="stat-card" title="Score moyen des participants">
                <div class="stat-icon">📈</div>
                <div class="stat-value"><?php echo $stats['avg_quiz_score']; ?>%</div>
                <div class="stat-label">Score moyen</div>
            </div>
            <div class="stat-card" title="Taux de conversion">
                <div class="stat-icon">💹</div>
                <div class="stat-value" style="color: var(--accent-green);"><?php echo $stats['conversion_rate']; ?>%</div>
                <div class="stat-label">Taux d'approbation</div>
            </div>
        </div>
        
        <!-- Métriques Investisseur -->
        <div class="investor-section">
            <h3 class="investor-title">💼 Métriques Investisseur</h3>
            <div class="metrics-grid">
                <div class="metric-card">
                    <div class="metric-header">
                        <span class="metric-icon">📊</span>
                        <span class="metric-trend <?php echo $stats['growth_rate'] >= 0 ? 'up' : 'down'; ?>">
                            <?php echo $stats['growth_rate'] >= 0 ? '↑' : '↓'; ?> <?php echo abs($stats['growth_rate']); ?>%
                        </span>
                    </div>
                    <div class="metric-value"><?php echo $stats['weekly_participations']; ?></div>
                    <div class="metric-label">Participations cette semaine</div>
                    <div class="metric-bar">
                        <div class="metric-bar-fill" style="width: <?php echo min($stats['weekly_participations'] * 10, 100); ?>%;"></div>
                    </div>
                </div>
                
                <div class="metric-card">
                    <div class="metric-header">
                        <span class="metric-icon">✅</span>
                        <span class="metric-trend up">Active</span>
                    </div>
                    <div class="metric-value"><?php echo $stats['conversion_rate']; ?>%</div>
                    <div class="metric-label">Taux de conversion</div>
                    <div class="metric-bar">
                        <div class="metric-bar-fill" style="width: <?php echo $stats['conversion_rate']; ?>%;"></div>
                    </div>
                </div>
                
                <div class="metric-card">
                    <div class="metric-header">
                        <span class="metric-icon">🎯</span>
                        <span class="metric-trend up">Performance</span>
                    </div>
                    <div class="metric-value"><?php echo $stats['avg_quiz_score']; ?>%</div>
                    <div class="metric-label">Performance moyenne Quiz</div>
                    <div class="metric-bar">
                        <div class="metric-bar-fill" style="width: <?php echo $stats['avg_quiz_score']; ?>%;"></div>
                    </div>
                </div>
                
                <div class="metric-card">
                    <div class="metric-header">
                        <span class="metric-icon">👥</span>
                        <span class="metric-trend up">Croissance</span>
                    </div>
                    <div class="metric-value"><?php echo $stats['total_users']; ?></div>
                    <div class="metric-label">Base d'utilisateurs</div>
                    <div class="metric-bar">
                        <div class="metric-bar-fill" style="width: <?php echo min($stats['total_users'] * 5, 100); ?>%;"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphiques -->
        <div class="charts-grid" style="margin-top: 40px;">
            <div class="chart-card">
                <h3 class="chart-title">📊 Types d'événements</h3>
                <div class="chart-container">
                    <canvas id="eventsChart"></canvas>
                </div>
            </div>
            <div class="chart-card">
                <h3 class="chart-title">📈 Statuts des participations</h3>
                <div class="chart-container">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
            <div class="chart-card">
                <h3 class="chart-title">📉 Participations (7 derniers jours)</h3>
                <div class="chart-container">
                    <canvas id="dailyChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Tableaux récents -->
        <div class="tables-grid">
            <div class="table-card">
                <h3 class="table-title">🕐 Dernières Participations</h3>
                <table>
                    <thead>
                        <tr><th>Utilisateur</th><th>Événement</th><th>Date</th><th>Statut</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach($stats['recent_participations'] as $p): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($p['nom'] . ' ' . $p['prenom']); ?></td>
                            <td><?php echo htmlspecialchars(substr($p['titre'], 0, 25)); ?>...</td>
                            <td><?php echo date('d/m H:i', strtotime($p['date_participation'])); ?></td>
                            <td><span class="status-badge <?php echo $p['statut']; ?>"><?php echo ucfirst(str_replace('_', ' ', $p['statut'])); ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($stats['recent_participations'])): ?>
                        <tr><td colspan="4" style="text-align:center; color: var(--text-secondary);">Aucune participation récente</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="table-card">
                <h3 class="table-title">🎯 Derniers Résultats Quiz</h3>
                <table>
                    <thead><tr><th>Utilisateur</th><th>Quiz</th><th>Score</th><th>Date</th></tr></thead>
                    <tbody>
                        <?php foreach($stats['recent_quiz_results'] as $r): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($r['nom'] . ' ' . $r['prenom']); ?></td>
                            <td><?php echo htmlspecialchars(substr($r['titre'], 0, 20)); ?>...</td>
                            <td>
                                <?php $scoreClass = $r['pourcentage'] >= 70 ? 'score-high' : ($r['pourcentage'] >= 40 ? 'score-medium' : 'score-low'); ?>
                                <span class="score-badge <?php echo $scoreClass; ?>">
                                    <?php echo $r['score']; ?>/<?php echo $r['total_questions']; ?> (<?php echo round($r['pourcentage']); ?>%)
                                </span>
                            </td>
                            <td><?php echo date('d/m H:i', strtotime($r['date_passage'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($stats['recent_quiz_results'])): ?>
                        <tr><td colspan="4" style="text-align:center; color: var(--text-secondary);">Aucun résultat récent</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="table-card">
                <h3 class="table-title">🏆 Top Événements</h3>
                <table>
                    <thead><tr><th>Événement</th><th>Type</th><th>Participations</th><th>Quiz</th></tr></thead>
                    <tbody>
                        <?php foreach($stats['top_events'] as $e): ?>
                        <tr>
                            <td><?php echo htmlspecialchars(substr($e['titre'], 0, 25)); ?>...</td>
                            <td><span style="color: <?php echo $e['type'] === 'quiz' ? 'var(--accent-purple)' : 'var(--accent-cyan)'; ?>"><?php echo $e['type'] === 'quiz' ? '🎯 Quiz' : '📅 Normal'; ?></span></td>
                            <td><?php echo $e['participations']; ?></td>
                            <td><?php echo $e['quiz_results']; ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($stats['top_events'])): ?>
                        <tr><td colspan="4" style="text-align:center; color: var(--text-secondary);">Aucun événement</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <script>
        const colors = {cyan:'#00ffff',purple:'#9945ff',green:'#00ff88',orange:'#ff9500',red:'#ff3333',blue:'#00a8ff'};

        new Chart(document.getElementById('eventsChart').getContext('2d'), {
            type: 'doughnut',
            data: {labels: <?php echo json_encode($eventTypes); ?>, datasets: [{data: <?php echo json_encode($eventCounts); ?>, backgroundColor: [colors.purple, colors.cyan], borderWidth: 0}]},
            options: {responsive: true, maintainAspectRatio: false, plugins: {legend: {position: 'bottom', labels: {color: '#b0b0b0', padding: 20}}}, animation: {animateRotate: true, duration: 1500}}
        });

        new Chart(document.getElementById('statusChart').getContext('2d'), {
            type: 'bar',
            data: {labels: <?php echo json_encode($statusLabels); ?>, datasets: [{label: 'Participations', data: <?php echo json_encode($statusCounts); ?>, backgroundColor: [colors.orange, colors.green, colors.red], borderRadius: 8}]},
            options: {responsive: true, maintainAspectRatio: false, plugins: {legend: {display: false}}, scales: {x: {grid: {display: false}, ticks: {color: '#808080'}}, y: {grid: {color: 'rgba(255,255,255,0.05)'}, ticks: {color: '#808080'}, beginAtZero: true}}, animation: {duration: 1500}}
        });
        
        new Chart(document.getElementById('dailyChart').getContext('2d'), {
            type: 'line',
            data: {labels: <?php echo json_encode($dailyLabels); ?>, datasets: [{label: 'Participations', data: <?php echo json_encode($dailyCounts); ?>, borderColor: colors.cyan, backgroundColor: 'rgba(0, 255, 255, 0.1)', fill: true, tension: 0.4, pointBackgroundColor: colors.cyan, pointBorderColor: '#fff', pointRadius: 5}]},
            options: {responsive: true, maintainAspectRatio: false, plugins: {legend: {display: false}}, scales: {x: {grid: {display: false}, ticks: {color: '#808080'}}, y: {grid: {color: 'rgba(255,255,255,0.05)'}, ticks: {color: '#808080'}, beginAtZero: true}}, animation: {duration: 2000}}
        });
        
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.metric-bar-fill').forEach(bar => {
                const width = bar.style.width;
                bar.style.width = '0%';
                setTimeout(() => bar.style.width = width, 500);
            });
        });
    </script>
</body>
</html>
