<div class="max-w-4xl mx-auto py-12 px-6">
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
        <div class="bg-slate-900 px-8 py-12 text-white">
            <div class="flex flex-col md:flex-row items-center gap-8">
                <div class="relative group">
                    <div class="w-32 h-32 rounded-3xl bg-blue-600 flex items-center justify-center text-4xl font-black shadow-2xl shadow-blue-500/40 border-4 border-white/20 overflow-hidden">
                        <?php if ($user['avatar_url']): ?>
                            <img src="<?= APP_URL ?>/<?= $user['avatar_url'] ?>" class="w-full h-full object-cover">
                        <?php else: ?>
                            <?= strtoupper(substr($user['username'], 0, 1)) ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="text-center md:text-left">
                    <h1 class="text-3xl font-black mb-2"><?= htmlspecialchars($user['full_name'] ?: $user['username']) ?></h1>
                    <p class="text-slate-400 font-medium"><?= ucfirst($_SESSION['role'] ?? 'User') ?> Portal • Member since <?= date('M Y', strtotime($user['created_at'])) ?></p>
                </div>
            </div>
        </div>

        <div class="p-8">
            <form action="<?= APP_URL ?>/profile/update" method="POST" enctype="multipart/form-data" class="space-y-8">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Photo Upload -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-gray-700 mb-4 tracking-wide uppercase">Profile Photo</label>
                        <div class="flex items-center gap-6">
                            <div class="w-20 h-20 rounded-2xl bg-gray-100 border-2 border-dashed border-gray-300 flex items-center justify-center text-gray-400 overflow-hidden" id="avatarPreview">
                                <?php if ($user['avatar_url']): ?>
                                    <img src="<?= APP_URL ?>/<?= $user['avatar_url'] ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <i class="fas fa-user text-2xl"></i>
                                <?php endif; ?>
                            </div>
                            <div>
                                <label class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-bold cursor-pointer transition-all shadow-lg shadow-blue-200">
                                    Change Photo
                                    <input type="file" name="avatar" class="hidden" onchange="previewAvatar(this)">
                                </label>
                                <p class="text-[10px] text-gray-400 mt-2">JPG, PNG up to 2MB</p>
                            </div>
                        </div>
                    </div>

                    <!-- Personal Info -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2 tracking-wide uppercase">Full Name</label>
                        <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" placeholder="Enter your full name" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2 tracking-wide uppercase">Username</label>
                        <input type="text" disabled value="<?= htmlspecialchars($user['username']) ?>" class="w-full px-4 py-3 bg-gray-100 border border-gray-200 rounded-2xl text-gray-400 cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2 tracking-wide uppercase">Email Address</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2 tracking-wide uppercase">Phone Number</label>
                        <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>
                </div>

                <div class="pt-6 border-t border-gray-100 flex justify-end gap-4">
                    <a href="<?= APP_URL ?>/dashboard" class="px-6 py-3 text-sm font-bold text-gray-500 hover:text-gray-800 transition-colors">Cancel</a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-10 py-3 rounded-2xl font-black shadow-xl shadow-blue-200 transition-all active:scale-95">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function previewAvatar(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('avatarPreview');
                preview.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
