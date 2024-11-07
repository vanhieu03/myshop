<?php
include 'includes/header.php';
include 'config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $feedback = $_POST['feedback'];

    $stmt = $conn->prepare("INSERT INTO customer_feedback (customer_name, email, feedback_text) VALUES (:name, :email, :feedback)");
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':feedback', $feedback);
    $stmt->execute();

    echo "Thank you for your feedback!";
}
?>

<form method="POST" action="contact.php" class="feedback-form">
    <input type="text" name="name" placeholder="Your Name" required class="form-input name-input">
    <input type="email" name="email" placeholder="Your Email" required class="form-input email-input">
    <textarea name="feedback" placeholder="Your Feedback" required class="form-textarea"></textarea>
    <button type="submit" class="submit-button">Submit Feedback</button>
</form>

