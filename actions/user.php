<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Models\User;
use App\Services\UserService;
use Camezilla\Dispatchers\Dispatcher;

$dispatcher = new Dispatcher(page('not-found.php'), page('error.php'));
$userService = new UserService();

$dispatcher->post('delete', function($params) use ($userService) {
    require_user_authentication();
    
    $user = new User($params['id'], null, null, null, null, null);

    try {
        $userService->delete($user);
        Dispatcher::ok_redirect();
    } catch (Exception $e) {
        Dispatcher::error_go_back($e->getMessage());
    }
});

$dispatcher->dispatch();