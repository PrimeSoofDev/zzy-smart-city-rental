<div class="bg-yellow-100 p-2 text-xs text-yellow-800 text-center font-mono">
    DEBUG: User ID: <?= $_SESSION['user_id'] ?? 'N/A' ?> | Role: <?= $_SESSION['role'] ?? 'N/A' ?> (Landlord Dashboard)
</div>
<div class="flex justify-between items-center mb-8">
    <h1 class="text-3xl font-bold">My Properties</h1>
    <a href="<?= APP_URL ?>/landlord/add-property" class="bg-blue-600 text-white px-6 py-2 rounded-xl font-bold hover:bg-blue-700 transition">+ Add Property</a>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <?php foreach($properties as $p): ?>
        <div class="bg-white p-6 rounded-2xl shadow-sm border flex justify-between items-center">
            <div>
                <h3 class="text-lg font-bold"><?= $p['title'] ?></h3>
                <p class="text-sm text-gray-500"><?= $p['address'] ?></p>
                <span class="px-2 py-1 rounded-full text-xs font-bold <?= $p['status'] == 'approved' ? 'bg-green-100 text-green-600' : 'bg-yellow-100 text-yellow-600' ?>">
                    <?= strtoupper($p['status']) ?>
                </span>
            </div>
            <div class="text-right">
                <span class="text-lg font-bold">$<?= number_format($p['price'], 2) ?></span>
            </div>
        </div>
    <?php endforeach; ?>
</div>
