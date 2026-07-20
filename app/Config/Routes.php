<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

// ==================== ADMIN ROUTES ====================
$routes->group('admin', function($routes) {
    // Dashboard
    $routes->get('dashboard', 'Admin::dashboard');
    
    // Operators CRUD
    $routes->get('operators', 'Admin::operators');
    $routes->post('operators/create', 'Admin::createOperator');
    $routes->post('operators/update/(:num)', 'Admin::updateOperator/$1');
    $routes->delete('operators/delete/(:num)', 'Admin::deleteOperator/$1');
    $routes->get('operators/get/(:num)', 'Admin::getOperator/$1');
    
    // Operations Types
    $routes->get('operations-types', 'Admin::operationsTypes');
    $routes->post('operations-types/create', 'Admin::createOperationType');
    $routes->post('operations-types/update/(:num)', 'Admin::updateOperationType/$1');
    $routes->delete('operations-types/delete/(:num)', 'Admin::deleteOperationType/$1');
    
    // Fees Configuration
    $routes->get('fees-config', 'Admin::feesConfig');
    $routes->post('fees/create', 'Admin::createFee');
    $routes->post('fees/update/(:num)', 'Admin::updateFee/$1');
    $routes->delete('fees/delete/(:num)', 'Admin::deleteFee/$1');
    $routes->get('fees/get/(:num)', 'Admin::getFee/$1');
    
    // Clients
    $routes->get('clients', 'Admin::clients');
    $routes->get('clients/list', 'Admin::clientList');
    
    // Transactions
    $routes->get('transactions', 'Admin::transactions');
    
    // Gains
    $routes->get('gains', 'Admin::gains');
});

// ==================== CLIENT ROUTES ====================
$routes->group('client', function($routes) {
    // Dashboard
    $routes->get('dashboard', 'Client::dashboard');
    
    // Balance
    $routes->get('balance', 'Client::balance');
    
    // Deposit
    $routes->get('deposit', 'Client::deposit');
    $routes->post('deposit', 'Client::doDeposit');
    
    // Withdraw
    $routes->get('withdraw', 'Client::withdraw');
    $routes->post('withdraw', 'Client::doWithdraw');
    
    // Transfer
    $routes->get('transfer', 'Client::transfer');
    $routes->post('transfer', 'Client::doTransfer');
    
    // History
    $routes->get('history', 'Client::history');
});

// ==================== OPERATOR ROUTES ====================
$routes->group('operator', function($routes) {
    // Dashboard
    $routes->get('dashboard', 'Operator::dashboard');
    
    // Prefix Configuration
    $routes->get('prefix-config', 'Operator::prefixConfig');
    $routes->post('prefix-config/update', 'Operator::updatePrefix');
    
    // Operations Types
    $routes->get('operations-types', 'Operator::operationsTypes');
    
    // Fees Configuration
    $routes->get('fees-config', 'Operator::feesConfig');
    
    // Gains
    $routes->get('gains', 'Operator::gains');
    
    // Clients
    $routes->get('clients', 'Operator::clients');
});

// ==================== API ROUTES ====================
$routes->group('api', function($routes) {
    // Client balance
    $routes->get('client/balance', 'Api::getBalance');
    
    // Calculate fees
    $routes->post('fees/calculate', 'Api::calculateFees');
});

// ==================== AUTH ROUTES ====================
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::doLogin');
$routes->get('logout', 'Auth::logout');

// ==================== HOME ROUTE ====================
$routes->get('/', 'Home::index');