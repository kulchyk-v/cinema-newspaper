<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Components\ArticleGroup;
use App\Layouts\MainLayout;
use App\Models\Category;
use App\Services\ArticleService;
use Camezilla\Pages\Page;

$page = new class extends Page {
    $viewService = new ViewService();
    $viewService->increase();

    public function __construct() {
        $articleService = new ArticleService();
        $articles = $articleService->get_all();

        parent::__construct(new MainLayout("Tutti gli Articoli"), function () use ($articles) { ?>

            <?= new ArticleGroup($articles) ?>

        <?php });
    }
};

echo $page->render();