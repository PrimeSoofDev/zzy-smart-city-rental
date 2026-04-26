<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Dispute Portal</h2>
        <a href="<?= APP_URL ?>/landlord/dashboard" class="text-sm font-bold text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Status and Info -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h3 class="font-bold text-gray-800 mb-4">Case Status</h3>
                <div class="flex items-center gap-3 p-3 rounded-xl bg-blue-50 text-blue-700 border border-blue-100">
                    <i class="fas fa-clock text-lg"></i>
                    <span class="font-bold uppercase text-xs"><?= $dispute['status'] ?></span>
                </div>
                <div class="mt-6 space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Request ID:</span>
                        <span class="font-bold text-gray-800">#<?= $dispute['request_id'] ?></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Disputed Amount:</span>
                        <span class="font-bold text-gray-800">₦<?= number_format($dispute['amount'], 2) ?></span>
                    </div>
                </div>
                <?php if ($dispute['status'] === 'resolved'): ?>
                    <div class="mt-6 p-4 bg-green-50 rounded-xl border border-green-100">
                        <p class="text-xs font-bold text-green-700 uppercase mb-2">Resolution Outcome</p>
                        <p class="text-sm text-green-800 mb-2"><?= htmlspecialchars($dispute['resolution_notes']) ?></p>
                        <p class="text-xs text-green-600">Type: <?= str_replace('_', ' ', $dispute['resolution_type']) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Evidence Management -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 bg-gray-50 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="font-bold text-gray-800">Your Evidence</h3>
                    <?php if ($dispute['status'] === 'open' || $dispute['status'] === 'resolving'): ?>
                        <button id="btnOpenUpload" class="px-3 py-1 bg-blue-600 text-white text-xs font-bold rounded-lg hover:bg-blue-700 transition-colors">
                            <i class="fas fa-plus mr-1"></i> Add Evidence
                        </button>
                    <?php endif; ?>
                </div>

                <div class="p-6 space-y-4" id="evidenceList">
                    <?php if (empty($evidence)): ?>
                        <div class="text-center py-12 text-gray-400">
                            <i class="fas fa-folder-open text-3xl mb-2 block"></i>
                            <p>No evidence uploaded yet. Please provide documents or photos to support your claim.</p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($evidence as $ev): ?>
                            <div class="flex items-center gap-4 p-3 rounded-xl border border-gray-100 bg-white shadow-sm">
                                <div class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center text-gray-500 font-bold">
                                    <?= strtoupper(substr($ev['file_type'], 0, 3)) ?>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-bold text-gray-800"><?= htmlspecialchars($ev['description']) ?></p>
                                    <p class="text-[10px] text-gray-400"><?= $ev['created_at'] ?> | Uploaded as <?= $ev['user_name'] ?></p>
                                </div>
                                <a href="<?= $ev['file_path'] ?>" target="_blank" class="text-blue-600 hover:text-blue-800 p-2">
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Upload Form (Hidden by default) -->
            <div id="uploadForm" class="hidden bg-white rounded-2xl shadow-sm border border-gray-100 p-6 animate-fade-in">
                <h3 class="font-bold text-gray-800 mb-4">Upload Evidence</h3>
                <form action="<?= APP_URL ?>/dispute/uploadEvidence" method="POST" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="dispute_id" value="<?= $dispute['id'] ?>">
                    <input type="hidden" name="request_id" value="<?= $requestId ?>">

                    <div class="flex gap-4">
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">File</label>
                            <input type="file" name="evidence" required class="w-full p-2 text-sm border border-gray-200 rounded-lg bg-gray-50">
                        </div>
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Description</label>
                            <input type="text" name="description" required placeholder="e.g. Photo of broken window" class="w-full p-3 text-sm border border-gray-200 rounded-lg bg-gray-50 outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" id="btnCancelUpload" class="px-4 py-2 text-sm font-bold text-gray-500 hover:text-gray-700">Cancel</button>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white text-sm font-bold rounded-lg hover:bg-blue-700 transition-colors">Upload Evidence</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const btnOpenUpload = document.getElementById('btnOpenUpload');
    const uploadForm = document.getElementById('uploadForm');
    const btnCancelUpload = document.getElementById('btnCancelUpload');

    if (btnOpenUpload) {
        btnOpenUpload.addEventListener('click', () => uploadForm.classList.toggle('hidden'));
    }
    if (btnCancelUpload) {
        btnCancelUpload.addEventListener('click', () => uploadForm.classList.add('hidden'));
    }
</script>