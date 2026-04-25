<?php 
$title = 'How it Works';
include '../views/layouts/guest_layout_start.php'; 
?>

    <section class="py-32 bg-white">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-24">
                <h1 class="text-6xl font-black text-slate-900 mb-6 tracking-tighter"><?= $content['intro']['title'] ?? 'The Future of Renting is <span class="text-gradient">Simple</span>.' ?></h1>
                <p class="text-xl text-slate-500 max-w-2xl mx-auto"><?= $content['intro']['subtitle'] ?? "We've automated the entire rental lifecycle, from discovery to legal agreements and payments." ?></p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-16">
                <!-- Step 1 -->
                <div class="text-center group">
                    <div class="w-24 h-24 bg-blue-50 rounded-[2.5rem] flex items-center justify-center mx-auto mb-8 transition-all group-hover:-rotate-12 group-hover:bg-blue-600 group-hover:text-white">
                        <i class="fas fa-search-location text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 mb-4">Smart Discovery</h3>
                    <p class="text-slate-500 leading-relaxed">Our AI analyzes your preferences to match you with properties that actually fit your life.</p>
                </div>

                <!-- Step 2 -->
                <div class="text-center group">
                    <div class="w-24 h-24 bg-purple-50 rounded-[2.5rem] flex items-center justify-center mx-auto mb-8 transition-all group-hover:rotate-12 group-hover:bg-purple-600 group-hover:text-white">
                        <i class="fas fa-file-signature text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 mb-4">Automated Legal</h3>
                    <p class="text-slate-500 leading-relaxed">Verified lawyers draft agreements instantly. Sign digitally and secure your new home in minutes.</p>
                </div>

                <!-- Step 3 -->
                <div class="text-center group">
                    <div class="w-24 h-24 bg-teal-50 rounded-[2.5rem] flex items-center justify-center mx-auto mb-8 transition-all group-hover:-rotate-12 group-hover:bg-teal-600 group-hover:text-white">
                        <i class="fas fa-shield-check text-3xl"></i>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 mb-4">Secure Escrow</h3>
                    <p class="text-slate-500 leading-relaxed">Your payment is held in a secure platform escrow until you are fully moved in and satisfied.</p>
                </div>
            </div>

            <!-- Detailed Section -->
            <div class="mt-40 bg-slate-50 rounded-[4rem] p-12 md:p-24 overflow-hidden relative">
                <div class="blob w-96 h-96 bg-blue-100 -top-20 -right-20"></div>
                <div class="relative z-10 grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
                    <div>
                        <h2 class="text-4xl font-black text-slate-900 mb-8 leading-tight">Built for Trust and <br> Absolute Transparency.</h2>
                        <ul class="space-y-6">
                            <li class="flex gap-4">
                                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white shrink-0">
                                    <i class="fas fa-check"></i>
                                </div>
                                <p class="text-slate-600"><strong>Verified Landlords:</strong> Every property owner goes through a rigorous KYC process.</p>
                            </li>
                            <li class="flex gap-4">
                                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white shrink-0">
                                    <i class="fas fa-check"></i>
                                </div>
                                <p class="text-slate-600"><strong>Professional Staff:</strong> On-ground inspection teams verify every single listing.</p>
                            </li>
                            <li class="flex gap-4">
                                <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white shrink-0">
                                    <i class="fas fa-check"></i>
                                </div>
                                <p class="text-slate-600"><strong>Instant Support:</strong> 24/7 assistance for all maintenance and legal queries.</p>
                            </li>
                        </ul>
                    </div>
                    <div class="bg-white p-8 rounded-[3rem] shadow-2xl">
                        <div class="flex items-center gap-4 mb-8">
                            <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white font-black">Z</div>
                            <h4 class="text-lg font-black text-slate-900">Platform Guarantee</h4>
                        </div>
                        <p class="text-slate-500 text-sm leading-relaxed mb-8">
                            ZZY Smart Rental acts as the middleman between you and the landlord. We ensure that your money is safe, your contract is fair, and your living experience is modern.
                        </p>
                        <a href="<?= APP_URL ?>/auth/signup" class="block w-full py-4 bg-slate-900 text-white text-center font-bold rounded-2xl transition-transform hover:scale-[1.02]">Start Your Journey</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

<?php include '../views/layouts/guest_layout_end.php'; ?>
