<?php
$logoUrl = SiteSetting::get('logo_url');
$faviconUrl = SiteSetting::get('favicon_url');
$siteName = SiteSetting::get('site_name', 'ZZY Smart Rental');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? $siteName ?> | Future of Living</title>
    <?php if($faviconUrl): ?>
    <link rel="icon" type="image/x-icon" href="<?= APP_URL . '/' . $faviconUrl ?>">
    <?php endif; ?>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass { background: rgba(15, 23, 42, 0.95); backdrop-filter: blur(20px); border-bottom: 1px solid rgba(255, 255, 255, 0.05); }
        .nav-scrolled { background: rgba(15, 23, 42, 1); box-shadow: 0 10px 40px -10px rgba(0,0,0,0.3); }
        .text-gradient { background: linear-gradient(to right, #2563eb, #7c3aed); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .blob { filter: blur(80px); position: absolute; z-index: -1; }
        @keyframes float { 0% { transform: translateY(0px); } 50% { transform: translateY(-15px); } 100% { transform: translateY(0px); } }
        .animate-float { animation: float 8s ease-in-out infinite; }
    </style>
</head>
<body class="bg-white text-slate-800 overflow-x-hidden">

    <!-- SINGLE STICKY HEADER -->
    <header class="sticky top-0 z-50 transition-all duration-300 glass" id="mainHeader">
        <div class="max-w-7xl mx-auto flex items-center justify-between px-6 h-20">
            <!-- Branding -->
            <a href="<?= APP_URL ?>/" class="flex items-center gap-3">
                <?php if($logoUrl): ?>
                    <img src="<?= APP_URL . '/' . $logoUrl ?>" alt="Logo" class="h-10">
                <?php else: ?>
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white font-black shadow-lg shadow-blue-500/20">Z</div>
                <?php endif; ?>
                <span class="text-xl font-black text-white tracking-tighter uppercase"><?= $siteName ?></span>
            </a>
            
            <!-- Menu Items -->
            <div class="hidden lg:flex items-center gap-10 font-bold text-[13px] text-slate-400 uppercase tracking-widest">
                <a href="<?= APP_URL ?>/find-homes" class="hover:text-blue-400 transition-colors">Find Homes</a>
                <a href="<?= APP_URL ?>/how-it-works" class="hover:text-blue-400 transition-colors">How it Works</a>
                <a href="<?= APP_URL ?>/pricing" class="hover:text-blue-400 transition-colors">Pricing</a>
                <a href="<?= APP_URL ?>/support" class="hover:text-blue-400 transition-colors">Support</a>
            </div>

            <!-- Auth Controls -->
            <div class="flex items-center gap-8">
                <a href="<?= APP_URL ?>/auth/login" class="text-xs font-black text-slate-400 hover:text-white transition-colors tracking-widest uppercase">Login</a>
                <a href="<?= APP_URL ?>/auth/signup" class="px-7 py-3 bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-black rounded-xl transition-all shadow-xl shadow-blue-500/20 active:scale-95 uppercase tracking-widest">Join Us</a>
            </div>
        </div>
    </header>
