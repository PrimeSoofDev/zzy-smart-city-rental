<h1 class="text-3xl font-bold text-gray-800 mb-8">User Management</h1>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4">
        <div class="flex items-center gap-2">
            <span class="text-sm font-medium text-gray-500">Showing:</span>
            <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-full uppercase">
                <?= isset($_GET['role']) ? htmlspecialchars($_GET['role']) : 'All Users' ?>
            </span>
        </div>
        <div class="flex gap-3 w-full sm:w-auto">
            <div class="relative flex-grow">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" placeholder="Search users..." class="pl-10 pr-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none w-full">
            </div>
            <button class="bg-white border border-gray-200 p-2 rounded-xl hover:bg-gray-50 text-gray-600">
                <i class="fas fa-filter"></i>
            </button>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-400 text-xs uppercase font-bold">
                    <th class="px-6 py-4">User</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4">Role</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach($users as $u): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600 uppercase">
                                    <?= substr($u['username'], 0, 1) ?>
                                </div>
                                <span class="font-medium text-gray-800"><?= htmlspecialchars($u['username']) ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500"><?= htmlspecialchars($u['email']) ?></td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-blue-100 text-blue-700 uppercase">
                                <?= htmlspecialchars($u['role_name'] ?? 'No Role') ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="flex items-center gap-1.5 text-xs font-medium <?= $u['status'] === 'verified' ? 'text-green-600' : 'text-orange-500' ?>">
                                <span class="w-1.5 h-1.5 rounded-full <?= $u['status'] === 'verified' ? 'bg-green-600' : 'bg-orange-500' ?>"></span>
                                <?= ucfirst($u['status']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="<?= APP_URL ?>/admin/edit-user?id=<?= $u['id'] ?>" class="text-gray-400 hover:text-blue-600 transition-colors px-2">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="<?= APP_URL ?>/admin/delete-user" method="POST" class="inline-block">
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors px-2" onclick="return confirm('Are you sure you want to delete this user?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
