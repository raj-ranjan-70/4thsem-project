<?php
session_start();

// Session timeout (5 minutes)
$timeout_duration = 300;
if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header("Location: ../Login/login.html?timeout=1");
    exit();
}
$_SESSION['LAST_ACTIVITY'] = time();

if (!isset($_SESSION['username'])) {
    header("Location: ../Login/login.html");
    exit();
}

// Database connection
$host = "localhost";
$user = "root";
$password = "";
$dbname = "meal_planner";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Clear existing meals for this user
$username = $_SESSION['username'];
$stmt = $conn->prepare("DELETE FROM meals WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->close();

// Save new meals from the form
if (isset($_POST['day']) && isset($_POST['type']) && isset($_POST['meal'])) {
    $stmt = $conn->prepare("INSERT INTO meals (username, day, type, meal) VALUES (?, ?, ?, ?)");
    
    for ($i = 0; $i < count($_POST['day']); $i++) {
        $day = $_POST['day'][$i];
        $type = $_POST['type'][$i];
        $meal = $_POST['meal'][$i];
        
        $stmt->bind_param("ssss", $username, $day, $type, $meal);
        $stmt->execute();
    }
    $stmt->close();
}

$conn->close();

// Redirect to grocery cart page
header("Location: ../grocery_cart.php");
exit();
?>