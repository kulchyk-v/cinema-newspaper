<?php

namespace App\Components;

use Camezilla\Components\Component;
use App\Models\Article;

class ArticleForm extends Component {

    private ?Article $article;

    public function __construct(?Article $article = null) {
        $this->article = $article;
        parent::__construct();
    }

    protected function build(): void { ?>
        Form Articolo
    <?php }
}