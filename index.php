<?php
/**
 * MVC Application Entry Point
 * Chợ Xanh - Shop Tết
 */

// Bật hiển thị lỗi để debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Load core files
require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/core/Model.php';
require_once __DIR__ . '/core/Controller.php';
require_once __DIR__ . '/core/App.php';

// Include header
require_once __DIR__ . '/partials/head.php';
?>

<div class="main-content-wrapper">
    <?php
    // Initialize the application (Router)
    $app = new App();
    ?>
</div>

<?php
// Include footer
require_once __DIR__ . '/partials/footer.php';
?>