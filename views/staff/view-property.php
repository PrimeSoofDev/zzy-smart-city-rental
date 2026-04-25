<?php
// views/staff/view-property.php
$p = $property;
$statusBadge = match($p['status']) {
    'pending_verification' => ['bg-yellow-100 text-yellow-700',  'Pending Verification'],
    'approved'             => ['bg-green-100 text-green-700',    'Approved'],
    'rejected'             => ['bg-red-100 text-red-700',        'Rejected'],
    default                => ['bg-gray-100 text-gray-600',      ucfirst($p['status'])],
};
?>

<!-- Breadcrumb -->
<div class="mb-6 flex items-center gap-2 text-sm text-gray-400">
    <a href="<?= APP_URL ?>/staff/dashboard" class="hover:text-violet-600 transition">Dashboard</a>
    <i class="fas fa-chevron-right text-xs"></i>
    <a href="<?= APP_URL ?>/staff/pending" class="hover:text-violet-600 transition">Pending</a>
    <i class="fas fa-chevron-right text-xs"></i>
    <span class="text-gray-700 font-medium truncate max-w-[200px]"><?= htmlspecialchars($p['title']) ?></span>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    <!-- LEFT: Property Details -->
    <div class="xl:col-span-2 space-y-5">

        <!-- Header Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3 mb-4">
                <div>
                    <span class="text-[11px] font-bold uppercase px-2.5 py-1 rounded-full <?= $statusBadge[0] ?> mr-2">
                        <?= $statusBadge[1] ?>
                    </span>
                    <span class="text-[11px] font-bold uppercase px-2.5 py-1 rounded-full bg-slate-100 text-slate-600">
                        <?= ucfirst($p['property_type']) ?>
                    </span>
                </div>
                <p class="text-xs text-gray-400">
                    <i class="fas fa-calendar-alt mr-1"></i>
                    Listed <?= date('M j, Y', strtotime($p['created_at'])) ?>
                </p>
            </div>
            <h1 class="text-xl font-bold text-gray-900 mb-1"><?= htmlspecialchars($p['title']) ?></h1>
            <p class="text-sm text-gray-500 flex items-center gap-1.5 mb-4">
                <i class="fas fa-map-marker-alt text-violet-400"></i>
                <?= htmlspecialchars($p['address']) ?>
            </p>

            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                <div class="bg-violet-50 rounded-xl px-4 py-3 text-center">
                    <p class="text-xs text-violet-500 font-semibold uppercase">Price</p>
                    <p class="text-lg font-bold text-violet-700">₦<?= number_format($p['price'], 0) ?></p>
                </div>
                <?php if (!empty($p['rooms'])): ?>
                <div class="bg-blue-50 rounded-xl px-4 py-3 text-center">
                    <p class="text-xs text-blue-500 font-semibold uppercase">Rooms</p>
                    <p class="text-lg font-bold text-blue-700"><?= $p['rooms'] ?></p>
                </div>
                <?php endif; ?>
                <?php if (!empty($p['bathrooms'])): ?>
                <div class="bg-green-50 rounded-xl px-4 py-3 text-center">
                    <p class="text-xs text-green-500 font-semibold uppercase">Bathrooms</p>
                    <p class="text-lg font-bold text-green-700"><?= $p['bathrooms'] ?></p>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Description -->
        <?php if (!empty($p['description'])): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                <i class="fas fa-align-left text-violet-400"></i> Description
            </h2>
            <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line"><?= htmlspecialchars($p['description']) ?></p>
        </div>
        <?php endif; ?>

        <!-- Images -->
        <?php if (!empty($images)): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-images text-violet-400"></i> Property Images (<?= count($images) ?>)
            </h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                <?php foreach($images as $img): ?>
                <a href="<?= APP_URL ?>/<?= htmlspecialchars($img['image_url']) ?>" target="_blank"
                   class="block rounded-xl overflow-hidden border border-gray-100 hover:border-violet-300 transition aspect-video bg-gray-100">
                    <img src="<?= APP_URL ?>/<?= htmlspecialchars($img['image_url']) ?>"
                         alt="Property Image"
                         class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php else: ?>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                <i class="fas fa-images text-violet-400"></i> Property Images
            </h2>
            <div class="bg-gray-50 rounded-xl p-8 text-center text-gray-400">
                <i class="fas fa-camera text-3xl mb-2 text-gray-300"></i>
                <p class="text-sm">No images uploaded for this property.</p>
            </div>
        </div>
        <?php endif; ?>

        <!-- Location -->
        <?php if (!empty($p['latitude']) && !empty($p['longitude'])): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                <i class="fas fa-map text-violet-400"></i> Location
            </h2>
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-gray-50 rounded-xl px-4 py-3">
                    <p class="text-xs text-gray-400 uppercase font-semibold">Latitude</p>
                    <p class="text-sm font-bold text-gray-700 mt-0.5"><?= $p['latitude'] ?></p>
                </div>
                <div class="bg-gray-50 rounded-xl px-4 py-3">
                    <p class="text-xs text-gray-400 uppercase font-semibold">Longitude</p>
                    <p class="text-sm font-bold text-gray-700 mt-0.5"><?= $p['longitude'] ?></p>
                </div>
            </div>
            <a href="https://www.google.com/maps?q=<?= $p['latitude'] ?>,<?= $p['longitude'] ?>" target="_blank"
               class="mt-3 inline-flex items-center gap-2 text-xs text-violet-600 hover:text-violet-800 font-semibold">
                <i class="fas fa-external-link-alt"></i> Open in Google Maps
            </a>
        </div>
        <?php endif; ?>

        <!-- Past Verifications -->
        <?php if (!empty($verificationHistory)): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-history text-violet-400"></i> Verification History
            </h2>
            <div class="space-y-3">
                <?php foreach($verificationHistory as $vh): ?>
                <div class="flex items-start gap-3 p-3 rounded-xl bg-gray-50">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 <?= $vh['result'] === 'approved' ? 'bg-green-100' : 'bg-red-100' ?>">
                        <i class="fas <?= $vh['result'] === 'approved' ? 'fa-check text-green-600' : 'fa-times text-red-600' ?> text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <span class="text-xs font-bold <?= $vh['result'] === 'approved' ? 'text-green-700' : 'text-red-700' ?>">
                                <?= ucfirst($vh['result']) ?>
                            </span>
                            <span class="text-[10px] text-gray-400"><?= date('M j, Y g:ia', strtotime($vh['verified_at'])) ?></span>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">By <strong><?= htmlspecialchars($vh['verified_by']) ?></strong></p>
                        <?php if (!empty($vh['notes'])): ?>
                        <p class="text-xs text-gray-600 mt-1 italic">"<?= htmlspecialchars($vh['notes']) ?>"</p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>

    <!-- RIGHT: Landlord Info + Action -->
    <div class="space-y-5">

        <!-- Landlord Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-user text-violet-400"></i> Landlord Info
            </h2>
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 rounded-full bg-violet-100 flex items-center justify-center text-violet-700 font-bold text-lg">
                    <?= strtoupper(substr($p['landlord_name'], 0, 1)) ?>
                </div>
                <div>
                    <p class="font-bold text-gray-900 text-sm"><?= htmlspecialchars($p['landlord_name']) ?></p>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full <?= $p['landlord_verified'] === 'approved' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' ?>">
                        <?= $p['landlord_verified'] === 'approved' ? 'KYC Verified' : 'KYC Pending' ?>
                    </span>
                </div>
            </div>
            <div class="space-y-2.5 text-sm">
                <?php if (!empty($p['landlord_email'])): ?>
                <div class="flex items-center gap-2 text-gray-600">
                    <i class="fas fa-envelope w-4 text-gray-400"></i>
                    <span class="truncate"><?= htmlspecialchars($p['landlord_email']) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($p['landlord_phone'])): ?>
                <div class="flex items-center gap-2 text-gray-600">
                    <i class="fas fa-phone w-4 text-gray-400"></i>
                    <span><?= htmlspecialchars($p['landlord_phone']) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($p['bvn'])): ?>
                <div class="flex items-center gap-2 text-gray-600">
                    <i class="fas fa-id-card w-4 text-gray-400"></i>
                    <span class="font-mono text-xs bg-gray-100 px-2 py-0.5 rounded"><?= htmlspecialchars($p['bvn']) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($p['landlord_address'])): ?>
                <div class="flex items-center gap-2 text-gray-600">
                    <i class="fas fa-map-marker-alt w-4 text-gray-400"></i>
                    <span class="text-xs truncate"><?= htmlspecialchars($p['landlord_address']) ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Verification Action -->
        <?php if ($p['status'] === 'pending_verification'): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-bold text-gray-800 mb-1 flex items-center gap-2">
                <i class="fas fa-clipboard-check text-violet-400"></i> Submit Verdict
            </h2>
            <p class="text-xs text-gray-400 mb-5">Review the property details above, then submit your decision.</p>

            <form method="POST" action="<?= APP_URL ?>/staff/submit-verification" id="verdictForm">
                <input type="hidden" name="property_id" value="<?= $p['id'] ?>">

                <label class="block text-xs font-semibold text-gray-600 mb-1.5">Notes / Remarks</label>
                <textarea name="notes" rows="4" placeholder="Add inspection notes, reason for rejection, or any remarks…"
                          class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400 focus:border-transparent resize-none mb-4"></textarea>

                <input type="hidden" name="result" id="verdictInput" value="">

                <div class="grid grid-cols-2 gap-3">
                    <button type="button" id="approveBtn"
                            onclick="setVerdict('approved')"
                            class="flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-xl transition-all text-sm">
                        <i class="fas fa-check"></i> Approve
                    </button>
                    <button type="button" id="rejectBtn"
                            onclick="setVerdict('rejected')"
                            class="flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white font-bold py-3 rounded-xl transition-all text-sm">
                        <i class="fas fa-times"></i> Reject
                    </button>
                </div>
            </form>
        </div>

        <!-- Confirm Modal -->
        <div id="confirmModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">
            <div class="bg-white rounded-2xl shadow-2xl p-7 w-full max-w-sm mx-4 animate-fade-in">
                <div id="modalIcon" class="w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4"></div>
                <h3 id="modalTitle" class="text-lg font-bold text-center text-gray-900 mb-1"></h3>
                <p id="modalBody" class="text-sm text-gray-500 text-center mb-6"></p>
                <div class="flex gap-3">
                    <button onclick="closeModal()" class="flex-1 py-2.5 rounded-xl border border-gray-200 text-sm font-semibold text-gray-600 hover:bg-gray-50 transition">Cancel</button>
                    <button id="modalConfirm" class="flex-1 py-2.5 rounded-xl text-white text-sm font-bold transition"></button>
                </div>
            </div>
        </div>

        <script>
            let pendingVerdict = '';

            function setVerdict(verdict) {
                pendingVerdict = verdict;
                const modal   = document.getElementById('confirmModal');
                const icon    = document.getElementById('modalIcon');
                const title   = document.getElementById('modalTitle');
                const body    = document.getElementById('modalBody');
                const confirm = document.getElementById('modalConfirm');

                if (verdict === 'approved') {
                    icon.className  = 'w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4 bg-green-100';
                    icon.innerHTML  = '<i class="fas fa-check text-green-600 text-2xl"></i>';
                    title.textContent = 'Approve this property?';
                    body.textContent  = 'This will mark the property as approved and make it visible to tenants.';
                    confirm.className = 'flex-1 py-2.5 rounded-xl text-white text-sm font-bold transition bg-green-600 hover:bg-green-700';
                    confirm.textContent = 'Yes, Approve';
                } else {
                    icon.className  = 'w-14 h-14 rounded-full flex items-center justify-center mx-auto mb-4 bg-red-100';
                    icon.innerHTML  = '<i class="fas fa-times text-red-600 text-2xl"></i>';
                    title.textContent = 'Reject this property?';
                    body.textContent  = 'This will mark the property as rejected. The landlord will need to resubmit.';
                    confirm.className = 'flex-1 py-2.5 rounded-xl text-white text-sm font-bold transition bg-red-600 hover:bg-red-700';
                    confirm.textContent = 'Yes, Reject';
                }

                confirm.onclick = submitVerdict;
                modal.classList.remove('hidden');
            }

            function submitVerdict() {
                document.getElementById('verdictInput').value = pendingVerdict;
                document.getElementById('verdictForm').submit();
            }

            function closeModal() {
                document.getElementById('confirmModal').classList.add('hidden');
            }

            // Close modal on backdrop click
            document.getElementById('confirmModal').addEventListener('click', function(e) {
                if (e.target === this) closeModal();
            });
        </script>

        <?php else: ?>
        <div class="bg-gray-50 rounded-2xl border border-gray-200 p-6 text-center">
            <i class="fas fa-lock text-gray-300 text-3xl mb-3"></i>
            <p class="text-sm font-semibold text-gray-500">This property has already been <strong><?= $p['status'] ?></strong>.</p>
            <p class="text-xs text-gray-400 mt-1">No further action needed.</p>
        </div>
        <?php endif; ?>

        <!-- Back Button -->
        <a href="<?= APP_URL ?>/staff/pending"
           class="flex items-center justify-center gap-2 w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 rounded-xl text-sm transition-all">
            <i class="fas fa-arrow-left"></i> Back to Queue
        </a>

    </div>
</div>
