<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ==================== ACCUEIL → Redirige vers login ====================
$routes->get('/', 'AuthController::loginClient');

// ==================== AUTH ROUTES ====================
$routes->get('login', function() {
    return redirect()->to('login/client');
});

$routes->get('login/client', 'AuthController::loginClient');
$routes->post('login/client', 'AuthController::doLoginClient');

$routes->get('login/admin', 'AuthController::loginAdmin');
$routes->post('login/admin', 'AuthController::doLoginAdmin');

$routes->get('logout', 'AuthController::logout');

// ==================== ADMIN ROUTES (protégées par AdminFilter) ====================
$routes->group('admin', function($routes) {
    // Dashboard
    $routes->get('dashboard', 'AdminController::dashboard');
    
    // Préfixes Opérateurs CRUD
    $routes->get('operators', 'AdminController::operators');
    $routes->get('operators/detail/(:num)', 'AdminController::operatorDetail/$1');
    $routes->post('operators/create', 'AdminController::createOperator');
    $routes->post('operators/update/(:num)', 'AdminController::updateOperator/$1');
    $routes->post('operators/delete/(:num)', 'AdminController::deleteOperator/$1');

    // Barèmes de Frais (par opérateur)
    $routes->post('operators/(:num)/fees/create', 'AdminController::createFee/$1');
    $routes->post('operators/(:num)/fees/update/(:num)', 'AdminController::updateFee/$2/$1');

    // Comptes Clients
    $routes->get('clients', 'AdminController::clients');

    // Situation des Gains
    $routes->get('gains', 'AdminController::gains');
});

// ==================== CLIENT ROUTES (protégées par ClientFilter) ====================
$routes->group('client', function($routes) {
    $routes->get('dashboard', 'Client::dashboard');
    $routes->get('balance', 'Client::balance');
    $routes->get('deposit', 'Client::deposit');
    $routes->post('deposit', 'Client::doDeposit');
    $routes->get('withdraw', 'Client::withdraw');
    $routes->post('withdraw', 'Client::doWithdraw');
    $routes->get('transfer', 'Client::transfer');
    $routes->post('transfer', 'Client::doTransfer');
    $routes->get('history', 'Client::history');
});

// ==================== API ROUTES (protégées par ClientFilter) ====================
$routes->group('api', function($routes) {
    $routes->get('client/balance', 'Api::getBalance');
    $routes->post('fees/calculate', 'Api::calculateFees');
});