<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/swagger', 'Swagger\SwaggerController::index');

// pelanggan routes
$routes->get('/api/pelanggan', 'PelangganController::index');
$routes->get('/api/pelanggan/(:num)', 'PelangganController::show/$1');
$routes->post('/api/pelanggan/create', 'PelangganController::create');
$routes->put('/api/pelanggan/(:num)', 'PelangganController::update/$1');
$routes->delete('/api/pelanggan/(:num)', 'PelangganController::delete/$1');

// penjualan routes
$routes->get('/api/penjualan', 'PenjualanController::index');
$routes->get('/api/penjualan/(:num)', 'PenjualanController::show/$1');
$routes->post('/api/penjualan/create', 'PenjualanController::create');
$routes->put('/api/penjualan/(:num)', 'PenjualanController::update/$1');
$routes->delete('/api/penjualan/(:num)', 'PenjualanController::delete/$1');

// produk routes
$routes->get('/api/produk', 'ProdukController::index');
$routes->get('/api/produk/(:num)', 'ProdukController::show/$1');
$routes->post('/api/produk/create', 'ProdukController::create');
$routes->put('/api/produk/(:num)', 'ProdukController::update/$1');
$routes->delete('/api/produk/(:num)', 'ProdukController::delete/$1');

// detail penjualan routes
$routes->get('/api/detail-penjualan', 'DetailPenjualanController::index');
$routes->get('/api/detail-penjualan/(:num)', 'DetailPenjualanController::show/$1');
$routes->post('/api/detail-penjualan/create', 'DetailPenjualanController::create');
$routes->put('/api/detail-penjualan/(:num)', 'DetailPenjualanController::update/$1');
$routes->delete('/api/detail-penjualan/(:num)', 'DetailPenjualanController::delete/$1');

// invoice
$routes->get('/api/invoice', 'VWInvoiceController::index');
$routes->get('/api/invoice/(:num)', 'VWInvoiceController::showInvoice/$1');
$routes->get('/api/invoice/filter', 'VWInvoiceController::filterByDate');
$routes->get('/api/invoice/detail', 'VWInvoiceController::getAll');

$routes->get('/api/invoice/excel/create', 'VWInvoiceController::createExcel');
$routes->get('/api/invoice/excel/bydate/create/(:any)/(:any)', 'VWInvoiceController::createExcelByDate/$1/$2');

// $routes->setAutoRoute(true); // for allow access all route
