<?php
// views/lawyer/dashboard.php
$hour = (int)date('H');
$greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
$name = htmlspecialchars($_SESSION['username'] ?? 'Counsel');
?>

<!-- Header -->
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900"><?= $greeting ?>, <?= $name ?> ⚖️</h1>
        <p class="text-sm text-gray-500 mt-1">Manage rental agreements and legal documentation.</p>
    </div>
    <a href="<?= APP_URL ?>/lawyer/requests"
       class="inline-flex items-center gap-2 bg-teal-700 hover:bg-teal-800 text-white font-semibold px-5 py-2.5 rounded-xl shadow transition-all text-sm">
        <i class="fas fa-file-signature"></i> Draft New Agreement
    </a>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
        <div class="w-12 h-12 rounded-xl bg-yellow-100 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-inbox text-yellow-600 text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900"><?= $totalPaidRequests ?></p>
            <p class="text-xs text-gray-500 font-medium mt-0.5">Paid Requests</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
        <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-file-alt text-blue-600 text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900"><?= $myDrafts ?></p>
            <p class="text-xs text-gray-500 font-medium mt-0.5">Draft Agreements</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
        <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-file-signature text-green-600 text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900"><?= $mySigned ?></p>
            <p class="text-xs text-gray-500 font-medium mt-0.5">Signed Agreements</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
        <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-ban text-red-500 text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900"><?= $myExpired ?></p>
            <p class="text-xs text-gray-500 font-medium mt-0.5">Expired Agreements</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <!-- Requests Needing Attention -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-inbox text-yellow-500"></i> Requests Needing Attention
            </h2>
            <a href="<?= APP_URL ?>/lawyer/requests" class="text-xs text-teal-700 hover:underline font-semibold">View All</a>
        </div>
        <div class="divide-y divide-gray-50">
            <?php if (empty($recentRequests)): ?>
                <div class="px-6 py-10 text-center">
                    <i class="fas fa-check-double text-green-400 text-3xl mb-3"></i>
                    <p class="text-gray-500 text-sm font-medium">All agreements are up to date.</p>
                </div>
            <?php else: ?>
                <?php foreach($recentRequests as $r): ?>
                <div class="px-6 py-4 flex items-start justify-between hover:bg-gray-50 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-teal-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-home text-teal-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800 leading-tight"><?= htmlspecialchars($r['property_title']) ?></p>
                            <p class="text-xs text-gray-400 mt-0.5">Tenant: <strong><?= htmlspecialchars($r['tenant_name']) ?></strong></p>
                        </div>
                    </div>
                    <?php if ($r['agreement_status']): ?>
                        <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-blue-100 text-blue-700">
                            <?= ucfirst($r['agreement_status']) ?>
                        </span>
                    <?php else: ?>
                        <a href="<?= APP_URL ?>/lawyer/draft-agreement?id=<?= $r['id'] ?>"
                           class="text-xs bg-teal-700 text-white px-3 py-1.5 rounded-lg hover:bg-teal-800 transition font-semibold whitespace-nowrap">
                            Draft
                        </a>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- My Recent Agreements -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-file-signature text-teal-600"></i> My Recent Agreements
            </h2>
            <a href="<?= APP_URL ?>/lawyer/agreements" class="text-xs text-teal-700 hover:underline font-semibold">View All</a>
        </div>
        <div class="divide-y divide-gray-50">
            <?php if (empty($recentAgreements)): ?>
                <div class="px-6 py-10 text-center">
                    <i class="fas fa-folder-open text-gray-300 text-3xl mb-3"></i>
                    <p class="text-gray-500 text-sm font-medium">No agreements drafted yet.</p>
                </div>
            <?php else: ?>
                <?php foreach($recentAgreements as $ag): ?>
                <?php
                $badge = match($ag['status']) {
                    'draft'   => 'bg-blue-100 text-blue-700',
                    'signed'  => 'bg-green-100 text-green-700',
                    'expired' => 'bg-red-100 text-red-700',
                    default   => 'bg-gray-100 text-gray-600',
                };
                ?>
                <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-teal-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-file-alt text-teal-600 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800 leading-tight"><?= htmlspecialchars($ag['property_title']) ?></p>
                            <p class="text-xs text-gray-400 mt-0.5"><?= htmlspecialchars($ag['tenant_name']) ?> ↔ <?= htmlspecialchars($ag['landlord_name']) ?></p>
                        </div>
                    </div>
                    <span class="text-[11px] font-bold px-2.5 py-1 rounded-full <?= $badge ?>">
                        <?= ucfirst($ag['status']) ?>
                    </span>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</div>
