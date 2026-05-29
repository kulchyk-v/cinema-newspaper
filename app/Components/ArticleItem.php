<?php

namespace App\Components;

use Camezilla\Components\Component;
use App\Models\Article;

class ArticleItem extends Component
{
    private Article $article;

    public function __construct(Article $article) {
        parent::__construct();
        $this->article = $article;
    }

    protected function build(): void { 
        // 1. GESTIONE LINK E FALLBACK
        $dbLink = $this->article->get_link();
        
        if (empty($dbLink) || trim($dbLink) === '' || str_contains($dbLink, 'localhost')) {
            $link = page('notizia.php', ['id' => $this->article->get_id()]);
            $isExternal = false;
        } else {
            $link = $dbLink;
            $isExternal = true;
        }
        
        $target = $isExternal ? 'target="_blank"' : '';

        // 2. RECUPERO FOTO DINAMICO PROVANDO I VARI METODI DI CAMEZILLA
        $imageData = null;

        if (method_exists($this->article, 'get_image')) {
            $imageData = $this->article->get_image();
        } elseif (method_exists($this->article, 'image')) {
            $imageData = $this->article->image();
        } elseif (isset($this->article->image)) {
            $imageData = $this->article->image;
        }

        // Controlliamo che l'immagine sia valida e non sia solo uno spazio vuoto
        $hasImage = (!empty($imageData) && trim($imageData) !== '');
        ?>
        
        <article class="card">
            <?php if ($hasImage): ?>
                <img src="data:image/jpeg;base64,<?= base64_encode($imageData) ?>" alt="<?= e($this->article->get_title()) ?>">
            <?php else: ?>
                <img src="https://images.unsplash.com/photo-1507676184212-d03ab07a01bf?q=80&w=400" alt="Placeholder">
            <?php endif; ?>
            
            <div class="card-content">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; font-size: 0.85rem; color: #666;">
                    <span style="background: #0d47a1; color: white; padding: 4px 10px; border-radius: 50px; font-weight: 600; text-transform: uppercase;">
                        <i class="fas fa-tag"></i> <?= e(is_object($this->article->get_category()) ? $this->article->get_category()->value : $this->article->get_category()) ?>
                    </span>
                    <span><?= $this->article->get_date()->format('d M Y') ?></span>
                </div>

                <h3><?= e($this->article->get_title()) ?></h3>
                <p><?= e($this->article->get_description()) ?></p>
                
                <a href="<?= $link ?>" <?= $target ?> class="read-more">
                    <?= $isExternal ? "Leggi l'articolo sul sito <i class='fas fa-external-link-alt'></i>" : "Continua a leggere <i class='fas fa-arrow-right'></i>" ?>
                </a>
            </div>
        </article>
    <?php }
}