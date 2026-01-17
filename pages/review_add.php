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
?>


<style>

h3, h4 {
    color: #d32f2f;
    font-weight: bold;
    margin-bottom: 1rem;
}

form label {
    font-weight: 600;
    color: #5d4037;
}

.form-control {
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 0.5rem 0.75rem;
    font-size: 1rem;
    background-color: #fff;
    transition: border-color 0.2s ease;
}

.form-control:focus {
    border-color: #ffffff;
    outline: none;
    box-shadow: 0 0 0 2px rgba(251, 192, 45, 0.25);
}

textarea.form-control {
    resize: vertical;
}

.btn-success {
    background-color: #388e3c;
    border: none;
    color: #fff;
    font-weight: bold;
    border-radius: 6px;
    padding: 0.5rem 1rem;
    transition: background-color 0.2s ease;
}

.btn-success:hover {
    background-color: #2e7d32;
}

.review-item {
    background-color: #ffffff;
    border: 1px solid #ffffff;
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 1rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.review-item strong {
    color: #d32f2f;
    font-weight: bold;
}

.review-item .stars {
    color: #fbc02d;
    font-size: 1.1rem;
    margin-left: 0.25rem;
}

.review-item p {
    margin: 0.5rem 0;
    color: #5d4037;
}

.review-item small {
    color: #8d6e63;
    font-size: 0.875rem;
}

.review-item .btn-danger {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
    margin-top: 0.5rem;
}
</style>
