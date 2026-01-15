<?php

include __DIR__ . '/../config/db.php';


if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'test'])) {
    echo "Bạn không thể đăng tin.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $imageName = '';

    $author_id  = $_SESSION['user_id'] ?? null;

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $imageName;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            echo '<div class="container mt-4"><div class="alert alert-danger">Lỗi khi tải ảnh lên.</div></div>';
            exit;
        }
    }

    
    $stmt = $conn->prepare("INSERT INTO news (title, content, image,author_id) VALUES (?, ?, ?,?)");
    $stmt->bind_param("sssi", $title, $content, $imageName,$author_id);

    if ($stmt->execute()) {
        echo '<div class="container mt-4"><div class="alert alert-success">Đăng tin thành công</div></div>';
    } else {
        echo '<div class="container mt-4"><div class="alert alert-danger">Lỗi khi lưu tin: ' . htmlspecialchars($stmt->error) . '</div></div>';
    }

    $stmt->close();
    $conn->close();
}
?>
<div class="container mt-4">
     <h2>Thêm Tin Tức Mới</h2>
      <form method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="title">Tiêu đề</label>
            <input type="text" class="form-control" id="title" name="title" required>
            </div> 
        <div class="form-group">
            <label for="content">Nội dung</label>
            <textarea class="form-control" id="content" name="content" rows="6" required></textarea>
            </div> 
        <div class="form-group">
            <label for="image">Ảnh minh họa (tuỳ chọn)</label> 
            <input type="file" class="form-control-file" id="image" name="image" accept="image/*"> 
        </div> 
        <button type="submit" class="btn btn-success">Đăng tin</button> 
    </form> 
</div>