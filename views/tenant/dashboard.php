<h1 class="text-3xl font-bold mb-8">Find Your Next Home</h1>
<div class="grid grid-cols-1 md:grid-cols-3 gap-8">
    <?php if(empty($properties)): ?>
        <div class="col-span-full text-center py-12 bg-white rounded-2xl shadow-sm border">
            <p class="text-gray-500">No approved properties available at the moment.</p>
        </div>
    <?php else: ?>
        <?php foreach($properties as $p): ?>
            <div class="bg-white rounded-2xl shadow-sm border overflow-hidden hover:shadow-md transition">
                <div class="p-6">
                    <h3 class="text-xl font-bold mb-2"><?= $p['title'] ?></h3>
                    <p class="text-gray-600 mb-4 line-clamp-3"><?= $p['description'] ?></p>
                    <div class="flex justify-between items-center mb-4">
                        <span class="text-2xl font-extrabold text-blue-600">$<?= number_format($p['price'], 2) ?></span>
                        <a href="<?= APP_URL ?>/tenant/request-rental?id=<?= $p['id'] ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition">Request</a>
                    </div>
                    <div class="flex gap-4 text-xs text-gray-500 font-medium">
                        <span>🛏️ <?= $p['rooms'] ?> Rooms</span>
                        <span>🚿 <?= $p['bathrooms'] ?> Baths</span>
                        <span>📍 <?= $p['address'] ?></span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
