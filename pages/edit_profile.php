<?php
if (session_status() == PHP_SESSION_NONE) session_start();

// Kiểm tra đăng nhập AN TOÀN - không dùng header()
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    echo '<div style="color:#e74c3c; padding:30px; background:white; border-radius:12px; text-align:center; margin:20px;">
            <h2>❌ Vui lòng đăng nhập!</h2>
            <a href="index.php?page=login" style="color:#3498db; font-weight:bold;">→ Đăng nhập ngay</a>
          </div>';
    exit;
}

$user_id = $_SESSION['user_id'];
include __DIR__ . '/../config/db.php';
$message = '';
$error = '';
$user = null; // Khởi tạo trước

// Lấy thông tin user
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Role tiếng Việt
$role_vi = 'Người dùng';
if ($user && isset($user['role'])) {
    if ($user['role'] == 'admin') $role_vi = 'Quản trị viên';
    elseif ($user['role'] == 'user') $role_vi = 'Người dùng';
}

// Kiểm tra profile hoàn thiện
$is_complete = $user && !empty($user['phone']) && !empty($user['NgaySinh']) && !empty($user['DiaChi']);

// Xử lý form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user) {
    $phone = trim($_POST['phone'] ?? '');
    $ngaysinh = trim($_POST['ngaysinh'] ?? '');
    $diachi = trim($_POST['diachi'] ?? '');
    $new_password = trim($_POST['new_password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    
    // Validate
    $errors = [];
    if (empty($phone)) $errors[] = "Số điện thoại không được để trống";
    elseif (!preg_match('/^[0-9]{10,11}$/', $phone)) $errors[] = "Số điện thoại không hợp lệ (10-11 số)";
    if (empty($ngaysinh)) $errors[] = "Ngày sinh không được để trống";
    if (empty($diachi)) $errors[] = "Địa chỉ không được để trống";
    if (!empty($new_password)) {
        if (strlen($new_password) < 3) $errors[] = "Mật khẩu mới phải có ít nhất 3 ký tự";
        if ($new_password !== $confirm_password) $errors[] = "Mật khẩu xác nhận không khớp";
    }
    
    if (empty($errors)) {
        if (!empty($new_password)) {
            $sql_update = "UPDATE users SET phone = ?, NgaySinh = ?, DiaChi = ?, password = ? WHERE id = ?";
            $stmt = $conn->prepare($sql_update);
            $stmt->bind_param('ssssi', $phone, $ngaysinh, $diachi, $new_password, $user_id);
        } else {
            $sql_update = "UPDATE users SET phone = ?, NgaySinh = ?, DiaChi = ? WHERE id = ?";
            $stmt = $conn->prepare($sql_update);
            $stmt->bind_param('sssi', $phone, $ngaysinh, $diachi, $user_id);
        }
        
        if ($stmt->execute()) {
            $message = "Cập nhật thông tin thành công!";
            // Reload user data
            $stmt2 = $conn->prepare("SELECT * FROM users WHERE id = ?");
            $stmt2->bind_param('i', $user_id);
            $stmt2->execute();
            $result = $stmt2->get_result();
            $user = $result->fetch_assoc();
            $is_complete = true;
        } else {
            $error = "Có lỗi xảy ra. Vui lòng thử lại!";
        }
    } else {
        $error = implode('<br>', $errors);
    }
}
?>

<style>
.content-wrapper { display: flex; min-height: 70vh; gap: 20px; }
.sidebar { 
    width: 260px; background: linear-gradient(145deg, #2c3e50, #34495e); 
    color: white; padding: 25px 0; border-radius: 12px; box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}
.setting-title { 
    padding: 25px 20px; font-size: 22px; font-weight: bold; 
    border-bottom: 2px solid #3498db; text-align: center; 
    background: rgba(255,255,255,0.1);
}
.menu ul { list-style: none; margin-top: 20px; }
.menu li { padding: 18px 25px; cursor: pointer; transition: all 0.3s; border-left: 3px solid transparent; }
.menu li:hover, .menu li.active { background: rgba(255,255,255,0.15); border-left-color: #3498db; }
.menu a { color: white; text-decoration: none; display: block; font-size: 15px; }
.main-content { flex: 1; padding: 25px; background: #f8f9fa; border-radius: 12px; }
.user-header { 
    background: white; padding: 30px; border-radius: 16px; 
    box-shadow: 0 10px 30px rgba(0,0,0,0.1); margin-bottom: 30px; 
    border-left: 5px solid #3498db;
}
.user-header h1 { color: #2c3e50; margin-bottom: 12px; font-size: 32px; }
.user-role { 
    color: #27ae60; font-weight: bold; font-size: 18px; 
    background: rgba(39,174,96,0.1); padding: 8px 16px; 
    border-radius: 20px; display: inline-block;
}
.profile-card { 
    background: white; padding: 35px; border-radius: 16px; 
    box-shadow: 0 8px 25px rgba(0,0,0,0.08); border-top: 4px solid #3498db;
}
.card-title { 
    font-size: 22px; color: #2c3e50; margin-bottom: 25px; 
    display: flex; align-items: center; gap: 12px;
}
.form-group { margin-bottom: 25px; }
.form-label { font-weight: bold; color: #7f8c8d; margin-bottom: 8px; display: block; }
.form-control { 
    width: 100%; padding: 12px 15px; border: 2px solid #e0e0e0; 
    border-radius: 8px; font-size: 16px; transition: all 0.3s;
}
.form-control:focus { outline: none; border-color: #3498db; box-shadow: 0 0 0 3px rgba(52,152,219,0.1); }
.btn { padding: 12px 30px; border-radius: 8px; font-weight: bold; border: none; cursor: pointer; font-size: 16px; }
.btn-primary { background: #3498db; color: white; }
.btn-primary:hover { background: #2980b9; transform: translateY(-2px); }
.btn-secondary { background: #95a5a6; color: white; }
.alert { padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; }
.alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.alert-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
</style>

<?php if (!$user): ?>
<div style="color:#e74c3c; padding:30px; background:white; border-radius:12px; text-align:center; margin:20px;">
    <h2>❌ Không tìm thấy thông tin user</h2>
    <p>User ID: <?= htmlspecialchars($user_id) ?> không tồn tại trong database</p>
    <a href="index.php?page=profile" class="btn btn-secondary" style="margin-top:15px;">← Quay lại</a>
</div>
<?php else: ?>

<div class="content-wrapper">
    <!-- Sidebar Setting -->
    <div class="sidebar">
        <div class="setting-title">⚙️ Setting</div>
        <div class="menu">
            <ul>
                <li><a href="index.php?page=profile">• Trang cá nhân</a></li>
                <li class="active"><a href="index.php?page=edit_profile">• Thay đổi thông tin</a></li>
                <li><a href="index.php?page=lichsu">• Lịch sử mua hàng</a></li>
            </ul>
        </div>
    </div>

    <!-- Main content -->
    <div class="main-content">
        <div class="user-header">
            <h1><?= htmlspecialchars($user['username']) ?></h1>
            <div class="user-role">Vai trò: <?= $role_vi ?></div>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-success"><?= $message ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <div class="profile-card" style="max-width: 600px;">
            <div class="card-title">✏️ Cập nhật thông tin cá nhân</div>
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Số điện thoại <span style="color:#e74c3c;">*</span></label>
                    <input type="text" name="phone" class="form-control" 
                           value="<?= htmlspecialchars($user['phone'] ?? '') ?>" required 
                           placeholder="0123456789">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Ngày sinh <span style="color:#e74c3c;">*</span></label>
                    <input type="date" name="ngaysinh" class="form-control" 
                           value="<?= $user['NgaySinh'] ?? '' ?>" required max="<?= date('Y-m-d') ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Địa chỉ <span style="color:#e74c3c;">*</span></label>
                    <textarea name="diachi" class="form-control" rows="3" required 
                              placeholder="Nhập địa chỉ đầy đủ"><?= htmlspecialchars($user['DiaChi'] ?? '') ?></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Mật khẩu mới (để trống nếu không đổi)</label>
                    <input type="password" name="new_password" class="form-control" minlength="3">
                    <small style="color:#7f8c8d;">Để trống nếu không muốn đổi mật khẩu</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Xác nhận mật khẩu mới</label>
                    <input type="password" name="confirm_password" class="form-control">
                </div>
                
                <div style="display: flex; gap: 15px; flex-wrap: wrap;">
                    <button type="submit" class="btn btn-primary">💾 Cập nhật thông tin</button>
                    <a href="index.php?page=profile" class="btn btn-secondary">← Xem trang cá nhân</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php endif; ?>

<script>
document.querySelectorAll('.menu a').forEach(link => {
    link.addEventListener('click', e => {
        document.querySelector('.menu li.active')?.classList.remove('active');
        link.parentElement.classList.add('active');
    });
});
</script>
