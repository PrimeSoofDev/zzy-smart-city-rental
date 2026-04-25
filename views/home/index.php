<?php include '../views/layouts/guest_layout_start.php'; ?>

    <!-- HERO SECTION -->
    <section class="relative min-h-[90vh] flex items-center overflow-hidden bg-slate-50">
        <!-- Animated Blobs -->
        <div class="blob w-96 h-96 bg-blue-100 top-20 -left-20 animate-float"></div>
        <div class="blob w-[500px] h-[500px] bg-purple-100 bottom-0 -right-20 animate-float" style="animation-delay: -3s"></div>

        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="z-10 py-20">
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-blue-50 border border-blue-100 rounded-full text-blue-600 text-xs font-bold uppercase tracking-widest mb-6">
                    <i class="fas fa-sparkles"></i> AI-Powered Real Estate
                </div>
                <h1 class="text-6xl lg:text-8xl font-black text-slate-900 leading-[0.9] tracking-tighter mb-8">
                    <?= $content['hero']['title'] ?? 'Discover your <br> <span class="text-gradient">Future Home</span> <br> using Intelligence.' ?>
                </h1>
                <p class="text-lg text-slate-600 mb-10 max-w-lg leading-relaxed">
                    <?= $content['hero']['subtitle'] ?? 'ZZY Smart Rental uses advanced AI matching to find the perfect living space based on your lifestyle, budget, and location preferences.' ?>
                </p>
                
                <!-- Smart Search Bar -->
                <div class="p-2 bg-white rounded-[2rem] shadow-2xl shadow-blue-900/5 flex items-center max-w-xl border border-slate-100">
                    <div class="flex-1 flex items-center gap-3 px-6">
                        <i class="fas fa-search text-slate-300"></i>
                        <input type="text" placeholder="Where do you want to live?" class="bg-transparent border-none outline-none text-slate-900 w-full font-medium">
                    </div>
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-[1.5rem] font-bold transition-all shadow-xl shadow-blue-600/30">
                        AI Search
                    </button>
                </div>

                <div class="mt-12 flex items-center gap-8">
                    <div>
                        <p class="text-3xl font-black text-slate-900">10k+</p>
                        <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">Verified Houses</p>
                    </div>
                    <div class="w-px h-10 bg-slate-200"></div>
                    <div>
                        <p class="text-3xl font-black text-slate-900">99%</p>
                        <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">AI Accuracy</p>
                    </div>
                    <div class="w-px h-10 bg-slate-200"></div>
                    <div>
                        <p class="text-3xl font-black text-slate-900">₦0</p>
                        <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">Listing Fee</p>
                    </div>
                </div>
            </div>

            <div class="relative lg:block hidden">
                <div class="relative rounded-[3rem] overflow-hidden shadow-2xl border border-slate-100 group">
                    <img src="<?= APP_URL ?>/landing_hero_modern_house_1777129698447.png" alt="Modern House" class="w-full h-[600px] object-cover transition-transform duration-700 group-hover:scale-105">
                    <div class="absolute inset-0 bg-gradient-to-t from-white/20 via-transparent to-transparent"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- SMART PROPERTY FEED -->
    <section class="py-32 px-6 bg-white">
        <div class="max-w-7xl mx-auto">
            <div class="flex items-end justify-between mb-16">
                <div>
                    <h2 class="text-sm font-bold text-blue-600 uppercase tracking-widest mb-4">Live Smart</h2>
                    <h3 class="text-5xl font-black text-slate-900 tracking-tighter">AI-Curated Feed</h3>
                </div>
                <a href="<?= APP_URL ?>/find-homes" class="text-blue-600 font-bold hover:underline">Explore all properties <i class="fas fa-arrow-right ml-2"></i></a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($properties as $property): ?>
                    <?php 
                        $images = json_decode($property['images'], true) ?: [];
                        $firstImage = !empty($images) ? APP_URL . '/' . $images[0] : 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?auto=format&fit=crop&q=80&w=800';
                    ?>
                    <div class="group relative bg-slate-50 rounded-[2.5rem] border border-slate-100 overflow-hidden transition-all hover:-translate-y-2 hover:shadow-2xl hover:shadow-blue-900/5">
                        <div class="h-72 overflow-hidden relative">
                            <img src="<?= $firstImage ?>" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                            <div class="absolute top-6 left-6 px-4 py-2 bg-white/90 backdrop-blur-md rounded-2xl text-[10px] font-black text-slate-900 uppercase tracking-widest border border-white/20">
                                <?= $property['type'] ?>
                            </div>
                            <div class="absolute bottom-6 right-6 px-4 py-2 bg-blue-600 text-white rounded-2xl text-sm font-black shadow-lg">
                                ₦<?= number_format($property['price']) ?>/yr
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
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

<?php include '../views/layouts/guest_layout_end.php'; ?>
