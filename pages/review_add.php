<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id    = $_SESSION['user_id'] ?? null;
    $username   = $_SESSION['username'] ?? 'Ẩn danh';
    $product_id = isset($_POST['product_id']) ? intval($_POST['product_id']) : 0;
    $rating     = isset($_POST['rating']) ? intval($_POST['rating']) : 0;
    $comment    = trim($_POST['comment'] ?? '');

    
    if (!$user_id || $product_id <= 0 || $rating <= 0 || $comment === '') {
        header("Location: index.php?page=product&id=$product_id&error=missing");
        exit;
    }

   
    $stmt = $conn->prepare(
        "INSERT INTO reviews (user_id, username, product_id, rating, comment, created_at)
         VALUES (?, ?, ?, ?, ?, NOW())"
    );

    if ($stmt) {
        $stmt->bind_param("isiss", $user_id, $username, $product_id, $rating, $comment);
        $stmt->execute();
        $stmt->close();
    }

    $conn->close();

    echo "<script>window.location.href = 'index.php?page=product&id=$product_id';</script>";
    exit;

}
