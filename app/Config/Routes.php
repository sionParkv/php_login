<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');

/*
|--------------------------------------------------------------------------
| AuthController - dynamic
|--------------------------------------------------------------------------
*/
$routes->match(['GET', 'POST'], 'auth/(:any)', 'AuthController::$1');
/*
|--------------------------------------------------------------------------
| BoardController - dynamic
|--------------------------------------------------------------------------
*/

// /board -> index 유지
$routes->get('/board', 'BoardController::index');

// /board/{method}/...
$routes->match(['GET', 'POST'], 'board/(:any)', 'BoardController::$1');
