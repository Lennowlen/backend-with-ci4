<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// pelanggan routes
$routes->get('/api/pelanggan/all', 'PelangganController::index');
$routes->post('/api/pelanggan/create', 'PelangganController::create');
$routes->put('/api/pelanggan/(:num)', 'PelangganController::update/$1');
$routes->delete('/api/pelanggan/(:num)', 'PelangganController::delete/$1');

// penjualan routes
$routes->get('/api/penjualan/all', 'PenjualanController::index');
$routes->get('/api/penjualan/', 'PenjualanController::home');
$routes->post('/api/penjualan/create', 'PenjualanController::create');
$routes->put('/api/penjualan/(:num)', 'PenjualanController::update/$1');
$routes->delete('/api/penjualan/(:num)', 'PenjualanController::delete/$1');

// produk routes
$routes->get('/api/produk/all', 'ProdukController::index');
$routes->post('/api/produk/create', 'ProdukController::create');
$routes->put('/api/produk/(:num)', 'ProdukController::update/$1');
$routes->delete('/api/produk/(:num)', 'ProdukController::delete/$1');

// detail penjualan routes
$routes->get('/api/detail-penjualan/all', 'DetailPenjualanController::index');
$routes->post('/api/detail-penjualan/create', 'DetailPenjualanController::create');
$routes->put('/api/detail-penjualan/(:num)', 'DetailPenjualanController::update/$1');
$routes->delete('/api/detail-penjualan/(:num)', 'DetailPenjualanController::delete/$1');


// $routes->setAutoRoute(true); // for allow access all route
