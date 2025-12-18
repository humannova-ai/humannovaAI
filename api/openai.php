<?php
/**
 * Intégration de l’API Groq – gratuite et très rapide
 * Utilise Llama 3.3 70B
 */

error_reporting(E_ALL);
ini_set('display_errors', 0);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

// ============================================
// Configuration de l’API Groq – gratuite !
// ============================================
$GROQ_API_KEY = '****';
$GROQ_MODEL = 'llama-3.3-70b-versatile';

// ============================================
// Contexte de l’assistant
// ============================================
$siteContext = "Tu es Nova AI, un assistant intelligent, professionnel et amical. 
Tu peux répondre à toute question dans n’importe quel domaine :
- Sciences (physique, chimie, biologie)
- Histoire et géographie
- Mathématiques
- Programmation et technologie
- Culture, art et littérature
- Sport
- Cuisine et santé
- Et tout autre sujet

Tu aides également sur la plateforme Human Nova AI pour la gestion des événements et des activités.

Règles importantes :
- Réponds de manière détaillée et utile
- Utilise les émojis avec modération pour rendre les réponses agréables
- Parle en arabe, français ou anglais selon la langue de la question
- Sois amical et professionnel";

// ============================================
// Traitement de la requête
// ============================================
$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);

if (!$input || !isset($input['message']) || empty(trim($input['message']))) {
    echo json_encode(['success' => false, 'error' => 'Le message est obligatoire']);
    exit;
}

$userMessage = trim($input['message']);
$conversationHistory = isset($input['history']) ? $input['history'] : [];

// ============================================
// Préparation des messages
// ============================================
$messages = [
    ['role' => 'system', 'content' => $siteContext]
];

// Ajout de l’historique de conversation
$historySlice = array_slice($conversationHistory, -6);
foreach ($historySlice as $msg) {
    if (isset($msg['role']) && isset($msg['content'])) {
        $messages[] = ['role' => $msg['role'], 'content' => $msg['content']];
    }
}

$messages[] = ['role' => 'user', 'content' => $userMessage];

// ============================================
// Appel de l’API Groq
// ============================================
function callGroq($messages, $apiKey, $model) {
    $data = [
        'model' => $model,
        'messages' => $messages,
        'max_tokens' => 1024,
        'temperature' => 0.7
    ];
    
    $ch = curl_init();
    
    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://api.groq.com/openai/v1/chat/completions',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($data),
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $apiKey
        ],
        CURLOPT_TIMEOUT => 60,
        CURLOPT_SSL_VERIFYPEER => true
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if ($curlError) {
        return ['success' => false, 'error' => 'Erreur de connexion : ' . $curlError];
    }
    
    $result = json_decode($response, true);
    
    if ($httpCode !== 200) {
        $errorMsg = isset($result['error']['message']) ? $result['error']['message'] : 'Erreur API';
        return ['success' => false, 'error' => 'Erreur API (HTTP ' . $httpCode . ') : ' . $errorMsg];
    }
    
    if (isset($result['choices'][0]['message']['content'])) {
        return ['success' => true, 'response' => $result['choices'][0]['message']['content']];
    }
    
    return ['success' => false, 'error' => 'Réponse invalide de l’API'];
}

$result = callGroq($messages, $GROQ_API_KEY, $GROQ_MODEL);
echo json_encode($result, JSON_UNESCAPED_UNICODE);
