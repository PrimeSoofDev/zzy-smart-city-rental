<?php
// Layout Wrapper for Lawyer Pages
require_once "../config/config.php";
require_once "../app/Core/Database.php";
require_once "../app/Models/SiteSetting.php";
$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("SELECT avatar_url FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id'] ?? 0]);
$userAvatar = $stmt->fetchColumn();

$siteName = SiteSetting::get('site_name', 'ZZY Rental');
$faviconUrl = SiteSetting::get('favicon_url');
$logoUrl = SiteSetting::get('logo_url');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $siteName ?> | Lawyer Portal</title>
    <?php if($faviconUrl): ?>
    <link rel="icon" href="<?= APP_URL . '/' . $faviconUrl ?>">
    <?php endif; ?>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar-transition { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .active-link { background-color: #0f766e; color: #fff; box-shadow: 0 4px 12px rgba(15,118,110,0.3); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-8px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fade-in { animation: fadeIn 0.4s ease forwards; }

        /* Collapsible Sidebar Styles */
        .sidebar-collapsed { width: 80px !important; }
        .sidebar-collapsed .sidebar-text, 
        .sidebar-collapsed .sidebar-header-text,
        .sidebar-collapsed .sidebar-section-title,
        .sidebar-collapsed .sidebar-profile-card { display: none !important; }
        .sidebar-collapsed .sidebar-item { justify-content: center; padding-left: 0; padding-right: 0; }
        .sidebar-collapsed .sidebar-item i { font-size: 1.25rem; margin: 0; }
        .content-expanded { margin-left: 80px !important; }
        
        @media (max-width: 1024px) {
            .content-expanded { margin-left: 0 !important; }
            .sidebar-collapsed { transform: translateX(-100%); width: 256px !important; }
        }
    </style>
</head>
<body class="bg-gray-50 font-sans text-gray-900 overflow-x-hidden">

    <!-- Mobile Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden transition-opacity duration-300"></div>

    <!-- SIDEBAR -->
    <aside id="sidebar" class="fixed top-0 left-0 z-50 h-screen w-64 bg-slate-900 text-slate-300 sidebar-transition transform -translate-x-full lg:translate-x-0 overflow-y-auto">
        <div class="flex items-center justify-between p-6 border-b border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold shadow-sm overflow-hidden border border-gray-100">
                    <?php if ($userAvatar): ?>
                        <img src="<?= APP_URL ?>/<?= $userAvatar ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <?= strtoupper(substr($_SESSION['username'] ?? 'L', 0, 1)) ?>
                    <?php endif; ?>
                </div>
                <span class="text-xl font-bold text-white tracking-tight sidebar-header-text"><?= htmlspecialchars($_SESSION['username'] ?? 'Lawyer') ?></span>
            </div>
            <button id="closeSidebar" class="lg:hidden text-slate-400 hover:text-white">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div class="px-4 py-4 border-b border-slate-800 sidebar-profile-card">
            <div class="flex items-center gap-3 bg-slate-800 rounded-xl px-3 py-3">
                <div class="w-9 h-9 rounded-full bg-teal-700 flex items-center justify-center text-white font-bold text-sm shadow">
                    <?= strtoupper(substr($_SESSION['username'] ?? 'L', 0, 1)) ?>
                </div>
                <div>
                    <p class="text-sm font-semibold text-white leading-tight"><?= htmlspecialchars($_SESSION['username'] ?? 'Lawyer') ?></p>
                    <p class="text-[10px] text-teal-400 uppercase font-bold">Legal Representative</p>
                </div>
            </div>
        </div>

        <nav class="p-4 space-y-1">
            <p class="text-xs font-semibold text-slate-500 uppercase px-4 mb-2 mt-2 tracking-wider sidebar-section-title">Overview</p>
            <a href="<?= APP_URL ?>/lawyer/dashboard" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-slate-800 hover:text-white <?= (strpos($_SERVER['REQUEST_URI'], 'lawyer/dashboard') !== false) ? 'active-link' : '' ?>">
                <i class="fas fa-chart-bar w-5"></i> <span class="sidebar-text">Dashboard</span>
            </a>

            <p class="text-xs font-semibold text-slate-500 uppercase px-4 mb-2 mt-5 tracking-wider">Agreements</p>
            <a href="<?= APP_URL ?>/lawyer/requests" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-slate-800 hover:text-white <?= (strpos($_SERVER['REQUEST_URI'], 'lawyer/requests') !== false) ? 'active-link' : '' ?>">
                <i class="fas fa-inbox w-5 text-yellow-400"></i> <span class="sidebar-text">Paid Requests</span>
                <?php
                try {
                    $db = Database::getInstance()->getConnection();
                    // Count paid requests that don't yet have a 'signed' agreement
                    $count = $db->query("
                        SELECT COUNT(*) FROM rental_requests rr
                        WHERE rr.status = 'paid'
                        AND NOT EXISTS (
                            SELECT 1 FROM agreements a WHERE a.request_id = rr.id AND a.status = 'signed'
                        )
                    ")->fetchColumn();
                    if ($count > 0) echo '<span class="ml-auto bg-yellow-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full">' . $count . '</span>';
                } catch(Exception $e) {}
                ?>
            </a>
            <a href="<?= APP_URL ?>/lawyer/agreements" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-slate-800 hover:text-white <?= (strpos($_SERVER['REQUEST_URI'], 'lawyer/agreements') !== false) ? 'active-link' : '' ?>">
                <i class="fas fa-file-signature w-5 text-teal-400"></i> <span class="sidebar-text">All Agreements</span>
            </a>

            <?php
            require_once __DIR__ . '/../../app/Models/Notification.php';
            $unreadNotifs = Notification::getUnreadCount($_SESSION['user_id'] ?? 0);
            ?>
            <a href="<?= APP_URL ?>/notifications" class="flex items-center justify-between px-4 py-3 rounded-xl transition-all hover:bg-slate-800 hover:text-white <?= (strpos($_SERVER['REQUEST_URI'], 'notifications') !== false) ? 'active-link' : '' ?>">
                <div class="flex items-center gap-3">
                    <i class="fas fa-bell w-5 text-teal-400"></i> <span class="sidebar-text">Notifications</span>
                </div>
                <?php if ($unreadNotifs > 0): ?>
                    <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full"><?= $unreadNotifs ?></span>
                <?php endif; ?>
            </a>

            <?php
            require_once __DIR__ . '/../../app/Models/Message.php';
            $unreadMsgs = Message::getUnreadCount($_SESSION['user_id'] ?? 0);
            ?>
            <a href="<?= APP_URL ?>/messages" class="flex items-center justify-between px-4 py-3 rounded-xl transition-all hover:bg-slate-800 hover:text-white <?= (strpos($_SERVER['REQUEST_URI'], 'messages') !== false) ? 'active-link' : '' ?>">
                <div class="flex items-center gap-3">
                    <i class="fas fa-envelope w-5 text-teal-400"></i> <span class="sidebar-text">Chat</span>
                </div>
                <?php if ($unreadMsgs > 0): ?>
                    <span class="bg-teal-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full"><?= $unreadMsgs ?></span>
                <?php endif; ?>
            </a>

            <div class="pt-6 mt-6 border-t border-slate-800 space-y-2">
                <button id="desktopCollapse" class="hidden lg:flex w-full items-center gap-3 px-4 py-3 rounded-xl text-slate-400 transition-all hover:bg-slate-800 hover:text-white">
                    <i class="fas fa-indent w-5" id="collapseIcon"></i> <span class="sidebar-text">Collapse Sidebar</span>
                </button>

                <a href="<?= APP_URL ?>/auth/logout" class="sidebar-item flex items-center gap-3 px-4 py-3 rounded-xl text-red-400 transition-all hover:bg-red-900/20 hover:text-red-300">
                    <i class="fas fa-sign-out-alt w-5"></i> <span class="sidebar-text">Logout</span>
                </a>
            </div>
        </nav>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <div id="mainContent" class="lg:ml-64 min-h-screen flex flex-col sidebar-transition">
        <!-- TOP NAVBAR -->
        <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-6 sticky top-0 z-30 shadow-sm">
            <div class="flex items-center gap-4">
                <button id="menuToggle" class="lg:hidden text-gray-600 p-2 hover:bg-gray-100 rounded-lg">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <div class="flex items-center gap-3">
                    <?php if($logoUrl): ?>
                        <img src="<?= APP_URL . '/' . $logoUrl ?>" class="h-8">
                    <?php else: ?>
                        <h2 class="text-lg font-black text-slate-800 hidden sm:block uppercase tracking-tighter"><?= $siteName ?></h2>
                    <?php endif; ?>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="<?= APP_URL ?>/messages" class="relative text-gray-400 hover:text-teal-600 transition p-2 mr-2">
                    <i class="fas fa-envelope text-xl"></i>
                    <?php if ($unreadMsgs > 0): ?>
                        <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-teal-500 rounded-full border-2 border-white"></span>
                    <?php endif; ?>
                </a>
                <a href="<?= APP_URL ?>/notifications" class="relative text-gray-400 hover:text-gray-600 transition p-2 mr-2">
                    <i class="fas fa-bell text-xl"></i>
                    <?php if ($unreadNotifs > 0): ?>
                        <span class="absolute top-1 right-1 w-2.5 h-2.5 bg-red-500 rounded-full border-2 border-white"></span>
                    <?php endif; ?>
                </a>
                <span class="hidden sm:inline-flex items-center gap-2 bg-teal-50 text-teal-700 text-xs font-semibold px-3 py-1.5 rounded-full border border-teal-200">
                    <i class="fas fa-circle text-[8px] text-teal-500 animate-pulse"></i> Active Session
                </span>
                <div class="w-9 h-9 rounded-full bg-teal-700 flex items-center justify-center text-white font-bold text-sm shadow">
                    <?= strtoupper(substr($_SESSION['username'] ?? 'L', 0, 1)) ?>
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

