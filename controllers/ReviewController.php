<?php
require_once __DIR__ . '/../core/Controller.php';

/**
 * Review Controller
 */
class ReviewController extends Controller {
    private $reviewModel;
    
    public function __construct() {
        parent::__construct();
        $this->reviewModel = $this->model('ReviewModel');
    }
    
    /**
     * Add review
     */
    public function add() {
        if (!$this->isLoggedIn()) {
            echo '<script>alert("Bạn cần đăng nhập để đánh giá sản phẩm!"); history.back();</script>';
            exit;
        }
        
        $user_id = $this->getUserId();
        $username = $this->getUsername();
        $product_id = $this->post('product_id', 0);
        $rating = $this->post('rating', 0);
        $comment = trim($this->post('comment', ''));
        
        if ($product_id <= 0 || $rating <= 0 || $comment === '') {
            echo '<script>alert("Vui lòng điền đầy đủ thông tin!"); history.back();</script>';
            exit;
        }
        
        $this->reviewModel->addReview($user_id, $username, $product_id, $rating, $comment);
        
        echo '<script>window.location.href="index.php?page=product&id=' . $product_id . '";</script>';
        exit;
    }
    
    /**
     * Delete review
     */
    public function delete() {
        // Only admin or user 'test' can delete
        if (!$this->isAdmin() && $this->getUsername() !== 'test') {
            echo '<script>alert("Bạn không có quyền xóa đánh giá!"); history.back();</script>';
            exit;
        }
        
        $id = $this->get('id', 0);
        $product_id = $this->get('product_id', 0);
        
        if ($id > 0) {
            $this->reviewModel->deleteReview($id);
        }
        
        echo '<script>window.location.href="index.php?page=product&id=' . $product_id . '";</script>';
        exit;
    }
}
?>
