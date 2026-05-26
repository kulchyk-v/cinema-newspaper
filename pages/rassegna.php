<?php
require_once __DIR__ . '/../camezilla/camezilla.php';
use App\Components\Header;
use App\Components\Navbar;
use App\Components\ArticleItem;
use App\Layouts\MainLayout;
use App\Services\ArticleService;
use Camezilla\Pages\Page;

$page = new class extends Page {

    public function __construct() {
        $articleService = new ArticleService();
        $allArticles = $articleService->get_all();
        
        // Filtriamo gli articoli tenendo solo quelli che hanno categoria 'press_review'
        $articlesStampa = array_filter($allArticles, function($article) {
            return $article->get_category()->value === 'press_review';
        });

        parent::__construct(new MainLayout("Rassegna Stampa - Accademia del Cinema"), function () use ($articlesStampa) { ?>
            <?= new Header('rassegna') ?>
            <?= new Navbar('rassegna') ?>

            <section class="description-section">
                <div style="max-width: 1200px; margin: 0 auto; text-align: left; margin-bottom: 20px;">
                    <a href="<?= page('index.php') ?>" style="color: #90caf9; text-decoration: none; font-weight: 700; font-size: 0.9rem;">
                        <i class="fas fa-arrow-left"></i> TORNA ALLA HOME
                    </a>
                </div>
                <h1>Rassegna Stampa</h1>
                <p>Le testate giornalistiche raccontano l'innovazione e il talento della nostra Accademia.</p>
            </section>

            <main class="news-section">
                <div class="cards-grid">
                    <?php foreach ($articlesStampa as $article): ?>
                        <?= new ArticleItem($article) ?>
                    <?php endforeach; ?>
                </div>
            </main>

        <?php });
    }
};

echo $page->render();