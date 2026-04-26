<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Dispute Mediation</h2>
            <p class="text-sm text-gray-500">Review evidence and determine fund distribution.</p>
        </div>
        <a href="<?= APP_URL ?>/staff/disputes" class="text-sm font-bold text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i> Back to List
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Evidence Review Area -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800">Evidence Log</h3>
                    <span class="text-xs text-gray-500"><?= count($evidence) ?> uploads</span>
                </div>
                <div class="p-6 space-y-4 max-h-[600px] overflow-y-auto">
                    <?php if (empty($evidence)): ?>
                        <div class="text-center py-12 text-gray-400">
                            <i class="fas fa-folder-open text-3xl mb-2 block"></i>
                            <p>No evidence provided by either party.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($evidence as $ev): ?>
                            <div class="flex gap-4 p-4 rounded-xl border border-gray-100 bg-white shadow-sm hover:border-blue-200 transition-colors">
                                <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 font-bold flex-shrink-0">
                                    <?= strtoupper(substr($ev['file_type'], 0, 3)) ?>
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start mb-1">
                                        <span class="text-xs font-bold text-blue-600"><?= htmlspecialchars($ev['user_name']) ?></span>
                                        <span class="text-[10px] text-gray-400"><?= $ev['created_at'] ?></span>
                                    </div>
                                    <p class="text-sm text-gray-700 mb-2"><?= htmlspecialchars($ev['description']) ?></p>
                                    <a href="<?= $ev['file_path'] ?>" target="_blank" class="text-xs font-bold text-gray-500 hover:text-blue-600 flex items-center gap-2">
                                        <i class="fas fa-eye"></i> View Evidence
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Resolution Panel -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-6">
                <h3 class="font-bold text-gray-800 mb-4">Resolution Decision</h3>

                <form action="<?= APP_URL ?>/staff/resolveDispute" method="POST" class="space-y-6">
                    <input type="hidden" name="dispute_id" value="<?= $dispute['id'] ?>">

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Outcome</label>
                        <select name="resolution_type" required class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-blue-500 outline-none">
                            <option value="full_release">Full Release to Landlord</option>
                            <option value="full_refund">Full Refund to Tenant</option>
                            <option value="partial_split">Partial Split</option>
                        </select>
                    </div>

                    <div id="splitFields" class="hidden space-y-4 p-4 bg-blue-50 rounded-xl border border-blue-100">
                        <div class="flex justify-between items-center">
                            <label class="text-xs font-bold text-blue-700">Landlord Amount (₦)</label>
                            <input type="number" name="amount_landlord" step="0.01" placeholder="0.00" class="w-32 p-2 text-sm rounded-lg border-blue-200 outline-none">
                        </div>
                        <div class="flex justify-between items-center">
                            <label class="text-xs font-bold text-blue-700">Tenant Amount (₦)</label>
                            <input type="number" name="amount_tenant" step="0.01" placeholder="0.00" class="w-32 p-2 text-sm rounded-lg border-blue-200 outline-none">
                        </div>
                        <p class="text-[10px] text-blue-500 text-center italic">Total must equal ₦<?= number_format($dispute['amount'], 2) ?></p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Resolution Notes</label>
                        <textarea name="notes" rows="4" placeholder="Explain the reasoning for this decision..." class="w-full p-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:ring-blue-500 outline-none"></textarea>
                    </div>

                    <button type="submit" class="w-full py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-colors shadow-lg">
                        Execute Resolution
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const resolutionSelect = document.querySelector('select[name="resolution_type"]');
    const splitFields = document.getElementById('splitFields');

    resolutionSelect.addEventListener('change', () => {
        splitFields.classList.toggle('hidden', resolutionSelect.value !== 'partial_split');
    });
</script>