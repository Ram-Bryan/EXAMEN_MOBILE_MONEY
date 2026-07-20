<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ==================== ACCUEIL → Redirige vers login ====================
$routes->get('/', 'AuthController::login');

// ==================== AUTH ROUTES ====================
$routes->get('login', 'AuthController::login');
$routes->post('login', 'AuthController::doLogin');
$routes->get('logout', 'AuthController::logout');

// ==================== ADMIN ROUTES (protégées par AdminFilter) ====================
$routes->group('admin', function($routes) {
    // Dashboard
    $routes->get('dashboard', 'AdminController::dashboard');
    
    // Préfixes Opérateurs CRUD
    $routes->get('operators', 'AdminController::operators');
    $routes->post('operators/create', 'AdminController::createOperator');
    $routes->post('operators/update/(:num)', 'AdminController::updateOperator/$1');
    $routes->post('operators/delete/(:num)', 'AdminController::deleteOperator/$1');
    
    // Barèmes de Frais
    $routes->get('fees-config', 'AdminController::feesConfig');
    $routes->post('fees/create', 'AdminController::createFee');
    $routes->post('fees/update/(:num)', 'AdminController::updateFee/$1');
    
    // Comptes Clients
    $routes->get('clients', 'AdminController::clients');
    
    // Situation des Gains
    $routes->get('gains', 'AdminController::gains');
});