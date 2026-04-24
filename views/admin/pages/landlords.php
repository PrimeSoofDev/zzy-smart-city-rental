<h1 class="text-3xl font-bold text-gray-800 mb-8">Landlord Management</h1>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <h3 class="font-bold text-gray-800">Registered Landlords</h3>
        <div class="flex gap-2">
            <button class="px-3 py-1.5 bg-gray-100 text-gray-600 text-xs font-bold rounded-lg hover:bg-gray-200">Filter</button>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 text-gray-400 text-xs uppercase font-bold">
                <tr>
                    <th class="px-6 py-4">Landlord</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4">Location</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach($landlords as $l): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 font-medium"><?= htmlspecialchars($l['username']) ?></td>
                        <td class="px-6 py-4 text-sm text-gray-500"><?= htmlspecialchars($l['email']) ?></td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?= htmlspecialchars($l['location']) ?></td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-[10px] font-bold rounded-full <?= $l['verification_status'] === 'approved' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' ?> uppercase">
                                <?= $l['verification_status'] ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="<?= APP_URL ?>/admin/edit-landlord?id=<?= $l['id'] ?>" class="text-blue-600 hover:text-blue-800 transition-colors" title="Edit Landlord">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if($l['verification_status'] !== 'approved'): ?>
                                    <form action="<?= APP_URL ?>/admin/approve-landlord" method="POST" class="inline">
                                        <input type="hidden" name="user_id" value="<?= $l['id'] ?>">
                                        <button class="text-green-600 hover:text-green-800 transition-colors" title="Approve">
                                            <i class="fas fa-check-circle"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <form action="<?= APP_URL ?>/admin/delete-landlord" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to permanently delete this landlord?');">
                                    <input type="hidden" name="user_id" value="<?= $l['id'] ?>">
                                    <button class="text-red-600 hover:text-red-800 transition-colors" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
