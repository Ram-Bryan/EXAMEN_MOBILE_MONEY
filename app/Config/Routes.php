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

    // Préfixes historiques d'un opérateur (v2)
    $routes->post('operators/(:num)/prefixes/add', 'AdminController::addPrefixe/$1');

    // Commissions inter-opérateurs (v2)
    $routes->post('operators/(:num)/commission/update', 'AdminController::updateCommission/$1');

    // Comptes Clients
    $routes->get('clients', 'AdminController::clients');

    // Commissions inter-opérateurs
    $routes->get('commissions', 'AdminController::commissions');
    $routes->post('commissions/update/(:num)', 'AdminController::updateCommission/$1');

    // Historique des transactions
    $routes->get('transactions', 'AdminController::transactionsHistory');

    // Situation des Gains
    $routes->get('gains', 'AdminController::gains');
});

// ==================== CLIENT ROUTES (protégées par ClientFilter) ====================
$routes->group('client', function($routes) {
    $routes->get('dashboard', 'ClientModel::dashboard');
    $routes->get('balance', 'ClientModel::balance');
    $routes->get('deposit', 'ClientModel::deposit');
    $routes->post('deposit', 'ClientModel::doDeposit');
    $routes->get('withdraw', 'ClientModel::withdraw');
    $routes->post('withdraw', 'ClientModel::doWithdraw');
    $routes->get('transfer', 'ClientModel::transfer');
    $routes->post('transfer', 'ClientModel::doTransfer');
    $routes->get('history', 'ClientModel::history');
});

// ==================== API ROUTES (protégées par ClientFilter) ====================
$routes->group('api', function($routes) {
    $routes->get('client/balance', 'ApiController::getBalance');
    $routes->get('fees/calculate', 'ApiController::calculateFees');
});