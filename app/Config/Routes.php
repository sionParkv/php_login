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

// 로그인
$routes->get('/login', 'AuthController::login');          // 로그인 화면
$routes->post('/login', 'AuthController::loginPost');     // 로그인 처리

// 회원가입
$routes->get('/register', 'AuthController::register');        // 회원가입 화면
$routes->post('/register', 'AuthController::registerPost');   // 회원가입 처리

// 아이디 / 비밀번호 찾기
$routes->get('/find', 'AuthController::find');

$routes->post('/find-email', 'AuthController::findEmail');
$routes->post('/reset-password', 'AuthController::resetPassword');

// 로그아웃
$routes->get('/logout', 'AuthController::logout');

/*
|--------------------------------------------------------------------------
| BoardController
|--------------------------------------------------------------------------
*/

// 게시판 목록 페이지 (검색 + 페이지네이션 포함)
$routes->get('/board', 'BoardController::index');

// 특정 게시글 상세보기
// (:num) → 게시글 ID
$routes->get('/board/view/(:num)', 'BoardController::view/$1');

// 글쓰기 화면
$routes->get('/board/write', 'BoardController::write');

// 글쓰기 처리 (DB insert)
$routes->post('/board/write', 'BoardController::writePost');

// 수정 화면 (본인 글만 접근 가능)
$routes->get('/board/edit/(:num)', 'BoardController::edit/$1');

// 수정 처리 (DB update)
$routes->post('/board/edit/(:num)', 'BoardController::editPost/$1');

// 게시글 삭제 (본인 글만 가능)
$routes->get('/board/delete/(:num)', 'BoardController::delete/$1');

// 댓글 등록 (DB insert)
$routes->post('/comment/write', 'BoardController::commentWrite');

// 좋아요 토글 (있으면 삭제, 없으면 추가)
$routes->get('/like/toggle/(:num)', 'BoardController::toggleLike/$1');

