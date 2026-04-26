<div class="mb-8 flex justify-between items-center">
    <div>
        <h1 class="text-3xl font-black text-slate-900 mb-2">User Reviews</h1>
        <p class="text-slate-500">Monitor and moderate reviews between landlords and tenants.</p>
    </div>
</div>

<div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-100">
                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Reviewer</th>
                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Reviewee</th>
                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Property</th>
                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Rating</th>
                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Comment</th>
                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($reviews)): ?>
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                        <i class="fas fa-star-half-alt text-4xl mb-4 opacity-20"></i>
                        <p>No reviews found.</p>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($reviews as $r): ?>
                    <tr class="border-b border-slate-50 hover:bg-slate-50/50 transition-all">
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-900"><?= htmlspecialchars($r['reviewer_name']) ?></div>
                            <div class="text-[10px] font-black uppercase text-blue-500"><?= $r['reviewer_role'] ?></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-slate-900"><?= htmlspecialchars($r['reviewee_name']) ?></div>
                            <div class="text-[10px] font-black uppercase text-amber-500"><?= $r['reviewee_role'] ?></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs font-medium text-slate-600"><?= htmlspecialchars($r['property_title'] ?? 'N/A') ?></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex text-amber-400 gap-0.5">
                                <?php for($i=1; $i<=5; $i++): ?>
                                    <i class="<?= $i <= $r['rating'] ? 'fas' : 'far' ?> fa-star text-[10px]"></i>
                                <?php endfor; ?>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-xs text-slate-500 max-w-xs truncate" title="<?= htmlspecialchars($r['comment']) ?>">
                                <?= htmlspecialchars($r['comment']) ?>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider <?= $r['status'] == 'active' ? 'bg-green-50 text-green-600 border border-green-100' : 'bg-red-50 text-red-600 border border-red-100' ?>">
                                <?= $r['status'] ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <?php if($r['status'] == 'active'): ?>
                                <a href="<?= APP_URL ?>/admin/review/toggle?id=<?= $r['id'] ?>&status=hidden" class="p-2 text-red-400 hover:text-red-600 transition-all" title="Hide Review">
                                    <i class="fas fa-eye-slash"></i>
                                </a>
                            <?php else: ?>
                                <a href="<?= APP_URL ?>/admin/review/toggle?id=<?= $r['id'] ?>&status=active" class="p-2 text-green-400 hover:text-green-600 transition-all" title="Show Review">
                                    <i class="fas fa-eye"></i>
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
