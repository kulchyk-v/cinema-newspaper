<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Models\Article;
use App\Models\Category;
use App\Models\PriorityLevel;
use App\Services\ArticleService;
use Camezilla\Dispatchers\Dispatcher;

$dispatcher = new Dispatcher(page('not-found.php'), page('error.php'));
$articleService = new ArticleService();

$dispatcher->post('create', function($params) use ($articleService) {
    require_user_authentication();

    $article = new Article(null, $params['title'], $params['description'], Category::from($params['category']), $params['link'], $params['text'], PriorityLevel::from($params['priority_level']), $params['author'], $params['date']);
    
    try {
        $articleService->create($article);
        Dispatcher::ok_redirect();
    } catch (Exception $e) {
        Dispatcher::error_go_back($e->getMessage());
    }
});

$dispatcher->post('update', function($params) use ($articleService) {
    require_user_authentication();

    $article = new Article($params['id'], $params['title'], $params['description'], Category::from($params['category']), $params['link'], $params['text'], PriorityLevel::from($params['priority_level']), $params['author'], $params['date']);
    
    try {
        $articleService->update($article);
        Dispatcher::ok_redirect();
    } catch (Exception $e) {
        Dispatcher::error_go_back($e->getMessage());
    }
});

$dispatcher->post('delete', function($params) use ($articleService) {
    require_user_authentication();
    
    $article = new Article($params['id'], null, null, null, null, null, null, null, null);

    try {
        $articleService->delete($article);
        Dispatcher::ok_redirect();
    } catch (Exception $e) {
        Dispatcher::error_go_back($e->getMessage());
    }
});

$dispatcher->dispatch();