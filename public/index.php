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
$router->add('find-homes', 'PropertyController', 'findHomes');
$router->add('how-it-works', 'PropertyController', 'howItWorks');
$router->add('pricing', 'PropertyController', 'pricing');
$router->add('support', 'PropertyController', 'support');
$router->add('property/searchMap', 'PropertyController', 'searchMap');
$router->add('auth/login', 'AuthController', 'login');
$router->add('auth/signup', 'AuthController', 'signup');
$router->add('auth/logout', 'AuthController', 'logout');
$router->add('tenant/dashboard', 'TenantController', 'dashboard');
$router->add('tenant/verify', 'TenantController', 'verify');
$router->add('tenant/verify-submit', 'TenantController', 'submitVerification');
$router->add('tenant/request-rental', 'TenantController', 'requestRental');
$router->add('tenant/property', 'TenantController', 'propertyDetails');
$router->add('tenant/pay', 'TenantController', 'processPayment');
$router->add('landlord/dashboard', 'LandlordController', 'dashboard');
$router->add('landlord/verify', 'LandlordController', 'verify');
$router->add('landlord/verify-submit', 'LandlordController', 'submitVerification');
$router->add('landlord/add-property', 'LandlordController', 'addProperty');
$router->add('landlord/save-property', 'LandlordController', 'saveProperty');
$router->add('landlord/edit-property', 'LandlordController', 'editProperty');
$router->add('landlord/update-property', 'LandlordController', 'updateProperty');
$router->add('landlord/dashboard', 'LandlordController', 'dashboard');
$router->add('profile/edit', 'ProfileController', 'edit');
$router->add('profile/update', 'ProfileController', 'update');

$router->add('admin/cms', 'CmsController', 'index');
$router->add('staff/cms', 'CmsController', 'index');
$router->add('cms/update-page', 'CmsController', 'updatePage');
$router->add('cms/update-settings', 'CmsController', 'updateSettings');

$router->add('staff/dashboard', 'StaffController', 'dashboard');
$router->add('staff/pending', 'StaffController', 'pending');
$router->add('staff/view-property', 'StaffController', 'viewProperty');
$router->add('staff/submit-verification', 'StaffController', 'submitVerification');
$router->add('staff/history', 'StaffController', 'history');
$router->add('lawyer/dashboard', 'LawyerController', 'dashboard');
$router->add('lawyer/requests', 'LawyerController', 'requests');
$router->add('lawyer/draft-agreement', 'LawyerController', 'draftAgreement');
$router->add('lawyer/save-agreement', 'LawyerController', 'saveAgreement');
$router->add('lawyer/view-agreement', 'LawyerController', 'viewAgreement');
$router->add('lawyer/sign-agreement', 'LawyerController', 'signAgreement');
$router->add('lawyer/agreements', 'LawyerController', 'agreements');
$router->add('admin/dashboard', 'AdminController', 'dashboard');
$router->add('admin/users', 'AdminController', 'users');
$router->add('admin/properties', 'AdminController', 'properties');
$router->add('admin/verifications', 'AdminController', 'verifications');
$router->add('admin/transactions', 'AdminController', 'transactions');
$router->add('admin/requests', 'AdminController', 'requests');
$router->add('admin/updateRequestStatus', 'AdminController', 'updateRequestStatus');
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
$router->add('notifications', 'NotificationController', 'index');
$router->add('notifications/mark-read', 'NotificationController', 'markRead');

$router->add('messages', 'MessageController', 'index');
$router->add('messages/fetch', 'MessageController', 'fetchThread');
$router->add('messages/send', 'MessageController', 'send');

$router->dispatch($_SERVER['REQUEST_URI']);
