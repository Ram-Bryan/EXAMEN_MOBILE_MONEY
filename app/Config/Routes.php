<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'HomeController::index');

$routes->group('login', function ($routes) {
    $routes->get('client', 'AuthController::loginClient');
    $routes->post('client', 'AuthController::doLoginClient');
    $routes->get('admin', 'AuthController::loginAdmin');
    $routes->post('admin', 'AuthController::doLoginAdmin');
});

$routes->get('logout', 'AuthController::logout');

$routes->group('admin', ['filter' => 'adminAuth'], function ($routes) {
    $routes->get('dashboard', 'AdminController::dashboard');

    $routes->get('operators', 'AdminController::operators');
    $routes->get('operators/detail/(:num)', 'AdminController::operatorDetail/$1');
    $routes->post('operators/create', 'AdminController::createOperator');
    $routes->post('operators/update/(:num)', 'AdminController::updateOperator/$1');
    $routes->post('operators/delete/(:num)', 'AdminController::deleteOperator/$1');

    $routes->post('operators/(:num)/fees/create', 'AdminController::createFee/$1');
    $routes->post('operators/(:num)/fees/update/(:num)', 'AdminController::updateFee/$2/$1');

    $routes->post('operators/(:num)/prefixes/add', 'AdminController::addPrefixe/$1');

    $routes->post('operators/(:num)/commission/update', 'AdminController::updateCommission/$1');

    $routes->get('clients', 'AdminController::clients');
    $routes->get('commissions', 'AdminController::commissions');
    $routes->get('transactions', 'AdminController::transactionsHistory');
    $routes->get('gains', 'AdminController::gains');
    $routes->get('commissions', 'AdminController::commissions');
});

$routes->group('client', ['filter' => 'clientAuth'], function ($routes) {
    $routes->get('dashboard', 'ClientController::dashboard');
    $routes->get('balance', 'ClientController::balance');
    $routes->get('deposit', 'ClientController::deposit');
    $routes->post('deposit', 'ClientController::doDeposit');
    $routes->get('withdraw', 'ClientController::withdraw');
    $routes->post('withdraw', 'ClientController::doWithdraw');
    $routes->get('transfer', 'ClientController::transfer');
    $routes->post('transfer', 'ClientController::doTransfer');
    $routes->get('history', 'ClientController::history');
});

$routes->group('api', ['filter' => 'clientAuth'], function ($routes) {
    $routes->get('client/balance', 'ApiController::getBalance');
    $routes->get('fees/calculate', 'ApiController::calculateFees');
});