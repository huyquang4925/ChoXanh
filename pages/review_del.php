<?php
if (session_status() == PHP_SESSION_NONE){
    session_start();
}

include __DIR__ . '/../config/db.php';

if (!isset($_SESSION['role'])|| (!in_array($_SESSION['role'],['admin']) && $_SESSION['username'] !== 'test')){
    echo " ban khong co quyen danh gia ";
    exit;
}

$review_id = isset($_GET['id']) ? intval($_GET['id']) : 0 ;
$product_id= isset($_GET['product_id'])? intval($_GET['product_id']) : 0;

if ($review_id >0 ){
    $stmt = $conn-> prepare("DELETE FROM reviews WHERE id= ?");
    $stmt->bind_param("i",$review_id);
    $stmt->execute();
    $stmt->close();
}
$conn->close();

echo "<script>window.location.href = 'index.php?page=product&id=$product_id';</script>";
exit;
?>

