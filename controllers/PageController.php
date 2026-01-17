<?php
require_once __DIR__ . '/../core/Controller.php';

/**
 * Page Controller - Static pages
 */
class PageController extends Controller {
    
    public function __construct() {
        parent::__construct();
    }
    
    /**
     * About us page
     */
    public function aboutUs() {
        $this->view('page/about_us');
    }
    
    /**
     * Contact page
     */
    public function contact() {
        $this->view('page/contact');
    }
}
?>
