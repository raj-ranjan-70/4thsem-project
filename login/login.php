<?php
session_start();

// Set secure session cookie parameters
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_secure', 1); // Enable if using HTTPS
ini_set('session.use_strict_mode', 1);

// DB connection
$conn = new mysqli("localhost", "root", "", "meal_planner");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Input validation
if (empty($_POST['username']) || empty($_POST['password'])) {
    die("<script>alert('Please fill all fields'); window.history.back();</script>");
}

$email = trim($_POST['username']);
$password = $_POST['password'];

// Check if user exists
$stmt = $conn->prepare("SELECT id, email, password, name FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 1) {
    $stmt->bind_result($user_id, $fetched_email, $hashed_pass, $name);
    $stmt->fetch();

    if (password_verify($password, $hashed_pass)) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $name; // Use $name instead of undefined $username
        $_SESSION['email'] = $fetched_email;
        $_SESSION['logged_in'] = true;
        header("Location: ../index.html");
        exit();
    }
    else {
        echo "<script>alert('Incorrect password. Please try again.'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('Email not found. Please sign up.'); window.location.href='../Registration/register.html';</script>";
}

$stmt->close();
$conn->close();
?>