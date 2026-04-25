<?php
// views/tenant/property_details.php
$p = $property;
?>

<div class="max-w-6xl mx-auto pb-12">
    <!-- Breadcrumb & Back -->
    <div class="mb-6 flex items-center justify-between">
        <a href="<?= APP_URL ?>/tenant/dashboard" class="flex items-center gap-2 text-gray-500 hover:text-blue-600 transition-colors font-medium">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Listings</span>
        </a>
        <div class="flex items-center gap-2 text-sm text-gray-400">
            <span>Marketplace</span>
            <i class="fas fa-chevron-right text-[10px]"></i>
            <span class="text-gray-600 font-semibold"><?= htmlspecialchars($p['property_type']) ?></span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Image Gallery Carousel -->
            <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden">
                <?php if (!empty($images)): ?>
                    <div class="swiper propertySwiper">
                        <div class="swiper-wrapper">
                            <?php foreach($images as $img): ?>
                                <div class="swiper-slide">
                                    <img src="<?= APP_URL ?>/<?= $img ?>" class="w-full h-[500px] object-cover">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="swiper-button-next !text-white !bg-black/20 backdrop-blur-md !w-12 !h-12 !rounded-full after:!text-xl"></div>
                        <div class="swiper-button-prev !text-white !bg-black/20 backdrop-blur-md !w-12 !h-12 !rounded-full after:!text-xl"></div>
                        <div class="swiper-pagination"></div>
                    </div>
                    <!-- Thumbnails -->
                    <div thumbsSlider="" class="swiper thumbSwiper p-4 bg-gray-50/50">
                        <div class="swiper-wrapper">
                            <?php foreach($images as $img): ?>
                                <div class="swiper-slide !w-24 !h-24">
                                    <img src="<?= APP_URL ?>/<?= $img ?>" class="w-full h-full rounded-xl object-cover cursor-pointer border-2 border-transparent">
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="w-full h-[500px] bg-gray-100 flex flex-col items-center justify-center text-gray-400 gap-3">
                        <i class="fas fa-image text-6xl"></i>
                        <p class="font-medium text-lg">No photos available</p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Property Title & Description -->
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-6 pb-6 border-b border-gray-50">
                    <div>
                        <div class="flex items-center gap-3 mb-2">
                            <span class="bg-blue-50 text-blue-600 text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-full border border-blue-100">
                                Verified Listing
                            </span>
                            <span class="bg-green-50 text-green-600 text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-full border border-green-100">
                                Available Now
                            </span>
                        </div>
                        <h1 class="text-3xl font-extrabold text-gray-900 leading-tight"><?= htmlspecialchars($p['title']) ?></h1>
                        <p class="text-gray-500 flex items-center gap-2 mt-2">
                            <i class="fas fa-map-marker-alt text-red-400"></i>
                            <?= htmlspecialchars($p['address']) ?>
                        </p>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-400 text-sm font-semibold uppercase tracking-widest">Monthly Rent</p>
                        <p class="text-4xl font-black text-blue-600 tracking-tight">₦<?= number_format($p['price'], 2) ?></p>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
                    <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 flex flex-col items-center text-center">
                        <i class="fas fa-bed text-blue-500 text-xl mb-2"></i>
                        <span class="text-xs text-gray-500 font-bold uppercase">Bedrooms</span>
                        <span class="text-lg font-black text-gray-800"><?= $p['rooms'] ?></span>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 flex flex-col items-center text-center">
                        <i class="fas fa-bath text-blue-500 text-xl mb-2"></i>
                        <span class="text-xs text-gray-500 font-bold uppercase">Bathrooms</span>
                        <span class="text-lg font-black text-gray-800"><?= $p['bathrooms'] ?></span>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 flex flex-col items-center text-center">
                        <i class="fas fa-home text-blue-500 text-xl mb-2"></i>
                        <span class="text-xs text-gray-500 font-bold uppercase">Type</span>
                        <span class="text-lg font-black text-gray-800"><?= ucfirst(htmlspecialchars($p['property_type'])) ?></span>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 flex flex-col items-center text-center">
                        <i class="fas fa-vector-square text-blue-500 text-xl mb-2"></i>
                        <span class="text-xs text-gray-500 font-bold uppercase">Status</span>
                        <span class="text-lg font-black text-gray-800">Approved</span>
                    </div>
                </div>

                <div class="space-y-4">
                    <h2 class="text-xl font-bold text-gray-800 flex items-center gap-3">
                        <span class="w-1.5 h-6 bg-blue-600 rounded-full"></span>
                        Description
                    </h2>
                    <p class="text-gray-600 leading-relaxed whitespace-pre-line">
                        <?= htmlspecialchars($p['description']) ?>
                    </p>
                </div>
            </div>

            <!-- Features / Amenities (Static placeholders for premium feel) -->
            <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-3 mb-6">
                    <span class="w-1.5 h-6 bg-blue-600 rounded-full"></span>
                    Amenities & Features
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="flex items-center gap-3 text-gray-600">
                        <div class="w-8 h-8 rounded-lg bg-green-50 text-green-500 flex items-center justify-center">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <span>Security Gate & 24/7 Surveillance</span>
                    </div>
                    <div class="flex items-center gap-3 text-gray-600">
                        <div class="w-8 h-8 rounded-lg bg-green-50 text-green-500 flex items-center justify-center">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <span>Central Air Conditioning</span>
                    </div>
                    <div class="flex items-center gap-3 text-gray-600">
                        <div class="w-8 h-8 rounded-lg bg-green-50 text-green-500 flex items-center justify-center">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <span>Dedicated Parking Space</span>
                    </div>
                    <div class="flex items-center gap-3 text-gray-600">
                        <div class="w-8 h-8 rounded-lg bg-green-50 text-green-500 flex items-center justify-center">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <span>High-Speed Internet Ready</span>
                    </div>
                </div>
            </div>
            <!-- Suggested Houses -->
            <?php if (!empty($suggested)): ?>
            <div class="space-y-6 pb-12">
                <h2 class="text-xl font-bold text-gray-800 flex items-center gap-3">
                    <span class="w-1.5 h-6 bg-blue-600 rounded-full"></span>
                    Suggested Houses Near You
                </h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <?php foreach($suggested as $sp): ?>
                        <a href="<?= APP_URL ?>/tenant/property?id=<?= $sp['id'] ?>" class="group bg-white rounded-3xl overflow-hidden border border-gray-100 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                            <div class="relative h-48">
                                <?php
                                    $db = Database::getInstance()->getConnection();
                                    $sImg = $db->prepare("SELECT image_url FROM property_images WHERE property_id = ? LIMIT 1");
                                    $sImg->execute([$sp['id']]);
                                    $thumb = $sImg->fetchColumn();
                                ?>
                                <?php if ($thumb): ?>
                                    <img src="<?= APP_URL ?>/<?= $thumb ?>" class="w-full h-full object-cover">
                                <?php else: ?>
                                    <div class="w-full h-full bg-gray-100 flex items-center justify-center text-gray-400">
                                        <i class="fas fa-image text-3xl"></i>
                                    </div>
                                <?php endif; ?>
                                <div class="absolute top-4 right-4 bg-white/95 backdrop-blur px-3 py-1.5 rounded-xl shadow-lg border border-white">
                                    <p class="text-sm font-black text-blue-600">₦<?= number_format($sp['price'], 0) ?></p>
                                </div>
                                <div class="absolute bottom-0 left-0 right-0 h-2/3 bg-gradient-to-t from-black/60 to-transparent"></div>
                                <div class="absolute bottom-4 left-4">
                                    <p class="text-white font-bold text-lg drop-shadow-md"><?= htmlspecialchars($sp['title']) ?></p>
                                </div>
                            </div>
                            <div class="p-5">
                                <div class="flex items-center gap-2 text-xs text-gray-500 mb-4">
                                    <i class="fas fa-map-marker-alt text-red-400"></i>
                                    <span class="truncate"><?= htmlspecialchars($sp['address']) ?></span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex gap-4">
                                        <div class="flex items-center gap-1.5 text-xs font-bold text-gray-700">
                                            <i class="fas fa-bed text-blue-500"></i>
                                            <?= $sp['rooms'] ?>
                                        </div>
                                        <div class="flex items-center gap-1.5 text-xs font-bold text-gray-700">
                                            <i class="fas fa-bath text-blue-500"></i>
                                            <?= $sp['bathrooms'] ?>
                                        </div>
                                    </div>
                                    <span class="text-[10px] font-black uppercase tracking-widest text-blue-600 bg-blue-50 px-2.5 py-1 rounded-lg border border-blue-100">
                                        View Details
                                    </span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Sidebar Actions -->
        <div class="space-y-6">
            <!-- Payment Card -->
            <div class="bg-slate-900 rounded-3xl p-8 shadow-2xl shadow-blue-200 text-white sticky top-24">
                <h3 class="text-xl font-bold mb-6 flex items-center gap-2">
                    <i class="fas fa-credit-card text-blue-400"></i>
                    Secure Checkout
                </h3>
                
                <div class="space-y-4 mb-8">
                    <div class="flex justify-between text-sm text-slate-400 font-medium">
                        <span>First Month Rent</span>
                        <span class="text-white">₦<?= number_format($p['price'], 2) ?></span>
                    </div>
                    <div class="flex justify-between text-sm text-slate-400 font-medium">
                        <span>Platform Fee (20%)</span>
                        <span class="text-white">₦<?= number_format($p['price'] * 0.20, 2) ?></span>
                    </div>
                    <div class="flex justify-between text-sm text-slate-400 font-medium">
                        <span>Legal Verification (10%)</span>
                        <span class="text-white">₦<?= number_format($p['price'] * 0.10, 2) ?></span>
                    </div>
                    <div class="pt-4 border-t border-slate-800 flex justify-between items-end">
                        <span class="text-lg font-bold">Total Due</span>
                        <span class="text-3xl font-black text-blue-400 tracking-tight">₦<?= number_format($p['price'] * 1.30, 2) ?></span>
                    </div>
                </div>

                <form action="<?= APP_URL ?>/tenant/pay" method="POST">
                    <input type="hidden" name="property_id" value="<?= $p['id'] ?>">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-black py-4 rounded-2xl transition-all active:scale-95 shadow-xl shadow-blue-900/40 flex items-center justify-center gap-3">
                        <span>Rent & Pay Now</span>
                        <i class="fas fa-arrow-right text-xs"></i>
                    </button>
                </form>

                <div class="mt-6 space-y-4">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-shield-check text-blue-400 mt-1"></i>
                        <p class="text-[10px] text-slate-400 leading-relaxed font-medium">
                            Your payment will be held in our <span class="text-blue-300 font-bold">Escrow System</span> until you sign the legal agreement and take possession of the keys.
                        </p>
                    </div>
                    <div class="flex items-start gap-3">
                        <i class="fas fa-file-contract text-blue-400 mt-1"></i>
                        <p class="text-[10px] text-slate-400 leading-relaxed font-medium">
                            After payment, a legal representative will be assigned to draft your rental agreement within 24 hours.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Swiper CSS & JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<style>
    .thumbSwiper .swiper-slide-thumb-active img {
        border-color: #2563eb;
        transform: scale(0.95);
    }
    .propertySwiper {
        --swiper-theme-color: #2563eb;
        --swiper-navigation-size: 20px;
    }
    .swiper-pagination-bullet-active {
        background: #2563eb !important;
    }
</style>

<script>
    var swiperThumbs = new Swiper(".thumbSwiper", {
        spaceBetween: 12,
        slidesPerView: "auto",
        freeMode: true,
        watchSlidesProgress: true,
    });
    var swiperMain = new Swiper(".propertySwiper", {
        spaceBetween: 10,
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        thumbs: {
            swiper: swiperThumbs,
        },
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
    });
</script>
