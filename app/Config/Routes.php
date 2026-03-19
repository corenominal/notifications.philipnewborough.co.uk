<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// Admin routes
$routes->get('/admin', 'Admin\Home::index');
$routes->get('/admin/datatable', 'Admin\Home::datatable');
$routes->get('/admin/notification/(:segment)', 'Admin\Home::getNotification/$1');
$routes->post('/admin/notifications/delete', 'Admin\Home::delete');
$routes->post('/admin/notification/update', 'Admin\Home::update');

$routes->get('/admin/create', 'Admin\Create::index');
$routes->post('/admin/create', 'Admin\Create::create');

// Admin create icon upload and listing
$routes->match(['post', 'options'], '/admin/create/icon-upload', 'Admin\Create::iconUpload');
$routes->match(['get', 'options'], '/admin/create/icons', 'Admin\Create::iconsList');
$routes->match(['get', 'options'], '/admin/create/users-search', 'Admin\Create::usersSearch');

// API routes
$routes->match(['get', 'options'], '/api/test/ping', 'Api\Test::ping');

$routes->match(['get', 'options'], '/api/notifications/(:segment)', 'Api\Notifications::index/$1');
$routes->match(['get', 'options'], '/api/notifications/(:segment)/(:num)', 'Api\Notifications::index/$1/$2');

$routes->match(['post', 'options'], '/api/notification', 'Api\Notification::insert');
$routes->match(['post', 'options'], '/api/notification/clear', 'Api\Notification::clear');
$routes->match(['post', 'options'], '/api/notification/clearall', 'Api\Notification::clearall');
$routes->match(['post', 'options'], '/api/notification/read', 'Api\Notification::read');
$routes->match(['post', 'options'], '/api/notification/readall', 'Api\Notification::readall');

// TODO: Add route for subscription insertion
// $routes->match(['post', 'options'], '/api/subscription', 'Api\Subscription::insert');

// Command line routes
$routes->cli('cli/test/index/(:segment)', 'CLI\Test::index/$1');
$routes->cli('cli/test/count', 'CLI\Test::count');

// Metrics route
$routes->post('/metrics/receive', 'Metrics::receive');

// Logout route
$routes->get('/logout', 'Auth::logout');

// Unauthorised route
$routes->get('/unauthorised', 'Unauthorised::index');

// Custom 404 route
$routes->set404Override('App\Controllers\Errors::show404');

// Debug routes
$routes->get('/debug', 'Debug\Home::index');
$routes->get('/debug/(:segment)', 'Debug\Rerouter::reroute/$1');
$routes->get('/debug/(:segment)/(:segment)', 'Debug\Rerouter::reroute/$1/$2');
