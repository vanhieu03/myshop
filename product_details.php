<?php
include 'config/db.php';
include 'includes/header.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['id'])) {
    $product_id = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = :id");
    $stmt->bindParam(':id', $product_id);
    $stmt->execute();
    $product = $stmt->fetch();

    if ($product) {
        // Kiểm tra và cập nhật số lượng
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        if (isset($_POST['decrease']) && $quantity > 1) {
            $quantity--;
        }
        if (isset($_POST['increase'])) {
            $quantity++;
        }

        echo "<div class='product-container mw12 mla mra mt20p'>";
        echo "<div class='df'>";
        echo "<div class='img-wrapper'>";
        echo "<img class='product-image' src='assets/images/" . htmlspecialchars($product['image_url']) . "' alt='" . htmlspecialchars($product['product_name']) . "'>";
        echo "</div>";
        echo "<div class='ml50p'>";
        echo "<span class='product-title fs20 fw5 lh24p'>" . htmlspecialchars($product['product_name']) . "</span>";
        echo "<p class='product-description'>" . htmlspecialchars($product['description']) . "</p>";
        echo "<p class='product-price'>Giá: ₫" . htmlspecialchars($product['price']) . "</p>";
        
        // Form để điều chỉnh số lượng
        echo "<form action='' method='POST'>";
        echo "<div class='mt20p mb20p'>";
        echo "Số lượng: ";
        echo "<input type='hidden' name='product_id' value='" . htmlspecialchars($product['product_id']) . "'>";
        echo "<input type='hidden' name='quantity' value='" . htmlspecialchars($quantity) . "'>";

        // Nút giảm số lượng
        echo "<button type='submit' name='decrease' class='quantity-button'>-</button>";

        // Hiển thị số lượng
        // Trường nhập số lượng
        echo "<input type='number' id='quantity' name='quantity' value='" . htmlspecialchars($quantity) . "' min='1' class='quantity-input'>";

        // Nút tăng số lượng
        echo "<button type='submit' name='increase' class='quantity-button'>+</button>";
        echo "</div>";
        echo "<button type='submit' name='add_to_cart' class='add-to-cart-button'>Mua ngay</button>";
        echo "</form>";

        echo "</div>";
        echo "</div>";
        echo "</div>";
    } else {
        echo "<div class='error-message'>Không tìm thấy sản phẩm.</div>";
    }

    if (isset($_POST['add_to_cart']) && isset($_SESSION['user_id'])) {
        $customer_id = $_SESSION['user_id'];
        $product_id = $_POST['product_id'];
        $quantity = $_POST['quantity'];

        $stmt = $conn->prepare("SELECT * FROM cart WHERE customer_id = :customer_id AND product_id = :product_id");
        $stmt->bindParam(':customer_id', $customer_id);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();
        $cart_item = $stmt->fetch();

        if ($cart_item) {
            $new_quantity = $cart_item['quantity'] + $quantity;
            $stmt = $conn->prepare("UPDATE cart SET quantity = :quantity WHERE customer_id = :customer_id AND product_id = :product_id");
            $stmt->bindParam(':quantity', $new_quantity);
            $stmt->bindParam(':customer_id', $customer_id);
            $stmt->bindParam(':product_id', $product_id);
            $stmt->execute();
        } else {
            $stmt = $conn->prepare("INSERT INTO cart (customer_id, product_id, quantity) VALUES (:customer_id, :product_id, :quantity)");
            $stmt->bindParam(':customer_id', $customer_id);
            $stmt->bindParam(':product_id', $product_id);
            $stmt->bindParam(':quantity', $quantity);
            $stmt->execute();
        }

        header("Location: product_details.php?id=" . $product_id);
        exit;
    }
} else {
    echo "<div class='error-message'>ID sản phẩm không hợp lệ.</div>";
}

include 'includes/footer.php';
?>
