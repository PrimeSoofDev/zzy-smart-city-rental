<?php
require_once '../config/config.php';
require_once '../app/Core/Database.php';
require_once '../app/Core/Controller.php';
require_once '../app/Core/Router.php';
require_once '../app/Core/Request.php';
require_once '../app/Middleware/RbacMiddleware.php';

spl_autoload_register(function ($class) {
    $paths = ['../app/Models/', '../app/Controllers/', '../app/Core/', '../app/Services/'];
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
$router->add('tenant/disputes', 'TenantController', 'disputes');
$router->add('tenant/verify', 'TenantController', 'verify');
$router->add('tenant/verify-submit', 'TenantController', 'submitVerification');
$router->add('tenant/request-rental', 'TenantController', 'requestRental');
$router->add('tenant/property', 'TenantController', 'propertyDetails');
$router->add('tenant/pay', 'TenantController', 'processPayment');
$router->add('tenant/payment-verify', 'TenantController', 'verifyPayment');
$router->add('landlord/dashboard', 'LandlordController', 'dashboard');
$router->add('landlord/disputes', 'LandlordController', 'disputes');
$router->add('landlord/verify', 'LandlordController', 'verify');
$router->add('landlord/verify-submit', 'LandlordController', 'submitVerification');
$router->add('landlord/add-property', 'LandlordController', 'addProperty');
$router->add('landlord/save-property', 'LandlordController', 'saveProperty');
$router->add('landlord/edit-property', 'LandlordController', 'editProperty');
$router->add('landlord/update-property', 'LandlordController', 'updateProperty');
$router->add('landlord/bank-details', 'LandlordController', 'bankDetails');
$router->add('landlord/bank-save', 'LandlordController', 'saveBankDetails');
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
$router->add('staff/escrow', 'StaffController', 'escrowPayments');
$router->add('staff/escrow-release', 'StaffController', 'releaseFunds');
$router->add('staff/escrow-refund', 'StaffController', 'refundFunds');
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
$router->add('admin/export-properties', 'AdminController', 'exportProperties');
$router->add('admin/verifications', 'AdminController', 'verifications');
$router->add('admin/transactions', 'AdminController', 'transactions');
$router->add('admin/requests', 'AdminController', 'requests');
$router->add('admin/updateRequestStatus', 'AdminController', 'updateRequestStatus');
$router->add('admin/disputes', 'AdminController', 'disputes');
$router->add('admin/disputes/mediate', 'AdminController', 'mediateDispute');
$router->add('admin/reviews', 'ReviewController', 'adminIndex');
$router->add('admin/review/toggle', 'ReviewController', 'toggleStatus');
$router->add('review/submit', 'ReviewController', 'submit');
$router->add('admin/resolveDispute', 'AdminController', 'resolveDispute');
$router->add('admin/settings', 'AdminController', 'settings');
$router->add('admin/updateSettings', 'AdminController', 'updateSettings');
$router->add('admin/add-user', 'AdminController', 'addUser');
$router->add('auth/reset-password', 'AuthController', 'resetPasswordRequest');
$router->add('auth/set-password', 'AuthController', 'setPassword');
$router->add('auth/verify-otp', 'AuthController', 'verifyOtpView');
$router->add('auth/verify-otp-submit', 'AuthController', 'verifyOtpSubmit');
$router->add('admin/landlords', 'AdminController', 'landlords');
$router->add('admin/edit-user', 'AdminController', 'editUser');
$router->add('admin/update-user', 'AdminController', 'updateLandlord');
$router->add('admin/approve-user', 'AdminController', 'approveUser');
$router->add('admin/reject-user', 'AdminController', 'rejectUser');
$router->add('admin/reject-user-with-reason', 'AdminController', 'rejectUserWithReason');
$router->add('admin/ban-user', 'AdminController', 'banUser');
$router->add('profile/edit', 'ProfileController', 'edit');
$router->add('profile/update', 'ProfileController', 'update');
$router->add('profile/change-password', 'ProfileController', 'changePassword');
$router->add('profile/update-password', 'ProfileController', 'updatePassword');

$router->add('admin/update-user-profile', 'AdminController', 'updateUserProfile');
$router->add('admin/approve-property', 'AdminController', 'approveProperty');
$router->add('admin/reject-property', 'AdminController', 'rejectProperty');
$router->add('admin/approve-landlord', 'AdminController', 'approveLandlord');
$router->add('admin/delete-user', 'AdminController', 'deleteLandlord');
$router->add('admin/manageUser', 'AdminController', 'manageUser');
$router->add('admin/logs', 'AdminController', 'logs');
$router->add('admin/export-logs', 'AdminController', 'exportLogs');
$router->add('notifications', 'NotificationController', 'index');
$router->add('notifications/mark-read', 'NotificationController', 'markRead');

$router->add('messages', 'MessageController', 'index');
$router->add('messages/fetch', 'MessageController', 'fetchThread');
$router->add('messages/send', 'MessageController', 'send');
$router->add('messages/uploadFile', 'MessageController', 'uploadFile');
$router->add('api/otp/send', 'OtpController', 'sendOtp');
$router->add('api/otp/verify', 'OtpController', 'verifyOtp');

$router->dispatch($_SERVER['REQUEST_URI']);
