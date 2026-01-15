<?php
if (session_status() == PHP_SESSION_NONE) session_start();

// Kiểm tra đăng nhập (dùng $_SESSION['user_id'] từ code gốc)
if (!isset($_SESSION['user_id'])) {
    echo '<div style="color:#e74c3c; padding:30px; background:white; border-radius:12px; text-align:center; margin:20px;">
            <h2>❌ Vui lòng đăng nhập!</h2>
            <a href="index.php?page=login" style="color:#3498db; font-weight:bold;">→ Đăng nhập ngay</a>
          </div>';
} else {
    $user_id = $_SESSION['user_id'];
include __DIR__ . '/../config/db.php';
    $message = '';
    $error = '';
    
    // Lấy thông tin user
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    // Kiểm tra profile hoàn thiện
    $is_complete = !empty($user['phone']) && !empty($user['NgaySinh']) && !empty($user['DiaChi']);
    
    // Role tiếng Việt
    $role_vi = 'Người dùng';
    if (isset($user['role'])) {
        if ($user['role'] == 'admin') $role_vi = 'Quản trị viên';
        elseif ($user['role'] == 'user') $role_vi = 'Người dùng';
    }
    
    // Format ngày sinh
    $ngaysinh_display = isset($user['NgaySinh']) && $user['NgaySinh'] ? 
                       date('d/m/Y', strtotime($user['NgaySinh'])) : 'Chưa cập nhật';
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
.profile-grid { 
    display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); 
    gap: 25px; 
}
.profile-card { 
    background: white; padding: 35px; border-radius: 16px; 
    box-shadow: 0 8px 25px rgba(0,0,0,0.08); border-top: 4px solid #3498db;
}
.card-title { 
    font-size: 22px; color: #2c3e50; margin-bottom: 25px; 
    display: flex; align-items: center; gap: 12px;
}
.info-item { display: flex; margin-bottom: 20px; align-items: center; }
.info-label { 
    font-weight: bold; width: 150px; color: #7f8c8d; font-size: 16px; 
}
.info-value { 
    flex: 1; color: #2c3e50; padding: 12px 18px; 
    background: linear-gradient(90deg, #f8f9fa 0%, #e9ecef 100%); 
    border-radius: 10px; border-left: 5px solid #3498db; 
    font-size: 16px; box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);
}
.profile-status {
    padding: 15px; border-radius: 10px; margin-bottom: 25px; text-align: center;
    font-weight: bold; font-size: 16px;
}
.status-complete { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.status-incomplete { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
@media (max-width: 768px) { 
    .content-wrapper { flex-direction: column; } 
    .sidebar { width: 100%; }
}
</style>

<?php if (!isset($user) || !$user): ?>
<div style="color:#e74c3c; padding:30px; background:white; border-radius:12px; text-align:center; margin:20px;">
    <h2>❌ Không tìm thấy thông tin user</h2>
    <p>User ID: <?= htmlspecialchars($user_id ?? 'N/A') ?></p>
</div>
<?php else: ?>

<div class="content-wrapper">
    <!-- LEFT TOP: Setting -->
    <div class="sidebar">
        <div class="setting-title">⚙️ Setting</div>
        <div class="menu">
            <ul>
                <li class="active"><a href="index.php?page=profile">• Trang cá nhân</a></li>
                <li><a href="index.php?page=edit_profile">• Thay đổi thông tin</a></li>
                <li><a href="index.php?page=lichsu">• Lịch sử mua hàng</a></li>
            </ul>
        </div>
    </div>

    <!-- CONTENT: Username + Role + Profile READ-ONLY -->
    <div class="main-content">
        <div class="user-header">
            <h1><?= htmlspecialchars($user['username']) ?></h1>
            <div class="user-role">Vai trò: <?= $role_vi ?></div>
        </div>

        <!-- Profile hoàn thiện status -->
        <div class="profile-status <?= $is_complete ? 'status-complete' : 'status-incomplete' ?>">
            <?= $is_complete ? '✅ Profile đã hoàn thiện' : '⚠️ Vui lòng hoàn thiện thông tin cá nhân' ?>
        </div>

        <div class="profile-grid">
            <!-- Card 1: Liên lạc -->
            <div class="profile-card">
                <div class="card-title">📧 Thông tin liên lạc</div>
                <div class="info-item">
                    <div class="info-label">ID:</div>
                    <div class="info-value"><?= $user['id'] ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Email:</div>
                    <div class="info-value"><?= htmlspecialchars($user['email']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Số ĐT:</div>
                    <div class="info-value"><?= htmlspecialchars($user['phone'] ?: 'Chưa cập nhật') ?></div>
                </div>
            </div>

            <!-- Card 2: Cá nhân -->
            <div class="profile-card">
                <div class="card-title"> Thông tin cá nhân</div>
                <div class="info-item">
                    <div class="info-label">Tên đăng nhập:</div>
                    <div class="info-value"><?= htmlspecialchars($user['username']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Ngày sinh:</div>
                    <div class="info-value"><?= $ngaysinh_display ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">Địa chỉ:</div>
                    <div class="info-value"><?= htmlspecialchars($user['DiaChi'] ?: 'Chưa cập nhật') ?></div>
                </div>
            </div>
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
