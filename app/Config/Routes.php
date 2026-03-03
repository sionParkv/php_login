<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');

/*
|--------------------------------------------------------------------------
| AuthController
|--------------------------------------------------------------------------
*/
$routes->match(['GET', 'POST'], 'auth/(:any)', 'AuthController::$1');
/*
|--------------------------------------------------------------------------
| BoardController
|--------------------------------------------------------------------------
*/

// /board/{method}/...
$routes->match(['GET', 'POST'], 'board/(:any)', 'BoardController::$1');
