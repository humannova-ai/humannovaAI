<?php
/**
 * Chatbot API Controller
 * Handles AJAX requests for chatbot responses
 */

// Disable error display to prevent HTML output
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Set JSON header first
header('Content-Type: application/json; charset=utf-8');

// chatbot-controller.php is in the same directory as Chatbot.php
// Use __DIR__ to get the current directory
$chatbotPath = __DIR__ . DIRECTORY_SEPARATOR . 'Chatbot.php';

// Check if file exists
if (!file_exists($chatbotPath)) {
    echo json_encode(array(
        'success' => false,
        'message' => 'Fichier Chatbot.php introuvable: ' . $chatbotPath
    ), JSON_UNESCAPED_UNICODE);
    exit;
}

require_once $chatbotPath;

try {
    $chatbot = new Chatbot();
    
    $action = $_GET['action'] ?? $_POST['action'] ?? '';
    $message = $_GET['message'] ?? $_POST['message'] ?? '';
    
    if ($action === 'getResponse' && !empty($message)) {
        $response = $chatbot->getResponse($message);
        echo json_encode(array(
            'success' => true,
            'response' => $response
        ), JSON_UNESCAPED_UNICODE);
    } elseif ($action === 'getGreeting') {
        $greeting = $chatbot->getGreeting();
        echo json_encode(array(
            'success' => true,
            'response' => $greeting
        ), JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(array(
            'success' => false,
            'message' => 'Action ou message manquant'
        ), JSON_UNESCAPED_UNICODE);
    }
} catch (Exception $e) {
    error_log("Chatbot error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    echo json_encode(array(
        'success' => false,
        'message' => 'Erreur du chatbot: ' . $e->getMessage()
    ), JSON_UNESCAPED_UNICODE);
} catch (Error $e) {
    error_log("Chatbot fatal error: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    echo json_encode(array(
        'success' => false,
        'message' => 'Erreur fatale du chatbot'
    ), JSON_UNESCAPED_UNICODE);
}
?>

