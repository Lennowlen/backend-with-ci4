<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->get('/all', 'PelangganController::index');
$routes->post('/pelanggan/create', 'PelangganController::create');
$routes->put('/pelanggan/(:num)', 'PelangganController::update/$1');
$routes->delete('/pelanggan/(:num)', 'PelangganController::delete/$1');

// $routes->setAutoRoute(true); // for allow access all route
