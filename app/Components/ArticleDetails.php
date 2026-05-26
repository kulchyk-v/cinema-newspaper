<?php

namespace App\Components;

use Camezilla\Components\Component;
use App\Models\Article;

class ArticleDetails extends Component {

    private Article $article;

    public function __construct(Article $article) {
        parent::__construct();
        $this->article = $article;
    }

    protected function build(): void { ?>
        <div>
            Dettagli Articolo
        </div>
    <?php }
}