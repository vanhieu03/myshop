<?php
include 'includes/header.php';
include 'config/db.php';

// Lấy danh sách các danh mục
$stmt1 = $conn->prepare("SELECT DISTINCT categories.name, categories.category_id FROM categories");
$stmt1->execute();
$categories = $stmt1->fetchAll();

// Kiểm tra nếu có truy vấn tìm kiếm từ thanh tìm kiếm
$searchQuery = ''; // Khai báo biến mặc định là chuỗi rỗng
if (isset($_GET['query']) && !empty($_GET['query'])) {
    $searchQuery = $_GET['query'];
    // Chuẩn bị câu truy vấn có điều kiện tìm kiếm
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_name LIKE :search_query OR description LIKE :search_query");
    $searchParam = '%' . $searchQuery . '%';
    $stmt->bindParam(':search_query', $searchParam);
} else {
    // Nếu không có từ khóa tìm kiếm, hiển thị tất cả sản phẩm
    $stmt = $conn->prepare("SELECT * FROM products");
}

// Chuẩn bị câu truy vấn cho sản phẩm
$sql = "SELECT * FROM products";

// Kiểm tra nếu có checkbox được chọn
if (isset($_GET['categories']) && !empty($_GET['categories'])) {
    // Lọc theo danh mục
    $selectedCategories = $_GET['categories'];
    // Tạo placeholder cho câu truy vấn
    $placeholders = implode(',', array_fill(0, count($selectedCategories), '?'));
    $sql .= " WHERE category_id IN ($placeholders)";
}

$stmt = $conn->prepare($sql);

// Bind giá trị cho các placeholder nếu có lọc theo danh mục
if (isset($_GET['categories']) && !empty($_GET['categories'])) {
    foreach ($selectedCategories as $index => $category) {
        $stmt->bindValue($index + 1, $category, PDO::PARAM_INT); // Gắn giá trị cho từng category_id
    }
}

$stmt->execute();
$products = $stmt->fetchAll();
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
    <div class="product-wapper jcc">
        <div class="mw12 df">
            <div class="menu-left">
                <div class="mt20p mb20p fs20">Theo danh mục</div>
                <form action="index.php" method="GET">
                    <?php if (!empty($categories)): ?>
                        <ul>
                            <?php foreach ($categories as $category): ?>
                                <li>
                                    <input type="checkbox" class="checkbox-input" id="category_<?php echo htmlspecialchars($category['name']); ?>" name="categories[]" value="<?php echo htmlspecialchars($category['category_id']); ?>" 
                                    <?php if (isset($_GET['categories']) && in_array($category['category_id'], $_GET['categories'])) echo 'checked'; ?>>
                                    <label for="category_<?php echo htmlspecialchars($category['name']); ?>"><?php echo htmlspecialchars($category['name']); ?></label>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No categories found.</p>
                    <?php endif; ?>
                    <button type="submit" class="filter-btn">Lọc</button>
                </form>
            </div>
            <div class="product-collection">
                <?php if (!empty($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <div class="single-product">
                            <img src="assets/images/<?php echo htmlspecialchars($product['image_url']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" class="image-display">
                            <h2 class="title-display"><?php echo htmlspecialchars($product['product_name']); ?></h2>
                            <p class="price-display">Giá: ₫<?php echo htmlspecialchars($product['price']); ?></p>
                            <a class="details-link" href="product_details.php?id=<?php echo $product['product_id']; ?>">Chi tiết</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No products found<?php echo $searchQuery ? ' for "' . htmlspecialchars($searchQuery) . '"' : ''; ?>.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

<?php include 'includes/footer.php'; ?>
