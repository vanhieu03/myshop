<?php
include 'includes/header.php';
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $stmt = $conn->prepare("INSERT INTO customers (username, password, email, phone, address) VALUES (:username, :password, :email, :phone, :address)");
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':address', $address);
    $stmt->execute();
    header("Location: login.php");
}
?>

<form method="POST" action="register.php" class="register-form">
    <input type="text" name="username" placeholder="Username" required class="register-input">
    <input type="password" name="password" placeholder="Password" required class="register-input">
    <input type="email" name="email" placeholder="Email" required class="register-input">
    <input type="text" name="phone" placeholder="Phone" required class="register-input">
    <input type="text" name="address" placeholder="Address" required class="register-input">
    <button type="submit" class="register-button">Register</button>
</form>