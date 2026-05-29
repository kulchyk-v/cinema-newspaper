<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Layouts\MainLayout;
use Camezilla\Pages\Page;

$page = new class extends Page {

    public function __construct() {
        $viewService = new viewService();
        $viewService->increase();
        
        parent::__construct(new MainLayout("Error"), function () { ?>

            Error
            
        <?php });
    }
};

echo $page->render();