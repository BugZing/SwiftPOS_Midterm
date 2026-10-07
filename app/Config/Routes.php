<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Landing & System Info Pages
$routes->get('/', 'Pages::index');
$routes->get('about', 'Pages::about');

// Authentication
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::authenticate');
$routes->post('logout', 'Auth::logout', ['filter' => 'auth']);

// All management pages are strictly protected by the auth filter
$routes->group('', ['filter' => 'auth'], static function ($routes): void {
    // Product Management (List, Add, Edit, Delete with Image Upload)
    $routes->get('products', 'Products::index');
    $routes->get('products/new', 'Products::newForm');
    $routes->post('products', 'Products::create');
    $routes->get('products/(:num)/edit', 'Products::edit/$1');
    $routes->post('products/(:num)', 'Products::update/$1');
    $routes->post('products/(:num)/delete', 'Products::delete/$1');

    // Customer Management (Full CRUD)
    $routes->get('customers', 'Customers::index');
    $routes->get('customers/new', 'Customers::newForm');
    $routes->post('customers', 'Customers::create');
    $routes->get('customers/(:num)/edit', 'Customers::edit/$1');
    $routes->post('customers/(:num)', 'Customers::update/$1');
    $routes->post('customers/(:num)/delete', 'Customers::delete/$1');

    // Staff (User) Management (Full CRUD, Avatar Upload, Password Hashing)
    $routes->get('users', 'Users::index');
    $routes->get('users/new', 'Users::newForm');
    $routes->post('users', 'Users::create');
    $routes->get('users/(:num)/edit', 'Users::edit/$1');
    $routes->post('users/(:num)', 'Users::update/$1');
    $routes->post('users/(:num)/delete', 'Users::delete/$1');

    // POS Transactions (Record Sale & Sales History)
    $routes->get('sales', 'Sales::index');
    $routes->get('sales/history', 'Sales::index');
    $routes->get('sales/new', 'Sales::newForm');
    $routes->post('sales', 'Sales::create');
});
