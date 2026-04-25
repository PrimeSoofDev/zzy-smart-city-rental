<?php
// views/lawyer/draft-agreement.php
$r = $request;
$existing = $existingAgreement;
?>

<!-- Breadcrumb -->
<div class="mb-6 flex items-center gap-2 text-sm text-gray-400">
    <a href="<?= APP_URL ?>/lawyer/dashboard" class="hover:text-teal-700 transition">Dashboard</a>
    <i class="fas fa-chevron-right text-xs"></i>
    <a href="<?= APP_URL ?>/lawyer/requests" class="hover:text-teal-700 transition">Paid Requests</a>
    <i class="fas fa-chevron-right text-xs"></i>
    <span class="text-gray-700 font-medium">Draft Agreement #<?= $r['id'] ?></span>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">

    <!-- LEFT: Agreement Form -->
    <div class="xl:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 rounded-xl bg-teal-100 flex items-center justify-center">
                    <i class="fas fa-pen-nib text-teal-700"></i>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-gray-900"><?= $existing ? 'Edit Agreement Draft' : 'Draft New Agreement' ?></h1>
                    <p class="text-xs text-gray-400">Request ID: <strong class="font-mono">#<?= $r['id'] ?></strong></p>
                </div>
            </div>

            <?php if ($existing): ?>
            <div class="mb-5 p-3 bg-blue-50 border border-blue-200 rounded-xl text-sm text-blue-700 flex items-center gap-2">
                <i class="fas fa-info-circle"></i>
                An existing draft exists. You are now editing it.
                <a href="<?= APP_URL ?>/lawyer/view-agreement?id=<?= $existing['id'] ?>" class="ml-auto text-xs font-bold underline">View Current</a>
            </div>
            <?php endif; ?>

            <form method="POST" action="<?= APP_URL ?>/lawyer/save-agreement">
                <input type="hidden" name="request_id" value="<?= $r['id'] ?>">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-5">
                    <!-- Rent Amount -->
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Agreed Rent Amount (₦/year)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 font-bold">₦</span>
                            <input type="number" name="rent_amount" step="0.01"
                                   value="<?= htmlspecialchars($r['price']) ?>"
                                   class="w-full pl-8 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                                   required>
                        </div>
                    </div>

                    <!-- Duration -->
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Lease Duration</label>
                        <select name="duration"
                                class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 bg-white">
                            <option value="6 months">6 Months</option>
                            <option value="1 year" selected>1 Year</option>
                            <option value="2 years">2 Years</option>
                            <option value="3 years">3 Years</option>
                            <option value="5 years">5 Years</option>
                        </select>
                    </div>

                    <!-- Start Date -->
                    <div>
                        <label class="block text-xs font-bold text-gray-600 mb-1.5">Tenancy Start Date</label>
                        <input type="date" name="start_date"
                               value="<?= date('Y-m-d') ?>"
                               class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent"
                               required>
                    </div>
                </div>

                <!-- Terms & Conditions -->
                <div class="mb-5">
                    <label class="block text-xs font-bold text-gray-600 mb-1.5">Agreement Terms & Conditions</label>
                    <p class="text-xs text-gray-400 mb-2">Write the full terms of the tenancy agreement. This will be saved as the legal document.</p>
                    <textarea name="terms" rows="14"
                              class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm font-mono focus:outline-none focus:ring-2 focus:ring-teal-500 focus:border-transparent resize-y"
                              placeholder="1. The Tenant agrees to pay rent of ₦[amount] per annum...&#10;2. The tenancy commences on [date]...&#10;3. The Tenant shall not sublet the property without written consent...&#10;..." required><?= htmlspecialchars($existingAgreement ? '(Edit to re-draft terms)' : $defaultTerms ?? '') ?></textarea>
                </div>

                <!-- Template Buttons -->
                <div class="mb-5 flex flex-wrap gap-2">
                    <p class="w-full text-xs text-gray-400 mb-1">Quick-insert template clauses:</p>
                    <button type="button" onclick="insertClause('no_pets')"
                            class="text-xs bg-gray-100 hover:bg-teal-100 hover:text-teal-700 text-gray-600 px-3 py-1.5 rounded-lg transition font-medium">
                        <i class="fas fa-plus mr-1"></i>No Pets
                    </button>
                    <button type="button" onclick="insertClause('utilities')"
                            class="text-xs bg-gray-100 hover:bg-teal-100 hover:text-teal-700 text-gray-600 px-3 py-1.5 rounded-lg transition font-medium">
                        <i class="fas fa-plus mr-1"></i>Utilities Responsibility
                    </button>
                    <button type="button" onclick="insertClause('damage')"
                            class="text-xs bg-gray-100 hover:bg-teal-100 hover:text-teal-700 text-gray-600 px-3 py-1.5 rounded-lg transition font-medium">
                        <i class="fas fa-plus mr-1"></i>Property Damage
                    </button>
                    <button type="button" onclick="insertClause('notice')"
                            class="text-xs bg-gray-100 hover:bg-teal-100 hover:text-teal-700 text-gray-600 px-3 py-1.5 rounded-lg transition font-medium">
                        <i class="fas fa-plus mr-1"></i>Termination Notice
                    </button>
                </div>

                <button type="submit"
                        class="w-full bg-teal-700 hover:bg-teal-800 text-white font-bold py-3.5 rounded-xl text-sm transition-all flex items-center justify-center gap-2 shadow-md hover:shadow-lg">
                    <i class="fas fa-save"></i>
                    <?= $existing ? 'Update Draft Agreement' : 'Save Draft Agreement' ?>
                </button>
            </form>
        </div>
    </div>

    <!-- RIGHT: Request Info -->
    <div class="space-y-5">

        <!-- Property Info -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-home text-teal-600"></i> Property
            </h2>
            <p class="font-bold text-gray-900 text-sm mb-1"><?= htmlspecialchars($r['property_title']) ?></p>
            <p class="text-xs text-gray-500 mb-3 flex items-start gap-1.5">
                <i class="fas fa-map-marker-alt text-teal-400 mt-0.5"></i>
                <?= htmlspecialchars($r['address']) ?>
            </p>
            <div class="grid grid-cols-2 gap-2">
                <div class="bg-teal-50 rounded-xl px-3 py-2.5 text-center">
                    <p class="text-[10px] text-teal-500 font-semibold uppercase">Annual Rent</p>
                    <p class="text-sm font-bold text-teal-800">₦<?= number_format($r['price'], 0) ?></p>
                </div>
                <div class="bg-gray-50 rounded-xl px-3 py-2.5 text-center">
                    <p class="text-[10px] text-gray-400 font-semibold uppercase">Type</p>
                    <p class="text-sm font-bold text-gray-700"><?= ucfirst($r['property_type'] ?? 'N/A') ?></p>
                </div>
            </div>
        </div>

        <!-- Tenant Info -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-user text-teal-600"></i> Tenant
            </h2>
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-full bg-teal-100 flex items-center justify-center font-bold text-teal-700">
                    <?= strtoupper(substr($r['tenant_name'], 0, 1)) ?>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-900"><?= htmlspecialchars($r['tenant_name']) ?></p>
                    <p class="text-xs text-gray-400"><?= htmlspecialchars($r['tenant_email']) ?></p>
                </div>
            </div>
            <?php if (!empty($r['tenant_address'])): ?>
            <p class="text-xs text-gray-500">
                <i class="fas fa-map-marker-alt mr-1 text-gray-300"></i>
                <?= htmlspecialchars($r['tenant_address']) ?>
            </p>
            <?php endif; ?>
        </div>

        <!-- Landlord Info -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
            <h2 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-user-tie text-teal-600"></i> Landlord
            </h2>
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center font-bold text-slate-700">
                    <?= strtoupper(substr($r['landlord_name'], 0, 1)) ?>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-900"><?= htmlspecialchars($r['landlord_name']) ?></p>
                    <p class="text-xs text-gray-400"><?= htmlspecialchars($r['landlord_email']) ?></p>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
const clauses = {
    no_pets: "\n\nNo Pets Clause:\nThe Tenant shall not keep any pets, animals or birds in the property without prior written consent from the Landlord.",
    utilities: "\n\nUtilities:\nThe Tenant shall be responsible for all utility bills including electricity, water, and internet charges for the duration of the tenancy.",
    damage: "\n\nProperty Damage:\nThe Tenant shall be responsible for any damage caused to the property beyond normal wear and tear. Any damage must be reported to the Landlord immediately and repaired at the Tenant's expense.",
    notice: "\n\nTermination Notice:\nEither party may terminate this agreement by giving 30 (thirty) days written notice to the other party. Failure to provide notice shall result in liability for rent equivalent to the notice period."
};

function insertClause(type) {
    const textarea = document.querySelector('textarea[name="terms"]');
    textarea.value += clauses[type];
    textarea.focus();
    textarea.scrollTop = textarea.scrollHeight;
}
</script>
