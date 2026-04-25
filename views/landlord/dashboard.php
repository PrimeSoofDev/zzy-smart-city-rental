<div class="flex justify-between items-center mb-8">
    <h1 class="text-3xl font-bold">My Properties</h1>
    <a href="<?= APP_URL ?>/landlord/add-property" class="bg-blue-600 text-white px-6 py-2 rounded-xl font-bold hover:bg-blue-700 transition">+ Add Property</a>
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
