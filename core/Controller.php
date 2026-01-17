<?php
/**
 * Base Controller Class
 * All controllers should extend this class
 */
class Controller {
    protected $db;
    protected $conn;
    
    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        $this->db = Database::getInstance();
        $this->conn = $this->db->getConnection();
    }
    
    /**
     * Load a model
     */
    protected function model($model) {
        $modelFile = __DIR__ . '/../models/' . $model . '.php';
        
        if (file_exists($modelFile)) {
            require_once $modelFile;
            return new $model();
        }
        
        return null;
    }
    
    /**
     * Render a view
     */
    protected function view($view, $data = []) {
        // Extract data to variables
        extract($data);
        
        // Also pass conn for backward compatibility
        $conn = $this->conn;
        
        // Include the view file
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            echo "View not found: " . $view;
        }
    }
    
    /**
     * Redirect to another page
     */
    protected function redirect($url) {
        if (!headers_sent()) {
            header('Location: ' . $url);
            exit;
        }
        echo '<script>window.location.href = "' . htmlspecialchars($url, ENT_QUOTES) . '";</script>';
        echo '<noscript><meta http-equiv="refresh" content="0;url=' . htmlspecialchars($url, ENT_QUOTES) . '" /></noscript>';
        exit;
    }
    
    /**
     * Check if user is logged in
     */
    protected function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Check if user is admin
     */
    protected function isAdmin() {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }
    
    /**
     * Get current user ID
     */
    protected function getUserId() {
        return isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
    }
    
    /**
     * Get current username
     */
    protected function getUsername() {
        return isset($_SESSION['username']) ? $_SESSION['username'] : '';
    }
    
    /**
     * Require login
     */
    protected function requireLogin() {
        if (!$this->isLoggedIn()) {
            $this->redirect('index.php?page=login');
        }
    }
    
    /**
     * Require admin
     */
    protected function requireAdmin() {
        if (!$this->isAdmin()) {
            $this->redirect('index.php?page=home');
        }
    }
    
    /**
     * Get POST data
     */
    protected function post($key, $default = null) {
        return isset($_POST[$key]) ? $_POST[$key] : $default;
    }
    
    /**
     * Get GET data
     */
    protected function get($key, $default = null) {
        return isset($_GET[$key]) ? $_GET[$key] : $default;
    }
    
    /**
     * Get REQUEST data
     */
    protected function request($key, $default = null) {
        return isset($_REQUEST[$key]) ? $_REQUEST[$key] : $default;
    }
}
?>
