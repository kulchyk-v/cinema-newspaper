<?php

namespace App\Layouts;
use Camezilla\Layouts\Layout;
use App\Components\BackButton; 
use App\Components\Footer; 

class MainLayout2 extends Layout
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
            
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
             <link rel="stylesheet" href="<?= resource('css/modify.css'); ?>"> 
             <link rel="stylesheet" href="<?= resource('css/style.css'); ?>"> 
        </head>
        <body>
             <?= new BackButton() ?>
            <main>
                <?php $this->render_content(); ?>
            </main>

            <?= new Footer() ?>

        </body>
        </html>
    <?php }
}