<?php

namespace App\Layouts;

use App\Components\Header;
use Camezilla\Layouts\Layout;
use App\Components\Footer; 

class MainLayout extends Layout
{
    public function __construct(string $title)
    {
        parent::__construct($title);
    }

    protected function build(): void
    { ?>
        <!DOCTYPE html>
        <html lang="it">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            
            <title><?= e($this->title); ?></title>
            
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
            <script src="<?= resource('js/Js.js'); ?>"></script>
            
            <link rel="stylesheet" href="<?= resource('css/style.css'); ?>"> 
            <link rel="stylesheet" href="<?= resource('css/login.css'); ?>">  
            <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

        </head>
        <body>

            <main>
                <?php $this->render_content(); ?>
            </main>

            <?= new Footer() ?>

        </body>
        </html>
    <?php }
}