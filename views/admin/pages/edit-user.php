<h1 class="text-3xl font-bold text-gray-800 mb-8">Manage User Account</h1>

<div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden max-w-4xl mx-auto">
    <div class="bg-slate-900 p-6 text-white flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-blue-600 rounded-lg">
                <i class="fas fa-user-shield"></i>
            </div>
            <div>
                <h3 class="font-bold">Full User Control</h3>
                <p class="text-xs text-slate-400">Update profile, change status, or modify permissions</p>
            </div>
        </div>
        <a href="<?= APP_URL ?>/admin/users" class="text-xs text-slate-400 hover:text-white transition-colors">
            <i class="fas fa-times mr-1"></i> Close
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3">
        <!-- Left: Form -->
        <div class="lg:col-span-2 p-8 border-r border-gray-100">
            <form action="<?= APP_URL ?>/admin/update-user" method="POST" class="space-y-6">
                <input type="hidden" name="user_id" value="<?= $user['id'] ?>">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-600">Full Name</label>
                        <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>
                    <div class="space-y-2">
                        <label class="text-sm font-semibold text-gray-600">Email Address</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required
                               class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-600">Role</label>
                    <select name="role" class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                        <option value="Admin" <?= ($user['role'] ?? '') === 'Admin' ? 'selected' : '' ?>>Administrator</option>
                        <option value="Staff" <?= ($user['role'] ?? '') === 'Staff' ? 'selected' : '' ?>>Staff Member</option>
                        <option value="Landlord" <?= ($user['role'] ?? '') === 'Landlord' ? 'selected' : '' ?>>Landlord</option>
                        <option value="Tenant" <?= ($user['role'] ?? '') === 'Tenant' ? 'selected' : '' ?>>Tenant</option>
                        <option value="Lawyer" <?= ($user['role'] ?? '') === 'Lawyer' ? 'selected' : '' ?>>Legal Representative</option>
                    </select>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-200">
                        Update User
                    </button>
                </div>
            </form>
        </div>

        <!-- Right: Control Panel -->
        <div class="p-8 bg-gray-50 space-y-6">
            <h4 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Quick Actions</h4>

            <div class="grid grid-cols-1 gap-3">
                <form action="<?= APP_URL ?>/admin/manage-user" method="POST" class="flex flex-col">
                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                    <input type="hidden" name="status" value="verified">
                    <button class="w-full flex items-center justify-between px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm font-bold text-green-600 hover:bg-green-50 hover:border-green-200 transition-all group">
                        <span><i class="fas fa-check-circle mr-2"></i> Approve</span>
                        <i class="fas fa-chevron-right text-gray-300 group-hover:text-green-400"></i>
                    </button>
                </form>

                <form action="<?= APP_URL ?>/admin/manage-user" method="POST" class="flex flex-col">
                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                    <input type="hidden" name="status" value="pending">
                    <button class="w-full flex items-center justify-between px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm font-bold text-orange-600 hover:bg-orange-50 hover:border-orange-200 transition-all group">
                        <span><i class="fas fa-clock mr-2"></i> Reject/Pending</span>
                        <i class="fas fa-chevron-right text-gray-300 group-hover:text-orange-400"></i>
                    </button>
                </form>

                <form action="<?= APP_URL ?>/admin/manage-user" method="POST" class="flex flex-col">
                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                    <input type="hidden" name="status" value="blocked">
                    <button class="w-full flex items-center justify-between px-4 py-3 bg-white border border-gray-200 rounded-xl text-sm font-bold text-red-600 hover:bg-red-50 hover:border-red-200 transition-all group">
                        <span><i class="fas fa-ban mr-2"></i> Block User</span>
                        <i class="fas fa-chevron-right text-gray-300 group-hover:text-red-400"></i>
                    </button>
                </form>

                <form action="<?= APP_URL ?>/admin/delete-user" method="POST" class="flex flex-col">
                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                    <button class="w-full flex items-center justify-between px-4 py-3 bg-red-50 border border-red-100 rounded-xl text-sm font-bold text-red-700 hover:bg-red-100 hover:border-red-200 transition-all group">
                        <span><i class="fas fa-trash-alt mr-2"></i> Delete Account</span>
                        <i class="fas fa-chevron-right text-red-300 group-hover:text-red-500"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
