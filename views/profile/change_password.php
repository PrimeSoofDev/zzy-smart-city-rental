<div class="max-w-2xl mx-auto px-4 py-12">
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Security Settings</h1>
        <p class="text-gray-500 mt-1">Update your password to keep your account secure.</p>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="mb-6 p-4 bg-green-50 border border-green-100 text-green-700 rounded-2xl flex items-center gap-3 animate-fade-in">
            <i class="fas fa-check-circle"></i>
            <p class="font-bold text-sm"><?= $_SESSION['success']; unset($_SESSION['success']); ?></p>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="mb-6 p-4 bg-red-50 border border-red-100 text-red-700 rounded-2xl flex items-center gap-3 animate-fade-in">
            <i class="fas fa-exclamation-circle"></i>
            <p class="font-bold text-sm"><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-8 md:p-12">
            <form action="<?= APP_URL ?>/profile/update-password" method="POST" class="space-y-8">
                <!-- Current Password -->
                <div class="space-y-2">
                    <label class="text-xs font-black uppercase tracking-widest text-gray-400 ml-1">Current Password</label>
                    <div class="relative">
                        <span class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" name="current_password" required
                               class="w-full pl-12 pr-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none transition-all placeholder-gray-400"
                               placeholder="Enter current password">
                    </div>
                </div>

                <hr class="border-gray-50">

                <!-- New Password -->
                <div class="space-y-2">
                    <label class="text-xs font-black uppercase tracking-widest text-gray-400 ml-1">New Password</label>
                    <div class="relative">
                        <span class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400">
                            <i class="fas fa-shield-alt"></i>
                        </span>
                        <input type="password" name="new_password" required
                               class="w-full pl-12 pr-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none transition-all placeholder-gray-400"
                               placeholder="Minimum 8 characters">
                    </div>
                </div>

                <!-- Confirm Password -->
                <div class="space-y-2">
                    <label class="text-xs font-black uppercase tracking-widest text-gray-400 ml-1">Confirm New Password</label>
                    <div class="relative">
                        <span class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400">
                            <i class="fas fa-check-double"></i>
                        </span>
                        <input type="password" name="confirm_password" required
                               class="w-full pl-12 pr-6 py-4 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none transition-all placeholder-gray-400"
                               placeholder="Repeat new password">
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full py-5 bg-gray-900 text-white font-black rounded-2xl hover:bg-black transition-all shadow-xl shadow-gray-200 active:scale-[0.98] flex items-center justify-center gap-3">
                        <i class="fas fa-key"></i>
                        Update Security Credentials
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="mt-8 text-center">
        <a href="<?= APP_URL ?>/profile/edit" class="text-sm font-bold text-gray-400 hover:text-blue-600 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Back to Profile Details
        </a>
    </div>
</div>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in {
    animation: fade-in 0.4s ease-out forwards;
}
input:focus {
    transform: translateY(-1px);
    background: white !important;
    box-shadow: 0 10px 20px -5px rgba(0,0,0,0.05) !important;
}
</style>
