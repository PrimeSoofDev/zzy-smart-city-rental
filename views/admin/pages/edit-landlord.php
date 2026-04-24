<h1 class="text-3xl font-bold text-gray-800 mb-8">Edit Landlord Profile</h1>

<div class="bg-white rounded-3xl shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden max-w-3xl mx-auto">
    <div class="bg-slate-900 p-6 text-white flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-blue-600 rounded-lg">
                <i class="fas fa-user-edit"></i>
            </div>
            <div>
                <h3 class="font-bold">Manage Landlord Account</h3>
                <p class="text-xs text-slate-400">Update profile and verification status</p>
            </div>
        </div>
        <a href="<?= APP_URL ?>/admin/landlords" class="text-xs text-slate-400 hover:text-white transition-colors">
            <i class="fas fa-times mr-1"></i> Close
        </a>
    </div>

    <form action="<?= APP_URL ?>/admin/update-landlord" method="POST" class="p-8 space-y-8">
        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">

        <section class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
            <div class="md:col-span-2 space-y-2">
                <label class="text-sm font-semibold text-gray-600">Primary Business Location</label>
                <input type="text" name="location" value="<?= htmlspecialchars($profile['address'] ?? '') ?>" required
                       class="w-full px-4 py-3 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all">
            </div>
        </section>

        <section class="pt-8 border-t border-gray-100">
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-2xl border border-gray-200">
                <div>
                    <p class="text-sm font-bold text-gray-800">Verification Status</p>
                    <p class="text-xs text-gray-500">Change whether this landlord can list properties.</p>
                </div>
                <select name="status" class="px-4 py-2 border rounded-lg text-sm outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="pending" <?= ($profile['verification_status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="approved" <?= ($profile['verification_status'] ?? '') === 'approved' ? 'selected' : '' ?>>Approved</option>
                    <option value="rejected" <?= ($profile['verification_status'] ?? '') === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                </select>
            </div>
        </section>

        <div class="flex justify-end gap-4 pt-4">
            <a href="<?= APP_URL ?>/admin/landlords" class="px-6 py-3 rounded-xl text-sm font-bold text-gray-500 hover:bg-gray-100 transition-all">
                Cancel
            </a>
            <button type="submit" class="bg-blue-600 text-white px-8 py-3 rounded-xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-200">
                Save Changes
            </button>
        </div>
    </form>
</div>
