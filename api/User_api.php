<?php
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Model.php';
require_once __DIR__ . '/../core/Api.php';
require_once __DIR__ . '/../models/UserModel.php';

class UserApi extends Api {
    private $userModel;

    public function __construct() {
        parent::__construct();
        $this->userModel = new UserModel();
    }

    public function handleRequest() {
        $action = $this->getParam('action');

        switch ($action) {

            // Lấy danh sách nhân viên
            case 'list':
                $users = $this->userModel->getStaff();
                $this->response(true, $users);
                break;

            // Lấy danh sách khách hàng
            case 'customers':
                $customers = $this->userModel->getCustomers();
                $this->response(true, $customers);
                break;

            // Lấy thông tin một người dùng theo id
            case 'get':
                $id = intval($this->getParam('id', 0));
                if ($id <= 0) {
                    $this->response(false, [], "ID không hợp lệ");
                }
                $user = $this->userModel->findById($id);
                if ($user) {
                    unset($user['password']); // không trả password ra ngoài
                    $this->response(true, $user);
                } else {
                    $this->response(false, [], "Không tìm thấy người dùng");
                }
                break;

            // Thêm người dùng mới
            case 'add':
                $username = trim($this->input['username'] ?? '');
                $email    = trim($this->input['email']    ?? '');
                $password = trim($this->input['password'] ?? '');
                $phone    = trim($this->input['phone']    ?? '');
                $role     = trim($this->input['role']     ?? 'customer');

                if (empty($username) || empty($email) || empty($password)) {
                    $this->response(false, [], "Vui lòng điền đầy đủ username, email và password");
                }

                if ($this->userModel->exists($username, $email)) {
                    $this->response(false, [], "Username hoặc email đã tồn tại");
                }

                $newId = $this->userModel->insert([
                    'username' => $username,
                    'email'    => $email,
                    'password' => $password,
                    'phone'    => $phone,
                    'role'     => $role
                ]);

                if ($newId) {
                    $this->response(true, ['id' => $newId], "Thêm người dùng thành công");
                } else {
                    $this->response(false, [], "Thêm người dùng thất bại");
                }
                break;

            // Cập nhật thông tin người dùng
            case 'update':
                $id = intval($this->input['id'] ?? 0);
                if ($id <= 0) {
                    $this->response(false, [], "ID không hợp lệ");
                }

                $data = [];
                if (!empty($this->input['username'])) $data['username'] = trim($this->input['username']);
                if (!empty($this->input['email']))    $data['email']    = trim($this->input['email']);
                if (!empty($this->input['phone']))    $data['phone']    = trim($this->input['phone']);
                if (!empty($this->input['role']))     $data['role']     = trim($this->input['role']);
                if (!empty($this->input['password'])) $data['password'] = trim($this->input['password']);

                if (empty($data)) {
                    $this->response(false, [], "Không có dữ liệu để cập nhật");
                }

                $success = $this->userModel->updateProfile($id, $data);
                $this->response($success, [], $success ? "Cập nhật thành công" : "Cập nhật thất bại");
                break;

            // Xóa người dùng
            case 'delete':
                $id = intval($this->input['id'] ?? 0);
                if ($id <= 0) {
                    $this->response(false, [], "ID không hợp lệ");
                }

                $success = $this->userModel->deleteUser($id);
                $this->response($success, [], $success ? "Xóa thành công" : "Xóa thất bại");
                break;

            default:
                $this->response(false, [], "Hành động không hợp lệ");
                break;
        }
    }
}

$api = new UserApi();
$api->handleRequest();
