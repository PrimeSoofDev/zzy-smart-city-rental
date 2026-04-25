<h1 class="text-3xl font-bold text-gray-800 mb-8">Rental Requests Management</h1>

<!-- Filters -->
<div class="mb-6 flex gap-2">
    <a href="<?= APP_URL ?>/admin/requests?status=all" class="px-4 py-2 rounded-lg text-sm font-semibold <?= $status === 'all' ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' ?> shadow-sm">All</a>
    <a href="<?= APP_URL ?>/admin/requests?status=pending" class="px-4 py-2 rounded-lg text-sm font-semibold <?= $status === 'pending' ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' ?> shadow-sm">Pending</a>
    <a href="<?= APP_URL ?>/admin/requests?status=processing" class="px-4 py-2 rounded-lg text-sm font-semibold <?= $status === 'processing' ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' ?> shadow-sm">Processing</a>
    <a href="<?= APP_URL ?>/admin/requests?status=paid" class="px-4 py-2 rounded-lg text-sm font-semibold <?= $status === 'paid' ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' ?> shadow-sm">Paid</a>
    <a href="<?= APP_URL ?>/admin/requests?status=completed" class="px-4 py-2 rounded-lg text-sm font-semibold <?= $status === 'completed' ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' ?> shadow-sm">Completed</a>
    <a href="<?= APP_URL ?>/admin/requests?status=cancelled" class="px-4 py-2 rounded-lg text-sm font-semibold <?= $status === 'cancelled' ? 'bg-blue-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50' ?> shadow-sm">Cancelled</a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-400 text-xs uppercase font-bold">
                    <th class="px-6 py-4">ID</th>
                    <th class="px-6 py-4">Property</th>
                    <th class="px-6 py-4">Tenant</th>
                    <th class="px-6 py-4">Landlord</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4">Date</th>
                    <th class="px-6 py-4">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($requests)): ?>
                    <tr><td colspan="7" class="px-6 py-8 text-center text-gray-500">No rental requests found.</td></tr>
                <?php else: ?>
                    <?php foreach ($requests as $r): ?>
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 font-mono text-xs text-gray-500">#<?= $r['id'] ?></td>
                            <td class="px-6 py-4 text-sm text-gray-800 font-bold">
                                <?= htmlspecialchars($r['property_title']) ?>
                                <div class="text-xs text-gray-500 font-normal mt-0.5">₦<?= number_format($r['price'], 2) ?>/yr</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-800"><?= htmlspecialchars($r['tenant_name']) ?></td>
                            <td class="px-6 py-4 text-sm text-gray-800"><?= htmlspecialchars($r['landlord_name']) ?></td>
                            <td class="px-6 py-4">
                                <?php
                                $badgeClass = match($r['status']) {
                                    'pending' => 'bg-yellow-100 text-yellow-700',
                                    'processing' => 'bg-blue-100 text-blue-700',
                                    'paid' => 'bg-purple-100 text-purple-700',
                                    'completed' => 'bg-green-100 text-green-700',
                                    'cancelled' => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-100 text-gray-700'
                                };
                                ?>
                                <span class="px-2 py-1 text-[10px] font-bold rounded-full <?= $badgeClass ?> uppercase">
                                    <?= $r['status'] ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-400"><?= date('Y-m-d', strtotime($r['request_date'])) ?></td>
                            <td class="px-6 py-4">
                                <form method="POST" action="<?= APP_URL ?>/admin/updateRequestStatus" class="flex items-center gap-2">
                                    <input type="hidden" name="request_id" value="<?= $r['id'] ?>">
                                    <select name="status" class="px-2 py-1 border rounded text-xs outline-none">
                                        <option value="pending" <?= $r['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="processing" <?= $r['status'] === 'processing' ? 'selected' : '' ?>>Processing</option>
                                        <option value="paid" <?= $r['status'] === 'paid' ? 'selected' : '' ?>>Paid</option>
                                        <option value="completed" <?= $r['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                                        <option value="cancelled" <?= $r['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                    </select>
                                    <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-600 px-2 py-1 rounded text-xs font-bold transition-colors">
                                        Update
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
