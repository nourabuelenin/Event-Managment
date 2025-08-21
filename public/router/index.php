<?php
require_once __DIR__ . '/../../config/smarty_config.php';

require_once __DIR__ . '/../controllers/HomeController.php';
require_once __DIR__ . '/../controllers/UserController.php';
require_once __DIR__ . '/../controllers/EventController.php';
require_once __DIR__ . '/../controllers/VenueController.php';

// Routes
$routes = [
    ''                   => ['controller' => 'HomeController',  'method' => 'index'],
    'home'               => ['controller' => 'HomeController',  'method' => 'index'],
    'register'           => ['controller' => 'UserController',  'method' => 'register'],
    'login'              => ['controller' => 'UserController',  'method' => 'login'],
    'logout'             => ['controller' => 'UserController',  'method' => 'logout'],

    // pages
    'events'             => ['controller' => 'EventController', 'method' => 'index'],
    'events/(\d+)' => ['controller' => 'EventController', 'method' => 'view', 'params' => ['id' => 1]],
    'events/create'      => ['controller' => 'EventController', 'method' => 'create'],
    'events/update/(\d+)' => ['controller'=>'EventController','method'=>'update','params'=>['id'=>1]],
    'events/delete/(\d+)' => ['controller'=>'EventController','method'=>'delete','params'=>['id'=>1]],
    'events/register/(\d+)' => ['controller' => 'EventController', 'method' => 'register', 'params' => ['id' => 1]],

    // api
    'api/events'         => ['controller' => 'EventController', 'method' => 'apiList'],
    'api/venues'         => ['controller' => 'VenueController', 'method' => 'apiList'],

    'api/events/(\d+)'    => ['controller' => 'EventController', 'method' => 'apiView', 'params' => ['id' => 1]],   // ?id=...
    'api/events/create'  => ['controller' => 'EventController', 'method' => 'apiCreate'],
    'api/events/update/(\d+)'  => ['controller' => 'EventController', 'method' => 'apiUpdate','params'=>['id'=>1]], // ?id=...
    'api/events/delete'  => ['controller' => 'EventController', 'method' => 'apiDelete' ,'params'=>['id'=>1]], // ?id=...
    'api/events/register/(\d+)' => ['controller' => 'EventController', 'method' => 'apiRegister', 'params' => ['id' => 1]],
];

// Resolve path
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = str_replace('/test/public/router', '', $requestUri);
$path = trim($path, '/');

// Dispatch
$matched = false;
foreach ($routes as $pattern => $route) {
    if (preg_match("#^$pattern$#", $path, $matches)) {
        $controllerName = $route['controller'];
        $method         = $route['method'];
        $controller     = new $controllerName($db, $smarty);

        if (isset($route['params'])) {
            foreach ($route['params'] as $key => $idx) {
                $_GET[$key] = $matches[$idx];
            }
        }
        $controller->$method();
        $matched = true;
        break;
    }
}

if (!$matched) {
    http_response_code(404);
    $smarty->assign('flash', ['message' => 'Page not found.', 'type' => 'error']);
    $smarty->display('base.tpl');
}
