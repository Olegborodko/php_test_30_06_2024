<?php
require_once '../phpmailer/src/PHPMailer.php';
require_once '../phpmailer/src/SMTP.php';
require_once '../phpmailer/src/Exception.php';
require_once 'Env.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
  public function sendEmail($to, $subject, $body)
  {
    $mail = new PHPMailer(true);
    try {
      $htmlBody = "
        <h3>Task details</h1>
        <p><strong>Full Name:</strong> {$body['fullname']}</p>
        <p><strong>Email:</strong> {$body['email']}</p>
        <p><strong>Due Date:</strong> {$body['duedate']}</p>
        <p><strong>Title:</strong> {$body['title']}</p>
        <p><strong>Description:</strong> {$body['description']}</p>
      ";

      $mail->isSMTP();
      $mail->Host = $_ENV['SMTP_HOST'];
      $mail->SMTPSecure = 'tls';
      $mail->SMTPAuth = true;
      $mail->Username = $_ENV['SMTP_USERNAME'];
      $mail->Password = $_ENV['SMTP_PASSWORD'];
      $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
      $mail->Port = 587;

      $mail->setFrom($_ENV['SMTP_USERNAME'], 'Mailer');
      $mail->addAddress($to);

      $mail->isHTML(true);
      $mail->Subject = $subject;
      $mail->Body = $htmlBody;
      $mail->AltBody = $htmlBody;

      $mail->send();
      return true;
    } catch (Exception $e) {
      error_log('error send email: ' . $e->getMessage());
      return false;
    }
  }
}
