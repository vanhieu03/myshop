<?php
include 'config/db.php';

// Kiểm tra xem phiên đã được khởi tạo chưa
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Bắt đầu phiên nếu chưa có
}

// Hiển thị giỏ hàng nếu người dùng đã đăng nhập
if (isset($_SESSION['user_id'])) {
    $customer_id = $_SESSION['user_id'];
    
    // Lấy số lượng sản phẩm trong giỏ hàng
    $stmt = $conn->prepare("SELECT COUNT(product_id) AS total_products FROM cart WHERE customer_id = :customer_id");
    $stmt->bindParam(':customer_id', $customer_id);
    $stmt->execute();
    $result = $stmt->fetch();
    $total_products = $result['total_products'] ? $result['total_products'] : 0;

    echo '<a href="cart.php" class="cart-link">Cart (' . $total_products . ')</a>'; // Liên kết đến trang giỏ hàng
}
?>
