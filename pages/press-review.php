<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Components\ArticleGroup;
use App\Layouts\MainLayout;
use App\Models\Category;
use App\Services\ArticleService;
use Camezilla\Pages\Page;

$page = new class extends Page {

    public function __construct() {
        $viewService = new ViewService();
        $viewService->increase();
        
        $articleService = new ArticleService();
        $articles = $articleService->get_by_category(Category::PressReview);

        parent::__construct(new MainLayout("Rassegna Stampa"), function () use ($articles) { ?>

            <?= new ArticleGroup($articles) ?>

        <?php });
    }
};

echo $page->render();