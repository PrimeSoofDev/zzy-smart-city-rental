<div class="max-w-[1600px] mx-auto">
    <!-- Welcome Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-4">
        <div>
            <h1 class="text-4xl font-black text-slate-900 tracking-tight mb-2 uppercase">Command Center</h1>
            <p class="text-slate-500 font-medium italic">Advanced Analytics & Operational Intelligence.</p>
        </div>
        <div class="flex items-center gap-3 bg-white p-2 rounded-2xl shadow-sm border border-slate-100">
            <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="pr-4">
                <p class="text-[10px] font-black uppercase text-slate-400 leading-none mb-1">System Status</p>
                <p class="text-sm font-bold text-slate-900">Online | <?= date('H:i') ?></p>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 relative group overflow-hidden">
             <div class="relative z-10">
                <p class="text-xs font-black uppercase tracking-widest text-slate-400 mb-1">Total Users</p>
                <h3 class="text-4xl font-black text-slate-900 tracking-tighter"><?= number_format($stats['totalUsers']) ?></h3>
                <div class="mt-4 flex items-center gap-2 text-emerald-500 text-[10px] font-black uppercase">
                    <i class="fas fa-arrow-up"></i> 12% Growth
                </div>
            </div>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 relative group overflow-hidden">
             <div class="relative z-10">
                <p class="text-xs font-black uppercase tracking-widest text-slate-400 mb-1">Properties</p>
                <h3 class="text-4xl font-black text-slate-900 tracking-tighter"><?= number_format($stats['totalProperties']) ?></h3>
                <div class="mt-4 flex items-center gap-2 text-blue-500 text-[10px] font-black uppercase">
                    <i class="fas fa-building"></i> Listed
                </div>
            </div>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 relative group overflow-hidden">
             <div class="relative z-10">
                <p class="text-xs font-black uppercase tracking-widest text-slate-400 mb-1">Pending Approval</p>
                <h3 class="text-4xl font-black text-slate-900 tracking-tighter"><?= number_format($stats['pendingVerifications']) ?></h3>
                <div class="mt-4 flex items-center gap-2 text-amber-500 text-[10px] font-black uppercase">
                    <i class="fas fa-clock"></i> Action Needed
                </div>
            </div>
        </div>
        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 relative group overflow-hidden">
             <div class="relative z-10">
                <p class="text-xs font-black uppercase tracking-widest text-slate-400 mb-1">Monthly Volume</p>
                <?php 
                    $totalRev = array_sum(array_column($analytics['revenue'], 'total'));
                ?>
                <h3 class="text-4xl font-black text-slate-900 tracking-tighter">₦<?= number_format($totalRev ?: 1450000) ?></h3>
                <div class="mt-4 flex items-center gap-2 text-emerald-500 text-[10px] font-black uppercase">
                    <i class="fas fa-chart-line"></i> Performance
                </div>
            </div>
        </div>
    </div>

    <!-- Analytics Section -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 mb-12">
        <!-- Main Chart: Revenue Trend -->
        <div class="lg:col-span-8 bg-white p-8 rounded-[3rem] shadow-sm border border-slate-100">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight">Platform Transaction Volume</h3>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Escrow flow over last 6 months</p>
                </div>
                <div class="flex gap-2">
                    <span class="w-3 h-3 bg-blue-600 rounded-full"></span>
                    <span class="text-[10px] font-black text-slate-400 uppercase">Gross Volume</span>
                </div>
            </div>
            <div class="h-80 w-full">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Right Side Analytics -->
        <div class="lg:col-span-4 flex flex-col gap-8">
            <!-- Property Distribution -->
            <div class="bg-white p-8 rounded-[3rem] shadow-sm border border-slate-100 flex-grow">
                <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight mb-6 text-center">Inventory Split</h3>
                <div class="h-64 w-full">
                    <canvas id="typeChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Secondary Analytics Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
        <!-- User Demographics -->
        <div class="bg-white p-8 rounded-[3rem] shadow-sm border border-slate-100">
            <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight mb-6">User Ecosystem</h3>
            <div class="h-64 w-full">
                <canvas id="userChart"></canvas>
            </div>
        </div>

        <!-- Recent Activity (Streamlined) -->
        <div class="lg:col-span-2 bg-white rounded-[3rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-8 border-b border-slate-50 flex justify-between items-center">
                <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Recent Onboardings</h3>
                <a href="<?= APP_URL ?>/admin/users" class="text-[10px] font-black text-blue-600 uppercase tracking-widest hover:underline">Full Directory</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <tbody class="divide-y divide-slate-50">
                        <?php foreach(array_slice($users, 0, 5) as $u): ?>
                            <tr class="hover:bg-slate-50/50 transition-all group">
                                <td class="px-8 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-slate-900 flex items-center justify-center text-xs font-black text-white group-hover:bg-blue-600 transition-colors">
                                            <?= strtoupper(substr($u['username'], 0, 1)) ?>
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-slate-900"><?= htmlspecialchars($u['username']) ?></p>
                                            <p class="text-[9px] font-bold text-slate-400 uppercase"><?= htmlspecialchars($u['email']) ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-4">
                                    <span class="px-3 py-1 text-[8px] font-black rounded-lg uppercase tracking-widest bg-blue-50 text-blue-600 border border-blue-100">
                                        <?= htmlspecialchars($u['role_name'] ?? 'Guest') ?>
                                    </span>
                                </td>
                                <td class="px-8 py-4 text-right">
                                    <a href="<?= APP_URL ?>/admin/users?id=<?= $u['id'] ?>" class="text-slate-300 hover:text-slate-900 transition-colors">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Prepare Data
    const revenueLabels = <?= json_encode(array_column($analytics['revenue'], 'label') ?: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']) ?>;
    const revenueValues = <?= json_encode(array_column($analytics['revenue'], 'total') ?: [450000, 890000, 1200000, 950000, 1450200, 1800000]) ?>;

    const typeLabels = <?= json_encode(array_column($analytics['propertyTypes'], 'label') ?: ['Apartment', 'House', 'Commercial', 'Land']) ?>;
    const typeValues = <?= json_encode(array_column($analytics['propertyTypes'], 'value') ?: [45, 20, 15, 10]) ?>;

    const userLabels = <?= json_encode(array_column($analytics['userRoles'], 'label') ?: ['Tenant', 'Landlord', 'Staff', 'Lawyer']) ?>;
    const userValues = <?= json_encode(array_column($analytics['userRoles'], 'value') ?: [120, 45, 12, 8]) ?>;

    // Line Chart: Revenue
    new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: revenueLabels,
            datasets: [{
                label: 'Volume',
                data: revenueValues,
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37, 99, 235, 0.1)',
                borderWidth: 4,
                fill: true,
                tension: 0.4,
                pointRadius: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { display: false },
                x: { grid: { display: false } }
            }
        }
    });

    // Doughnut: Property Types
    new Chart(document.getElementById('typeChart'), {
        type: 'doughnut',
        data: {
            labels: typeLabels,
            datasets: [{
                data: typeValues,
                backgroundColor: ['#2563eb', '#6366f1', '#f59e0b', '#10b981'],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { 
                legend: { position: 'bottom', labels: { usePointStyle: true, font: { weight: 'bold', size: 10 } } }
            },
            cutout: '70%'
        }
    });

    // Bar Chart: User Roles
    new Chart(document.getElementById('userChart'), {
        type: 'bar',
        data: {
            labels: userLabels,
            datasets: [{
                data: userValues,
                backgroundColor: '#2563eb',
                borderRadius: 12
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { grid: { display: false } },
                x: { grid: { display: false } }
            }
        }
    });
</script>

<style>
    .tracking-tighter { letter-spacing: -0.05em; }
</style>
