<?php

namespace App\Components;

use Camezilla\Components\Component;
use App\Models\Article;

class ArticleGroup extends Component {

    private array $article;

    public function __construct(array $article) {
        parent::__construct();
        $this->article = $article;
    }

    protected function build(): void { ?>
        <div>
            Gruppo Articoli
            
            <?php if (empty($this->article)): ?>

                <p>Nessun articolo trovato.</p>

            <?php else: ?>

                <?php foreach ($this->article as $article): ?>

                    <?= new ArticleItem($article) ?>

                <?php endforeach; ?>

            <?php endif; ?>
        </div>
    <?php }
}