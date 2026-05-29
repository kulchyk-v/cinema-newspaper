<?php
require_once __DIR__ . '/../camezilla/camezilla.php';

use App\Models\Role;
use App\Services\AuthenticationService;
use Camezilla\Dispatchers\Dispatcher;
use App\Models\User;
use App\Services\UserService;

$dispatcher = new Dispatcher(page('not-found.php'), page('error.php'));
$userService = new UserService();
$authenticationService = new AuthenticationService();

function fill_user_role_session() {
    $current_user_id = get_session_item('authentication.user-id');

    try {
        connect_database();
        $camezillaDb = get_database();

        $stmt = $camezillaDb->prepare("SELECT role FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$current_user_id]);
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($user_data) {
            $current_user_role = $user_data['role'] ?? 'viewer';
            add_session_item('user_role', $current_user_role);
        }
    } catch (Exception $e) {
        log_error("Errore recupero info utente: " . $e->getMessage());
    }

}


$dispatcher->post('register', function($params) use ($authenticationService) {
    $user = new User (null, $params['first_name'], $params['last_name'], $params['email'], $params['password'], Role::from($params['role']));

    try {
        $authenticationService->register($user);
        fill_user_role_session();
        Dispatcher::ok_redirect();
    } catch (Exception $e) {
        Dispatcher::error_go_back($e->getMessage());
    }
});

$dispatcher->post('login', function($params) use ($authenticationService) {
    $user = new User (null, null, null, $params['email'], $params['password'], null);

    try {
        $authenticationService->login($user);
        fill_user_role_session();
        Dispatcher::ok_redirect();
    } catch (Exception $e) {
        Dispatcher::error_go_back($e->getMessage());
    }
});

$dispatcher->get('logout', function() use ($authenticationService) {
    try {
        $authenticationService->logout();
        Dispatcher::ok_redirect();
    } catch (Exception $e) {
        Dispatcher::error_go_back($e->getMessage());
    }
});

$dispatcher->post('update', function($params) use ($userService) {
    $user = new User ($params['id'], $params['first_name'], $params['last_name'], $params['email'], null, Role::from($params['role']));

    try {
        $userService->update($user);
        Dispatcher::ok_redirect();
    } catch (Exception $e) {
        Dispatcher::error_go_back($e->getMessage());
    }
});

$dispatcher->dispatch();