<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../Login/login.html");
    exit();
}

$host = "localhost";
$user = "root";
$password = "";
$dbname = "meal_planner";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username'];

// Delete existing meals for this user
$delete_stmt = $conn->prepare("DELETE FROM meals WHERE username = ?");
$delete_stmt->bind_param("s", $username);
$delete_stmt->execute();
$delete_stmt->close();

// Insert new meals
if (isset($_POST['day'])){
    $insert_stmt = $conn->prepare("INSERT INTO meals (username, day, type, meal) VALUES (?, ?, ?, ?)");
    
    $days = $_POST['day'];
    $types = $_POST['type'];
    $meals = $_POST['meal'];
    
    for ($i = 0; $i < count($days); $i++) {
        $insert_stmt->bind_param("ssss", $username, $days[$i], $types[$i], $meals[$i]);
        $insert_stmt->execute();
    }
    
    $insert_stmt->close();
    
    // Redirect back with success message
    $_SESSION['message'] = "Meal plan saved successfully!";
    header("Location: index.php");
    exit();
}

$conn->close();
header("Location: index.php");
exit();
?>