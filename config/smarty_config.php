<?php
require_once __DIR__ . '/config.php'; // Includes autoloader and $db

use Smarty\Smarty;

class MySmarty extends Smarty {
    public function __construct() {
        parent::__construct();
        $this->setTemplateDir(__DIR__ . '/../public/views/templates/');
        $this->setCompileDir(__DIR__ . '/../public/views/templates_c/');
        $this->setConfigDir(__DIR__ . '/../configs/');
        $this->setCacheDir(__DIR__ . '/../cache/');

        $this->setEscapeHtml(true); // Enable auto-escaping

        // Optional: Enable caching
        // $this->caching = Smarty::CACHING_LIFETIME_CURRENT;
        // $this->cache_lifetime = 3600;
    }
}
// Smarty
$smarty = new MySmarty();
$smarty->assign('base_url', BASE_URL);
$smarty->assign('assets_url', ASSETS_URL);
$smarty->assign('views_url', VIEWS_URL);
$db = Database::getInstance(); // Use singleton
$smarty->assign('current_user', getCurrentUser());
$smarty->assign('csrf_token', $_SESSION['csrf_token']);