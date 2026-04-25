<div class="min-h-screen bg-gray-50 py-12 px-6">
    <div class="max-w-2xl mx-auto">
        <!-- Time-based Greeting & Branding -->
        <div class="text-center mb-10">
            <h2 class="text-sm font-bold text-blue-600 uppercase tracking-widest mb-2">Welcome to ZZY Smart Rental</h2>
            <h1 class="text-4xl font-black text-gray-900">
                <?php
                    $hour = date('H');
                    if ($hour < 12) echo "Good Morning";
                    elseif ($hour < 17) echo "Good Afternoon";
                    else echo "Good Evening";
                ?>, <?= explode(' ', $_SESSION['username'])[0] ?>!
            </h1>
        </div>

        <?php if ($status === 'approved'): ?>
            <!-- Verified User Screen -->
            <div class="bg-white p-12 rounded-[2.5rem] shadow-2xl border border-gray-100 text-center animate-fade-in">
                <div class="w-24 h-24 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                    <i class="fas fa-check-double text-4xl"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">You're a Verified User</h3>
                <p class="text-gray-500 mb-8 max-w-sm mx-auto">Your identity has been successfully confirmed. You can now proceed to rent a house in a minute.</p>
                
                <div class="flex flex-col gap-4">
                    <a href="<?= APP_URL ?>/tenant/dashboard" class="w-full py-4 bg-slate-900 text-white rounded-2xl font-bold hover:bg-slate-800 transition-all shadow-lg shadow-slate-200">
                        Explore Available Houses
                    </a>
                    <p class="text-[10px] text-gray-400 font-medium uppercase tracking-tighter">Verified on <?= date('d M, Y') ?></p>
                </div>
            </div>

        <?php elseif ($status === 'pending'): ?>
            <!-- Pending Verification Screen -->
            <div class="bg-white p-12 rounded-[2.5rem] shadow-xl border border-gray-100 text-center">
                <div class="w-24 h-24 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-hourglass-half text-4xl animate-pulse"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Verification Pending</h3>
                <p class="text-gray-500 mb-8">Our team is currently reviewing your documents. We'll notify you as soon as you're approved.</p>
                <a href="<?= APP_URL ?>/tenant/dashboard" class="text-blue-600 font-bold hover:underline">Return to Dashboard</a>
            </div>

        <?php else: ?>
            <!-- Verification Form -->
            <div class="bg-white p-10 rounded-[2.5rem] shadow-xl border border-gray-100">
                <div class="mb-8 border-b border-gray-50 pb-6">
                    <h3 class="text-xl font-bold text-gray-900">Start Your Verification</h3>
                    <p class="text-sm text-gray-500">Submit your details to unlock full access to rental properties.</p>
                </div>

                <?php if(isset($_SESSION['success'])): ?>
                    <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-xl border border-green-100 flex items-center gap-3">
                        <i class="fas fa-check-circle"></i>
                        <span class="text-sm font-medium"><?= $_SESSION['success']; unset($_SESSION['success']); ?></span>
                    </div>
                <?php endif; ?>

                <?php if(isset($_SESSION['error'])): ?>
                    <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-xl border border-red-100 flex items-center gap-3">
                        <i class="fas fa-exclamation-circle"></i>
                        <span class="text-sm font-medium"><?= $_SESSION['error']; unset($_SESSION['error']); ?></span>
                    </div>
                <?php endif; ?>

                <form action="<?= APP_URL ?>/tenant/verify-submit" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">ID Number (NIMC/BVN/Passport)</label>
                            <input type="text" name="id_number" required placeholder="Enter your valid ID number" class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">Residential Address</label>
                            <textarea name="address" required rows="3" placeholder="Your current full address" class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none transition-all"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2 uppercase tracking-wide">ID Document Upload</label>
                            <div class="relative group h-40 border-2 border-dashed border-gray-200 rounded-3xl flex flex-col items-center justify-center bg-gray-50 hover:bg-white hover:border-blue-400 transition-all cursor-pointer">
                                <input type="file" name="id_doc" required class="absolute inset-0 opacity-0 cursor-pointer">
                                <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl mb-2 group-hover:text-blue-500 transition-colors"></i>
                                <span class="text-sm font-bold text-gray-500 group-hover:text-blue-600">Select Document</span>
                                <p class="text-[10px] text-gray-400 mt-1">PDF, JPG, PNG up to 10MB</p>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="w-full py-5 bg-blue-600 text-white rounded-[1.5rem] font-black hover:bg-blue-700 transition-all shadow-xl shadow-blue-200 active:scale-95">
                        Submit for Verification
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    @keyframes fade-in {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fade-in 0.6s ease-out forwards;
    }
</style>
