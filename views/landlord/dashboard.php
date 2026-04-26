<?php if (isset($bankSetupRequired) && $bankSetupRequired): ?>
    <div class="mb-8 bg-amber-50 border-l-4 border-amber-400 p-6 rounded-2xl shadow-sm animate-pulse">
        <div class="flex items-start gap-4">
            <div class="bg-amber-400 p-2 rounded-xl text-white">
                <i class="fas fa-university text-xl"></i>
            </div>
            <div>
                <h3 class="text-amber-800 font-black text-lg mb-1 uppercase tracking-tight">Bank Payout Required</h3>
                <p class="text-amber-700 text-sm font-medium leading-relaxed">
                    You haven't set up your payout destination yet. 
                    <div class="mt-4">
                        <a href="<?= APP_URL ?>/landlord/bank-details" class="inline-flex items-center gap-2 bg-amber-400 text-white px-6 py-2 rounded-xl font-black uppercase tracking-tight hover:bg-amber-500 transition-all shadow-md shadow-amber-200">
                            <i class="fas fa-plus-circle"></i>
                            Add Bank Details
                        </a>
                    </div>
                </p>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if(!empty($payoutItems)): ?>
    <div class="mb-12">
        <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
            <i class="fas fa-wallet text-blue-500"></i> Payouts & Escrow
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach($payoutItems as $item): ?>
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-md transition-all">
                    <div class="flex justify-between items-start mb-4">
                        <div class="bg-blue-50 text-blue-600 p-3 rounded-2xl">
                            <i class="fas fa-hand-holding-usd text-xl"></i>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full 
                            <?= $item['status'] == 'escrow_hold' ? 'bg-yellow-50 text-yellow-600 border border-yellow-100' : 'bg-green-50 text-green-600 border border-green-100' ?>">
                            <?= $item['status'] == 'escrow_hold' ? 'Awaiting move-in' : 'Released' ?>
                        </span>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-1"><?= htmlspecialchars($item['property_title']) ?></h3>
                    <p class="text-xs text-gray-500 mb-4 line-clamp-1">Tenant: <?= htmlspecialchars($item['tenant_name']) ?></p>
                    
                    <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Total Rent</p>
                            <p class="font-black text-gray-900">₦<?= number_format($item['amount'], 2) ?></p>
                        </div>
                        <?php if($item['status'] == 'escrow_hold'): ?>
                            <div class="text-right">
                                <span class="block text-[10px] font-black text-blue-600 uppercase">Held In Escrow</span>
                                <span class="text-[9px] text-gray-400">Secure & Verified</span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<div class="flex justify-between items-center mb-8">
    <h1 class="text-3xl font-bold">My Properties</h1>
    <div class="flex gap-4">
        <a href="<?= APP_URL ?>/landlord/bank-details" class="bg-slate-100 text-slate-700 px-6 py-2 rounded-xl font-bold hover:bg-slate-200 transition flex items-center gap-2">
            <i class="fas fa-university"></i>
            Add Bank Details
        </a>
        <a href="<?= APP_URL ?>/landlord/add-property" class="bg-blue-600 text-white px-6 py-2 rounded-xl font-bold hover:bg-blue-700 transition">+ Add Property</a>
    </div>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <?php foreach($properties as $p): ?>
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 mb-1"><?= htmlspecialchars($p['title']) ?></h3>
                        <p class="text-sm text-gray-500 flex items-center gap-1">
                            <i class="fas fa-map-marker-alt text-red-400 text-xs"></i>
                            <?= htmlspecialchars($p['address']) ?>
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Rent</p>
                        <p class="text-xl font-black text-blue-600 tracking-tight">₦<?= number_format($p['price'], 2) ?></p>
                    </div>
                </div>
                
                <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider <?= $p['status'] == 'approved' ? 'bg-green-50 text-green-600 border border-green-100' : 'bg-yellow-50 text-yellow-600 border border-yellow-100' ?>">
                        <i class="fas <?= $p['status'] == 'approved' ? 'fa-check-circle' : 'fa-clock' ?> mr-1"></i>
                        <?= $p['status'] ?>
                    </span>
                    <div class="flex gap-2">
                        <a href="<?= APP_URL ?>/landlord/edit-property?id=<?= $p['id'] ?>" class="flex items-center gap-2 bg-slate-900 text-white px-4 py-2 rounded-xl text-xs font-bold hover:bg-slate-800 transition shadow-lg shadow-slate-200">
                            <i class="fas fa-edit"></i>
                            <span>Edit Property</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>
