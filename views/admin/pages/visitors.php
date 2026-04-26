<div class="flex justify-between items-center mb-8">
    <div>
        <h1 class="text-3xl font-black text-slate-900 tracking-tight">Visitor Analytics</h1>
        <p class="text-slate-500 font-medium">Real-time traffic and user behavior tracking</p>
    </div>
    <div class="px-4 py-2 bg-indigo-50 text-indigo-700 rounded-2xl text-xs font-black uppercase tracking-widest border border-indigo-100">
        Live Traffic
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 relative group overflow-hidden">
        <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-50 rounded-full blur-2xl group-hover:bg-blue-100 transition-all"></div>
        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">Total Visits</p>
        <h3 class="text-4xl font-black text-slate-900 tracking-tighter"><?= number_format($stats['totalVisits']) ?></h3>
        <div class="mt-4 flex items-center gap-2 text-blue-500 text-[10px] font-black uppercase">
            <i class="fas fa-users"></i> All Time Traffic
        </div>
    </div>
    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 relative group overflow-hidden">
        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">Engagement Rate</p>
        <h3 class="text-4xl font-black text-slate-900 tracking-tighter"><?= $stats['interactionRate'] ?>%</h3>
        <div class="mt-4 flex items-center gap-2 text-emerald-500 text-[10px] font-black uppercase">
            <i class="fas fa-mouse-pointer"></i> Scrolled/Interacted
        </div>
    </div>
    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 relative group overflow-hidden">
        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">Direct Signups</p>
        <h3 class="text-4xl font-black text-slate-900 tracking-tighter"><?= number_format($stats['newSignups']) ?></h3>
        <div class="mt-4 flex items-center gap-2 text-indigo-500 text-[10px] font-black uppercase">
            <i class="fas fa-user-plus"></i> Conversion from Visit
        </div>
    </div>
    <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100 relative group overflow-hidden">
        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">First-Visit Verification</p>
        <h3 class="text-4xl font-black text-slate-900 tracking-tighter"><?= number_format($stats['verifiedFirstVisit']) ?></h3>
        <div class="mt-4 flex items-center gap-2 text-amber-500 text-[10px] font-black uppercase">
            <i class="fas fa-check-double"></i> Verified Instantly
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
    <!-- Top Locations -->
    <div class="lg:col-span-1 bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-100">
        <h3 class="text-xl font-black text-slate-900 mb-6 uppercase tracking-tight">Top Locations</h3>
        <div class="space-y-4">
            <?php foreach($locations as $loc): ?>
            <div class="flex items-center justify-between group cursor-default">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-blue-50 group-hover:text-blue-600 transition-all">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <span class="text-sm font-bold text-slate-600"><?= $loc['city'] ?: 'Unknown' ?></span>
                </div>
                <span class="text-xs font-black text-slate-400 bg-slate-50 px-2 py-1 rounded-md"><?= $loc['count'] ?></span>
            </div>
            <?php endforeach; ?>
            <?php if(empty($locations)): ?>
                <p class="text-center text-slate-400 italic py-10">No location data yet.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent Traffic Feed -->
    <div class="lg:col-span-2 bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/50">
            <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight">Recent Activity Feed</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">
                        <th class="px-8 py-4">IP Address</th>
                        <th class="px-8 py-4">User</th>
                        <th class="px-8 py-4">Behavior</th>
                        <th class="px-8 py-4">Status</th>
                        <th class="px-8 py-4 text-right">Time</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php foreach($activity as $row): ?>
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="px-8 py-5">
                            <span class="text-xs font-mono text-slate-500"><?= $row['ip_address'] ?></span>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 text-[10px] font-black">
                                    <?= $row['username'] ? strtoupper(substr($row['username'], 0, 1)) : 'G' ?>
                                </div>
                                <span class="text-sm font-bold text-slate-700"><?= $row['username'] ?: 'Guest' ?></span>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex gap-2">
                                <?php if($row['has_scrolled']): ?>
                                    <span title="Scrolled" class="w-6 h-6 rounded-md bg-emerald-50 text-emerald-600 flex items-center justify-center text-[10px]"><i class="fas fa-mouse"></i></span>
                                <?php endif; ?>
                                <?php if($row['is_new_signup']): ?>
                                    <span title="New Signup" class="w-6 h-6 rounded-md bg-blue-50 text-blue-600 flex items-center justify-center text-[10px]"><i class="fas fa-user-plus"></i></span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <?php if($row['is_first_visit_verified']): ?>
                                <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-black uppercase rounded-full">Insta-Verified</span>
                            <?php else: ?>
                                <span class="px-2 py-1 bg-slate-100 text-slate-500 text-[10px] font-black uppercase rounded-full">Regular</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <span class="text-[10px] font-bold text-slate-400"><?= date('H:i', strtotime($row['created_at'])) ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
