<?php
session_start();
session_destroy(); // Hủy phiên
header("Location: index.php"); // Chuyển hướng về trang chính
exit();
?>
