<?php
// Kiểm tra xem phiên đã được khởi tạo chưa
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Bắt đầu phiên nếu chưa có
}

include 'config/db.php'; // Kiểm tra tệp kết nối

// Kiểm tra kết nối
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Shop</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header class="site-header">
        <div class="header-container">
            <div class="logo-nav-container">
                <div class="logo-container cf">My Shop</div>
                <nav class="site-navigation">
                    <a href="index.php" class="nav-link cf">Trang chủ</a>
                    <a href="contact.php" class="nav-link cf">Liên hệ</a>
                </nav>
            </div>
            <div class="search-container">
                <form action="index.php" method="GET" class="search-form">
                    <input type="text" name="query" placeholder="Son môi, mặt nạ,..." class="search-input">
                    <button type="submit" class="search-button">Tìm kiếm</button>
                </form>
            </div>
            <div class="user-cart-container">
                <div class="user-container cf">
                    <?php
                    // Hiển thị tên người dùng nếu đã đăng nhập
                    if (isset($_SESSION['user_id'])) {
                        $customer_id = $_SESSION['user_id'];
                        $stmt = $conn->prepare("SELECT username FROM customers WHERE customer_id = :customer_id");
                        $stmt->bindParam(':customer_id', $customer_id);
                        $stmt->execute();
                        $user = $stmt->fetch();

                        if ($user) {
                            echo '<span class="user-name cf">' . htmlspecialchars($user['username']) . '</span>';
                            echo ' | <a href="logout.php" class="nav-link cf">Đăng xuất</a>'; // Liên kết đăng xuất
                        }
                    } else {
                        echo '<a href="login.php" class="nav-link cf">Đăng nhập</a>';
                        echo '<a href="register.php" class="nav-link cf">Đăng ký</a>';
                    }
                    ?>
                </div>
                <div class="cart-container">
                    <a href="cart.php" class="nav-link cf">Cart( 
                        <?php
                        // Cập nhật số lượng giỏ hàng
                        if (isset($_SESSION['user_id'])) {
                            $customer_id = $_SESSION['user_id'];
                            $stmt = $conn->prepare("SELECT COUNT(product_id) AS total_products FROM cart WHERE customer_id = :customer_id");
                            $stmt->bindParam(':customer_id', $customer_id);
                            $stmt->execute();
                            $result = $stmt->fetch();
                            echo $result['total_products'] ? $result['total_products'] : 0; // Hiển thị số lượng sản phẩm trong giỏ hàng
                        } else {
                            echo "0"; // Hiển thị 0 nếu người dùng chưa đăng nhập
                        }
                        ?>
                    )</a> <!-- Hiển thị số lượng trong giỏ hàng -->
                </div>
            </div>
        </div>
    </header>
</body>
</html>
