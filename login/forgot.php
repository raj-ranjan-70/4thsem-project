<?php
// Connect to DB
$conn = new mysqli("localhost", "root", "", "meal_planner");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch email
$email = $_POST['email'];

// Check if user exists
$stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    // User exists
    $token = bin2hex(random_bytes(16)); // Secure token
    $expires = date("Y-m-d H:i:s", strtotime('+1 hour'));

    // Save token in DB (create password_resets table if not exists)
    $insert = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, ?)");
    $insert->bind_param("sss", $email, $token, $expires);
    $insert->execute();

    // Send Email
    $resetLink = "http://yourdomain.com/Reset/reset_password.php?token=$token&email=" . urlencode($email);
    $subject = "Reset Your Password";
    $message = "Hi,\n\nClick the link below to reset your password:\n$resetLink\n\nLink valid for 1 hour.";
    $headers = "From: support@yourdomain.com";

    if (mail($email, $subject, $message, $headers)) {
        echo "<script>alert('Reset link sent to your email.'); window.location.href='../Login/login.html';</script>";
    } else {
        echo "<script>alert('Failed to send email.'); window.history.back();</script>";
    }

} else {
    echo "<script>alert('Email not found!'); window.history.back();</script>";
}

$stmt->close();
$conn->close();
?>
