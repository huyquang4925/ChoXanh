<?php
require_once __DIR__ . '/../core/Controller.php';

/**
 * Staff Controller
 */
class StaffController extends Controller {
    private $userModel;
    
    public function __construct() {
        parent::__construct();
        $this->userModel = $this->model('UserModel');
    }
    
    /**
     * Admin staff list
     */
    public function admin() {
        $this->requireAdmin();
        
        $staff = $this->userModel->getStaff();
        
        $this->view('staff/admin', [
            'staff' => $staff
        ]);
    }
    
    /**
     * Add staff
     */
    public function add() {
        $this->requireAdmin();
        
        $error = '';
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $this->post('username');
            $email = $this->post('email');
            $password = $this->post('password');
            
            if (empty($username) || empty($email) || empty($password)) {
                $error = 'Vui lòng điền đầy đủ thông tin';
            } elseif ($this->userModel->exists($username, $email)) {
                $error = 'Tên đăng nhập hoặc email đã tồn tại';
            } else {
                $result = $this->userModel->register($username, $email, $password, 'admin');
                
                if ($result) {
                    $this->redirect('index.php?page=admin_staff');
                } else {
                    $error = 'Thêm nhân viên thất bại';
                }
            }
        }
        
        $this->view('staff/add', [
            'error' => $error,
            'success' => $success
        ]);
    }
    
    /**
     * Edit staff
     */
    public function edit() {
        $this->requireAdmin();
        
        $id = $this->get('id', 0);
        $staff = $this->userModel->findById($id);
        
        if (!$staff) {
            $this->redirect('index.php?page=admin_staff');
        }
        
        $error = '';
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $this->post('email');
            $phone = $this->post('phone', '');
            $password = $this->post('password');
            
            $data = [
                'email' => $email,
                'phone' => $phone
            ];
            
            if (!empty($password)) {
                $data['password'] = $password;
            }
            
            $result = $this->userModel->updateProfile($id, $data);
            
            if ($result) {
                $success = 'Cập nhật thành công';
                $staff = $this->userModel->findById($id);
            } else {
                $error = 'Cập nhật thất bại';
            }
        }
        
        $this->view('staff/edit', [
            'staff' => $staff,
            'error' => $error,
            'success' => $success
        ]);
    }
    
    /**
     * Delete staff
     */
    public function delete() {
        $this->requireAdmin();
        
        $id = $this->get('id', 0);
        $user = $this->userModel->findById($id);
        
        // Don't delete yourself
        if ($id == $this->getUserId()) {
            echo '<script>alert("Không thể xóa chính mình!"); history.back();</script>';
            exit;
        }
        
        if (!$user) {
            echo '<script>alert("Không tìm thấy người dùng!"); history.back();</script>';
            exit;
        }
        
        // If confirmed, delete the user
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->post('confirm')) {
            $result = $this->userModel->deleteUser($id);
            if ($result) {
                echo '<script>alert("Xóa người dùng thành công!"); window.location.href="index.php?page=admin_staff";</script>';
            } else {
                echo '<script>alert("Xóa người dùng thất bại!"); history.back();</script>';
            }
            exit;
        }
        
        // Show confirmation page
        $this->view('staff/delete', [
            'user' => $user
        ]);
    }
}
?>
