<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load environment variables
require_once __DIR__ . '/../config/env.php';

// Include PHPMailer manually
require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';

class Mailer
{
    private $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);

        try {
            $this->mail->isSMTP();
            $this->mail->Host = $_ENV['SMTP_HOST'];
            $this->mail->SMTPAuth = true;
            $this->mail->Username = $_ENV['SMTP_USERNAME'];
            $this->mail->Password = $_ENV['SMTP_PASSWORD'];
            $this->mail->SMTPSecure = $_ENV['SMTP_SECURE'] ?? PHPMailer::ENCRYPTION_STARTTLS;
            $this->mail->Port = $_ENV['SMTP_PORT'];

            $this->mail->setFrom(
                $_ENV['SMTP_FROM_EMAIL'],
                $_ENV['SMTP_FROM_NAME'] ?? 'School Admin'
            );

            $this->mail->isHTML(true);
        } catch (Exception $e) {
            throw new \Exception("Mailer setup failed: " . $e->getMessage());
        }
    }

    // Returns true if email sent, throws Exception if failed
    public function send($to, $subject, $body)
    {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($to);
            $this->mail->Subject = $subject;
            $this->mail->Body = $body;

            return $this->mail->send();
        } catch (Exception $e) {
            throw new \Exception("Email could not be sent. Error: {$this->mail->ErrorInfo}");
        }
    }
}
