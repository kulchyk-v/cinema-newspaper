<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Components\Header;
use App\Components\Navbar;
use App\Components\ArticleItem;
use App\Components\ArticleGroup;
use App\Layouts\MainLayout;
use App\Services\ArticleService;
use Camezilla\Pages\Page;

// 1. ATTIVAZIONE FONDAMENTALE DEL DB
connect_database();

$articleService = new ArticleService();
$articles = $articleService->get_all(); // Recupera tutti i dati reali dal DB

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
            <?php 
            $ha_articoli_validi = false;
            
            foreach ($data['articles'] as $article): 
                
                // --- 1. FILTRO CATEGORIA (Già presente) ---
                if (is_object($article) && method_exists($article, 'get_category')) {
                    $categoria_obj = $article->get_category();
                    $categoria_valore = (is_object($categoria_obj) && isset($categoria_obj->value)) ? $categoria_obj->value : (string)$categoria_obj;
                    
                    if (strtolower($categoria_valore) === 'video' || strtolower($categoria_valore) === 'press_review') {
                        continue;
                    }
                }

                // --- 2. NUOVO FILTRO: ESCLUSIONE ARTICOLI DI BASSO LIVELLO ---
                if (is_object($article)) {
                    // Controlla se esiste il metodo per la priorità (adatta il nome se si chiama diversamente, es. get_priority)
                    $metodo_priorita = method_exists($article, 'get_priority_level') ? 'get_priority_level' : (method_exists($article, 'get_priority') ? 'get_priority' : null);
                    
                    if ($metodo_priorita) {
                        $priorita_obj = $article->$metodo_priorita();
                        $priorita_valore = (is_object($priorita_obj) && isset($priorita_obj->value)) ? $priorita_obj->value : (string)$priorita_obj;
                        
                        // Se la priorità è 'low' (basso livello), lo saltiamo e non lo mostriamo in Home
                        if (strtolower($priorita_valore) === 'low') {
                            continue;
                        }
                    }
                }
                
                $ha_articoli_validi = true;
                ?>
                <?= new ArticleItem($article) ?>
            <?php endforeach; ?>

            <?php if (!$ha_articoli_validi): ?>
                <p style="text-align: center; color: #fff; grid-column: 1 / -1;">Nessun articolo in evidenza trovato per la Home.</p>
            <?php endif; ?>

        <?php else: ?>
            <p style="text-align: center; color: #fff; grid-column: 1 / -1;">Nessun articolo trovato.</p>
        <?php endif; ?>
    </div>
</section>
        <?php }, $data);
    }
};

echo $page->render();