<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-10">
        <div>
            <h1 class="text-4xl font-extrabold text-gray-900 tracking-tight">System Audit Trail</h1>
            <p class="text-gray-500 mt-2 flex items-center gap-2">
                <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                Monitoring real-time system activities and security events.
            </p>
        </div>
        <div class="flex gap-3">
            <form action="<?= APP_URL ?>/admin/logs" method="GET" class="flex gap-2">
                <select name="action" onchange="this.form.submit()" class="bg-white border border-gray-200 text-gray-700 px-4 py-2.5 rounded-xl font-bold hover:bg-gray-50 transition-all shadow-sm outline-none cursor-pointer">
                    <option value="">All Activities</option>
                    <option value="Created" <?= ($currentFilter === 'Created') ? 'selected' : '' ?>>Creations</option>
                    <option value="Updated" <?= ($currentFilter === 'Updated') ? 'selected' : '' ?>>Updates</option>
                    <option value="Approved" <?= ($currentFilter === 'Approved') ? 'selected' : '' ?>>Approvals</option>
                    <option value="Logged" <?= ($currentFilter === 'Logged') ? 'selected' : '' ?>>Logins</option>
                </select>
            </form>
            <a href="<?= APP_URL ?>/admin/export-logs" class="bg-gray-900 text-white px-5 py-2.5 rounded-xl font-bold hover:bg-black transition-all shadow-lg flex items-center gap-2">
                <i class="fas fa-download"></i> Export CSV
            </a>
        </div>
    </div>

    <div class="bg-white rounded-[2rem] shadow-xl shadow-gray-200/50 border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100 text-gray-400 text-[11px] uppercase tracking-widest font-bold">
                        <th class="px-8 py-5">Event Timeline</th>
                        <th class="px-8 py-5">Initiator</th>
                        <th class="px-8 py-5">Action Performed</th>
                        <th class="px-8 py-5">Target Entity</th>
                        <th class="px-8 py-5 text-right">Access Point</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="5" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center text-gray-300 text-2xl">
                                        <i class="fas fa-history"></i>
                                    </div>
                                    <p class="text-gray-400 font-medium">No system activities recorded yet.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($logs as $log): ?>
                            <tr class="hover:bg-blue-50/30 transition-all duration-300 group">
                                <td class="px-8 py-6">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-900"><?= date('M d, Y', strtotime($log['created_at'])) ?></span>
                                        <span class="text-[11px] text-gray-400 font-medium"><?= date('h:i:s A', strtotime($log['created_at'])) ?></span>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xs font-bold shadow-md shadow-blue-100">
                                            <?= strtoupper(substr($log['username'] ?? 'S', 0, 1)) ?>
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-gray-800"><?= htmlspecialchars($log['full_name'] ?: ($log['username'] ?? 'System')) ?></span>
                                            <span class="text-[10px] px-2 py-0.5 rounded-md bg-gray-100 text-gray-500 font-bold self-start mt-0.5">
                                                <?= $log['user_role'] ?? 'Automated' ?>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <?php 
                                        $actionColor = 'bg-blue-100 text-blue-700';
                                        if (stripos($log['action'], 'delete') !== false || stripos($log['action'], 'reject') !== false) $actionColor = 'bg-red-100 text-red-700';
                                        if (stripos($log['action'], 'create') !== false || stripos($log['action'], 'approve') !== false) $actionColor = 'bg-emerald-100 text-emerald-700';
                                        if (stripos($log['action'], 'update') !== false) $actionColor = 'bg-amber-100 text-amber-700';
                                    ?>
                                    <span class="px-3 py-1.5 rounded-xl text-[10px] font-black tracking-wider uppercase <?= $actionColor ?>">
                                        <?= htmlspecialchars($log['action']) ?>
                                    </span>
                                </td>
                                <td class="px-8 py-6">
                                    <?php if ($log['entity_type']): ?>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[11px] font-bold text-gray-500"><?= $log['entity_type'] ?>:</span>
                                            <code class="px-2 py-1 bg-gray-50 rounded-lg text-xs font-mono text-gray-600">#<?= $log['entity_id'] ?></code>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-xs text-gray-300 font-italic">Global System Event</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <span class="text-xs font-mono text-gray-400 group-hover:text-blue-500 transition-colors">
                                        <i class="fas fa-network-wired text-[10px] mr-1 opacity-50"></i>
                                        <?= $log['ip_address'] ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <div class="mt-8 flex justify-center">
        <nav class="flex gap-2">
            <button class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-400 hover:bg-gray-50 transition-all">
                <i class="fas fa-chevron-left text-xs"></i>
            </button>
            <button class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold shadow-lg shadow-blue-200">1</button>
            <button class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-700 font-bold hover:bg-gray-50 transition-all">2</button>
            <button class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-700 font-bold hover:bg-gray-50 transition-all">3</button>
            <button class="w-10 h-10 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-gray-400 hover:bg-gray-50 transition-all">
                <i class="fas fa-chevron-right text-xs"></i>
            </button>
        </nav>
    </div>
</div>
