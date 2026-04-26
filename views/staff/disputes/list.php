<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Dispute Management</h2>
        <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-full uppercase">Active Disputes</span>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b border-gray-100">
                <tr class="text-xs font-bold text-gray-500 uppercase tracking-wider">
                    <th class="px-6 py-4">Property</th>
                    <th class="px-6 py-4">Parties</th>
                    <th class="px-6 py-4">Reason</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($disputes)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                            <i class="fas fa-check-circle text-3xl mb-2 block"></i>
                            <p>No active disputes at the moment.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($disputes as $d): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4">
                                <p class="text-sm font-bold text-gray-900"><?= htmlspecialchars($d['property_title']) ?></p>
                                <p class="text-xs text-gray-500">ID: #<?= $d['id'] ?></p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col gap-1">
                                    <span class="text-xs font-medium text-gray-700">T: <?= htmlspecialchars($d['tenant_name']) ?></span>
                                    <span class="text-xs font-medium text-gray-700">L: <?= htmlspecialchars($d['landlord_name']) ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-gray-600 truncate max-w-xs"><?= htmlspecialchars($d['reason']) ?></p>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-[10px] font-bold rounded-full uppercase
                                    <?= $d['status'] === 'open' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700' ?>">
                                    <?= $d['status'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="<?= APP_URL ?>/staff/disputes/mediate?id=<?= $d['id'] ?>"
                                   class="text-sm font-bold text-blue-600 hover:text-blue-800 transition-colors">
                                    Mediate <i class="fas fa-chevron-right ml-1 text-[10px]"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </div>
        </div>
    </div>
</div>