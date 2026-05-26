<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Components\Header;
use App\Components\Navbar;
use App\Components\ArticleItem;
use App\Layouts\MainLayout;
use App\Services\ArticleService;
use Camezilla\Pages\Page;

// 1. ATTIVAZIONE FONDAMENTALE DEL DB (Risolve il blocco "Error")
connect_database();

$articleService = new ArticleService();
$articles = $articleService->get_all(); // Recupera i dati reali dal DB

$immagini_slider = ["foto/Carousel1.jpg", "foto/Carousel2.jpg", "foto/Carousel3.jpg","foto/Carousel4.jpg","foto/Carousel5.jpg","foto/Carousel6.jpg"];

// Raggruppiamo i dati in un array da passare alla pagina anonima
$pageData = [
    'articles' => $articles,
    'slider'   => $immagini_slider
];

// Istanziamo la pagina passando $pageData
$page = new class($pageData) extends Page {

    public function __construct($data) {
        parent::__construct(new MainLayout("Accademia del Cinema - Home"), function ($data) { ?>
            
            <?= new Header('home') ?>
            
            <section class="description-section">
                <h1>Scuola di Cinema Professionale</h1>
                <p>Forma il tuo futuro nel mondo del cinema con i migliori docenti del settore.</p>
            </section>

            <section class="slider-section">
                <div class="swiper mySwiper">
                    <div class="swiper-wrapper">
                        <?php if (!empty($data['slider'])): ?>
                            <?php foreach ($data['slider'] as $img_src): ?>
                                <div class="swiper-slide">
                                    <img src="<?= resource($img_src) ?>" alt="Immagine in evidenza">
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                    <div class="swiper-pagination"></div>
                    <div class="swiper-button-prev"></div>
                    <div class="swiper-button-next"></div>
                </div>
            </section>

            <?= new Navbar('home') ?>

            <section class="news-section">
                <div class="news-header">
                    <h2>Incontri in Evidenza</h2>
                </div>
                <div class="cards-grid">
                    <?php if (!empty($data['articles']) && is_array($data['articles'])): ?>
                        <?php foreach ($data['articles'] as $article): ?>
                            <?= new ArticleItem($article) ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p style="text-align: center; color: #fff; grid-column: 1 / -1;">Nessun articolo trovato.</p>
                    <?php endif; ?>
                </div>
            </section>
        <?php }, $data);
    }
};

echo $page->render();