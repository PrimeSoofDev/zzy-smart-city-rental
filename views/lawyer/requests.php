<?php
// views/lawyer/requests.php
?>

<!-- Header -->
<div class="mb-8">
    <h1 class="text-2xl font-bold text-gray-900">Paid Rental Requests</h1>
    <p class="text-sm text-gray-500 mt-1">Requests that have been paid and require a legal agreement.</p>
</div>

<!-- Filter Tabs -->
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-1.5 mb-6 inline-flex gap-1">
    <a href="<?= APP_URL ?>/lawyer/requests?filter=pending"
       class="px-5 py-2 rounded-xl text-sm font-semibold transition-all <?= $filter === 'pending' ? 'bg-teal-700 text-white shadow-sm' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' ?>">
        Needs Agreement
    </a>
    <a href="<?= APP_URL ?>/lawyer/requests?filter=all"
       class="px-5 py-2 rounded-xl text-sm font-semibold transition-all <?= $filter === 'all' ? 'bg-teal-700 text-white shadow-sm' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' ?>">
        All Paid
    </a>
</div>

<!-- Requests List -->
<?php if (empty($requests)): ?>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center">
        <i class="fas fa-check-double text-green-400 text-5xl mb-4"></i>
        <h3 class="text-lg font-bold text-gray-700 mb-1">All Clear!</h3>
        <p class="text-sm text-gray-400">No paid requests pending an agreement right now.</p>
    </div>
<?php else: ?>
    <div class="space-y-4">
        <?php foreach($requests as $r): ?>
        <?php
        $agStatus = $r['agreement_status'] ?? null;
        $agBadge  = match($agStatus) {
            'draft'   => ['bg-blue-100 text-blue-700',   'Agreement Drafted'],
            'signed'  => ['bg-green-100 text-green-700', 'Signed'],
            'expired' => ['bg-red-100 text-red-700',     'Expired'],
            default   => ['bg-yellow-100 text-yellow-700', 'No Agreement'],
        };
        ?>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
            <div class="flex flex-col sm:flex-row sm:items-start gap-4">

                <!-- Icon -->
                <div class="w-12 h-12 rounded-xl bg-teal-100 flex items-center justify-center flex-shrink-0">
                    <?php $icon = match($r['property_type'] ?? '') {
                        'apartment'  => 'fa-building',
                        'house'      => 'fa-home',
                        'commercial' => 'fa-store',
                        'land'       => 'fa-map',
                        default      => 'fa-home',
                    }; ?>
                    <i class="fas <?= $icon ?> text-teal-700 text-xl"></i>
                </div>

                <!-- Details -->
                <div class="flex-1 min-w-0">
                    <div class="flex flex-wrap items-center gap-2 mb-1">
                        <h3 class="font-bold text-gray-900 text-sm"><?= htmlspecialchars($r['property_title']) ?></h3>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full <?= $agBadge[0] ?>"><?= $agBadge[1] ?></span>
                    </div>
                    <p class="text-xs text-gray-400 mb-3">
                        <i class="fas fa-map-marker-alt mr-1 text-teal-400"></i><?= htmlspecialchars($r['address']) ?>
                    </p>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-semibold">Rent/yr</p>
                            <p class="text-sm font-bold text-gray-800">₦<?= number_format($r['price'], 0) ?></p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-semibold">Tenant</p>
                            <p class="text-sm font-semibold text-gray-700"><?= htmlspecialchars($r['tenant_name']) ?></p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-semibold">Landlord</p>
                            <p class="text-sm font-semibold text-gray-700"><?= htmlspecialchars($r['landlord_name']) ?></p>
                        </div>
                        <div>
                            <p class="text-[10px] text-gray-400 uppercase font-semibold">Request ID</p>
                            <p class="text-sm font-bold text-gray-600 font-mono">#<?= $r['id'] ?></p>
                        </div>
                    </div>
                </div>

                <!-- Action -->
                <div class="flex flex-col gap-2 flex-shrink-0">
                    <?php if (!$agStatus): ?>
                    <a href="<?= APP_URL ?>/lawyer/draft-agreement?id=<?= $r['id'] ?>"
                       class="inline-flex items-center justify-center gap-2 bg-teal-700 hover:bg-teal-800 text-white font-bold px-4 py-2.5 rounded-xl text-sm transition-all">
                        <i class="fas fa-pen-nib"></i> Draft Agreement
                    </a>
                    <?php elseif ($agStatus === 'draft'): ?>
                    <a href="<?= APP_URL ?>/lawyer/draft-agreement?id=<?= $r['id'] ?>"
                       class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2.5 rounded-xl text-sm transition-all">
                        <i class="fas fa-edit"></i> Edit Draft
                    </a>
                    <?php else: ?>
                    <span class="inline-flex items-center justify-center gap-2 bg-gray-100 text-gray-500 font-bold px-4 py-2.5 rounded-xl text-sm cursor-default">
                        <i class="fas fa-lock"></i> Finalized
                    </span>
                    <?php endif; ?>
                    <p class="text-[10px] text-gray-400 text-center">
                        <?= date('M j, Y', strtotime($r['request_date'])) ?>
                    </p>
                </div>

            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
