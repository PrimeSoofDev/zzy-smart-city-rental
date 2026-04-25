<?php
// views/lawyer/view-agreement.php
$ag = $agreement;
$badge = match($ag['status']) {
    'draft'   => ['bg-blue-100 text-blue-700 border-blue-200',   'fa-file-alt',       'Draft'],
    'signed'  => ['bg-green-100 text-green-700 border-green-200', 'fa-file-signature', 'Signed'],
    'expired' => ['bg-red-100 text-red-700 border-red-200',       'fa-ban',            'Expired'],
    default   => ['bg-gray-100 text-gray-600 border-gray-200',    'fa-file',           ucfirst($ag['status'])],
};
?>

<!-- Breadcrumb -->
<div class="mb-6 flex items-center gap-2 text-sm text-gray-400">
    <a href="<?= APP_URL ?>/lawyer/dashboard" class="hover:text-teal-700 transition">Dashboard</a>
    <i class="fas fa-chevron-right text-xs"></i>
    <a href="<?= APP_URL ?>/lawyer/agreements" class="hover:text-teal-700 transition">Agreements</a>
    <i class="fas fa-chevron-right text-xs"></i>
    <span class="text-gray-700 font-medium">Agreement #<?= $ag['id'] ?></span>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    <!-- LEFT: Document Viewer -->
    <div class="xl:col-span-2 space-y-5">

        <!-- Header Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-xl bg-teal-100 flex items-center justify-center">
                        <i class="fas <?= $badge[1] ?> text-teal-700 text-lg"></i>
                    </div>
                    <div>
                        <h1 class="text-lg font-bold text-gray-900">Rental Agreement #<?= $ag['id'] ?></h1>
                        <p class="text-xs text-gray-400">Request ID: <span class="font-mono font-bold">#<?= $ag['request_id'] ?></span></p>
                    </div>
                </div>
                <span class="inline-flex items-center gap-2 text-xs font-bold px-3 py-1.5 rounded-full border <?= $badge[0] ?>">
                    <i class="fas <?= $badge[1] ?> text-[10px]"></i>
                    <?= $badge[2] ?>
                </span>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                <div class="bg-teal-50 rounded-xl px-3 py-2.5">
                    <p class="text-[10px] text-teal-500 font-semibold uppercase">Property</p>
                    <p class="text-xs font-bold text-teal-800 truncate"><?= htmlspecialchars($ag['property_title']) ?></p>
                </div>
                <div class="bg-gray-50 rounded-xl px-3 py-2.5">
                    <p class="text-[10px] text-gray-400 font-semibold uppercase">Price/yr</p>
                    <p class="text-xs font-bold text-gray-700">₦<?= number_format($ag['price'], 0) ?></p>
                </div>
                <div class="bg-gray-50 rounded-xl px-3 py-2.5">
                    <p class="text-[10px] text-gray-400 font-semibold uppercase">Tenant</p>
                    <p class="text-xs font-bold text-gray-700 truncate"><?= htmlspecialchars($ag['tenant_name']) ?></p>
                </div>
                <div class="bg-gray-50 rounded-xl px-3 py-2.5">
                    <p class="text-[10px] text-gray-400 font-semibold uppercase">Landlord</p>
                    <p class="text-xs font-bold text-gray-700 truncate"><?= htmlspecialchars($ag['landlord_name']) ?></p>
                </div>
            </div>
        </div>

        <!-- Document Content -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-scroll text-teal-600"></i> Agreement Document
                </h2>
                <?php if (!empty($ag['document_path'])): ?>
                <a href="<?= APP_URL ?>/<?= htmlspecialchars($ag['document_path']) ?>" target="_blank"
                   class="inline-flex items-center gap-2 text-xs bg-gray-100 hover:bg-teal-100 hover:text-teal-700 text-gray-600 px-3 py-2 rounded-lg transition font-semibold">
                    <i class="fas fa-download"></i> Download
                </a>
                <?php endif; ?>
            </div>

            <?php if (!empty($docContent)): ?>
            <!-- Styled as legal document -->
            <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                <div class="max-w-none">
                    <!-- Header decoration -->
                    <div class="text-center mb-6 pb-4 border-b-2 border-teal-200">
                        <div class="w-12 h-12 rounded-full bg-teal-700 flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-gavel text-white text-lg"></i>
                        </div>
                        <h3 class="text-base font-bold text-gray-900 uppercase tracking-wide">ZZY Smart City Rental</h3>
                        <p class="text-xs text-gray-400">Official Tenancy Agreement</p>
                    </div>
                    <pre class="text-sm text-gray-700 whitespace-pre-wrap font-mono leading-relaxed"><?= htmlspecialchars($docContent) ?></pre>

                    <!-- Signature Section -->
                    <div class="mt-8 pt-6 border-t border-gray-200">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            <div class="text-center">
                                <div class="h-10 border-b-2 border-gray-400 mb-2 mx-4
                                    <?= $ag['status'] === 'signed' ? 'border-teal-500' : '' ?>">
                                    <?php if ($ag['status'] === 'signed'): ?>
                                    <p class="text-teal-700 font-bold font-mono text-sm italic">✓ Signed</p>
                                    <?php endif; ?>
                                </div>
                                <p class="text-xs text-gray-500 font-semibold">Tenant Signature</p>
                                <p class="text-xs text-gray-400"><?= htmlspecialchars($ag['tenant_name']) ?></p>
                            </div>
                            <div class="text-center">
                                <div class="h-10 border-b-2 border-gray-400 mb-2 mx-4
                                    <?= $ag['status'] === 'signed' ? 'border-teal-500' : '' ?>">
                                    <?php if ($ag['status'] === 'signed'): ?>
                                    <p class="text-teal-700 font-bold font-mono text-sm italic">✓ Signed</p>
                                    <?php endif; ?>
                                </div>
                                <p class="text-xs text-gray-500 font-semibold">Landlord Signature</p>
                                <p class="text-xs text-gray-400"><?= htmlspecialchars($ag['landlord_name']) ?></p>
                            </div>
                            <div class="text-center">
                                <div class="h-10 border-b-2 border-gray-400 mb-2 mx-4
                                    <?= $ag['status'] === 'signed' ? 'border-teal-500' : '' ?>">
                                    <?php if ($ag['status'] === 'signed'): ?>
                                    <p class="text-teal-700 font-bold font-mono text-sm italic">✓ Signed</p>
                                    <?php endif; ?>
                                </div>
                                <p class="text-xs text-gray-500 font-semibold">Legal Witness</p>
                                <p class="text-xs text-gray-400"><?= htmlspecialchars($ag['lawyer_name']) ?></p>
                            </div>
                        </div>

                        <?php if ($ag['status'] === 'signed' && $ag['signed_at']): ?>
                        <p class="text-center text-xs text-teal-700 font-semibold mt-4">
                            <i class="fas fa-check-circle mr-1"></i>
                            Legally signed on <?= date('F j, Y \a\t g:i a', strtotime($ag['signed_at'])) ?>
                        </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="bg-gray-50 rounded-xl p-10 text-center text-gray-400 border border-dashed border-gray-200">
                <i class="fas fa-file-alt text-3xl mb-2 text-gray-300"></i>
                <p class="text-sm">Document file not found on server.</p>
            </div>
            <?php endif; ?>
        </div>

    </div>

    <!-- RIGHT: Actions & Meta -->
    <div class="space-y-5">

        <!-- Sign Agreement -->
        <?php if ($ag['status'] === 'draft'): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-bold text-gray-800 mb-1 flex items-center gap-2">
                <i class="fas fa-pen-alt text-teal-600"></i> Finalize Agreement
            </h2>
            <p class="text-xs text-gray-400 mb-5">Once signed, the rental request will be marked as <strong>Completed</strong> and the agreement cannot be modified.</p>

            <div id="signConfirmBox" class="hidden mb-4 bg-amber-50 border border-amber-200 rounded-xl p-4">
                <p class="text-xs font-bold text-amber-700 flex items-center gap-2 mb-2">
                    <i class="fas fa-exclamation-triangle"></i> Are you sure?
                </p>
                <p class="text-xs text-amber-600">This action is irreversible. The agreement will be legally signed.</p>
            </div>

            <form method="POST" action="<?= APP_URL ?>/lawyer/sign-agreement" id="signForm">
                <input type="hidden" name="agreement_id" value="<?= $ag['id'] ?>">
                <button type="button" id="signBtn"
                        onclick="confirmSign()"
                        class="w-full bg-teal-700 hover:bg-teal-800 text-white font-bold py-3.5 rounded-xl text-sm transition-all flex items-center justify-center gap-2 shadow">
                    <i class="fas fa-signature"></i> Sign & Finalize Agreement
                </button>
            </form>
        </div>

        <script>
            let clickCount = 0;
            function confirmSign() {
                clickCount++;
                const box = document.getElementById('signConfirmBox');
                const btn = document.getElementById('signBtn');
                if (clickCount === 1) {
                    box.classList.remove('hidden');
                    btn.innerHTML = '<i class="fas fa-check mr-2"></i> Confirm — Sign Agreement';
                    btn.classList.add('bg-green-600', 'hover:bg-green-700');
                    btn.classList.remove('bg-teal-700', 'hover:bg-teal-800');
                    btn.onclick = function() { document.getElementById('signForm').submit(); };
                }
            }
        </script>

        <?php elseif ($ag['status'] === 'signed'): ?>
        <div class="bg-green-50 border border-green-200 rounded-2xl p-6 text-center">
            <div class="w-14 h-14 rounded-full bg-green-100 flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-check-circle text-green-600 text-2xl"></i>
            </div>
            <p class="font-bold text-green-800 text-sm">Agreement Signed</p>
            <p class="text-xs text-green-600 mt-1">
                <?= date('M j, Y', strtotime($ag['signed_at'])) ?>
            </p>
        </div>
        <?php endif; ?>

        <!-- Edit Draft -->
        <?php if ($ag['status'] === 'draft'): ?>
        <a href="<?= APP_URL ?>/lawyer/draft-agreement?id=<?= $ag['request_id'] ?>"
           class="flex items-center justify-center gap-2 w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-xl text-sm transition">
            <i class="fas fa-edit"></i> Edit Draft
        </a>
        <?php endif; ?>

        <!-- Agreement Meta -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-info-circle text-teal-600"></i> Agreement Details
            </h2>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-400">Agreement ID</span>
                    <span class="font-bold font-mono text-gray-700">#<?= $ag['id'] ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Status</span>
                    <span class="font-bold text-gray-700"><?= ucfirst($ag['status']) ?></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Drafted by</span>
                    <span class="font-bold text-gray-700"><?= htmlspecialchars($ag['lawyer_name']) ?></span>
                </div>
                <?php if ($ag['signed_at']): ?>
                <div class="flex justify-between">
                    <span class="text-gray-400">Signed at</span>
                    <span class="font-bold text-gray-700"><?= date('M j, Y', strtotime($ag['signed_at'])) ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Back -->
        <a href="<?= APP_URL ?>/lawyer/agreements"
           class="flex items-center justify-center gap-2 w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 rounded-xl text-sm transition">
            <i class="fas fa-arrow-left"></i> Back to Agreements
        </a>

    </div>
</div>
