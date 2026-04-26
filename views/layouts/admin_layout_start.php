<?php
// Layout Wrapper for Admin Pages
require_once "../config/config.php";
require_once "../app/Core/Database.php";
require_once "../app/Models/SiteSetting.php";
$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("SELECT avatar_url FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id'] ?? 0]);
$userAvatar = $stmt->fetchColumn();

$siteName = SiteSetting::get('site_name') ?: SiteSetting::get('platform_name', 'ZZY Rental');
$faviconUrl = SiteSetting::get('favicon_url');
$logoUrl = SiteSetting::get('logo_url');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $siteName ?> | Admin Panel</title>
    <?php if($faviconUrl): ?>
    <link rel="icon" href="<?= APP_URL . '/' . $faviconUrl ?>">
    <?php endif; ?>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class' }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .sidebar-transition { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        .active-link { @apply bg-blue-600 text-white shadow-md; }

        /* Collapsible Sidebar Styles */
        .sidebar-collapsed { width: 80px !important; }
        .sidebar-collapsed .sidebar-text, 
        .sidebar-collapsed .sidebar-header-text,
        .sidebar-collapsed .sidebar-section-title { display: none !important; }
        .sidebar-collapsed .sidebar-item { justify-content: center; padding-left: 0; padding-right: 0; }
        .sidebar-collapsed .sidebar-item i { font-size: 1.25rem; margin: 0; }
        .content-expanded { margin-left: 80px !important; }
        
        @media (max-width: 1024px) {
            .content-expanded { margin-left: 0 !important; }
            .sidebar-collapsed { transform: translateX(-100%); width: 256px !important; }
        }

        /* Dark Mode Overrides */
        html.dark body { background-color: #0f172a !important; color: #f8fafc !important; }
        html.dark header, html.dark .bg-white { background-color: #1e293b !important; color: #f8fafc !important; border-color: #334155 !important; }
        html.dark .text-gray-900, html.dark .text-gray-800, html.dark .text-gray-700, html.dark .text-gray-600, html.dark .text-slate-800, html.dark .text-slate-900 { color: #f1f5f9 !important; }
        html.dark .text-gray-500, html.dark .text-slate-500 { color: #94a3b8 !important; }
        html.dark .border-gray-100, html.dark .border-gray-200, html.dark .border-gray-300 { border-color: #334155 !important; }
        html.dark input, html.dark textarea, html.dark select { background-color: #0f172a !important; color: white !important; border-color: #334155 !important; }
        html.dark .bg-gray-50, html.dark .bg-gray-100, html.dark .bg-slate-50 { background-color: #0f172a !important; }
        html.dark .shadow-sm, html.dark .shadow, html.dark .shadow-md, html.dark .shadow-lg, html.dark .shadow-xl { box-shadow: none !important; border: 1px solid #334155 !important; }
        html.dark a:hover.bg-gray-50 { background-color: #334155 !important; color: white !important; }
        html.dark .bubble-in { background-color: #334155 !important; color: #f8fafc !important; border-color: #475569 !important; }
        html.dark .bubble-out { background-color: #054a40 !important; color: #f8fafc !important; }
        html.dark .message-actions, html.dark .reaction-bar { background-color: #1e293b !important; border: 1px solid #334155 !important; }
    </style>
    <script>
        if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
        function toggleDarkMode() {
            if (document.documentElement.classList.contains('dark')) {
                document.documentElement.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                document.documentElement.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        }
    </script>
</head>
<body class="bg-gray-100 font-sans text-gray-900 overflow-x-hidden">

    <!-- Mobile Overlay -->
    <div id="sidebarOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden transition-opacity duration-300"></div>

    <!-- SIDEBAR -->
    <aside id="sidebar" class="fixed top-0 left-0 z-50 h-screen w-64 bg-slate-900 text-slate-300 sidebar-transition transform -translate-x-full lg:translate-x-0 overflow-y-auto">
        <div class="flex items-center justify-between p-6 border-b border-slate-800">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white font-bold shadow-lg overflow-hidden border border-white/10">
                    <?php if ($userAvatar): ?>
                        <img src="<?= APP_URL ?>/<?= $userAvatar ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <?= strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)) ?>
                    <?php endif; ?>
                </div>
                <span class="text-xl font-bold text-white tracking-tight sidebar-header-text"><?= htmlspecialchars($_SESSION['username'] ?? 'Admin') ?></span>
            </div>
            <button id="closeSidebar" class="lg:hidden text-slate-400 hover:text-white">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <nav class="p-4 space-y-2">
            <p class="text-[10px] font-black text-slate-600 uppercase px-6 mb-4 mt-4 tracking-[0.2em] sidebar-section-title">Core</p>
            <a href="<?= APP_URL ?>/admin/dashboard" class="sidebar-item flex items-center gap-3 px-6 py-3 rounded-xl transition-all hover:bg-slate-800 hover:text-white <?= (strpos($_SERVER['REQUEST_URI'], 'admin/dashboard') !== false) ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/20' : '' ?>">
                <i class="fas fa-chart-pie w-5"></i> <span class="sidebar-text">Command Center</span>
            </a>

            <p class="text-[10px] font-black text-slate-600 uppercase px-6 mb-4 mt-8 tracking-[0.2em] sidebar-section-title">Staff Management</p>
            <div class="space-y-1">
                <a href="<?= APP_URL ?>/admin/add-user?role=Staff" class="sidebar-item flex items-center gap-3 px-6 py-2 rounded-xl transition-all hover:bg-slate-800 hover:text-white">
                    <i class="fas fa-user-shield w-5 text-blue-400"></i> <span class="sidebar-text">Onboard Staff</span>
                </a>
                <a href="<?= APP_URL ?>/admin/add-user?role=Lawyer" class="sidebar-item flex items-center gap-3 px-6 py-2 rounded-xl transition-all hover:bg-slate-800 hover:text-white">
                    <i class="fas fa-gavel w-5 text-indigo-400"></i> <span class="sidebar-text">Add Legal Rep</span>
                </a>
            </div>

            <p class="text-[10px] font-black text-slate-600 uppercase px-6 mb-4 mt-8 tracking-[0.2em] sidebar-section-title">Operations</p>
            <a href="<?= APP_URL ?>/admin/properties?status=pending" class="sidebar-item flex items-center gap-3 px-6 py-3 rounded-xl transition-all hover:bg-slate-800 hover:text-white <?= (strpos($_SERVER['REQUEST_URI'], 'status=pending') !== false) ? 'bg-amber-500 text-slate-900 shadow-lg' : '' ?>">
                <i class="fas fa-clock w-5 text-amber-400"></i> <span class="sidebar-text">Approvals</span>
            </a>
            <a href="<?= APP_URL ?>/admin/transactions" class="sidebar-item flex items-center gap-3 px-6 py-3 rounded-xl transition-all hover:bg-slate-800 hover:text-white">
                <i class="fas fa-vault w-5 text-emerald-400"></i> <span class="sidebar-text">Escrow & Payouts</span>
            </a>
            <a href="<?= APP_URL ?>/admin/disputes" class="sidebar-item flex items-center gap-3 px-6 py-3 rounded-xl transition-all hover:bg-slate-800 hover:text-white">
                <i class="fas fa-balance-scale w-5 text-red-400"></i> <span class="sidebar-text">Dispute Center</span>
            </a>
            <a href="<?= APP_URL ?>/admin/reviews" class="sidebar-item flex items-center gap-3 px-6 py-3 rounded-xl transition-all hover:bg-slate-800 hover:text-white <?= (strpos($_SERVER['REQUEST_URI'], 'admin/reviews') !== false) ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/20' : '' ?>">
                <i class="fas fa-star w-5 text-amber-400"></i> <span class="sidebar-text">User Reviews</span>
            </a>
            <a href="<?= APP_URL ?>/admin/visitors" class="sidebar-item flex items-center gap-3 px-6 py-3 rounded-xl transition-all hover:bg-slate-800 hover:text-white <?= (strpos($_SERVER['REQUEST_URI'], 'admin/visitors') !== false) ? 'bg-indigo-600 text-white shadow-lg' : '' ?>">
                <i class="fas fa-eye w-5 text-indigo-400"></i> <span class="sidebar-text">Visitor Analytics</span>
            </a>
            <?php
            require_once __DIR__ . '/../../app/Models/Message.php';
            $unreadMsgs = Message::getUnreadCount($_SESSION['user_id'] ?? 0);
            ?>
            <a href="<?= APP_URL ?>/messages" class="sidebar-item flex items-center justify-between px-6 py-3 rounded-xl transition-all hover:bg-slate-800 hover:text-white <?= (strpos($_SERVER['REQUEST_URI'], 'messages') !== false) ? 'bg-emerald-600 text-white shadow-lg' : '' ?>">
                <div class="flex items-center gap-3">
                    <i class="fas fa-comments w-5 text-emerald-400"></i> <span class="sidebar-text">Team Chat</span>
                </div>
                <?php if ($unreadMsgs > 0): ?>
                    <span class="bg-emerald-400 text-slate-900 text-[10px] font-black px-2 py-0.5 rounded-full"><?= $unreadMsgs ?></span>
                <?php endif; ?>
            </a>

            <p class="text-[10px] font-black text-slate-600 uppercase px-6 mb-4 mt-8 tracking-[0.2em] sidebar-section-title">Directory</p>
            <a href="<?= APP_URL ?>/admin/users" class="sidebar-item flex items-center gap-3 px-6 py-3 rounded-xl transition-all hover:bg-slate-800 hover:text-white">
                <i class="fas fa-users w-5"></i> <span class="sidebar-text">User Base</span>
            </a>
            <a href="<?= APP_URL ?>/admin/properties" class="sidebar-item flex items-center gap-3 px-6 py-3 rounded-xl transition-all hover:bg-slate-800 hover:text-white">
                <i class="fas fa-house-user w-5"></i> <span class="sidebar-text">Real Estate</span>
            </a>

            <p class="text-xs font-semibold text-slate-500 uppercase px-4 mt-6 mb-2 tracking-wider sidebar-section-title">System</p>
            <?php
            require_once __DIR__ . '/../../app/Models/Notification.php';
            $unreadNotifs = Notification::getUnreadCount($_SESSION['user_id'] ?? 0);
            ?>
            <a href="<?= APP_URL ?>/notifications" class="sidebar-item flex items-center justify-between px-4 py-3 rounded-xl transition-all hover:bg-slate-800 hover:text-white <?= (strpos($_SERVER['REQUEST_URI'], 'notifications') !== false) ? 'bg-blue-600 text-white shadow-md' : '' ?>">
                <div class="flex items-center gap-3">
                    <i class="fas fa-bell w-5"></i> <span class="sidebar-text">Notifications</span>
                </div>
                <?php if ($unreadNotifs > 0): ?>
                    <span class="bg-red-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-full"><?= $unreadNotifs ?></span>
                <?php endif; ?>
            </a>
            
            <p class="text-[10px] font-black text-slate-600 uppercase px-6 mb-4 mt-8 tracking-[0.2em] sidebar-section-title">System</p>
            <a href="<?= APP_URL ?>/admin/settings" class="sidebar-item flex items-center gap-3 px-6 py-3 rounded-xl transition-all hover:bg-slate-800 hover:text-white">
                <i class="fas fa-cog w-5 text-slate-400"></i> <span class="sidebar-text">Preferences</span>
            </a>
            <a href="<?= APP_URL ?>/admin/cms" class="sidebar-item flex items-center gap-3 px-6 py-3 rounded-xl transition-all hover:bg-slate-800 hover:text-white <?= (strpos($_SERVER['REQUEST_URI'], 'admin/cms') !== false) ? 'bg-blue-600 text-white shadow-md' : '' ?>">
                <i class="fas fa-desktop w-5 text-slate-400"></i> <span class="sidebar-text">Site Editor</span>
            </a>
            <a href="<?= APP_URL ?>/admin/logs" class="sidebar-item flex items-center gap-3 px-6 py-3 rounded-xl transition-all hover:bg-slate-800 hover:text-white">
                <i class="fas fa-history w-5 text-slate-400"></i> <span class="sidebar-text">Audit Trail</span>
            </a>

            <div class="pt-8 mt-8 border-t border-slate-800">
                <a href="<?= APP_URL ?>/auth/logout" class="sidebar-item flex items-center gap-3 px-6 py-4 rounded-xl text-red-400 transition-all hover:bg-red-900/20 hover:text-red-300 font-bold">
                    <i class="fas fa-power-off w-5"></i> <span class="sidebar-text">End Session</span>
                </a>
            </div>
        </nav>
    </aside>

    <!-- MAIN CONTENT AREA -->
    <div id="mainContent" class="lg:ml-64 min-h-screen flex flex-col sidebar-transition">
        <!-- TOP NAVBAR -->
        <header class="bg-white border-b border-gray-200 h-16 flex items-center justify-between px-6 sticky top-0 z-30">
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

            <div class="flex items-center gap-4">
                <a href="<?= APP_URL ?>/messages" class="relative p-2 text-gray-500 hover:text-blue-600 cursor-pointer transition-colors">
                    <i class="fas fa-envelope text-xl"></i>
                    <?php if ($unreadMsgs > 0): ?>
                        <span class="absolute top-1 right-1 bg-blue-500 text-white text-[10px] font-bold px-1 rounded-full"><?= $unreadMsgs ?></span>
                    <?php endif; ?>
                </a>
                <a href="<?= APP_URL ?>/notifications" class="relative p-2 text-gray-500 hover:text-blue-600 cursor-pointer transition-colors">
                    <i class="fas fa-bell text-xl"></i>
                    <?php if ($unreadNotifs > 0): ?>
                        <span class="absolute top-1 right-1 bg-red-500 text-white text-[10px] font-bold px-1 rounded-full"><?= $unreadNotifs ?></span>
                    <?php endif; ?>
                </a>
                
                <button onclick="toggleDarkMode()" class="relative p-2 text-gray-500 hover:text-blue-600 cursor-pointer transition-colors focus:outline-none" title="Toggle Dark Mode">
                    <i class="fas fa-sun text-xl dark:hidden"></i>
                    <i class="fas fa-moon text-xl hidden dark:inline-block"></i>
                </button>
                <div class="relative group">
                    <button class="flex items-center gap-3 pl-4 border-l border-gray-200 focus:outline-none">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-bold text-gray-800 leading-none"><?= $_SESSION['username'] ?? 'Admin' ?></p>
                            <p class="text-[10px] text-gray-500 uppercase font-semibold">System Administrator</p>
                        </div>
                        <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold shadow-sm overflow-hidden border border-gray-100">
                            <?php if ($userAvatar): ?>
                                <img src="<?= APP_URL ?>/<?= $userAvatar ?>" class="w-full h-full object-cover">
                            <?php else: ?>
                                <?= strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)) ?>
                            <?php endif; ?>
                        </div>
                    </button>
                    
                    <!-- Dropdown Menu -->
                    <div class="absolute right-0 mt-2 w-48 bg-white rounded-2xl shadow-xl border border-gray-100 py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 z-50 text-left">
                        <a href="<?= APP_URL ?>/profile/edit" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <i class="fas fa-user-edit text-gray-400"></i>
                            Edit Profile
                        </a>
                        <a href="<?= APP_URL ?>/profile/change-password" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <i class="fas fa-shield-alt text-gray-400"></i>
                            Security Settings
                        </a>
                        <a href="<?= APP_URL ?>/admin/settings" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                            <i class="fas fa-cog text-gray-400"></i>
                            Settings
                        </a>
                        <div class="border-t border-gray-100 my-1"></div>
                        <a href="<?= APP_URL ?>/auth/logout" class="flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors">
                            <i class="fas fa-sign-out-alt"></i>
                            Logout
                        </a>
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
