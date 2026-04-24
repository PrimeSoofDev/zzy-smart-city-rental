<h1 class="text-3xl font-bold text-gray-800 mb-8">Admin Dashboard</h1>

<!-- KPI Section -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-10">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-blue-400 transition-all group">
        <div class="flex justify-between items-start mb-4">
            <div class="p-3 bg-blue-50 text-blue-600 rounded-xl group-hover:bg-blue-600 group-hover:text-white transition-colors">
                <i class="fas fa-users text-xl"></i>
            </div>
            <span class="text-green-500 text-xs font-bold bg-green-50 px-2 py-1 rounded-full">+12%</span>
        </div>
        <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">Total Users</p>
        <p class="text-3xl font-extrabold text-gray-900"><?= $stats['totalUsers'] ?? '0' ?></p>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-blue-400 transition-all group">
        <div class="flex justify-between items-start mb-4">
            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                <i class="fas fa-house-user text-xl"></i>
            </div>
            <span class="text-green-500 text-xs font-bold bg-green-50 px-2 py-1 rounded-full">+5%</span>
        </div>
        <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">Properties</p>
        <p class="text-3xl font-extrabold text-gray-900"><?= $stats['totalProperties'] ?? '0' ?></p>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-yellow-400 transition-all group">
        <div class="flex justify-between items-start mb-4">
            <div class="p-3 bg-yellow-50 text-yellow-600 rounded-xl group-hover:bg-yellow-600 group-hover:text-white transition-colors">
                <i class="fas fa-clock text-xl"></i>
            </div>
            <span class="text-red-500 text-xs font-bold bg-red-50 px-2 py-1 rounded-full">Critical</span>
        </div>
        <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">Pending Approval</p>
        <p class="text-3xl font-extrabold text-gray-900"><?= $stats['pendingVerifications'] ?? '0' ?></p>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-blue-400 transition-all group">
        <div class="flex justify-between items-start mb-4">
            <div class="p-3 bg-green-50 text-green-600 rounded-xl group-hover:bg-green-600 group-hover:text-white transition-colors">
                <i class="fas fa-hand-holding-usd text-xl"></i>
            </div>
            <span class="text-green-500 text-xs font-bold bg-green-50 px-2 py-1 rounded-full">+2.4%</span>
        </div>
        <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">Active Rentals</p>
        <p class="text-3xl font-extrabold text-gray-900">124</p>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 hover:border-blue-400 transition-all group">
        <div class="flex justify-between items-start mb-4">
            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                <i class="fas fa-wallet text-xl"></i>
            </div>
            <span class="text-green-500 text-xs font-bold bg-green-50 px-2 py-1 rounded-full">+18%</span>
        </div>
        <p class="text-gray-500 text-sm font-medium uppercase tracking-wider">Commission</p>
        <p class="text-3xl font-extrabold text-gray-900">$12,450</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- User Management Quick List -->
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-800 text-lg">Recent Users</h3>
            <a href="<?= APP_URL ?>/admin/users" class="text-blue-600 text-sm font-semibold hover:underline">View All Users →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-400 text-xs uppercase font-bold">
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4">Role</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach(array_slice($users, 0, 5) as $u): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600 uppercase">
                                        <?= substr($u['username'], 0, 1) ?>
                                    </div>
                                    <span class="font-medium text-gray-800"><?= htmlspecialchars($u['username']) ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-blue-100 text-blue-700 uppercase">
                                    <?= htmlspecialchars($u['role_name'] ?? 'N/A') ?>
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="flex items-center gap-1.5 text-xs font-medium <?= $u['status'] === 'verified' ? 'text-green-600' : 'text-orange-500' ?>">
                                    <span class="w-1.5 h-1.5 rounded-full <?= $u['status'] === 'verified' ? 'bg-green-600' : 'bg-orange-500' ?>"></span>
                                    <?= ucfirst($u['status']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="<?= APP_URL ?>/admin/users?id=<?= $u['id'] ?>" class="text-gray-400 hover:text-blue-600 transition-colors">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- System Alerts / Pending Actions -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-bold text-gray-800 text-lg mb-6">Critical Actions</h3>
        <div class="space-y-4">
            <div class="flex items-start gap-4 p-4 rounded-xl bg-red-50 border border-red-100">
                <div class="p-2 bg-red-100 text-red-600 rounded-lg">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-red-800">Verification Pending</p>
                    <p class="text-xs text-red-600 mb-2"><?= $stats['pendingVerifications'] ?> properties need approval</p>
                    <a href="<?= APP_URL ?>/admin/properties?status=pending" class="text-[11px] font-bold uppercase text-red-700 hover:underline">Review Now →</a>
                </div>
            </div>

            <div class="flex items-start gap-4 p-4 rounded-xl bg-blue-50 border border-blue-100">
                <div class="p-2 bg-blue-100 text-blue-600 rounded-lg">
                    <i class="fas fa-file-signature"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-blue-800">Agreement Queue</p>
                    <p class="text-xs text-blue-600 mb-2">12 agreements awaiting lawyer sign-off</p>
                    <a href="<?= APP_URL ?>/admin/agreements" class="text-[11px] font-bold uppercase text-blue-700 hover:underline">Process Docs →</a>
                </div>
            </div>

            <div class="flex items-start gap-4 p-4 rounded-xl bg-purple-50 border border-purple-100">
                <div class="p-2 bg-purple-100 text-purple-600 rounded-lg">
                    <i class="fas fa-balance-scale"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-purple-800">Open Disputes</p>
                    <p class="text-xs text-purple-600 mb-2">3 cases require admin mediation</p>
                    <a href="<?= APP_URL ?>/admin/disputes" class="text-[11px] font-bold uppercase text-purple-700 hover:underline">Resolve Now →</a>
                </div>
            </div>
        </div>
    </div>
</div>
