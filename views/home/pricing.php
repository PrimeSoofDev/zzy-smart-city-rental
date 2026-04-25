<?php 
$title = 'Pricing';
include '../views/layouts/guest_layout_start.php'; 
?>

    <section class="py-32 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-24">
                <h1 class="text-6xl font-black text-slate-900 mb-6 tracking-tighter"><?= $content['intro']['title'] ?? 'Transparent <span class="text-gradient">Pricing</span>.' ?></h1>
                <p class="text-xl text-slate-500 max-w-2xl mx-auto"><?= $content['intro']['subtitle'] ?? 'No hidden fees. We believe in complete transparency for both tenants and landlords.' ?></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12 max-w-4xl mx-auto">
                <!-- For Tenants -->
                <div class="bg-white p-12 rounded-[3rem] shadow-xl border border-slate-100 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-blue-50 -mr-16 -mt-16 rounded-full"></div>
                    <h3 class="text-xs font-black text-blue-600 uppercase tracking-widest mb-4">For Tenants</h3>
                    <p class="text-4xl font-black text-slate-900 mb-8">Pay as you Live.</p>
                    <ul class="space-y-6 mb-12">
                        <li class="flex items-center gap-4 text-slate-600">
                            <i class="fas fa-check-circle text-blue-500"></i>
                            <span>Free Account Creation</span>
                        </li>
                        <li class="flex items-center gap-4 text-slate-600">
                            <i class="fas fa-check-circle text-blue-500"></i>
                            <span>Unlimited Property Viewing</span>
                        </li>
                        <li class="flex items-center gap-4 text-slate-600">
                            <i class="fas fa-check-circle text-blue-500"></i>
                            <span>10% Legal & Admin Fee</span>
                        </li>
                        <li class="flex items-center gap-4 text-slate-600">
                            <i class="fas fa-check-circle text-blue-500"></i>
                            <span>20% Platform/Escrow Fee</span>
                        </li>
                    </ul>
                    <a href="<?= APP_URL ?>/auth/signup" class="block w-full py-5 bg-blue-600 text-white text-center font-black rounded-2xl shadow-lg shadow-blue-600/20 hover:bg-blue-700 transition-all">Get Started Free</a>
                </div>

                <!-- For Landlords -->
                <div class="bg-slate-900 p-12 rounded-[3rem] shadow-2xl relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 -mr-16 -mt-16 rounded-full"></div>
                    <h3 class="text-xs font-black text-blue-400 uppercase tracking-widest mb-4">For Landlords</h3>
                    <p class="text-4xl font-black text-white mb-8">Zero Listing Fee.</p>
                    <ul class="space-y-6 mb-12">
                        <li class="flex items-center gap-4 text-slate-400">
                            <i class="fas fa-check-circle text-blue-400"></i>
                            <span>Free Property Listing</span>
                        </li>
                        <li class="flex items-center gap-4 text-slate-400">
                            <i class="fas fa-check-circle text-blue-400"></i>
                            <span>Free Marketing & Exposure</span>
                        </li>
                        <li class="flex items-center gap-4 text-slate-400">
                            <i class="fas fa-check-circle text-blue-400"></i>
                            <span>Verified Tenant Matching</span>
                        </li>
                        <li class="flex items-center gap-4 text-slate-400">
                            <i class="fas fa-check-circle text-blue-400"></i>
                            <span>Automated Payouts</span>
                        </li>
                    </ul>
                    <a href="<?= APP_URL ?>/auth/signup" class="block w-full py-5 bg-white text-slate-900 text-center font-black rounded-2xl shadow-xl hover:bg-slate-100 transition-all">List Your Property</a>
                </div>
            </div>

            <div class="mt-24 text-center">
                <p class="text-slate-400 font-bold uppercase text-xs tracking-[0.2em] mb-4">Need a custom plan?</p>
                <a href="<?= APP_URL ?>/support" class="text-blue-600 font-black hover:underline">Contact our Enterprise Team</a>
            </div>
        </div>
    </section>

<?php include '../views/layouts/guest_layout_end.php'; ?>
