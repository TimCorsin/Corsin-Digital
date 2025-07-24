<?php
// Your temporary test lines (which should now be commented out or removed)
// echo "This PHP file is being reached!";
// exit(); // Stop execution immediately after this message


// PHP script to process contact form submissions and send an email using PHPMailer.

// --- 1. Error Reporting (for Development/Debugging) ---
// Turn ON error display for development to see issues.
// For production, set display_errors to 0 and error_reporting to 0 or E_ALL & ~E_NOTICE.
ini_set('display_errors', 1);
error_reporting(E_ALL);

// --- 2. PHPMailer Autoload/Require Files ---
// Adjust these paths based on where you placed the PHPMailer 'src' folder
// relative to this 'process_contact.php' file.
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// A common setup after downloading the zip and putting it in 'vendor':
require __DIR__ . '/vendor/PHPMailer-master/src/Exception.php';
require __DIR__ . '/vendor/PHPMailer-master/src/PHPMailer.php';
require __DIR__ . '/vendor/PHPMailer-master/src/SMTP.php';


// --- 3. Check if the form was submitted via POST method ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- 4. Sanitize and Validate Form Inputs ---
    $name = htmlspecialchars(trim($_POST['name']));
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $message = htmlspecialchars(trim($_POST['message']));

    // Basic server-side validation.
    if (empty($name) || empty($email) || empty($message)) {
        header("Location: /contact.html?status=empty_fields");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: /contact.html?status=invalid_email");
        exit();
    }

    // --- 5. Initialize PHPMailer ---
    $mail = new PHPMailer(true);

    try {
        // For local MAMP testing with Mailhog (UNCOMMENT AND USE THESE INSTEAD ON LOCAL)
        $mail->isSMTP();
        $mail->Host       = 'localhost';
        $mail->SMTPAuth   = false;
        $mail->Port       = 1025; // Default Mailhog SMTP port
        
        // --- 7. Recipients ---
        $mail->setFrom($email, $name);
        $mail->addAddress('your_recipient_email@example.com', 'Your Name'); // *** REPLACE this ***
        $mail->addReplyTo($email, $name);

        // --- 8. Email Content ---
        $mail->isHTML(false);
        $mail->Subject = 'New Contact Form Submission from ' . $name;
        $mail->Body    = "You have received a new message from your website contact form.\n\n"
                       . "Name: " . $name . "\n"
                       . "Email: " . $email . "\n"
                       . "Message:\n" . $message;

        // --- 9. Send the Email ---
        $mail->send();

        // --- 10. Success Handling ---
        header("Location: /thank_you.html"); // *** REPLACE this ***
        exit();

    } catch (Exception $e) {
        // --- 11. Error Handling ---
        error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
        header("Location: /error.html?status=send_failed"); // *** REPLACE this ***
        exit();
    }

} else {
    // --- 12. Handle non-POST requests ---
    header("Location: /contact.html"); // *** REPLACE this ***
    exit();
}
?>