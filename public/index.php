<?php
require_once '../config/config.php';
require_once '../app/Core/Database.php';
require_once '../app/Core/Controller.php';
require_once '../app/Core/Router.php';
require_once '../app/Core/Request.php';
require_once '../app/Middleware/RbacMiddleware.php';

spl_autoload_register(function ($class) {
    $paths = ['../app/Models/', '../app/Controllers/', '../app/Core/'];
    foreach ($paths as $path) {
        $file = $path . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

$router = new Router();

$router->add('/', 'PropertyController', 'index');
$router->add('auth/login', 'AuthController', 'login');
$router->add('auth/signup', 'AuthController', 'signup');
$router->add('auth/logout', 'AuthController', 'logout');
$router->add('tenant/dashboard', 'TenantController', 'dashboard');
$router->add('tenant/request-rental', 'TenantController', 'requestRental');
$router->add('landlord/dashboard', 'LandlordController', 'dashboard');
$router->add('landlord/verify', 'LandlordController', 'verify');
$router->add('landlord/verify-submit', 'LandlordController', 'submitVerification');
$router->add('landlord/add-property', 'LandlordController', 'addProperty');
$router->add('landlord/save-property', 'LandlordController', 'saveProperty');
$router->add('landlord/dashboard', 'LandlordController', 'dashboard');
$router->add('staff/dashboard', 'StaffController', 'dashboard');
$router->add('staff/verify', 'StaffController', 'verify');
$router->add('admin/dashboard', 'AdminController', 'dashboard');
$router->add('admin/users', 'AdminController', 'users');
$router->add('admin/properties', 'AdminController', 'properties');
$router->add('admin/verifications', 'AdminController', 'verifications');
$router->add('admin/transactions', 'AdminController', 'transactions');
$router->add('admin/settings', 'AdminController', 'settings');
$router->add('admin/updateSettings', 'AdminController', 'updateSettings');
$router->add('admin/add-user', 'AdminController', 'addUser');
$router->add('auth/reset-password', 'AuthController', 'resetPasswordRequest');
$router->add('auth/set-password', 'AuthController', 'setPassword');
$router->add('admin/landlords', 'AdminController', 'landlords');
$router->add('admin/edit-user', 'AdminController', 'editLandlord');
$router->add('admin/update-user', 'AdminController', 'updateLandlord');
$router->add('admin/approve-user', 'AdminController', 'approveUser');
$router->add('admin/reject-user', 'AdminController', 'rejectUser');
$router->add('admin/approve-property', 'AdminController', 'approveProperty');
$router->add('admin/reject-property', 'AdminController', 'rejectProperty');
$router->add('admin/approve-landlord', 'AdminController', 'approveLandlord');
$router->add('admin/delete-user', 'AdminController', 'deleteLandlord');
$router->add('admin/manageUser', 'AdminController', 'manageUser');

$router->dispatch($_SERVER['REQUEST_URI']);
