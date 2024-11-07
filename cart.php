<?php
session_start();
include 'config/db.php';
include 'includes/header.php';

if (isset($_SESSION['user_id'])) {
    $customer_id = $_SESSION['user_id'];
    
    // Lấy tất cả sản phẩm trong giỏ hàng, bao gồm cả đường dẫn hình ảnh
    $stmt = $conn->prepare("SELECT c.quantity, p.product_name, p.price, p.product_id, p.image_url FROM cart c JOIN products p ON c.product_id = p.product_id WHERE c.customer_id = :customer_id");
    $stmt->bindParam(':customer_id', $customer_id);
    $stmt->execute();
    $cart_items = $stmt->fetchAll();

    echo "<h2>Giỏ hàng</h2>";
    
    $total_price = 0;

    if ($cart_items) {
        foreach ($cart_items as $item) {
            echo "<div class='cart-item'>";
            echo "<div class='df aic col-3'>";
            // Hiển thị hình ảnh sản phẩm
            echo "<img src='assets/images/" . htmlspecialchars($item['image_url']) . "' alt='" . htmlspecialchars($item['product_name']) . "' class='cart-item-image mr20p'>";
            echo "<p>" . htmlspecialchars($item['product_name']) . " - ₫" . htmlspecialchars($item['price']) . "</p>";
            echo "</div>";
            
            
            // Quantity control form
            echo "<form action='cart.php' method='POST' style='display:flex; align-items:center;'>";
            echo "<input type='hidden' name='product_id' value='" . htmlspecialchars($item['product_id']) . "'>";
            echo "<button type='submit' name='decrease' class='quantity-button'>-</button>";
            echo "<input type='number' name='quantity' value='" . htmlspecialchars($item['quantity']) . "' min='1' class='quantity-input'>";
            echo "<button type='submit' name='increase' class='quantity-button'>+</button>";
            echo "</form>";

            // Nút xóa sản phẩm
            echo "<form action='cart.php' method='POST' style='display:inline;'>";
            echo "<input type='hidden' name='remove_product_id' value='" . htmlspecialchars($item['product_id']) . "'>";
            echo "<button type='submit' class='remove-button'>Xóa sản phẩm</button>";
            echo "</form>";

            echo "</div>";

            $total_price += $item['price'] * $item['quantity'];
        }
        
        // Display total price
        echo "<h3>Tổng tiền: ₫" . number_format($total_price, 2) . "</h3>"; 
    } else {
        echo "<p>Giỏ hàng của bạn còn trống.</p>";
    }

    // Handle increase and decrease actions
    if (isset($_POST['product_id'])) {
        $product_id = $_POST['product_id'];
        $current_quantity = (int) $_POST['quantity'];

        if (isset($_POST['increase'])) {
            $current_quantity++;
        } elseif (isset($_POST['decrease']) && $current_quantity > 1) {
            $current_quantity--;
        }

        // Update the quantity in the database
        $stmt = $conn->prepare("UPDATE cart SET quantity = :quantity WHERE customer_id = :customer_id AND product_id = :product_id");
        $stmt->bindParam(':quantity', $current_quantity);
        $stmt->bindParam(':customer_id', $customer_id);
        $stmt->bindParam(':product_id', $product_id);
        $stmt->execute();

        // Redirect to refresh the cart display
        header("Location: cart.php");
        exit();
    }

    // Handle product removal
    if (isset($_POST['remove_product_id'])) {
        $remove_product_id = $_POST['remove_product_id'];

        $stmt = $conn->prepare("DELETE FROM cart WHERE customer_id = :customer_id AND product_id = :product_id");
        $stmt->bindParam(':customer_id', $customer_id);
        $stmt->bindParam(':product_id', $remove_product_id);
        $stmt->execute();

        // Update cart quantity
        $stmt = $conn->prepare("SELECT COUNT(product_id) AS total_products FROM cart WHERE customer_id = :customer_id");
        $stmt->bindParam(':customer_id', $customer_id);
        $stmt->execute();
        $result = $stmt->fetch();
        $_SESSION['cart_quantity'] = $result['total_products'] ? $result['total_products'] : 0;

        // Redirect to refresh the cart display
        header("Location: cart.php");
        exit();
    }

} else {
    echo "<p>Vui lòng đăng nhập để xem giỏ hàng của bạn.</p>";
}

include 'includes/footer.php';
?>
