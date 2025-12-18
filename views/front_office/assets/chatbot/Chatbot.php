<?php
/**
 * Chatbot System for Pro Manage AI
 * Reads Q&A from .txt files and provides intelligent responses about innovation, investment, and entrepreneurship
 */

class Chatbot {
    private $chatbotDir;
    private $responses = array();
    
    public function __construct() {
        // Chatbot.php is in the same directory as the .txt files
        // Use __DIR__ to get the current directory
        $this->chatbotDir = __DIR__ . DIRECTORY_SEPARATOR;
        
        // Ensure directory exists
        if (!is_dir($this->chatbotDir)) {
            error_log("Chatbot directory not found: " . $this->chatbotDir);
        }
        
        $this->loadResponses();
    }
    
    /**
     * Load all responses from .txt files
     */
    private function loadResponses() {
        if (!is_dir($this->chatbotDir)) {
            return;
        }
        
        $files = glob($this->chatbotDir . '*.txt');
        foreach ($files as $file) {
            // Skip PHP files
            if (strpos(basename($file), '.php') !== false) {
                continue;
            }
            
            $content = file_get_contents($file);
            $lines = explode("\n", $content);
            
            $currentQuestion = null;
            $currentAnswer = '';
            
            foreach ($lines as $line) {
                $line = trim($line);
                
                // Check if line is a question (ends with ? or starts with question words)
                $isQuestion = preg_match('/[?]$/', $line) || 
                             preg_match('/^(Qu|Comment|Quel|Quelle|Où|Pourquoi|C\'est quoi|Qu\'est-ce|Innovation|Investissement|Entrepreneur|Idée|Business|Finance|Événement)/i', $line);
                
                if ($isQuestion && !empty($line)) {
                    // Save previous Q&A if exists
                    if ($currentQuestion && !empty($currentAnswer)) {
                        $this->saveResponse($currentQuestion, $currentAnswer);
                    }
                    // New question
                    $currentQuestion = $line;
                    $currentAnswer = '';
                } else if (!empty($line)) {
                    // This is an answer line
                    if (!empty($currentAnswer)) {
                        $currentAnswer .= ' ';
                    }
                    $currentAnswer .= $line;
                } else if (empty($line) && $currentQuestion && !empty($currentAnswer)) {
                    // Empty line - save current Q&A
                    $this->saveResponse($currentQuestion, $currentAnswer);
                    $currentQuestion = null;
                    $currentAnswer = '';
                }
            }
            
            // Save last Q&A if exists
            if ($currentQuestion && !empty($currentAnswer)) {
                $this->saveResponse($currentQuestion, $currentAnswer);
            }
        }
    }
    
    /**
     * Save a question-answer pair
     */
    private function saveResponse($question, $answer) {
        $questionKey = strtolower($question);
        if (!isset($this->responses[$questionKey])) {
            $this->responses[$questionKey] = array();
        }
        $this->responses[$questionKey][] = trim($answer);
        
        // Also index by keywords for better matching
        $keywords = $this->extractKeywords($question);
        foreach ($keywords as $keyword) {
            if (strlen($keyword) > 3) {
                $keywordKey = strtolower($keyword);
                if (!isset($this->responses[$keywordKey])) {
                    $this->responses[$keywordKey] = array();
                }
                // Add answer to keyword index (limit to avoid too many duplicates)
                if (count($this->responses[$keywordKey]) < 5) {
                    $this->responses[$keywordKey][] = trim($answer);
                }
            }
        }
    }
    
    /**
     * Extract keywords from text
     */
    private function extractKeywords($text) {
        $words = explode(' ', strtolower($text));
        $keywords = array();
        $stopWords = array('le', 'la', 'les', 'un', 'une', 'des', 'de', 'du', 'et', 'ou', 'à', 'pour', 'avec', 'sur', 'dans', 'est', 'sont', 'que', 'qui', 'quoi');
        
        foreach ($words as $word) {
            $word = trim($word, '.,!?;:');
            if (strlen($word) > 3 && !in_array($word, $stopWords)) {
                $keywords[] = $word;
            }
        }
        return $keywords;
    }
    
    /**
     * Find best matching response
     * @param string $userMessage - user's message
     * @return string - chatbot response
     */
    public function getResponse($userMessage) {
        $userMessage = strtolower(trim($userMessage));
        
        // Direct match first
        if (isset($this->responses[$userMessage])) {
            $answers = $this->responses[$userMessage];
            return $answers[array_rand($answers)]; // Random answer from multiple options
        }
        
        // Fuzzy matching - find best match
        $bestMatch = null;
        $bestScore = 0;
        $bestAnswers = array();
        
        foreach ($this->responses as $question => $answers) {
            $questionLower = strtolower($question);
            
            // Exact word match
            $score = $this->calculateMatchScore($userMessage, $questionLower);
            
            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $question;
                $bestAnswers = $answers;
            }
        }
        
        // If good match found, return random answer
        if ($bestScore >= 0.3 && !empty($bestAnswers)) {
            return $bestAnswers[array_rand($bestAnswers)];
        }
        
        // Default responses
        $defaultResponses = array(
            "Je ne suis pas sûr de comprendre. Pouvez-vous reformuler votre question sur l'innovation, l'investissement ou l'entrepreneuriat?",
            "Hmm, je n'ai pas d'information précise sur ça. Essayez de poser une question sur les idées innovantes, les investissements, ou les événements d'affaires!",
            "Désolé, je ne peux répondre qu'aux questions sur l'innovation et l'investissement. Posez-moi quelque chose sur les idées, les investisseurs, ou l'entrepreneuriat!",
            "Je suis spécialisé dans l'innovation et l'investissement! Posez-moi des questions sur les idées innovantes, les investisseurs, les événements, ou les conseils d'affaires. 💡"
        );
        
        return $defaultResponses[array_rand($defaultResponses)];
    }
    
    /**
     * Calculate match score between user message and question
     * @param string $userMessage
     * @param string $question
     * @return float - score between 0 and 1
     */
    private function calculateMatchScore($userMessage, $question) {
        // Exact match
        if ($userMessage === $question) {
            return 1.0;
        }
        
        // Remove question marks and normalize
        $userMsg = str_replace('?', '', $userMessage);
        $q = str_replace('?', '', $question);
        
        // Check if question contains user message or vice versa
        if (strpos($q, $userMsg) !== false) {
            return 0.9;
        }
        
        if (strpos($userMsg, $q) !== false) {
            return 0.8;
        }
        
        // Word matching with similarity
        $userWords = $this->extractKeywords($userMessage);
        $questionWords = $this->extractKeywords($question);
        
        if (empty($userWords) || empty($questionWords)) {
            return 0;
        }
        
        $matchedWords = 0;
        foreach ($userWords as $userWord) {
            foreach ($questionWords as $questionWord) {
                // Exact match
                if ($userWord === $questionWord) {
                    $matchedWords += 2;
                    break;
                }
                // Partial match
                if (strpos($questionWord, $userWord) !== false || strpos($userWord, $questionWord) !== false) {
                    $matchedWords += 1;
                    break;
                }
            }
        }
        
        $maxWords = max(count($userWords), count($questionWords));
        if ($maxWords > 0) {
            return min(1.0, $matchedWords / $maxWords);
        }
        
        return 0;
    }
    
    /**
     * Get greeting response
     */
    public function getGreeting() {
        $greetings = array(
            "Salut! 👋 Bienvenue sur Pro Manage AI! Je suis là pour répondre à toutes vos questions sur l'innovation, l'investissement, et l'entrepreneuriat. Que puis-je faire pour vous?",
            "Hey! 💡 Content de te voir! Pose-moi tes questions sur les idées innovantes, les investisseurs, ou tout ce qui concerne l'entrepreneuriat. Je suis là pour t'aider!",
            "Bonjour! 🎯 Je suis le chatbot Pro Manage AI. Je peux t'aider avec des questions sur l'innovation, les investissements, les événements d'affaires, et bien plus. Que veux-tu savoir?"
        );
        return $greetings[array_rand($greetings)];
    }
}
?>

