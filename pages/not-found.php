<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Layouts\MainLayout;
use Camezilla\Pages\Page;

$page = new class extends Page {

    public function __construct() {
        $viewService = new ViewService();
        $viewService->increase();
        
        parent::__construct(new MainLayout("Not Found"), function () { ?>

            Not Found
            
        <?php });
    }
};

echo $page->render();