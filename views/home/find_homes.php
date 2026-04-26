<?php 
$title = 'Find Homes';
include '../views/layouts/guest_layout_start.php'; 
?>

    <section class="py-20 bg-slate-50 min-h-screen">
        <div class="max-w-7xl mx-auto px-6">
            <div class="mb-12">
                <h1 class="text-4xl font-black text-slate-900 mb-4">Discover Your Next Home</h1>
                <p class="text-slate-500">Browse our AI-curated selection of premium properties across Nigeria.</p>
            </div>

            <!-- Filters -->
            <form action="<?= APP_URL ?>/find-homes" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-12">
                <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100">
                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Location</label>
                    <input type="text" name="location" value="<?= htmlspecialchars($filters['location'] ?? 'Port Harcourt') ?>" placeholder="City or State" class="w-full bg-transparent border-none outline-none font-bold text-slate-700">
                </div>
                <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100">
                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Property Type</label>
                    <select name="type" class="w-full bg-transparent border-none outline-none font-bold text-slate-700 cursor-pointer">
                        <option value="All Types">All Types</option>
                        <option value="Apartment" <?= (($filters['type'] ?? '') === 'Apartment') ? 'selected' : '' ?>>Apartment</option>
                        <option value="House" <?= (($filters['type'] ?? '') === 'House') ? 'selected' : '' ?>>House</option>
                        <option value="Commercial" <?= (($filters['type'] ?? '') === 'Commercial') ? 'selected' : '' ?>>Commercial</option>
                        <option value="Land" <?= (($filters['type'] ?? '') === 'Land') ? 'selected' : '' ?>>Land</option>
                    </select>
                </div>
                <div class="bg-white p-4 rounded-2xl shadow-sm border border-slate-100">
                    <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Price Range</label>
                    <select name="price" class="w-full bg-transparent border-none outline-none font-bold text-slate-700 cursor-pointer">
                        <option value="Any Price">Any Price</option>
                        <option value="₦500k - ₦2M" <?= (($filters['price'] ?? '') === '₦500k - ₦2M') ? 'selected' : '' ?>>₦500k - ₦2M</option>
                        <option value="₦2M - ₦5M" <?= (($filters['price'] ?? '') === '₦2M - ₦5M') ? 'selected' : '' ?>>₦2M - ₦5M</option>
                        <option value="₦5M+" <?= (($filters['price'] ?? '') === '₦5M+') ? 'selected' : '' ?>>₦5M+</option>
                    </select>
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl transition-all shadow-lg shadow-blue-500/20 active:scale-95">
                    Apply Filters
                </button>
            </form>

            <!-- Property Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($properties as $property): ?>
                    <?php 
                        $firstImage = !empty($property['primary_image']) ? APP_URL . '/' . $property['primary_image'] : 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?auto=format&fit=crop&q=80&w=800';
                    ?>
                    <div class="group bg-white rounded-[2.5rem] border border-slate-100 overflow-hidden transition-all hover:-translate-y-2 hover:shadow-2xl hover:shadow-blue-900/5">
                        <div class="h-64 overflow-hidden relative">
                            <img src="<?= $firstImage ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                            <div class="absolute top-6 left-6 px-4 py-2 bg-white/90 backdrop-blur-md rounded-2xl text-[10px] font-black text-slate-900 uppercase tracking-widest">
                                <?= htmlspecialchars($property['property_type'] ?? 'House') ?>
                            </div>
                            <div class="absolute bottom-6 right-6 px-4 py-2 bg-blue-600 text-white rounded-2xl text-sm font-black">
                                ₦<?= number_format($property['price']) ?>
                            </div>
                        </div>
                        <div class="p-8">
                            <h4 class="text-xl font-bold text-slate-900 mb-2"><?= htmlspecialchars($property['title']) ?></h4>
                            <p class="text-sm text-slate-500 mb-6 flex items-center gap-2">
                                <i class="fas fa-map-marker-alt text-blue-500"></i>
                                <?= htmlspecialchars($property['address']) ?>
                            </p>
                            <div class="flex items-center gap-6 pt-6 border-t border-slate-100">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-bed text-blue-600"></i>
                                    <span class="text-sm font-bold text-slate-600"><?= $property['rooms'] ?> Bed</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-bath text-blue-600"></i>
                                    <span class="text-sm font-bold text-slate-600"><?= $property['bathrooms'] ?> Bath</span>
                                </div>
                            </div>
                            <div class="mt-8">
                                <a href="<?= APP_URL ?>/auth/login" class="block w-full py-4 bg-slate-50 hover:bg-slate-100 text-slate-900 text-center font-bold rounded-2xl transition-colors">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

<?php include '../views/layouts/guest_layout_end.php'; ?>
