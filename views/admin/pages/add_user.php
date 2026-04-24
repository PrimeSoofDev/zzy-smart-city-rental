<div class="max-w-4xl mx-auto">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Team Onboarding</h1>
            <p class="text-gray-500 mt-1">Create a professional account and send a secure activation invite.</p>
        </div>
        <a href="<?= APP_URL ?>/admin/users" class="flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-800 transition-colors">
            <i class="fas fa-arrow-left"></i> Back to User List
        </a>
    </div>

    <div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
        <!-- Form Header -->
        <div class="bg-slate-900 p-6 text-white flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="p-2 bg-blue-600 rounded-lg">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div>
                    <h3 class="font-bold">Account Setup</h3>
                    <p class="text-xs text-slate-400">Enter professional details below</p>
                </div>
            </div>
            <div class="text-right">
                <span class="text-xs font-mono bg-slate-800 px-2 py-1 rounded border border-slate-700 text-slate-400">SECURE ENCRYPTED</span>
            </div>
        </div>

        <form action="<?= APP_URL ?>/admin/add-user" method="POST" class="p-8 space-y-8">

            <?php if(isset($_SESSION['success'])): ?>
                <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-2xl flex items-center gap-3 animate-fade-in">
                    <i class="fas fa-check-circle text-green-500"></i>
                    <div class="flex-grow">
                        <p class="text-sm font-bold">User Created Successfully!</p>
                        <p class="text-xs opacity-80"><?= $_SESSION['success'] ?></p>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-green-400 hover:text-green-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <?php unset($_SESSION['success']); ?>
            <?php endif; ?>

            <?php if(isset($_SESSION['error'])): ?>
                <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl flex items-center gap-3 animate-fade-in">
                    <i class="fas fa-exclamation-triangle text-red-500"></i>
                    <div class="flex-grow">
                        <p class="text-sm font-bold">Error occurred</p>
                        <p class="text-xs opacity-80"><?= $_SESSION['error'] ?></p>
                    </div>
                    <button onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <?php unset($_SESSION['error']); ?>
            <?php endif; ?>

            <!-- Personal Information Section -->
            <section>
                <div class="flex items-center gap-2 mb-6">
                    <div class="w-1 h-6 bg-blue-600 rounded-full"></div>
                    <h4 class="text-lg font-bold text-gray-800">Personal Details</h4>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-600 flex items-center gap-2">
                            <i class="fas fa-user text-gray-400 text-xs"></i> Full Name
                        </label>
                        <input type="text" name="username" required placeholder="e.g. John Doe"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all placeholder:text-gray-300">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-600 flex items-center gap-2">
                            <i class="fas fa-envelope text-gray-400 text-xs"></i> Professional Email
                        </label>
                        <input type="email" name="email" required placeholder="email@company.com"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all placeholder:text-gray-300">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-600 flex items-center gap-2">
                            <i class="fas fa-phone text-gray-400 text-xs"></i> Phone Number
                        </label>
                        <input type="text" name="phone" required placeholder="+234 ..."
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all placeholder:text-gray-300">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-600 flex items-center gap-2">
                            <i class="fas fa-map-marker-alt text-gray-400 text-xs"></i> Assigned Location
                        </label>
                        <input type="text" name="location" required placeholder="e.g. Lagos Branch Office"
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all placeholder:text-gray-300">
                    </div>
                </div>
            </section>

            <!-- Role Assignment Section -->
            <section class="pt-8 border-t border-gray-100">
                <div class="flex items-center gap-2 mb-6">
                    <div class="w-1 h-6 bg-blue-600 rounded-full"></div>
                    <h4 class="text-lg font-bold text-gray-800">Access Control</h4>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
                    <div class="space-y-3">
                        <label class="text-sm font-semibold text-gray-600 flex items-center gap-2">
                            <i class="fas fa-shield-alt text-gray-400 text-xs"></i> Assigned Role
                        </label>
                        <div class="relative">
                            <select name="role" class="w-full px-4 py-3 border border-gray-200 rounded-xl appearance-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all bg-white cursor-pointer">
                                <option value="Staff" <?= ($defaultRole ?? 'Staff') === 'Staff' ? 'selected' : '' ?>>Staff Member (Operational)</option>
                                <option value="Lawyer" <?= ($defaultRole ?? 'Staff') === 'Lawyer' ? 'selected' : '' ?>>Legal Representative (Lawyer)</option>
                            </select>
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-gray-400">
                                <i class="fas fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                    </div>

                    <div class="bg-blue-50 p-4 rounded-2xl border border-blue-100 flex gap-4">
                        <div class="p-3 bg-blue-600 text-white rounded-xl h-fit">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-blue-800">Auto-Invite System</p>
                            <p class="text-xs text-blue-600 leading-relaxed">
                                The system will automatically generate a secure, 24-hour activation token and email it to the user.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Action Bar -->
            <div class="flex items-center justify-end gap-4 pt-8">
                <button type="button" class="px-6 py-3 rounded-xl text-sm font-bold text-gray-500 hover:bg-gray-100 transition-all">
                    Cancel
                </button>
                <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-200 flex items-center gap-2">
                    <i class="fas fa-paper-plane text-xs"></i> Create & Invite
                </button>
            </div>
        </form>
    </div>
</div>
