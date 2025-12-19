<?php
// debug_path.php - Placez ce fichier à la RACINE de votre blog
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>
<html>
<head>
    <title>Debug Path Info</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f6fa; }
        .info-box { 
            background: white; 
            padding: 20px; 
            margin: 15px 0; 
            border-radius: 10px;
            border-left: 5px solid #3498db;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        code { 
            background: #2d3436; 
            color: #dfe6e9; 
            padding: 10px; 
            border-radius: 5px;
            display: block;
            margin: 10px 0;
            word-break: break-all;
        }
        h3 { color: #2d3436; margin-top: 0; }
        .highlight { color: #e74c3c; font-weight: bold; }
        .success { color: #27ae60; }
        .warning { color: #f39c12; }
    </style>
</head>
<body>
    <h1>🔧 DEBUG PATH INFORMATION</h1>
    <p>Ce fichier vous montre la structure de votre installation.</p>";

// ===== 1. INFORMATIONS DU SERVEUR =====
echo "<div class='info-box'>
    <h3>📡 1. Informations du serveur :</h3>";

$server_info = [
    'SCRIPT_NAME' => $_SERVER['SCRIPT_NAME'] ?? 'N/A',
    'PHP_SELF' => $_SERVER['PHP_SELF'] ?? 'N/A',
    'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? 'N/A',
    'DOCUMENT_ROOT' => $_SERVER['DOCUMENT_ROOT'] ?? 'N/A',
    'HTTP_HOST' => $_SERVER['HTTP_HOST'] ?? 'N/A',
    'HTTPS' => $_SERVER['HTTPS'] ?? 'non',
    'SERVER_PORT' => $_SERVER['SERVER_PORT'] ?? 'N/A',
    'SERVER_NAME' => $_SERVER['SERVER_NAME'] ?? 'N/A',
    'SCRIPT_FILENAME' => $_SERVER['SCRIPT_FILENAME'] ?? 'N/A',
    'PATH_INFO' => $_SERVER['PATH_INFO'] ?? 'N/A'
];

foreach ($server_info as $key => $value) {
    echo "<strong>$key:</strong> <span class='highlight'>$value</span><br>";
}

echo "</div>";

// ===== 2. CHEMIN POUR VOTRE BLOG =====
echo "<div class='info-box'>
    <h3>📁 2. Chemin de votre blog :</h3>";

$script_name = $_SERVER['SCRIPT_NAME']; // ex: /blog/debug_path.php
$path_info = dirname($script_name);     // ex: /blog

echo "Script actuel: <code>$script_name</code><br>";
echo "Dossier parent: <code>$path_info</code><br><br>";

// Recommandation pour base_path
if ($path_info === '/blog' || $path_info === '\\blog') {
    echo "<span class='success'>✅ RECOMMANDATION: Utilisez \$base_path = '/blog'</span>";
} elseif ($path_info === '/' || $path_info === '\\' || $path_info === '.') {
    echo "<span class='success'>✅ RECOMMANDATION: Utilisez \$base_path = '/'</span>";
} else {
    echo "<span class='warning'>⚠️ Chemin inhabituel: $path_info</span>";
}

echo "</div>";

// ===== 3. TEST D'URL ABSOLUE =====
echo "<div class='info-box'>
    <h3>🌐 3. Test d'URL absolue :</h3>";

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$base_path = $path_info;

$test_url = $protocol . $host . $base_path . '/index.php?action=show&id=1';

echo "Protocole: <code>$protocol</code><br>";
echo "Host: <code>$host</code><br>";
echo "Base Path: <code>$base_path</code><br>";
echo "<br>URL générée: <code>$test_url</code><br>";

echo "<a href='$test_url' target='_blank' style='
    display: inline-block;
    margin-top: 10px;
    padding: 10px 20px;
    background: #3498db;
    color: white;
    text-decoration: none;
    border-radius: 5px;
'>Tester cette URL</a>";

echo "</div>";

// ===== 4. TEST DES INCLUDES =====
echo "<div class='info-box'>
    <h3>🔗 4. Test des includes :</h3>";

// Test 1: Core/Connection.php
echo "<strong>Test Core/Connection.php:</strong> ";
$core_path = __DIR__ . '/Core/Connection.php';
if (file_exists($core_path)) {
    echo "<span class='success'>✅ Existe</span> (Chemin: $core_path)<br>";
} else {
    echo "<span class='warning'>⚠️ Non trouvé</span><br>";
}

// Test 2: Views/articles/_social_share.php
echo "<strong>Test Views/articles/_social_share.php:</strong> ";
$view_path = __DIR__ . '/Views/articles/_social_share.php';
if (file_exists($view_path)) {
    echo "<span class='success'>✅ Existe</span><br>";
    
    // Lire le contenu pour vérifier
    $content = file_get_contents($view_path);
    if (strpos($content, '$article') !== false) {
        echo "✅ La variable \$article est utilisée dans le fichier<br>";
    } else {
        echo "⚠️ La variable \$article n'est pas dans le fichier<br>";
    }
} else {
    echo "<span class='warning'>⚠️ Non trouvé</span><br>";
}

echo "</div>";

// ===== 5. SIMULATION DE VOTRE CODE =====
echo "<div class='info-box'>
    <h3>💻 5. Simulation pour _social_share.php :</h3>";

echo "Code à mettre dans votre <strong>_social_share.php</strong> :<br><br>";

echo "<code>&lt;?php<br>";
echo "// DANS Views/articles/_social_share.php<br>";
echo "echo 'DEBUG: Script path = ' . \$_SERVER['SCRIPT_NAME'] . '&lt;br&gt;';<br>";
echo "echo 'DEBUG: Request URI = ' . \$_SERVER['REQUEST_URI'] . '&lt;br&gt;';<br>";
echo "echo 'DEBUG: Host = ' . \$_SERVER['HTTP_HOST'] . '&lt;br&gt;';<br>";
echo "echo 'DEBUG: \$article est défini: ' . (isset(\$article) ? 'OUI' : 'NON') . '&lt;br&gt;';<br>";
echo "if (isset(\$article)) {<br>";
echo "    echo 'DEBUG: Article ID = ' . (\$article['id'] ?? 'NULL') . '&lt;br&gt;';<br>";
echo "    echo 'DEBUG: Article Titre = ' . (\$article['titre'] ?? 'NULL') . '&lt;br&gt;';<br>";
echo "}<br>";
echo "?&gt;</code>";

echo "</div>";

// ===== 6. SOLUTION IMMÉDIATE =====
echo "<div class='info-box'>
    <h3>🚀 6. Solution immédiate :</h3>";

echo "Dans votre <strong>Views/articles/_social_share.php</strong>, remplacez TOUT par :<br><br>";

echo "<code>&lt;?php<br>";
echo "// FORCER LES DONNÉES<br>";
echo "\$article_id = \$_GET['id'] ?? 1; // 1 = ID de votre article<br>";
echo "\$article_title = 'Smart watch'; // Titre de votre article<br>";
echo "<br>";
echo "// URL ABSOLUE<br>";
echo "\$protocol = (!empty(\$_SERVER['HTTPS']) && \$_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';<br>";
echo "\$host = \$_SERVER['HTTP_HOST'];<br>";
echo "\$base_path = '/blog'; // CHANGEZ CE CI SI NÉCESSAIRE<br>";
echo "\$article_url = \$protocol . \$host . \$base_path . '/index.php?action=show&id=' . \$article_id;<br>";
echo "?&gt;<br>";
echo "<br>";
echo "&lt;div style='border:3px solid green; padding:20px;'&gt;<br>";
echo "&lt;h3&gt;Partager cet article&lt;/h3&gt;<br>";
echo "&lt;p&gt;&lt;strong&gt;URL:&lt;/strong&gt; &lt;?= \$article_url ?&gt;&lt;/p&gt;<br>";
echo "&lt;a href='https://www.facebook.com/sharer/sharer.php?u=&lt;?= urlencode(\$article_url) ?&gt;' target='_blank'&gt;Facebook&lt;/a&gt;<br>";
echo "&lt;/div&gt;";

echo "</div>";

// ===== LIENS UTILES =====
echo "<div class='info-box'>
    <h3>🔗 7. Liens de test :</h3>";

echo "<ul>
    <li><a href='test_share.php'>Test de partage</a> (doit fonctionner)</li>
    <li><a href='index.php?action=show&id=1'>Votre article</a></li>
    <li><a href='index.php'>Accueil du blog</a></li>
</ul>";

echo "</div>";

echo "</body></html>";