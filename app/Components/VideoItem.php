<?php

namespace App\Components;

use Camezilla\Components\Component;
use App\Models\Article;

class VideoItem extends Component
{
    private Article $videoArticle;

    public function __construct(Article $videoArticle)
    {
        parent::__construct();
        $this->videoArticle = $videoArticle;
    }

    protected function build(): void
    {
        // Se nel DB c'è memorizzato il link esteso di YouTube, lo convertiamo nel formato 'embed' per l'iframe
        $rawLink = $this->videoArticle->get_link() ?: '';
        $embedLink = $rawLink;

        // Piccola logica per convertire automaticamente i link classici di YouTube in link incorporabili
        if (str_contains($rawLink, 'watch?v=')) {
            $embedLink = str_replace('watch?v=', 'embed/', $rawLink);
        } elseif (str_contains($rawLink, 'youtu.be/')) {
            $embedLink = str_replace('youtu.be/', 'youtube.com/embed/', $rawLink);
        }
        ?>
        <div class="video-card">
            <div class="video-container">
                <?php if (!empty($embedLink)): ?>
                    <iframe 
                        src="<?= e($embedLink) ?>" 
                        title="<?= e($this->videoArticle->get_title()) ?>" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                        referrerpolicy="strict-origin-when-cross-origin" 
                        allowfullscreen>
                    </iframe>
                <?php else: ?>
                    <div style="background: #222; height: 100%; display: flex; align-items: center; justify-content: center; color: #555;">
                        <i class="fas fa-video-slash" style="font-size: 2rem;"></i>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="video-info">
                <h3>
                    <span class="play-icon-bg"><i class="fas fa-play"></i></span> 
                    <?= e($this->videoArticle->get_title()) ?>
                </h3>
                <p><?= e($this->videoArticle->get_description()) ?></p>
            </div>
        </div>
    <?php }
}