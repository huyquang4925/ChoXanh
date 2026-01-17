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

<style>
body {
    background-color: #ffffff;
    font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
    color: #1f2937;
    line-height: 1.6;
    }
    .container {
        max-width: 960px;
        margin: 2rem auto;
        padding: 0 1rem;
    } 
    .container h2 {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 1.25rem;
        color: #d32f2f;
    } 
    .card {
        background-color: #ffffff;
        border: 1px solid #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        margin-bottom: 1.5rem;
        transition: box-shadow 0.2s ease;
    }
    .card:hover {
        box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    } 
    .card-body {
        padding: 1rem 1.25rem;
    } 
    .card-title { 
        display: flex; 
        justify-content: space-between; 
        align-items: center; font-size: 1.25rem; 
        font-weight: bold; margin-bottom: 0.75rem; 
        color: #1f2937; 
        }
    .card-text { 
        font-size: 1rem;
        color: #374151; 
        margin-bottom: 0.75rem; 
        } 
    .card-body img {
        display: block; 
        max-width: 100%; 
        height: auto; 
        border-radius: 8px; 
        margin-bottom: 1rem; 
    } 
    .text-muted { 
        color: #6b7280 !important; 
        font-size: 0.875rem; 
    }
    .alert { 
        border-radius: 8px; 
        padding: 0.75rem 1rem; 
        margin-bottom: 1rem; 
        } 
    .alert-info { 
        background-color: #e3f2fd;
        color: #0d47a1; 
        border: 1px solid #90caf9; 
    } 
    .btn-danger { 
        background-color: #d32f2f; 
        border: none; 
        color: #fff; 
        padding: 0.375rem 0.75rem; 
        border-radius: 6px; font-size: 0.875rem; 
        cursor: pointer; 
        transition: background-color 0.2s ease, transform 0.1s ease; 
    } 
    .btn-danger:hover { 
        background-color: #b71c1c; 
        transform: translateY(-1px); 
    } 
    .btn-danger:active { 
        transform: translateY(0);
    } 
    
    .container:hover {
    box-shadow: 0 12px 24px rgba(0, 0, 0, 0.12);
    transform: translateY(-2px);
    transition: all 0.3s ease;
}


    .btn-success {
    background-color: #388e3c;
    border: none;
    color: #fff;
    font-weight: bold;
    border-radius: 6px;
    padding: 0.5rem 1rem;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
}

    .btn-success:hover {
    background-color: #2e7d32;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    transform: translateY(-1px);
}

</style>
