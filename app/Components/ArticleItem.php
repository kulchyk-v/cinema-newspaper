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
        // Recuperiamo il link: se è presente nel DB lo usiamo, altrimenti creiamo il link interno
        $link = $this->article->get_link() ?: page('notizia.php', ['id' => $this->article->get_id()]);
        $target = $this->article->get_link() ? 'target="_blank"' : '';
        ?>
        <article class="card">
            <?php if (false): ?> <!-- $this->article->get_image() -->
                <img src="data:image/jpeg;base64,<?= base64_encode($this->article->get_image()) ?>" alt="<?= e($this->article->get_title()) ?>">
            <?php else: ?>
                <img src="https://images.unsplash.com/photo-1507676184212-d03ab07a01bf?q=80&w=400" alt="Placeholder">
            <?php endif; ?>
            
            <div class="card-content">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; font-size: 0.85rem; color: #666;">
                    <span style="background: #0d47a1; color: white; padding: 4px 10px; border-radius: 50px; font-weight: 600; text-transform: uppercase;">
                        <i class="fas fa-tag"></i> <?= e($this->article->get_category()->value) ?>
                    </span>
                    <span><?= $this->article->get_date()->format('d M Y') ?></span>
                </div>

                <h3><?= e($this->article->get_title()) ?></h3>
                <p><?= e($this->article->get_description()) ?></p>
                
                <a href="<?= $link ?>" <?= $target ?> class="read-more">
                    <?= $this->article->get_link() ? "Leggi l'articolo sul sito <i class='fas fa-external-link-alt'></i>" : "Continua a leggere <i class='fas fa-arrow-right'></i>" ?>
                </a>
            </div>
        </article>
    <?php }
}