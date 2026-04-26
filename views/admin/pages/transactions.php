<h1 class="text-3xl font-bold text-gray-800 mb-8">Financial Overview</h1>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <p class="text-gray-500 text-xs font-bold uppercase mb-1">Escrow Balance</p>
        <p class="text-3xl font-extrabold text-gray-900">₦<?= number_format($stats['escrowBalance'], 2) ?></p>
        <div class="mt-2 text-blue-600 text-xs font-bold">Live Holdings</div>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <p class="text-gray-500 text-xs font-bold uppercase mb-1">Monthly Revenue</p>
        <p class="text-3xl font-extrabold text-gray-900">₦<?= number_format($stats['monthlyRevenue'], 2) ?></p>
        <div class="mt-2 text-green-600 text-xs font-bold">Platform Earnings</div>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <p class="text-gray-500 text-xs font-bold uppercase mb-1">Failed Payments</p>
        <p class="text-3xl font-extrabold text-red-600"><?= number_format($stats['failedCount']) ?></p>
        <div class="mt-2 text-red-600 text-xs font-bold">Requires Attention</div>
    </div>
</div>

<!-- Financial Trends Chart -->
<div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-gray-100 mb-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h3 class="text-xl font-black text-gray-900 uppercase tracking-tight">Marketplace Cashflow Trend</h3>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">Tenant Deposits vs Landlord Payouts</p>
        </div>
        <div class="flex gap-6">
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 bg-blue-600 rounded-full"></span>
                <span class="text-[10px] font-black text-gray-500 uppercase">Tenant Payments</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="w-3 h-3 bg-emerald-500 rounded-full"></span>
                <span class="text-[10px] font-black text-gray-500 uppercase">Landlord Earnings</span>
            </div>
        </div>
    </div>
    <div class="h-64 w-full">
        <canvas id="cashflowTrendChart"></canvas>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <h3 class="font-bold text-gray-800">Transaction Ledger</h3>
        <form action="" method="GET" class="flex flex-wrap gap-3">
            <select name="type" onchange="this.form.submit()" class="px-4 py-2 bg-gray-50 border-none rounded-xl text-xs font-bold focus:ring-2 focus:ring-blue-500 outline-none cursor-pointer">
                <option value="all">All Types</option>
                <option value="escrow_deposit" <?= $filters['type'] == 'escrow_deposit' ? 'selected' : '' ?>>Escrow Deposits</option>
                <option value="landlord_payout" <?= $filters['type'] == 'landlord_payout' ? 'selected' : '' ?>>Landlord Payouts</option>
                <option value="refund" <?= $filters['type'] == 'refund' ? 'selected' : '' ?>>Refunds</option>
            </select>
            <select name="status" onchange="this.form.submit()" class="px-4 py-2 bg-gray-50 border-none rounded-xl text-xs font-bold focus:ring-2 focus:ring-blue-500 outline-none cursor-pointer">
                <option value="all">All Statuses</option>
                <option value="completed" <?= $filters['status'] == 'completed' ? 'selected' : '' ?>>Completed</option>
                <option value="escrow_hold" <?= $filters['status'] == 'escrow_hold' ? 'selected' : '' ?>>Escrow Hold</option>
                <option value="released" <?= $filters['status'] == 'released' ? 'selected' : '' ?>>Released</option>
                <option value="failed" <?= $filters['status'] == 'failed' ? 'selected' : '' ?>>Failed</option>
            </select>
            <?php if($filters['type'] !== 'all' || $filters['status'] !== 'all'): ?>
                <a href="<?= APP_URL ?>/admin/transactions" class="px-4 py-2 bg-red-50 text-red-600 rounded-xl text-xs font-bold hover:bg-red-100 transition-colors">Clear</a>
            <?php endif; ?>
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-400 text-xs uppercase font-bold">
                    <th class="px-6 py-4">Transaction ID</th>
                    <th class="px-6 py-4">User</th>
                    <th class="px-6 py-4">Type</th>
                    <th class="px-6 py-4">Amount</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach($transactions as $tx): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 font-mono text-xs text-gray-500">
                        <?= $tx['paystack_reference'] ?: 'TX-' . str_pad($tx['id'], 8, '0', STR_PAD_LEFT) ?>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-800"><?= htmlspecialchars($tx['username']) ?></td>
                    <td class="px-6 py-4">
                        <?php 
                        $typeClass = match($tx['transaction_type']) {
                            'escrow_deposit' => 'bg-blue-100 text-blue-700',
                            'landlord_payout' => 'bg-emerald-100 text-emerald-700',
                            default => 'bg-gray-100 text-gray-700'
                        };
                        ?>
                        <span class="px-2 py-1 text-[10px] font-bold rounded-full <?= $typeClass ?> uppercase">
                            <?= str_replace('_', ' ', $tx['transaction_type']) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 font-bold text-gray-800">₦<?= number_format($tx['amount'], 2) ?></td>
                    <td class="px-6 py-4">
                        <?php 
                        $statusClass = match($tx['status']) {
                            'completed', 'released' => 'bg-green-100 text-green-700',
                            'escrow_hold' => 'bg-yellow-100 text-yellow-700',
                            'failed' => 'bg-red-100 text-red-700',
                            default => 'bg-gray-100 text-gray-700'
                        };
                        ?>
                        <span class="px-2 py-1 text-[10px] font-bold rounded-full <?= $statusClass ?> uppercase">
                            <?= $tx['status'] ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right text-xs text-gray-400"><?= date('Y-m-d', strtotime($tx['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($transactions)): ?>
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-gray-400 italic text-sm">No transactions found.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const labels = <?= json_encode(array_column($trends['payments'], 'label')) ?>;
    const paymentData = <?= json_encode(array_column($trends['payments'], 'total')) ?>;
    const earningData = <?= json_encode(array_column($trends['earnings'], 'total')) ?>;

    new Chart(document.getElementById('cashflowTrendChart'), {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Tenant Payments',
                    data: paymentData,
                    borderColor: '#2563eb',
                    backgroundColor: 'rgba(37, 99, 235, 0.1)',
                    borderWidth: 4,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#fff'
                },
                {
                    label: 'Landlord Earnings',
                    data: earningData,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 4,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointBackgroundColor: '#fff'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { 
                    beginAtZero: true,
                    grid: { color: '#f8fafc' },
                    ticks: { callback: value => '₦' + value.toLocaleString(), font: { size: 10 } }
                },
                x: { grid: { display: false }, ticks: { font: { size: 10, weight: 'bold' } } }
            }
        }
    });
</script>
