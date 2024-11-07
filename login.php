<?php
include 'includes/header.php';
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM customers WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_start();
        $_SESSION['user_id'] = $user['customer_id'];
        header("Location: index.php");
    } else {
        echo "Invalid credentials";
    }
}
?>

<form method="POST" action="login.php" class="login-form">
    <input type="text" name="username" placeholder="Username" required class="login-input">
    <input type="password" name="password" placeholder="Password" required class="login-input">
    <button type="submit" class="login-button">Login</button>
</form>

