<?php
require_once __DIR__ . '/../camezilla/camezilla.php';
use App\Components\Header;
use App\Components\Navbar;
use App\Components\VideoItem;
use App\Layouts\MainLayout;
use App\Services\ArticleService;
use Camezilla\Pages\Page;

$page = new class extends Page {

    public function __construct() {
        // 1. Recuperiamo tutti i record dal Database
        $articleService = new ArticleService();
        $allArticles = $articleService->get_all();
        
        // 2. Filtriamo estraendo SOLO i record che appartengono alla categoria 'video'
        $videoArticles = array_filter($allArticles, function($article) {
            return $article->get_category()->value === 'video';
        });

        // 3. Avviamo la renderizzazione della struttura tramite MainLayout
        parent::__construct(new MainLayout("Video e Media - Accademia del Cinema"), function () use ($videoArticles) { ?>
            <?= new Header('video') ?>
            <?= new Navbar('video') ?>

            <section class="description-section">
                <div style="max-width: 1200px; margin: 0 auto; text-align: left; margin-bottom: 20px;">
                    <a href="<?= page('index.php') ?>" style="color: #90caf9; text-decoration: none; font-weight: 700; font-size: 0.9rem;">
                        <i class="fas fa-arrow-left"></i> TORNA ALLA HOME
                    </a>
                </div>
                <h1>Video e Media</h1>
                <p>Esplora i lavori multimediali, i backstage e le masterclass esclusive della nostra accademia.</p>
            </section>

            <main style="padding: 0 20px 50px 20px; max-width: 1200px; margin: 0 auto;">
                <div class="video-grid">
                    <?php if (empty($videoArticles)): ?>
                        <p style="color: #aaa; text-align: center; grid-column: 1 / -1; padding: 4px 0;">
                            Nessun contenuto video disponibile al momento.
                        </p>
                    <?php else: ?>
                        <?php foreach ($videoArticles as $video): ?>
                            <?= new VideoItem($video) ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </main>

        <?php });
    }
};

echo $page->render();