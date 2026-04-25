<?php
// views/staff/dashboard.php
$name = htmlspecialchars($_SESSION['username'] ?? 'Staff Member');
$hour = (int)date('H');
$greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
?>

<!-- Page Header -->
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900"><?= $greeting ?>, <?= $name ?> 👋</h1>
        <p class="text-sm text-gray-500 mt-1">Here's your verification overview for today.</p>
    </div>
    <a href="<?= APP_URL ?>/staff/pending"
       class="inline-flex items-center gap-2 bg-violet-600 hover:bg-violet-700 text-white font-semibold px-5 py-2.5 rounded-xl shadow transition-all text-sm">
        <i class="fas fa-clipboard-check"></i> Start Verifying
    </a>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <!-- Pending -->
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
        <div class="w-12 h-12 rounded-xl bg-yellow-100 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-hourglass-half text-yellow-600 text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900"><?= $totalPending ?></p>
            <p class="text-xs text-gray-500 font-medium mt-0.5">Awaiting Verification</p>
        </div>
    </div>

    <!-- Approved by me -->
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
        <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-check-circle text-green-600 text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900"><?= $totalApproved ?></p>
            <p class="text-xs text-gray-500 font-medium mt-0.5">Approved by Me</p>
        </div>
    </div>

    <!-- Rejected by me -->
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
        <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-times-circle text-red-600 text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900"><?= $totalRejected ?></p>
            <p class="text-xs text-gray-500 font-medium mt-0.5">Rejected by Me</p>
        </div>
    </div>

    <!-- Total Reviewed -->
    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex items-center gap-4 hover:shadow-md transition-shadow">
        <div class="w-12 h-12 rounded-xl bg-violet-100 flex items-center justify-center flex-shrink-0">
            <i class="fas fa-star text-violet-600 text-xl"></i>
        </div>
        <div>
            <p class="text-2xl font-bold text-gray-900"><?= $totalDone ?></p>
            <p class="text-xs text-gray-500 font-medium mt-0.5">Total Reviewed</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <!-- Pending Queue Preview -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-clock text-yellow-500"></i> Pending Queue
            </h2>
            <a href="<?= APP_URL ?>/staff/pending" class="text-xs text-violet-600 hover:underline font-semibold">View All</a>
        </div>
        <div class="divide-y divide-gray-50">
            <?php if (empty($recentPending)): ?>
                <div class="px-6 py-10 text-center">
                    <i class="fas fa-check-double text-green-400 text-3xl mb-3"></i>
                    <p class="text-gray-500 text-sm font-medium">All caught up! No pending properties.</p>
                </div>
            <?php else: ?>
                <?php foreach($recentPending as $p): ?>
                <div class="px-6 py-4 flex items-start justify-between hover:bg-gray-50 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-violet-100 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-home text-violet-500 text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800 leading-tight"><?= htmlspecialchars($p['title']) ?></p>
                            <p class="text-xs text-gray-400 mt-0.5 truncate max-w-[180px]"><?= htmlspecialchars($p['address']) ?></p>
                        </div>
                    </div>
                    <a href="<?= APP_URL ?>/staff/view-property?id=<?= $p['id'] ?>"
                       class="text-xs bg-violet-600 text-white px-3 py-1.5 rounded-lg hover:bg-violet-700 transition font-semibold whitespace-nowrap">
                        Review
                    </a>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-bold text-gray-800 flex items-center gap-2">
                <i class="fas fa-history text-blue-500"></i> My Recent Activity
            </h2>
            <a href="<?= APP_URL ?>/staff/history" class="text-xs text-violet-600 hover:underline font-semibold">View All</a>
        </div>
        <div class="divide-y divide-gray-50">
            <?php if (empty($recentActivity)): ?>
                <div class="px-6 py-10 text-center">
                    <i class="fas fa-clipboard-list text-gray-300 text-3xl mb-3"></i>
                    <p class="text-gray-500 text-sm font-medium">No verifications completed yet.</p>
                </div>
            <?php else: ?>
                <?php foreach($recentActivity as $v): ?>
                <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0 <?= $v['result'] === 'approved' ? 'bg-green-100' : 'bg-red-100' ?>">
                            <i class="fas <?= $v['result'] === 'approved' ? 'fa-check text-green-600' : 'fa-times text-red-600' ?> text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-800 leading-tight"><?= htmlspecialchars($v['title']) ?></p>
                            <p class="text-xs text-gray-400 mt-0.5"><?= date('M j, Y g:ia', strtotime($v['verified_at'])) ?></p>
                        </div>
                    </div>
                    <span class="text-[11px] font-bold px-2.5 py-1 rounded-full <?= $v['result'] === 'approved' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                        <?= ucfirst($v['result']) ?>
                    </span>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

</div>
