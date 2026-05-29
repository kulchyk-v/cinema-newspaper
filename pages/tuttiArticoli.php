<?php
require_once __DIR__ . '/../camezilla/camezilla.php';
use App\Components\Header;
use App\Components\Navbar;
use App\Layouts\MainLayout;
use App\Services\ArticleService;
use Camezilla\Pages\Page;

$page = new class extends Page {

    public function __construct() {
        // 1. Chiediamo al Service di prendere TUTTI gli articoli reali dal Database
        $articleService = new ArticleService();
        $articles = $articleService->get_all();

        // 2. Passiamo il controllo al MainLayout
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
                        <?php 
                        $ha_articoli_validi = false;
                        
                        foreach ($articles as $article): 
                            // Verifichiamo che sia un oggetto valido
                            if (!is_object($article)) {
                                continue;
                            }

                            // Isoliamo la categoria per filtrare gli elementi indesiderati
                            $categoria_valore = '';
                            if (method_exists($article, 'get_category') && $article->get_category() !== null) {
                                $categoria_valore = $article->get_category()->value ?? (string)$article->get_category();
                            }
                            
                            // --- MODIFICA: Escludiamo sia i Video che le Rassegne Stampa ---
                            $categoria_pulita = strtolower($categoria_valore);
                            if ($categoria_pulita === 'video' || $categoria_pulita === 'press_review') {
                                continue;
                            }
                            
                            $ha_articoli_validi = true;

                            // Estraiamo i dati dall'oggetto in sicurezza usando i getter del modello
                            $id          = method_exists($article, 'get_id') ? $article->get_id() : 0;
                            $title       = method_exists($article, 'get_title') ? $article->get_title() : '';
                            $description = method_exists($article, 'get_description') ? $article->get_description() : '';
                            $author      = method_exists($article, 'get_author') ? $article->get_author() : 'Redazione';
                            $dateObj     = method_exists($article, 'get_date') ? $article->get_date() : null;
                            $dateStr     = ($dateObj instanceof DateTime) ? $dateObj->format('d M Y') : '';
                            ?>
                            
                            <div class="news-card" >
                                <div>
                                    <div>
                                        <span><i class="far fa-calendar-alt"></i> <?= e($dateStr) ?></span>
                                        <span><i class="fas fa-user"></i> <?= e($author) ?></span>
                                    </div>
                                    <h3><?= e($title) ?></h3>
                                    <p><?= e($description) ?></p>
                                </div>
                                
                                <div style="margin-top: auto;">
                                    <a href="notizia.php?id=<?= $id ?>" >
                                        Leggi notizia <i class="fas fa-chevron-right"></i>
                                    </a>
                                </div>
                            </div>

                        <?php endforeach; ?>

                        <?php if (!$ha_articoli_validi): ?>
                            <p style="color: #aaa; text-align: center; grid-column: 1 / -1;">Nessun articolo presente nel database.</p>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </main>

        <?php });
    }
};

echo $page->render();