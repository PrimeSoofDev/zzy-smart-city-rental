<?php
// Layout Wrapper for Landlord Pages
require_once "../config/config.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Landlord Dashboard | ZZY Rental</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar-transition { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .active-link { @apply bg-blue-600 text-white shadow-md; }
    </style>
</head>
<body class="bg-gray-100 font-sans text-gray-900 overflow-x-hidden">

    <!-- Mobile Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden transition-opacity duration-300"></div>

    <!-- SIDEBAR -->
    <aside id="sidebar" class="fixed top-0 left-0 z-50 h-screen w-64 bg-slate-900 text-slate-300 sidebar-transition transform -translate-x-full lg:translate-x-0 overflow-y-auto">
        <div class="flex items-center justify-between p-6 border-b border-slate-800">
            <div class="flex items-center gap-3">
                <div class="bg-blue-600 p-2 rounded-lg">
                    <i class="fas fa-building text-white text-xl"></i>
                </div>
                <span class="text-xl font-bold text-white tracking-tight">Landlord Hub</span>
            </div>
            <button id="closeSidebar" class="lg:hidden text-slate-400 hover:text-white">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <nav class="p-4 space-y-2">
            <p class="text-xs font-semibold text-slate-500 uppercase px-4 mb-2 tracking-wider">Main</p>
            <a href="<?= APP_URL ?>/landlord/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-slate-800 hover:text-white <?= (strpos($_SERVER['REQUEST_URI'], 'landlord/dashboard') !== false) ? 'bg-blue-600 text-white shadow-md' : '' ?>">
                <i class="fas fa-chart-pie w-5"></i> <span>Dashboard</span>
            </a>

            <a href="<?= APP_URL ?>/landlord/add-property" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-slate-800 hover:text-white <?= (strpos($_SERVER['REQUEST_URI'], 'landlord/add-property') !== false) ? 'bg-blue-600 text-white shadow-md' : '' ?>">
                <i class="fas fa-plus-circle w-5"></i> <span>Add Property</span>
            </a>

            <a href="<?= APP_URL ?>/landlord/verify" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-slate-800 hover:text-white <?= (strpos($_SERVER['REQUEST_URI'], 'landlord/verify') !== false) ? 'bg-blue-600 text-white shadow-md' : '' ?>">
                <i class="fas fa-id-card w-5"></i> <span>My Verification</span>
            </a>

            <?php
            require_once __DIR__ . '/../../app/Models/Notification.php';
            $unreadNotifs = Notification::getUnreadCount($_SESSION['user_id'] ?? 0);
            ?>
            <a href="<?= APP_URL ?>/notifications" class="flex items-center justify-between px-4 py-3 rounded-xl transition-all hover:bg-slate-800 hover:text-white <?= (strpos($_SERVER['REQUEST_URI'], 'notifications') !== false) ? 'bg-blue-600 text-white shadow-md' : '' ?>">
                <div class="flex items-center gap-3">
                    <i class="fas fa-bell w-5"></i> <span>Notifications</span>
                </div>
                <?php if ($unreadNotifs > 0): ?>
                    <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full"><?= $unreadNotifs ?></span>
                <?php endif; ?>
            </a>

            <div class="pt-6 mt-6 border-t border-slate-800">
                <a href="<?= APP_URL ?>/auth/logout" class="flex items-center gap-3 px-4 py-3 rounded-xl text-red-400 transition-all hover:bg-red-900/20 hover:text-red-300">
                    <i class="fas fa-sign-out-alt w-5"></i> <span>Logout</span>
                </a>
            </div>
        </nav>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <div class="lg:ml-64 min-h-screen flex flex-col">
        <!-- TOP NAVBAR -->
        <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-6 sticky top-0 z-30">
            <div class="flex items-center gap-4">
                <button id="menuToggle" class="lg:hidden text-gray-600 p-2 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <h2 class="text-lg font-semibold text-gray-800 hidden sm:block">Landlord Portal</h2>
            </div>

            <div class="flex items-center gap-4">
                <a href="<?= APP_URL ?>/notifications" class="relative text-gray-400 hover:text-gray-600 transition p-2">
                    <i class="fas fa-bell text-xl"></i>
                    <?php if ($unreadNotifs > 0): ?>
                        <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
                    <?php endif; ?>
                </a>
                <div class="flex items-center gap-3 pl-4 border-l border-gray-200">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-bold text-gray-800 leading-none"><?= $_SESSION['username'] ?? 'Landlord' ?></p>
                        <p class="text-[10px] text-gray-500 uppercase font-semibold">Verified Landlord</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold shadow-sm">
                        L
                    </div>
                </div>
            </div>
        </header>

        <!-- PAGE CONTENT -->
        <main class="p-6 flex-grow">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-800 rounded-r-xl shadow-sm flex items-center gap-3 animate-fade-in">
                    <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    <p class="text-sm font-medium"><?= $_SESSION['success'] ?></p>
                    <button onclick="this.parentElement.remove()" class="ml-auto text-green-600 hover:text-green-800">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-800 rounded-r-xl shadow-sm flex items-center gap-3 animate-fade-in">
                    <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                    <p class="text-sm font-medium"><?= $_SESSION['error'] ?></p>
                    <button onclick="this.parentElement.remove()" class="ml-auto text-red-600 hover:text-red-800">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>
