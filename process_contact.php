<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/PHPMailer-master/src/Exception.php';
require __DIR__ . '/vendor/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/vendor/PHPMailer-master/src/SMTP.php';

// Set the content type header for JSON response
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $message = htmlspecialchars(trim($_POST['message']));

    // Basic server-side validation.
    if (empty($name) || empty($email) || empty($message)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all fields.']);
        exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
        exit();
    }

    $mail = new PHPMailer(true);

    try {
        // Mailhog settings for local testing (UNCOMMENT FOR LOCAL)
        $mail->isSMTP();
        $mail->Host       = 'localhost';
        $mail->SMTPAuth   = false;
        $mail->Port       = 1025;

        // Hostinger settings (COMMENT OUT FOR LOCAL, UNCOMMENT FOR LIVE)
        /*
        $mail->Host       = 'smtp.hostinger.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'your_email@yourdomain.com';
        $mail->Password   = 'your_email_password';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;
        */

        $mail->setFrom($email, $name);
        $mail->addAddress('your_recipient_email@example.com', 'Your Name'); // *** REPLACE this ***
        $mail->addReplyTo($email, $name);

        $mail->isHTML(false);
        $mail->Subject = 'New Contact Form Submission from ' . $name;
        $mail->Body    = "You have received a new message from your website contact form.\n\n"
                       . "Name: " . $name . "\n"
                       . "Email: " . $email . "\n"
                       . "Message:\n" . $message;

        $mail->send();

        // Send success JSON response
        echo json_encode(['success' => true, 'message' => 'Your message has been sent successfully.']);
        exit();

    } catch (Exception $e) {
        // Send error JSON response
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        echo json_encode(['success' => false, 'message' => 'Oops! Something went wrong and we could not send your message. Please try again later.']);
        exit();
    }

} else {
    // If not a POST request, respond with an error or redirect to the form
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}
?>