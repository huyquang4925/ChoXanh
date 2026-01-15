<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include __DIR__ . '/../config/db.php';

$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
function safe_redirect($url) {
    if (!headers_sent()) {
        header('Location: ' . $url);
        exit;
    }
    echo '<script>window.location.href = "' . htmlspecialchars($url, ENT_QUOTES) . '";</script>';
    echo '<noscript><meta http-equiv="refresh" content="0;url=' . htmlspecialchars($url, ENT_QUOTES) . '" /></noscript>';
    exit;
}

if ($user_id <= 0) {
    safe_redirect('index.php?page=login');
}

$product_id = isset($_REQUEST['product_id']) ? intval($_REQUEST['product_id']) : 0;
$quantity = isset($_REQUEST['quantity']) ? intval($_REQUEST['quantity']) : 1;
if ($product_id <= 0) {
    safe_redirect('index.php?page=home');
}

$cart_id = 0;
$res = $conn->query("SELECT id FROM carts WHERE user_id = $user_id LIMIT 1");
if ($res && $res->num_rows > 0) {
    $row = $res->fetch_assoc();
    $cart_id = intval($row['id']);
} else {
    $conn->query("INSERT INTO carts (user_id) VALUES ($user_id)");
    $cart_id = $conn->insert_id;
}

// check if product already in cart
$res = $conn->query("SELECT id, quantity FROM cart_items WHERE cart_id = $cart_id AND product_id = $product_id LIMIT 1");
if ($res && $res->num_rows > 0) {
    $row = $res->fetch_assoc();
    $cid = intval($row['id']);
    $newq = max(1, intval($row['quantity']) + max(1, $quantity));
    $conn->query("UPDATE cart_items SET quantity = $newq WHERE id = $cid");
} else {
    $q = max(1, $quantity);
    $conn->query("INSERT INTO cart_items (cart_id, product_id, quantity) VALUES ($cart_id, $product_id, $q)");
}

safe_redirect('index.php?page=cart');


?>
