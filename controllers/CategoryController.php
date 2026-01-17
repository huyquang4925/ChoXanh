<?php
require_once __DIR__ . '/../core/Controller.php';

/**
 * Category Controller
 */
class CategoryController extends Controller {
    private $categoryModel;
    private $productModel;
    
    public function __construct() {
        parent::__construct();
        $this->categoryModel = $this->model('CategoryModel');
        $this->productModel = $this->model('ProductModel');
    }
    
    /**
     * Category page with products
     */
    public function index() {
        $id = $this->get('id', 0);
        $search = $this->get('search', '');
        
        $category = $this->categoryModel->getCategory($id);
        
        // Redirect back if category not found
        if (!$category) {
            echo '<script>history.back();</script>';
            exit;
        }
        
        $products = $this->productModel->getByCategory($id, $search);
        
        $this->view('category/index', [
            'category' => $category,
            'products' => $products,
            'search' => $search,
            'id' => $id,
            'isAdmin' => $this->isAdmin()
        ]);
    }
    
    /**
     * Admin categories list
     */
    public function admin() {
        $this->requireAdmin();
        
        $categories = $this->categoryModel->findAll('id DESC');
        
        $this->view('category/admin', [
            'categories' => $categories
        ]);
    }
    
    /**
     * Add category
     */
    public function add() {
        $this->requireAdmin();
        
        $error = '';
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $this->post('name');
            
            if (!empty($name)) {
                $result = $this->categoryModel->addCategory($name);
                
                if ($result) {
                    $this->redirect('index.php?page=admin_categories');
                } else {
                    $error = 'Thêm danh mục thất bại';
                }
            } else {
                $error = 'Tên danh mục không được để trống';
            }
        }
        
        $this->view('category/add', [
            'error' => $error,
            'success' => $success
        ]);
    }
    
    /**
     * Edit category
     */
    public function edit() {
        $this->requireAdmin();
        
        $id = $this->get('id', 0);
        $category = $this->categoryModel->getCategory($id);
        
        if (!$category) {
            $this->redirect('index.php?page=admin_categories');
        }
        
        $error = '';
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $this->post('name');
            
            if (!empty($name)) {
                $result = $this->categoryModel->updateCategory($id, $name);
                
                if ($result) {
                    $success = 'Cập nhật thành công';
                    $category['name'] = $name;
                } else {
                    $error = 'Cập nhật thất bại';
                }
            } else {
                $error = 'Tên danh mục không được để trống';
            }
        }
        
        $this->view('category/edit', [
            'category' => $category,
            'error' => $error,
            'success' => $success
        ]);
    }
    
    /**
     * Delete category
     */
    public function delete() {
        $this->requireAdmin();
        
        $id = $this->get('id', 0);
        
        if ($id > 0) {
            $this->categoryModel->deleteCategory($id);
        }
        
        $this->redirect('index.php?page=admin_categories');
    }
}
?>
