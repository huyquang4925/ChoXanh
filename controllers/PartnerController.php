<?php
require_once __DIR__ . '/../core/Controller.php';

/**
 * Partner Controller
 */
class PartnerController extends Controller {
    private $partnerModel;
    
    public function __construct() {
        parent::__construct();
        $this->partnerModel = $this->model('PartnerModel');
    }
    
    /**
     * Admin partners list
     */
    public function admin() {
        $this->requireAdmin();
        
        $partners = $this->partnerModel->getAllPartners();
        
        $this->view('partner/admin', [
            'partners' => $partners
        ]);
    }
    
    /**
     * Add partner
     */
    public function add() {
        $this->requireAdmin();
        
        $error = '';
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $this->post('name');
            $description = $this->post('description', '');
            
            if (!empty($name)) {
                $result = $this->partnerModel->addPartner($name, $description);
                
                if ($result) {
                    $this->redirect('index.php?page=admin_partners');
                } else {
                    $error = 'Thêm đối tác thất bại';
                }
            } else {
                $error = 'Tên đối tác không được để trống';
            }
        }
        
        $this->view('partner/add', [
            'error' => $error,
            'success' => $success
        ]);
    }
    
    /**
     * Edit partner
     */
    public function edit() {
        $this->requireAdmin();
        
        $id = $this->get('id', 0);
        $partner = $this->partnerModel->getPartner($id);
        
        if (!$partner) {
            $this->redirect('index.php?page=admin_partners');
        }
        
        $error = '';
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $this->post('name');
            $description = $this->post('description', '');
            
            if (!empty($name)) {
                $result = $this->partnerModel->updatePartner($id, $name, $description);
                
                if ($result) {
                    $success = 'Cập nhật thành công';
                    $partner = $this->partnerModel->getPartner($id);
                } else {
                    $error = 'Cập nhật thất bại';
                }
            } else {
                $error = 'Tên đối tác không được để trống';
            }
        }
        
        $this->view('partner/edit', [
            'partner' => $partner,
            'error' => $error,
            'success' => $success
        ]);
    }
    
    /**
     * Delete partner
     */
    public function delete() {
        $this->requireAdmin();
        
        $id = $this->get('id', 0);
        $partner = $this->partnerModel->getPartner($id);
        
        if (!$partner) {
            echo '<script>alert("Không tìm thấy đối tác!"); history.back();</script>';
            exit;
        }
        
        // If confirmed, delete the partner
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $this->post('confirm')) {
            $result = $this->partnerModel->deletePartner($id);
            if ($result) {
                echo '<script>window.location.href="index.php?page=admin_partners";</script>';
            } else {
                echo '<script>alert("Xóa đối tác thất bại!"); history.back();</script>';
            }
            exit;
        }
        
        // Show confirmation page
        $this->view('partner/delete', [
            'partner' => $partner
        ]);
    }
    
    /**
     * Partner detail
     */
    public function detail() {
        $this->requireAdmin();
        
        $id = $this->get('id', 0);
        $partner = $this->partnerModel->getPartner($id);
        
        if (!$partner) {
            echo '<div class="alert alert-danger">Không tìm thấy đối tác!</div>';
            return;
        }
        
        // Get products of this partner
        $productModel = $this->model('ProductModel');
        $products = $productModel->getByManufacturer($id);
        
        $this->view('partner/detail', [
            'partner' => $partner,
            'products' => $products
        ]);
    }
}
?>
