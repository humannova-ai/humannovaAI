<?php
// Lightweight mailer helper. Uses PHPMailer if available, otherwise falls back to PHP mail().
class Mailer {
    public static function sendMail($to, $subject, $htmlBody, $from = 'noreply@localhost') {
        // 1) Try SendGrid if configured in userai config/email.php
        // Prefer environment variable for the SendGrid API key, then fallback to userai config file.
        $envKey = getenv('SENDGRID_API_KEY') ?: getenv('SENDGRID_APIKEY');
        $emailConfigPath = __DIR__ . '/../../../userai/user/config/email.php';
        $cfg = [];
        if (file_exists($emailConfigPath)) {
            $cfg = include $emailConfigPath;
        }

        $sendgridKey = $envKey ?: ($cfg['sendgrid_api_key'] ?? null);
        if (!empty($sendgridKey) && $sendgridKey !== 'YOUR_SENDGRID_API_KEY_HERE') {
                // Prefer using the local SendGrid SDK if available
                $sendgridSdk = __DIR__ . '/../../../vendor/sendgrid/sendgrid/sendgrid-php-main/lib/SendGrid.php';
                if (file_exists($sendgridSdk)) {
                    require_once $sendgridSdk;
                    // If SendGrid SDK is present, note availability but do not instantiate the deprecated SendGrid\Mail class
                    try {
                        if (class_exists('SendGrid')) {
                            // SDK is available; we'll still use the HTTP API below for reliability
                        }
                    } catch (Throwable $t) {
                        // ignore and fallback to HTTP
                    }
                }

                // If SDK available, use simple cURL fallback for reliability here
                $payload = [
                    'personalizations' => [[ 'to' => [[ 'email' => $to ]], 'subject' => $subject ]],
                    'from' => [ 'email' => $from ],
                    'content' => [[ 'type' => 'text/html', 'value' => $htmlBody ]]
                ];

                $sendgridUrl = $cfg['sendgrid_api_url'] ?? 'https://api.sendgrid.com/v3/mail/send';
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, rtrim($sendgridUrl, '/'));
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Authorization: Bearer ' . $sendgridKey,
                    'Content-Type: application/json'
                ]);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
                $resp = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                if ($resp === false) {
                    error_log('SendGrid cURL error: ' . curl_error($ch));
                }
                curl_close($ch);

                if ($httpCode >= 200 && $httpCode < 300) {
                    return true;
                }
                // otherwise fall through to PHPMailer/mail fallback
        }

        // 2) Try to load PHPMailer from the project's vendor directory first, then userai bundle
        $phpmailerLocal = __DIR__ . '/../../../vendor/phpmailer/src/PHPMailer.php';
        $phpmailerFallback = __DIR__ . '/../../../userai/user/vendor/phpmailer/src/PHPMailer.php';
        $phpmailerLoaded = false;
        if (file_exists($phpmailerLocal)) {
            require_once $phpmailerLocal;
            require_once __DIR__ . '/../../../vendor/phpmailer/src/SMTP.php';
            require_once __DIR__ . '/../../../vendor/phpmailer/src/Exception.php';
            $phpmailerLoaded = true;
        } elseif (file_exists($phpmailerFallback)) {
            require_once $phpmailerFallback;
            require_once __DIR__ . '/../../../userai/user/vendor/phpmailer/src/SMTP.php';
            require_once __DIR__ . '/../../../userai/user/vendor/phpmailer/src/Exception.php';
            $phpmailerLoaded = true;
        }

        if ($phpmailerLoaded) {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            try {
                // Use mail() transport by default to avoid requiring SMTP config
                $mail->isMail();
                $mail->setFrom($from, 'No Reply');
                $mail->addAddress($to);
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body = $htmlBody;
                $mail->AltBody = strip_tags($htmlBody);
                return $mail->send();
            } catch (\PHPMailer\PHPMailer\Exception $e) {
                error_log('PHPMailer error: ' . $e->getMessage());
                // Fallback to mail()
            }
        }

        // 3) Fallback: basic mail()
        $headers  = 'MIME-Version: 1.0' . "\r\n";
        $headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
        $headers .= 'From: ' . $from . "\r\n";
        return mail($to, $subject, $htmlBody, $headers);
    }

    public static function notifyPostPublished($article, $recipients = array()) {
        $title = htmlspecialchars($article['titre']);
        $excerpt = htmlspecialchars($article['excerpt'] ?? substr(strip_tags($article['contenu'] ?? ''),0,150));
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https://' : 'http://') . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/blog/index.php?action=show&id=' . ($article['id'] ?? '');

        $subject = "Nouvel article publié: $title";
        $body = "<h2>$title</h2><p>$excerpt</p><p><a href=\"$url\">Voir l'article</a></p>";

        // If recipients explicitly provided, use them
        if (!empty($recipients)) {
            $targets = $recipients;
        } else {
            $targets = [];

            // 1) Try to notify the article author by looking up in userai DB if user_id present
            if (!empty($article['user_id'])) {
                $userEmail = null;
                $userDbPath = __DIR__ . '/../../../userai/user/config/database.php';
                if (file_exists($userDbPath)) {
                    // Load the userai database configuration (expected to return an array)
                    $dbConfig = include $userDbPath;
                    try {
                        if (!is_array($dbConfig)) {
                            throw new Exception('Invalid userai database config');
                        }

                        // If a full DSN is provided, prefer that
                        if (!empty($dbConfig['dsn']) || !empty($dbConfig['pdo_dsn'])) {
                            $dsn = $dbConfig['dsn'] ?? $dbConfig['pdo_dsn'];
                            $user = $dbConfig['username'] ?? $dbConfig['user'] ?? null;
                            $pass = $dbConfig['password'] ?? null;
                            $options = $dbConfig['options'] ?? [];
                            $conn = new PDO($dsn, $user, $pass, $options);
                        } else {
                            // Build a DSN from common config keys
                            $driver = $dbConfig['driver'] ?? 'mysql';
                            $host = $dbConfig['host'] ?? '127.0.0.1';
                            $dbname = $dbConfig['database'] ?? $dbConfig['dbname'] ?? null;
                            $charset = $dbConfig['charset'] ?? 'utf8mb4';
                            $user = $dbConfig['username'] ?? $dbConfig['user'] ?? null;
                            $pass = $dbConfig['password'] ?? null;
                            if (empty($dbname)) {
                                throw new Exception('Missing database name in userai config');
                            }
                            $dsn = "$driver:host={$host};dbname={$dbname};charset={$charset}";
                            $options = $dbConfig['options'] ?? [];
                            $conn = new PDO($dsn, $user, $pass, $options);
                        }

                        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                        $stmt = $conn->prepare('SELECT email FROM user WHERE id = ? LIMIT 1');
                        $stmt->execute([$article['user_id']]);
                        $row = $stmt->fetch(PDO::FETCH_ASSOC);
                        if ($row && !empty($row['email'])) {
                            $userEmail = $row['email'];
                        }
                    } catch (Exception $e) {
                        error_log('Mailer: failed to lookup author in userai DB: ' . $e->getMessage());
                    }
                }

                if ($userEmail) $targets[] = $userEmail;
            }

            // 2) Try to notify subscribers table in blog DB if it exists
            try {
                require_once __DIR__ . '/../Core/Connection.php';
                $db = (new Connection())->connect();
                $check = $db->query("SHOW TABLES LIKE 'subscribers'");
                if ($check && $check->rowCount() > 0) {
                    $stmt = $db->query('SELECT email FROM subscribers WHERE email IS NOT NULL');
                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($rows as $r) {
                        if (!empty($r['email'])) $targets[] = $r['email'];
                    }
                }
            } catch (Exception $e) {
                error_log('Mailer: failed to query subscribers: ' . $e->getMessage());
            }

            // 3) Fallback to current session admin email or default
            if (empty($targets)) {
                $targets[] = $_SESSION['user_email'] ?? 'admin@example.com';
            }
        }

        // Deduplicate recipients
        $targets = array_values(array_unique($targets));

        $success = true;
        foreach ($targets as $r) {
            if (!self::sendMail($r, $subject, $body)) {
                $success = false;
            }
        }

        return $success;
    }
}
?>
