<?php
// Layout Wrapper for Admin Pages
require_once "../config/config.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel | ZZY Rental</title>
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
                <span class="text-xl font-bold text-white tracking-tight">ZZY Admin</span>
            </div>
            <button id="closeSidebar" class="lg:hidden text-slate-400 hover:text-white">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <nav class="p-4 space-y-2">
            <p class="text-xs font-semibold text-slate-500 uppercase px-4 mb-2 tracking-wider">Main</p>
            <a href="<?= APP_URL ?>/admin/dashboard" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-slate-800 hover:text-white <?= (basename($_SERVER['PHP_SELF']) == 'dashboard.php' || $_SERVER['REQUEST_URI'] == '/zzy_rental/admin/dashboard') ? 'bg-blue-600 text-white shadow-md' : '' ?>">
                <i class="fas fa-chart-pie w-5"></i> <span>Dashboard</span>
            </a>

            <p class="text-xs font-semibold text-slate-500 uppercase px-4 mt-6 mb-2 tracking-wider">Users</p>
            <div class="space-y-1">
                <a href="<?= APP_URL ?>/admin/users" class="flex items-center gap-3 px-4 py-2 rounded-lg transition-all hover:bg-slate-800 hover:text-white">
                    <i class="fas fa-users w-5"></i> <span>All Users</span>
                </a>

                <!-- Dropdown for Adding Staff/Lawyer -->
                <div class="relative group">
                    <button class="w-full flex items-center justify-between px-4 py-2 rounded-lg transition-all hover:bg-slate-800 hover:text-white text-left">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-user-plus w-5 text-blue-400"></i> <span>Add Staff</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs transition-transform group-hover:rotate-180"></i>
                    </button>
                    <div class="absolute left-0 w-full bg-slate-800 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 translate-y-2 group-hover:translate-y-0 z-50 border border-slate-700">
                        <div class="p-2 space-y-1">
                            <a href="<?= APP_URL ?>/admin/add-user?role=Staff" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm transition-all hover:bg-blue-600 hover:text-white text-slate-400">
                                <i class="fas fa-user-shield w-4"></i> <span>Add Staff Member</span>
                            </a>
                            <a href="<?= APP_URL ?>/admin/add-user?role=Lawyer" class="flex items-center gap-3 px-3 py-2 rounded-md text-sm transition-all hover:bg-blue-600 hover:text-white text-slate-400">
                                <i class="fas fa-gavel w-4"></i> <span>Add Legal Representative</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="pl-4 space-y-1 mt-2 border-l border-slate-800 ml-6">
                    <a href="<?= APP_URL ?>/admin/users?role=Tenant" class="flex items-center gap-3 px-4 py-2 rounded-lg transition-all hover:bg-slate-800 hover:text-white text-sm">
                        <i class="fas fa-user-tag w-4"></i> <span>Tenants</span>
                    </a>
                    <a href="<?= APP_URL ?>/admin/users?role=Landlord" class="flex items-center gap-3 px-4 py-2 rounded-lg transition-all hover:bg-slate-800 hover:text-white text-sm">
                        <i class="fas fa-house-user w-4"></i> <span>Landlords</span>
                    </a>
                </div>
            </div>

            <p class="text-xs font-semibold text-slate-500 uppercase px-4 mt-6 mb-2 tracking-wider">Properties</p>
            <a href="<?= APP_URL ?>/admin/properties" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-slate-800 hover:text-white">
                <i class="fas fa-list w-5"></i> <span>All Properties</span>
            </a>
            <a href="<?= APP_URL ?>/admin/properties?status=pending" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-slate-800 hover:text-white">
                <i class="fas fa-clock w-5"></i> <span>Pending Approval</span>
            </a>
            <a href="<?= APP_URL ?>/admin/properties?status=approved" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-slate-800 hover:text-white">
                <i class="fas fa-check-circle w-5"></i> <span>Verified</span>
            </a>

            <p class="text-xs font-semibold text-slate-500 uppercase px-4 mt-6 mb-2 tracking-wider">Operations</p>
            <a href="<?= APP_URL ?>/admin/requests" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-slate-800 hover:text-white">
                <i class="fas fa-exchange-alt w-5"></i> <span>Rental Requests</span>
            </a>
            <a href="<?= APP_URL ?>/admin/transactions" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-slate-800 hover:text-white">
                <i class="fas fa-vault w-5"></i> <span>Escrow & Payments</span>
            </a>
            <a href="<?= APP_URL ?>/admin/agreements" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-slate-800 hover:text-white">
                <i class="fas fa-file-signature w-5"></i> <span>Legal Agreements</span>
            </a>
            <a href="<?= APP_URL ?>/admin/disputes" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-slate-800 hover:text-white">
                <i class="fas fa-balance-scale w-5"></i> <span>Disputes</span>
            </a>

            <p class="text-xs font-semibold text-slate-500 uppercase px-4 mt-6 mb-2 tracking-wider">System</p>
            <a href="<?= APP_URL ?>/admin/notifications" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-slate-800 hover:text-white">
                <i class="fas fa-bell w-5"></i> <span>Notifications</span>
            </a>
            <a href="<?= APP_URL ?>/admin/logs" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-slate-800 hover:text-white">
                <i class="fas fa-history w-5"></i> <span>Audit Logs</span>
            </a>
            <a href="<?= APP_URL ?>/admin/settings" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all hover:bg-slate-800 hover:text-white">
                <i class="fas fa-cog w-5"></i> <span>Settings</span>
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
                <h2 class="text-lg font-semibold text-gray-800 hidden sm:block">Admin Control Center</h2>
            </div>

            <div class="flex items-center gap-4">
                <div class="relative p-2 text-gray-500 hover:text-blue-600 cursor-pointer transition-colors">
                    <i class="fas fa-bell text-xl"></i>
                    <span class="absolute top-1 right-1 bg-red-500 text-white text-[10px] font-bold px-1 rounded-full">3</span>
                </div>
                <div class="flex items-center gap-3 pl-4 border-l border-gray-200">
                    <div class="text-right hidden sm:block">
                        <p class="text-sm font-bold text-gray-800 leading-none">Super Admin</p>
                        <p class="text-[10px] text-gray-500 uppercase font-semibold">Root Access</p>
                    </div>
                    <div class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center text-white font-bold shadow-sm">
                        SA
                    </div>
                </div>
            </div>
        </header>

        <!-- PAGE CONTENT -->
        <main class="p-6 flex-grow">
