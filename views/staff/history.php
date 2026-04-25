<?php
// views/staff/history.php
?>

<!-- Header -->
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">My Verification History</h1>
        <p class="text-sm text-gray-500 mt-1"><?= count($records) ?> record<?= count($records) !== 1 ? 's' : '' ?> found</p>
    </div>
</div>

<!-- Filter Tabs -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-1.5 mb-6 inline-flex gap-1">
    <?php
    $tabs = [
        'all'      => ['All',      'text-gray-600'],
        'approved' => ['Approved', 'text-green-700'],
        'rejected' => ['Rejected', 'text-red-700'],
    ];
    foreach($tabs as $val => [$label, $color]):
        $active = $filter === $val;
    ?>
    <a href="<?= APP_URL ?>/staff/history?filter=<?= $val ?>"
       class="px-5 py-2 rounded-xl text-sm font-semibold transition-all <?= $active
           ? ($val === 'approved' ? 'bg-green-600 text-white shadow-sm' : ($val === 'rejected' ? 'bg-red-600 text-white shadow-sm' : 'bg-violet-600 text-white shadow-sm'))
           : "text-gray-500 hover:text-gray-700 hover:bg-gray-50" ?>">
        <?= $label ?>
    </a>
    <?php endforeach; ?>
</div>

<!-- Table -->
<?php if (empty($records)): ?>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center">
        <i class="fas fa-clipboard-list text-gray-300 text-5xl mb-4"></i>
        <h3 class="text-lg font-bold text-gray-700 mb-1">No Records Yet</h3>
        <p class="text-sm text-gray-400">Your completed verifications will appear here.</p>
        <a href="<?= APP_URL ?>/staff/pending" class="mt-4 inline-flex items-center gap-2 text-sm text-violet-600 font-semibold hover:underline">
            <i class="fas fa-arrow-right"></i> Start reviewing properties
        </a>
    </div>
<?php else: ?>
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Property</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Landlord</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Type</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Price</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Verdict</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Notes</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php foreach($records as $r): ?>
                <tr class="hover:bg-gray-50 transition-colors group">
                    <td class="px-6 py-4">
                        <p class="font-semibold text-gray-900 group-hover:text-violet-700 transition truncate max-w-[180px]">
                            <?= htmlspecialchars($r['title']) ?>
                        </p>
                        <p class="text-xs text-gray-400 truncate max-w-[180px] mt-0.5">
                            <i class="fas fa-map-marker-alt mr-1 text-gray-300"></i><?= htmlspecialchars($r['address']) ?>
                        </p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-gray-700 font-medium"><?= htmlspecialchars($r['landlord_name']) ?></span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-xs font-semibold px-2.5 py-1 rounded-full
                            <?= match($r['property_type']) {
                                'apartment'  => 'bg-blue-100 text-blue-700',
                                'house'      => 'bg-green-100 text-green-700',
                                'commercial' => 'bg-orange-100 text-orange-700',
                                'land'       => 'bg-amber-100 text-amber-700',
                                default      => 'bg-gray-100 text-gray-600',
                            } ?>">
                            <?= ucfirst($r['property_type']) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 font-bold text-gray-800">
                        ₦<?= number_format($r['price'], 0) ?>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1.5 text-xs font-bold px-2.5 py-1 rounded-full
                            <?= $r['result'] === 'approved' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                            <i class="fas <?= $r['result'] === 'approved' ? 'fa-check' : 'fa-times' ?> text-[10px]"></i>
                            <?= ucfirst($r['result']) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <?php if (!empty($r['notes'])): ?>
                            <span class="text-xs text-gray-500 italic max-w-[150px] block truncate" title="<?= htmlspecialchars($r['notes']) ?>">
                                "<?= htmlspecialchars($r['notes']) ?>"
                            </span>
                        <?php else: ?>
                            <span class="text-gray-300 text-xs">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-6 py-4 text-xs text-gray-400 whitespace-nowrap">
                        <?= date('M j, Y', strtotime($r['verified_at'])) ?><br>
                        <span class="text-gray-300"><?= date('g:i a', strtotime($r['verified_at'])) ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>
