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
        // 1. Chiediamo al Service di prendere TUTTI gli articoli reali dal Database
        $articleService = new ArticleService();
        $articles = $articleService->get_all();

        // 2. Passiamo il controllo al MainLayout (che mette Head, CSS, Header e Footer in automatico)
        parent::__construct(new MainLayout("Tutti gli Articoli - Accademia del Cinema"), function () use ($articles) { ?>
            <?= new Header('tutti') ?>
            <?= new Navbar('tutti') ?>

            <section class="description-section">
                <div style="max-width: 1200px; margin: 0 auto; text-align: left; margin-bottom: 20px;">
                    <a href="<?= page('index.php') ?>" style="color: #90caf9; text-decoration: none; font-weight: 700; font-size: 0.9rem;">
                        <i class="fas fa-arrow-left"></i> TORNA ALLA HOME
                    </a>
                </div>
                <h1>Tutti Gli Articoli</h1>
                <p>Scopri tutte le ultime notizie, gli eventi e gli aggiornamenti dalla nostra accademia di cinema.</p>
            </section>

            <main class="news-section">
                <div class="cards-grid">
                    <?php if (empty($articles)): ?>
                        <p style="color: #aaa; text-align: center;">Nessun articolo presente nel database.</p>
                    <?php else: ?>
                        <?php foreach ($articles as $article): ?>
                            <?= new ArticleItem($article) ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </main>

        <?php });
    }
};

echo $page->render();