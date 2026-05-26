<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Components\ArticleDetails;
use App\Layouts\MainLayout;
use App\Services\ArticleService;
use Camezilla\Pages\Page;

$page = new class extends Page {

    public function __construct() {
        $viewService = new ViewService();
        $viewService->increase();

        $articleService = new ArticleService();
        $article = $articleService->get_by_id($_GET['id']);

        parent::__construct(new MainLayout("Articolo"), function () use ($article) { ?>

            <?php if (!$article): ?>

                <div>Articolo non trovato</div>

            <?php else: ?>

                <?= new ArticleDetails($article) ?>

            <?php endif; ?>

        <?php });
    }
};

echo $page->render();