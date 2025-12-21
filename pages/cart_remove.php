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

$cart_item_id = isset($_POST['cart_item_id']) ? intval($_POST['cart_item_id']) : 0;
if ($cart_item_id <= 0) {
    safe_redirect('index.php?page=cart');
}

// Verify ownership
$sql = "SELECT ci.id FROM cart_items ci JOIN carts c ON ci.cart_id = c.id WHERE ci.id = $cart_item_id AND c.user_id = $user_id LIMIT 1";
$res = $conn->query($sql);
if ($res && $res->num_rows > 0) {
    $conn->query("DELETE FROM cart_items WHERE id = $cart_item_id");
}

safe_redirect('index.php?page=cart');


?>
