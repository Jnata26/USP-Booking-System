<?php
$to = "test@example.com";
$subject = "Test Email to Mailpit";
$message = "If you see this, Mailpit and PHP are connected!";
$headers = "From: sender@local.test";

if (mail($to, $subject, $message, $headers)) {
    echo "Email sent successfully!";
} else {
    echo "Failed to send email.";
}
?>