<?php
// Step 1: Include Composer's autoloader
require '../vendor/autoload.php';  // Make sure this path is correct

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Step 2: Create a new PHPMailer instance
$mail = new PHPMailer(true);

try {
    // Step 3: Server settings
    $mail->isSMTP();  // Set mailer to use SMTP
    $mail->Host = 'smtp.gmail.com';  // Gmail SMTP server
    $mail->SMTPAuth = true;  // Enable SMTP authentication
    $mail->Username = 'stefanoszks@gmail.com';  // Your Gmail address
    $mail->Password = 'qbflsflvrfxwxxmh';  // Your Gmail App Password (use App Password if 2FA is enabled)
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // Enable TLS encryption
    $mail->Port = 587;  // Gmail's SMTP port

    // Step 4: Set email recipients
    $mail->setFrom('stefanoszks@gmail.com', 'Stefanos Ziakas');  // Sender's email and name
    $mail->addAddress('stefanoszks@gmail.com', 'Stefanos Ziakas');  // Recipient's email and name

    // Step 5: Set email content
    $mail->isHTML(true);  // Set email format to HTML
    $mail->Subject = 'Test Email from PHPMailer';  // Email subject
    $mail->Body    = 'This is a <b>test</b> email sent using <b>PHPMailer</b>.';  // HTML message body
    $mail->AltBody = 'This is a test email sent using PHPMailer.';  // Plain text message body

    // Step 6: Send email
    $mail->send();
    echo 'Email has been sent successfully!';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>
