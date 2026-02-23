<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Home::index');

/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/

// 로그인
$routes->get('/login', 'Auth::login');          // 로그인 화면
$routes->post('/login', 'Auth::loginPost');     // 로그인 처리

// 회원가입
$routes->get('/register', 'Auth::register');        // 회원가입 화면
$routes->post('/register', 'Auth::registerPost');   // 회원가입 처리

// 아이디 / 비밀번호 찾기
$routes->get('/find', 'Auth::find');

$routes->post('/find-email', 'Auth::findEmail');
$routes->post('/reset-password', 'Auth::resetPassword');

// 로그아웃
$routes->get('/logout', 'Auth::logout');

/*
|--------------------------------------------------------------------------
| Board
|--------------------------------------------------------------------------
*/

// 게시판 목록 페이지 (검색 + 페이지네이션 포함)
$routes->get('/board', 'Board::index');

// 특정 게시글 상세보기
// (:num) → 게시글 ID
$routes->get('/board/view/(:num)', 'Board::view/$1');

// 글쓰기 화면
$routes->get('/board/write', 'Board::write');

// 글쓰기 처리 (DB insert)
$routes->post('/board/write', 'Board::writePost');

// 수정 화면 (본인 글만 접근 가능)
$routes->get('/board/edit/(:num)', 'Board::edit/$1');

// 수정 처리 (DB update)
$routes->post('/board/edit/(:num)', 'Board::editPost/$1');

// 게시글 삭제 (본인 글만 가능)
$routes->get('/board/delete/(:num)', 'Board::delete/$1');

// 댓글 등록 (DB insert)
$routes->post('/comment/write', 'Board::commentWrite');

// 좋아요 토글 (있으면 삭제, 없으면 추가)
$routes->get('/like/toggle/(:num)', 'Board::toggleLike/$1');

