<?php
ini_set('display_errors', 1); // Keep for local debugging, but set to 0 for live production
error_reporting(E_ALL);

// No 'use' statements for PHPMailer needed anymore
// No 'require' for Composer autoload or individual PHPMailer files needed anymore

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

    // --- Start: PHP's native mail() function ---

    // Define the recipient email address
    $to = 'tim@corsinhaus.com'; // <--- THIS IS YOUR RECIPIENT EMAIL

    // Define the subject line
    $subject = 'New Contact Form Submission from ' . $name;

    // Build the email message body
    $email_body = "You have received a new message from your website contact form.\n\n"
                  . "Name: " . $name . "\n"
                  . "Email: " . $email . "\n"
                  . "Message:\n" . $message;

    // Headers for the email
    // IMPORTANT: For best deliverability, the 'From' address should ideally be an email on the sending domain (your Hostinger domain, e.g., corsindigital.com).
    // The 'Reply-To' header will ensure you can reply directly to the user who filled out the form.
    $headers = 'From: no-reply@corsindigital.com' . "\r\n" . // <--- Use an email on your Hostinger domain (can be a non-existent one if no email service is set up for it, but better if it exists)
               'Reply-To: ' . $email . "\r\n" .
               'X-Mailer: PHP/' . phpversion();

    // Send the email
    if (mail($to, $subject, $email_body, $headers)) {
        // Send success JSON response
        echo json_encode(['success' => true, 'message' => 'Your message has been sent successfully.']);
        exit();
    } else {
        // Log the error (optional, can be checked in Hostinger's error logs)
        error_log("Mail failed to send to $to from $email.");
        // Send error JSON response
        echo json_encode(['success' => false, 'message' => 'Oops! Something went wrong and we could not send your message. Please try again later.']);
        exit();
    }
    // --- End: PHP's native mail() function ---

} else {
    // If not a POST request, respond with an error or redirect to the form
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit();
}
?>