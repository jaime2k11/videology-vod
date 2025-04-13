<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// We get a performance increase by specifying the default
// route since we don't have to scan directories.
//$routes->get('/', 'Home::index');
$routes->get('/', 'UploadController::index');
$routes->post('/upload', 'UploadController::upload');
$routes->get('/videos', 'UploadController::list');
$routes->get('/test', 'UploadController::test');
$routes->cli('UploadController/test', 'UploadController::test');
