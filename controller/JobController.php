<?php
/**
 * Contrôleur Job
 * Gestion de la logique métier pour les offres d'emploi
 */

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../model/Job.php';
require_once __DIR__ . '/../model/Application.php';
require_once __DIR__ . '/../config/ai.php';

class JobController {
    private $db;
    private $job;
    private $application;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->job = new Job($this->db);
        $this->application = new Application($this->db);
    }

    /**
     * Récupérer tous les jobs
     */
    public function getAllJobs() {
        $stmt = $this->job->readAll();
        $jobs = array();
        
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Compter le nombre de candidatures pour chaque job
            $row['application_count'] = $this->application->countByJob($row['id']);
            $jobs[] = $row;
        }
        
        return $jobs;
    }

    /**
     * Récupérer uniquement les jobs actifs (status = 'active')
     */
    public function getActiveJobs() {
        $stmt = $this->job->readActive();
        $jobs = array();

        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            // Compter le nombre de candidatures pour chaque job
            $row['application_count'] = $this->application->countByJob($row['id']);
            $jobs[] = $row;
        }

        return $jobs;
    }

    /**
     * Récupérer un job par ID
     */
    public function getJob($id) {
        $this->job->id = $id;
        
        if($this->job->readOne()) {
            $jobData = array(
                'id' => $this->job->id,
                'user_id' => $this->job->user_id,
                'title' => $this->job->title,
                'company' => $this->job->company,
                'salary' => $this->job->salary,
                'description' => $this->job->description,
                'location' => $this->job->location,
                'date_posted' => $this->job->date_posted,
                'category' => $this->job->category,
                'contract_type' => $this->job->contract_type,
                'logo' => $this->job->logo,
                'status' => $this->job->status,
            );
            return $jobData;
        }
        
        return null;
    }

    /**
     * Récupérer un job avec ses candidatures
     */
    public function getJobWithApplications($id) {
        $jobData = $this->getJob($id);
        
        if(!$jobData) {
            return null;
        }
        
        // Récupérer les candidatures pour ce job
        $applications_stmt = $this->application->readByJob($id);
        $jobData['applications'] = array();
        
        while($app_row = $applications_stmt->fetch(PDO::FETCH_ASSOC)) {
            $jobData['applications'][] = $app_row;
        }
        
        return $jobData;
    }

    /**
     * Créer un job
     */
    public function createJob($data) {
        try {
            $this->job->user_id = $data['user_id'] ?? ($_SESSION['user_id'] ?? 1);
            $this->job->title = $data['title'];
            $this->job->company = $data['company'];
            $this->job->salary = $data['salary'];
            $this->job->description = $data['description'];
            $this->job->location = $data['location'];
            $this->job->date_posted = date('Y-m-d H:i:s');
            $this->job->category = $data['category'];
            $this->job->contract_type = $data['contract_type'];
            $this->job->logo = $data['logo'] ?? 'https://via.placeholder.com/200x200?text=LOGO';
            $this->job->status = $data['status'] ?? 'active';                   
            if($this->job->create()) {
                return array('success' => true, 'id' => $this->job->id, 'message' => 'Offre d\'emploi créée avec succès');
            } else {
                return array('success' => false, 'message' => 'Erreur lors de la création de l\'offre');
            }
            
        } catch(Exception $e) {
            return array('success' => false, 'message' => $e->getMessage());
        }
    }

    /**
     * Mettre à jour un job
     */
    public function updateJob($id, $data) {
        try {
            $this->job->id = $id;
            
            // Récupérer d'abord le job existant
            if(!$this->job->readOne()) {
                return array('success' => false, 'message' => 'Offre non trouvée');
            }
            
            $this->job->title = $data['title'] ?? $this->job->title;
            $this->job->company = $data['company'] ?? $this->job->company;
            $this->job->salary = $data['salary'] ?? $this->job->salary;
            $this->job->description = $data['description'] ?? $this->job->description;
            $this->job->location = $data['location'] ?? $this->job->location;
            $this->job->category = $data['category'] ?? $this->job->category;
            $this->job->contract_type = $data['contract_type'] ?? $this->job->contract_type;
            $this->job->logo = $data['logo'] ?? $this->job->logo;
            $this->job->status = $data['status'] ?? $this->job->status;
            
            if($this->job->update()) {
                return array('success' => true, 'message' => 'Offre d\'emploi mise à jour avec succès');
            } else {
                return array('success' => false, 'message' => 'Erreur lors de la mise à jour de l\'offre');
            }
            
        } catch(Exception $e) {
            return array('success' => false, 'message' => $e->getMessage());
        }
    }

    /**
     * Supprimer un job
     */
    public function deleteJob($id) {
        $this->job->id = $id;
        
        if($this->job->delete()) {
            return array('success' => true, 'message' => 'Offre d\'emploi supprimée avec succès');
        }
        
        return array('success' => false, 'message' => 'Erreur lors de la suppression');
    }

    /**
     * Soumettre une candidature
     */
    public function submitApplication($data) {
        try {
            $this->application->job_id = $data['job_id'];
            $this->application->user_id = $data['user_id'] ?? null;
            $this->application->name = $data['name'];
            $this->application->email = $data['email'];
            $this->application->cover = $data['cover_letter'] ?? '';
            $this->application->cv_filename = $data['cv_filename'] ?? '';
            $this->application->status = 'pending';
            
            if($this->application->create()) {
                return array('success' => true, 'id' => $this->application->id, 'message' => 'Candidature soumise avec succès');
            } else {
                return array('success' => false, 'message' => 'Erreur lors de la soumission de la candidature');
            }
            
        } catch(Exception $e) {
            return array('success' => false, 'message' => $e->getMessage());
        }
    }

    /**
     * Récupérer toutes les candidatures
     */
    public function getAllApplications() {
        $stmt = $this->application->readAll();
        $applications = array();
        
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $applications[] = $row;
        }
        
        return $applications;
    }

    /**
     * Récupérer une candidature par ID
     */
    public function getApplication($id) {
        $this->application->id = $id;
        
        if($this->application->readOne()) {
            return array(
                'id' => $this->application->id,
                'job_id' => $this->application->job_id,
                'user_id' => $this->application->user_id,
                'name' => $this->application->name,
                'email' => $this->application->email,
                'cover' => $this->application->cover,
                'cv_filename' => $this->application->cv_filename,
                'status' => $this->application->status,
                'created_at' => $this->application->created_at
            );
        }
        
        return null;
    }

    /**
     * Mettre à jour le statut d'une candidature
     */
    public function updateApplicationStatus($application_id, $status) {
        try {
            $this->application->id = $application_id;
            $this->application->status = $status;
            
            if($this->application->updateStatus()) {
                // Récupérer les informations de la candidature
                $this->application->id = $application_id;
                if($this->application->readOne()) {
                    // Envoyer un email au candidat
                    $this->sendApplicationStatusEmail(
                        $this->application->email,
                        $this->application->name,
                        $status,
                        $this->application->job_id
                    );
                }
                
                return array(
                    'success' => true, 
                    'message' => 'Statut de la candidature mis à jour et email envoyé au candidat'
                );
            }
            
            return array('success' => false, 'message' => 'Erreur lors de la mise à jour du statut');
        } catch(Exception $e) {
            return array('success' => false, 'message' => 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Envoyer un email de réponse à la candidature
     */
    private function sendApplicationStatusEmail($email, $candidateName, $status, $jobId) {
        try {
            // Validate email
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                error_log("Invalid email address: $email");
                return false;
            }
            
            // Récupérer les informations du job
            $this->job->id = $jobId;
            $jobInfo = null;
            if($this->job->readOne()) {
                $jobInfo = array(
                    'title' => $this->job->title,
                    'company' => $this->job->company,
                    'location' => $this->job->location
                );
            }
            
            // Préparer l'email
            $to = $email;
            $subject = '';
            $message = '';
            
            if($status === 'accepted') {
                $subject = 'Bonne nouvelle ! Votre candidature a été acceptée';
                $message = $this->getAcceptanceEmailTemplate($candidateName, $jobInfo);
            } elseif($status === 'refused') {
                $subject = 'Statut de votre candidature';
                $message = $this->getRejectionEmailTemplate($candidateName, $jobInfo);
            } else {
                return false; // Ne pas envoyer d'email pour les autres statuts
            }
            
            // Log attempt
            error_log("Attempting to send email to: $email for status: $status");
            
            // Always use PHPMailer first, then fallback to mail()
            if(class_exists('PHPMailer\PHPMailer\PHPMailer')) {
                $result = $this->sendEmailWithPHPMailer($to, $subject, $message);
            } else {
                $result = $this->sendEmailBasic($to, $subject, $message);
            }
            
            if($result) {
                error_log("Email successfully sent to: $email");
            } else {
                error_log("Failed to send email to: $email");
            }
            
            return $result;
            
        } catch(Exception $e) {
            error_log("Email Exception: " . $e->getMessage());
            return false;
        }
    }

    private function getAcceptanceEmailTemplate($candidateName, $jobInfo) {
        $jobTitle = $jobInfo ? htmlspecialchars($jobInfo['title']) : 'l\'offre d\'emploi';
        $company = $jobInfo ? htmlspecialchars($jobInfo['company']) : '';
        
        return "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f5f5f5; }
        .container { max-width: 600px; margin: 20px auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #6e45e2 0%, #88d3ce 100%); color: white; padding: 30px; text-align: center; }
        .content { padding: 30px; }
        .success { color: #28a745; font-size: 24px; margin-bottom: 10px; }
        .job-info { background: #f9f9f9; padding: 15px; border-radius: 8px; margin: 20px 0; }
        .footer { background: #f5f5f5; padding: 20px; text-align: center; color: #666; font-size: 12px; border-top: 1px solid #ddd; }
        .btn { display: inline-block; background: #6e45e2; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1 style='margin: 0;'>PRO MANGEAI</h1>
            <p style='margin: 5px 0 0 0;'>Plateforme d'emploi</p>
        </div>
        
        <div class='content'>
            <p>Bonjour <strong>" . htmlspecialchars($candidateName) . "</strong>,</p>
            
            <p style='font-size: 18px; margin: 20px 0;'>
                <span class='success'>✓</span> Excellent nouvelle !
            </p>
            
            <p>Nous sommes heureux de vous informer que votre candidature pour le poste de <strong>$jobTitle</strong> chez <strong>$company</strong> a été <strong style='color: #28a745;'>ACCEPTÉE</strong> ! 🎉</p>
            
            <div class='job-info'>
                <h3 style='margin-top: 0;'>Détails du poste :</h3>
                <p><strong>Titre :</strong> $jobTitle</p>
                <p><strong>Entreprise :</strong> $company</p>
            </div>
            
            <p>L'équipe de recrutement vous contactera prochainement pour les prochaines étapes du processus de sélection.</p>
            
            <p>Si vous avez des questions, n'hésitez pas à nous contacter.</p>
            
            <a href='http://localhost/projectphp' class='btn'>Voir mon profil</a>
        </div>
        
        <div class='footer'>
            <p>&copy; 2025  PRO MANGEAI. Tous droits réservés.</p>
            <p>Cet email a été envoyé automatiquement, veuillez ne pas y répondre.</p>
        </div>
    </div>
</body>
</html>
        ";
    }

    private function getRejectionEmailTemplate($candidateName, $jobInfo) {
        $jobTitle = $jobInfo ? htmlspecialchars($jobInfo['title']) : 'l\'offre d\'emploi';
        $company = $jobInfo ? htmlspecialchars($jobInfo['company']) : '';
        
        return "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f5f5f5; }
        .container { max-width: 600px; margin: 20px auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #1a1a2e 0%, #0f0f1a 100%); color: white; padding: 30px; text-align: center; }
        .content { padding: 30px; }
        .job-info { background: #f9f9f9; padding: 15px; border-radius: 8px; margin: 20px 0; }
        .footer { background: #f5f5f5; padding: 20px; text-align: center; color: #666; font-size: 12px; border-top: 1px solid #ddd; }
        .btn { display: inline-block; background: #6e45e2; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1 style='margin: 0;'>PRO MANGEAI</h1>
            <p style='margin: 5px 0 0 0;'>Plateforme d'emploi</p>
        </div>
        
        <div class='content'>
            <p>Bonjour <strong>" . htmlspecialchars($candidateName) . "</strong>,</p>
            
            <p>Nous vous remercions d'avoir soumis votre candidature pour le poste de <strong>$jobTitle</strong> chez <strong>$company</strong>.</p>
            
            <p>Après examen attentif de votre profil, nous regrettons de vous informer que nous avons décidé de poursuivre notre processus de sélection avec d'autres candidats qui correspondent mieux aux critères du poste.</p>
            
            <p>Cela ne reflète pas la qualité de votre profil. Nous vous encourageons à consulter les autres offres d'emploi disponibles sur notre plateforme qui pourraient correspondre à vos compétences et expériences.</p>
            
            <a href='http://localhost/projectphp/view/front/offres.php' class='btn'>Parcourir les autres offres</a>
            
            <p style='margin-top: 30px; color: #666;'>Merci de votre intérêt pour notre entreprise et nous vous souhaitons une excellente continuation.</p>
        </div>
        
        <div class='footer'>
            <p>&copy; 2025  PRO MANGEAI. Tous droits réservés.</p>
            <p>Cet email a été envoyé automatiquement, veuillez ne pas y répondre.</p>
        </div>
    </div>
</body>
</html>
        ";
    }

    /**
     * Envoyer un email avec PHPMailer
     */
    private function sendEmailWithPHPMailer($to, $subject, $body) {
        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer();
            
            // Try SMTP with Gmail settings
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'espsytunisia@gmail.com';
            $mail->Password = 'isae zjyl bkjm aiyv'; 
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            $mail->SMTPDebug = 0;
            
            $mail->setFrom('espsytunisia@gmail.com', 'PRO MANGEAI');
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = $subject;
            $mail->Body = $body;
            
            $result = $mail->send();
            error_log("Email sent successfully to: $to");
            return $result;
            
        } catch(Exception $e) {
            error_log("PHPMailer Error: " . $e->getMessage() . " - Falling back to mail()");
            return $this->sendEmailBasic($to, $subject, $body);
        }
    }

    /**
     * Envoyer un email avec mail() classique
     */
    private function sendEmailBasic($to, $subject, $body) {
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8" . "\r\n";
        $headers .= "From: noreply@promangeai.com" . "\r\n";
        $headers .= "Reply-To: noreply@promangeai.com" . "\r\n";
        $headers .= "X-Mailer: PRO MANGEAI" . "\r\n";
        
        try {
            $result = mail($to, $subject, $body, $headers);
            if($result) {
                error_log("Email sent via mail() to: $to");
            } else {
                error_log("mail() failed for recipient: $to");
            }
            return $result;
        } catch(Exception $e) {
            error_log("mail() error: " . $e->getMessage());
            return false;
        }
    }


    public function deleteApplication($application_id) {
        $this->application->id = $application_id;
        
        if($this->application->delete()) {
            return array('success' => true, 'message' => 'Candidature supprimée avec succès');
        }
        
        return array('success' => false, 'message' => 'Erreur lors de la suppression de la candidature');
    }


    public function getApplicationsByJob($job_id) {
        $stmt = $this->application->readByJob($job_id);
        $applications = array();
        
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $applications[] = $row;
        }
        
        return $applications;
    }

    public function getStats() {
        $stats = array(
            'total_jobs' => $this->job->countAll(),
            'active_jobs' => $this->job->countByStatus('active'),
            'inactive_jobs' => $this->job->countByStatus('inactive'),
            'total_applications' => $this->application->countAll(),
            'pending_applications' => $this->application->countByStatus('pending'),
            'accepted_applications' => $this->application->countByStatus('accepted'),
            'refused_applications' => $this->application->countByStatus('refused')
        );
        
        return $stats;
    }

    public function searchJobs($keywords, $location = '', $category = '', $contract_type = '') {
        $stmt = $this->job->search($keywords, $location, $category, $contract_type);
        $jobs = array();
        
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $row['application_count'] = $this->application->countByJob($row['id']);
            $jobs[] = $row;
        }
        
        return $jobs;
    }

    public function getApplicationsByUser($user_id) {
        $query = "SELECT a.*, j.title as job_title, j.company, j.location 
                FROM applications a 
                LEFT JOIN jobs j ON a.job_id = j.id 
                WHERE a.user_id = :user_id 
                ORDER BY a.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        $applications = array();
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $applications[] = $row;
        }
        
        return $applications;
    }

    public function searchJobsWithAI($query) {
        if (empty($query)) {
            return array('success' => false, 'message' => 'La requête ne peut pas être vide');
        }

        try {
            // Load all jobs
            $stmt = $this->job->readAll();
            $jobs = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Only keep the required attributes
                $filteredJob = array(
                    'id' => $row['id'],
                    'title' => $row['title'],
                    'company' => $row['company'],
                    'salary' => $row['salary'],
                    'description' => $row['description'],
                    'location' => $row['location'],
                    'date_posted' => $row['date_posted'],
                    'category' => $row['category'],
                    'contract_type' => $row['contract_type'],
                    'logo' => $row['logo']
                );
                $filteredJob['application_count'] = $this->application->countByJob($row['id']);
                $jobs[] = $filteredJob;
            }

            if (empty($jobs)) {
                return array('success' => false, 'message' => 'Aucun emploi disponible', 'jobs' => array());
            }

            // Call OpenAI API for semantic matching
            $matchedJobIds = $this->getAIMatchedJobIds($query, $jobs);

            if (!is_array($matchedJobIds)) {
                // If something went wrong, return empty results with success
                return array('success' => true, 'jobs' => array(), 'count' => 0);
            }

            // Filter jobs based on AI response
            $filteredJobs = array_filter($jobs, function($job) use ($matchedJobIds) {
                return in_array($job['id'], $matchedJobIds);
            });

            return array(
                'success' => true,
                'jobs' => array_values($filteredJobs),
                'count' => count($filteredJobs)
            );
        } catch (Exception $e) {
            error_log("searchJobsWithAI Error: " . $e->getMessage());
            return array('success' => false, 'message' => 'Erreur: ' . $e->getMessage());
        }
    }


    private function getAIMatchedJobIds($userQuery, $jobs) {
        // Check if API key is configured properly
        if (!defined('OPENAI_API_KEY') || OPENAI_API_KEY === 'sk-your-openai-api-key-here') {
            // Use fallback keyword search if API key is not configured
            return $this->basicKeywordSearch($userQuery, $jobs);
        }

        try {
            // Prepare job summaries for the AI using only specified attributes
            $jobSummaries = array_map(function($job, $index) {
                return ($index + 1) . ". " . $job['title'] . 
                       " - " . $job['company'] . 
                       " (" . $job['location'] . ")";
            }, $jobs, array_keys($jobs));

            $jobList = implode("\n", $jobSummaries);

            $prompt = "Match these jobs with the user query.\n\nUser query: " . $userQuery . "\n\nJobs:\n" . $jobList . "\n\nRespond with ONLY job numbers comma-separated (e.g., 1,3,5) or 'none' if no match.";

            $client = new \GuzzleHttp\Client([
                'verify' => false
            ]);
            
            $response = $client->post(OPENAI_API_URL, [
                'headers' => [
                    'Authorization' => 'Bearer ' . OPENAI_API_KEY,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => OPENAI_MODEL,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You are a job matching assistant. Respond with only job numbers that match, comma-separated.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'max_tokens' => 50,
                    'temperature' => 0.2,
                ],
                'timeout' => 30,
                'connect_timeout' => 30
            ]);

            $responseBody = json_decode($response->getBody(), true);
            
            if (!isset($responseBody['choices'][0]['message']['content'])) {
                error_log("Invalid API response structure");
                return $this->basicKeywordSearch($userQuery, $jobs);
            }
            
            $aiResponse = $responseBody['choices'][0]['message']['content'];
            
            // Parse the AI response to extract job numbers
            $matchedNumbers = $this->parseAIResponse($aiResponse);
            
            // Convert numbers to job IDs
            $matchedJobIds = array();
            foreach ($matchedNumbers as $number) {
                if (isset($jobs[$number - 1])) {
                    $matchedJobIds[] = $jobs[$number - 1]['id'];
                }
            }

            return $matchedJobIds;

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            error_log("OpenAI API Client Error: " . $e->getMessage());
            return $this->basicKeywordSearch($userQuery, $jobs);
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            error_log("OpenAI API Connection Error: " . $e->getMessage());
            return $this->basicKeywordSearch($userQuery, $jobs);
        } catch (\Exception $e) {
            error_log("OpenAI API Error: " . $e->getMessage());
            return $this->basicKeywordSearch($userQuery, $jobs);
        }
    }

    private function parseAIResponse($response) {
        $response = strtolower(trim($response));
        
        if (strpos($response, 'none') !== false) {
            return array();
        }

        // Extract numbers from the response
        preg_match_all('/\d+/', $response, $matches);
        return array_map('intval', $matches[0]);
    }

    private function basicKeywordSearch($query, $jobs) {
        $queryKeywords = array_filter(explode(' ', strtolower($query)));
        $matchedJobIds = array();

        foreach ($jobs as $job) {
            // Search only in specified attributes
            $jobText = strtolower(
                $job['title'] . ' ' . 
                $job['company'] . ' ' . 
                $job['salary'] . ' ' .
                $job['description'] . ' ' . 
                $job['location'] . ' ' .
                $job['category'] . ' ' .
                $job['contract_type']
            );
            
            $matchCount = 0;
            foreach ($queryKeywords as $keyword) {
                if (strlen($keyword) > 2 && strpos($jobText, $keyword) !== false) {
                    $matchCount++;
                }
            }

            // If at least one keyword matches, include the job
            if ($matchCount > 0) {
                $matchedJobIds[] = $job['id'];
            }
        }

        return $matchedJobIds;
    }
}


// Gestion des requêtes
if($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    // Set header immediately
    header('Content-Type: application/json; charset=utf-8');
    
    $controller = new JobController();
    $action = $_GET['action'] ?? $_POST['action'] ?? '';
    
    try {
        switch($action) {
            case 'getAll':
            case 'getAllJobs':
                echo json_encode($controller->getAllJobs());
                break;
                
            case 'getOne':
                $id = $_GET['id'] ?? $_POST['id'] ?? 0;
                echo json_encode($controller->getJob($id));
                break;
            case 'getJob':
                $id = $_GET['id'] ?? $_POST['id'] ?? 0;
                echo json_encode($controller->getJob($id));
                break;
                
            case 'getJobWithApplications':
                $id = $_GET['id'] ?? $_POST['id'] ?? 0;
                echo json_encode($controller->getJobWithApplications($id));
                break;
                
            case 'create':
            case 'createJob':
                $data = $_POST;
                if(empty($data)) {
                    $data = json_decode(file_get_contents('php://input'), true);
                }
                echo json_encode($controller->createJob($data));
                break;
                
            case 'update':
            case 'updateJob':
                $id = $_POST['id'] ?? $_GET['id'] ?? 0;
                $data = $_POST;
                if(empty($data)) {
                    $data = json_decode(file_get_contents('php://input'), true);
                }
                echo json_encode($controller->updateJob($id, $data));
                break;
                
            case 'delete':
            case 'deleteJob':
                $id = $_POST['id'] ?? $_GET['id'] ?? 0;
                echo json_encode($controller->deleteJob($id));
                break;
                
            case 'submitApplication':
                $data = $_POST;
                if(empty($data)) {
                    $data = json_decode(file_get_contents('php://input'), true);
                }
                echo json_encode($controller->submitApplication($data));
                break;
                
            case 'getAllApplications':
                echo json_encode($controller->getAllApplications());
                break;
                
            case 'getApplication':
                $id = $_GET['id'] ?? $_POST['id'] ?? 0;
                echo json_encode($controller->getApplication($id));
                break;
                
            case 'updateApplicationStatus':
                $app_id = $_POST['application_id'] ?? $_GET['application_id'] ?? 0;
                $status = $_POST['status'] ?? $_GET['status'] ?? '';
                echo json_encode($controller->updateApplicationStatus($app_id, $status));
                break;
                
            case 'deleteApplication':
                $app_id = $_POST['application_id'] ?? $_GET['application_id'] ?? 0;
                echo json_encode($controller->deleteApplication($app_id));
                break;
                
            case 'getApplicationsByJob':
                $job_id = $_GET['job_id'] ?? $_POST['job_id'] ?? 0;
                echo json_encode($controller->getApplicationsByJob($job_id));
                break;
                
            case 'getStats':
                echo json_encode($controller->getStats());
                break;
                
            case 'searchWithAI':
            case 'aiSearch':
                $query = $_GET['query'] ?? $_POST['query'] ?? '';
                echo json_encode($controller->searchJobsWithAI($query));
                break;
            case 'getActiveJobs':
                echo json_encode($controller->getActiveJobs());
                break;
            // Ajouter ce cas dans le switch principal
            case 'getApplicationsByUser':
                $user_id = $_GET['user_id'] ?? $_POST['user_id'] ?? 0;
                echo json_encode($controller->getApplicationsByUser($user_id));
                break;    
                
            default:
                echo json_encode(array('success' => false, 'message' => 'Action non reconnue'));
        }
    } catch(Exception $e) {
        echo json_encode(array('success' => false, 'message' => 'Erreur: ' . $e->getMessage()));
    }
}
?>