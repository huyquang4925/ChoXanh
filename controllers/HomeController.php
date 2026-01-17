<?php
require_once __DIR__ . '/../core/Controller.php';

/**
 * Home Controller
 */
class HomeController extends Controller {
    private $productModel;
    private $categoryModel;
    
    public function __construct() {
        parent::__construct();
        $this->productModel = $this->model('ProductModel');
        $this->categoryModel = $this->model('CategoryModel');
    }
    
    /**
     * Home page
     */
    public function index() {
        $limit = 12;
        $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        $page = max($page, 1);
        $offset = ($page - 1) * $limit;
        
        // Get categories
        $categories = $this->categoryModel->getFeatured(6);
        
        // Get products
        $products = $this->productModel->getProductsWithCategory($limit, $offset);
        
        // Get total products for pagination
        $total_products = $this->productModel->count();
        $total_pages = ceil($total_products / $limit);
        
        $this->view('home/index', [
            'categories' => $categories,
            'products' => $products,
            'current_page' => $page,
            'total_pages' => $total_pages
        ]);
    }
    
    /**
     * Search products
     */
    public function search() {
        $keyword = isset($_GET['q']) ? trim($_GET['q']) : '';
        $limit = 12;
        $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
        $page = max($page, 1);
        $offset = ($page - 1) * $limit;
        
        // Get categories
        $categories = $this->categoryModel->getFeatured(6);
        
        if (!empty($keyword)) {
            // Search products
            $products = $this->productModel->searchProducts($keyword, $limit, $offset);
            $total_products = $this->productModel->countSearchResults($keyword);
        } else {
            // No keyword, show all products
            $products = $this->productModel->getProductsWithCategory($limit, $offset);
            $total_products = $this->productModel->count();
        }
        
        $total_pages = ceil($total_products / $limit);
        
        $this->view('home/index', [
            'categories' => $categories,
            'products' => $products,
            'current_page' => $page,
            'total_pages' => $total_pages,
            'search_keyword' => $keyword
        ]);
    }
}
?>
