<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class PHPMailerService
{
    protected $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);

        // SMTP configuration
        $this->mailer->isSMTP();
        $this->mailer->Host       = env('MAIL_HOST', 'smtp.gmail.com');
        $this->mailer->SMTPAuth   = true;
        $this->mailer->Username   = env('MAIL_USERNAME');
        $this->mailer->Password   = env('MAIL_PASSWORD');
        $this->mailer->SMTPSecure = env('MAIL_ENCRYPTION', 'tls');
        $this->mailer->Port       = env('MAIL_PORT', 587);

        // Default sender
        $this->mailer->setFrom(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME'));
    }

    /** Send email with optional attachment */
    public function sendMail(string $to, string $subject, string $body, string $attachmentPath = null)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($to);

            if ($attachmentPath) {
                $this->mailer->addAttachment($attachmentPath);
            }

            $this->mailer->isHTML(true);
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $body;

            $this->mailer->send();

            return true;
        } catch (Exception $e) {
            \Log::error("PHPMailer Error: " . $this->mailer->ErrorInfo);
            return false;
        }
    }
}
