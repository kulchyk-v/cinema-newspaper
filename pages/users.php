<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Components\UserGroup;
use App\Layouts\MainLayout;
use App\Services\UserService;
use Camezilla\Pages\Page;

require_user_authentication();

$page = new class extends Page {

    public function __construct() {
        $viewService = new ViewService();
        $viewService->increase();
        
        $userService = new UserService();
        $users = $userService->get_all();

        parent::__construct(new MainLayout("Gestione Utenti"), function () use ($users) { ?>

            <?= new UserGroup($users) ?>

        <?php });
    }
};

echo $page->render();