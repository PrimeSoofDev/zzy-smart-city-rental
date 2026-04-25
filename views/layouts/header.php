<?php
require_once __DIR__ . '/../../app/Models/SiteSetting.php';
$logoUrl = SiteSetting::get('logo_url');
$faviconUrl = SiteSetting::get('favicon_url');
$siteName = SiteSetting::get('site_name', 'ZZY Rental');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $siteName ?></title>
    <?php if($faviconUrl): ?>
    <link rel="icon" href="<?= APP_URL . '/' . $faviconUrl ?>">
    <?php endif; ?>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .modal-overlay { background-color: rgba(0, 0, 0, 0.5); }
    </style>
</head>
<body class="bg-gray-50 text-gray-900">
    <nav class="bg-white shadow-sm p-4 flex justify-between items-center">
        <a href="<?= APP_URL ?>/" class="flex items-center gap-3">
            <?php if($logoUrl): ?>
                <img src="<?= APP_URL . '/' . $logoUrl ?>" class="h-8">
            <?php else: ?>
                <span class="text-2xl font-bold text-blue-600">ZZY Rental</span>
            <?php endif; ?>
        </a>
        <div class="flex items-center gap-4">
            <?php if(isset($_SESSION['user_id'])): ?>
                <span class="text-sm text-gray-500 font-medium"><?= $_SESSION['role'] ?></span>
                <a href="<?= APP_URL ?>/auth/logout" class="text-red-500 hover:text-red-700 font-semibold">Logout</a>
            <?php else: ?>
                <a href="<?= APP_URL ?>/auth/login" class="text-gray-600 hover:text-blue-600 font-semibold">Login</a>
                <a href="<?= APP_URL ?>/auth/signup" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition">Sign Up</a>
            <?php endif; ?>

        </div>
    </nav>
    <main class="container mx-auto p-6">
