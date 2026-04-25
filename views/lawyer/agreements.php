<?php
// views/lawyer/agreements.php
?>

<!-- Header -->
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">All My Agreements</h1>
        <p class="text-sm text-gray-500 mt-1"><?= count($agreements) ?> agreement<?= count($agreements) !== 1 ? 's' : '' ?> found</p>
    </div>
    <a href="<?= APP_URL ?>/lawyer/requests"
       class="inline-flex items-center gap-2 bg-teal-700 hover:bg-teal-800 text-white font-semibold px-5 py-2.5 rounded-xl shadow transition-all text-sm">
        <i class="fas fa-plus"></i> Draft New
    </a>
</div>

<!-- Filter Tabs -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-1.5 mb-6 inline-flex gap-1">
    <?php
    $tabs = ['all' => 'All', 'draft' => 'Drafts', 'signed' => 'Signed', 'expired' => 'Expired'];
    foreach($tabs as $val => $label):
        $active = $filter === $val;
        $activeClass = match($val) {
            'draft'   => 'bg-blue-600 text-white shadow-sm',
            'signed'  => 'bg-green-600 text-white shadow-sm',
            'expired' => 'bg-red-600 text-white shadow-sm',
            default   => 'bg-teal-700 text-white shadow-sm',
        };
    ?>
    <a href="<?= APP_URL ?>/lawyer/agreements?filter=<?= $val ?>"
       class="px-5 py-2 rounded-xl text-sm font-semibold transition-all <?= $active ? $activeClass : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' ?>">
        <?= $label ?>
    </a>
    <?php endforeach; ?>
</div>

<!-- Agreements List -->
<?php if (empty($agreements)): ?>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center">
        <i class="fas fa-folder-open text-gray-300 text-5xl mb-4"></i>
        <h3 class="text-lg font-bold text-gray-700 mb-1">No Agreements Found</h3>
        <p class="text-sm text-gray-400">Your drafted agreements will appear here.</p>
        <a href="<?= APP_URL ?>/lawyer/requests"
           class="mt-4 inline-flex items-center gap-2 text-sm text-teal-700 font-semibold hover:underline">
            <i class="fas fa-arrow-right"></i> Go to Paid Requests
        </a>
    </div>
<?php else: ?>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Property</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Tenant</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Landlord</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Price/yr</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Signed At</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php foreach($agreements as $ag): ?>
                    <?php
                    $badge = match($ag['status']) {
                        'draft'   => 'bg-blue-100 text-blue-700',
                        'signed'  => 'bg-green-100 text-green-700',
                        'expired' => 'bg-red-100 text-red-700',
                        default   => 'bg-gray-100 text-gray-600',
                    };
                    $icon = match($ag['status']) {
                        'draft'   => 'fa-file-alt',
                        'signed'  => 'fa-file-signature',
                        'expired' => 'fa-ban',
                        default   => 'fa-file',
                    };
                    ?>
                    <tr class="hover:bg-gray-50 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-teal-100 flex items-center justify-center flex-shrink-0">
                                    <i class="fas <?= $icon ?> text-teal-600 text-xs"></i>
                                </div>
                                <div>
                                    <p class="font-semibold text-gray-900 text-sm leading-tight truncate max-w-[150px]">
                                        <?= htmlspecialchars($ag['property_title']) ?>
                                    </p>
                                    <p class="text-xs text-gray-400 font-mono">Req #<?= $ag['request_id'] ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-gray-700 font-medium"><?= htmlspecialchars($ag['tenant_name']) ?></span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-gray-700 font-medium"><?= htmlspecialchars($ag['landlord_name']) ?></span>
                        </td>
                        <td class="px-6 py-4 font-bold text-gray-800">
                            ₦<?= number_format($ag['price'], 0) ?>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 text-xs font-bold px-2.5 py-1 rounded-full <?= $badge ?>">
                                <i class="fas <?= $icon ?> text-[10px]"></i>
                                <?= ucfirst($ag['status']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-400">
                            <?= $ag['signed_at'] ? date('M j, Y', strtotime($ag['signed_at'])) : '<span class="text-gray-300">—</span>' ?>
                        </td>
                        <td class="px-6 py-4">
                            <a href="<?= APP_URL ?>/lawyer/view-agreement?id=<?= $ag['id'] ?>"
                               class="inline-flex items-center gap-1.5 text-xs bg-teal-700 hover:bg-teal-800 text-white font-semibold px-3 py-2 rounded-lg transition">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
