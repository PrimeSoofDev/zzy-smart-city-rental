<?php
// views/staff/pending.php
$typeLabels = [
    'apartment'  => 'Apartment',
    'house'      => 'House',
    'commercial' => 'Commercial',
    'land'       => 'Land',
];
?>

<!-- Header -->
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Pending Properties</h1>
        <p class="text-sm text-gray-500 mt-1"><?= count($properties) ?> propert<?= count($properties) === 1 ? 'y' : 'ies' ?> awaiting your review</p>
    </div>
</div>

<!-- Filters -->
<form method="GET" action="<?= APP_URL ?>/staff/pending"
      class="bg-white rounded-2xl shadow-sm border border-gray-100 p-4 mb-6 flex flex-col sm:flex-row gap-3">
    <div class="flex-1 relative">
        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
               placeholder="Search by title, address, or landlord…"
               class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400 focus:border-transparent">
    </div>
    <select name="type"
            class="px-4 py-2.5 rounded-xl border border-gray-200 text-sm focus:outline-none focus:ring-2 focus:ring-violet-400 bg-white">
        <option value="all" <?= $type === 'all' ? 'selected' : '' ?>>All Types</option>
        <?php foreach($typeLabels as $val => $label): ?>
            <option value="<?= $val ?>" <?= $type === $val ? 'selected' : '' ?>><?= $label ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit"
            class="bg-violet-600 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-violet-700 transition">
        <i class="fas fa-filter mr-1"></i> Filter
    </button>
    <?php if($type !== 'all' || $search !== ''): ?>
    <a href="<?= APP_URL ?>/staff/pending"
       class="bg-gray-100 text-gray-700 px-4 py-2.5 rounded-xl text-sm font-semibold hover:bg-gray-200 transition flex items-center gap-1">
        <i class="fas fa-times"></i> Clear
    </a>
    <?php endif; ?>
</form>

<!-- Properties Grid -->
<?php if (empty($properties)): ?>
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-16 text-center">
        <i class="fas fa-check-double text-green-400 text-5xl mb-4"></i>
        <h3 class="text-lg font-bold text-gray-700 mb-1">Queue is Empty!</h3>
        <p class="text-sm text-gray-400">No properties are awaiting verification right now.</p>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
        <?php foreach($properties as $p): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow group">
            <!-- Property Type Badge Header -->
            <div class="h-2 <?=
                match($p['property_type']) {
                    'apartment'  => 'bg-blue-500',
                    'house'      => 'bg-green-500',
                    'commercial' => 'bg-orange-500',
                    'land'       => 'bg-amber-500',
                    default      => 'bg-gray-400'
                }
            ?>"></div>

            <div class="p-5">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-gray-900 text-sm leading-tight truncate"><?= htmlspecialchars($p['title']) ?></h3>
                        <p class="text-xs text-gray-400 mt-1 truncate">
                            <i class="fas fa-map-marker-alt mr-1 text-violet-400"></i>
                            <?= htmlspecialchars($p['address']) ?>
                        </p>
                    </div>
                    <span class="ml-2 text-[10px] font-bold uppercase px-2 py-1 rounded-full bg-yellow-100 text-yellow-700 whitespace-nowrap flex-shrink-0">
                        Pending
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-2 mb-4">
                    <div class="bg-gray-50 rounded-lg px-3 py-2">
                        <p class="text-[10px] text-gray-400 uppercase font-semibold">Price</p>
                        <p class="text-sm font-bold text-gray-800">₦<?= number_format($p['price'], 0) ?></p>
                    </div>
                    <div class="bg-gray-50 rounded-lg px-3 py-2">
                        <p class="text-[10px] text-gray-400 uppercase font-semibold">Type</p>
                        <p class="text-sm font-bold text-gray-800"><?= ucfirst($p['property_type']) ?></p>
                    </div>
                </div>

                <div class="flex items-center gap-2 text-xs text-gray-400 mb-4 pb-4 border-b border-gray-100">
                    <i class="fas fa-user text-violet-300"></i>
                    <span>By <strong class="text-gray-600"><?= htmlspecialchars($p['landlord_name']) ?></strong></span>
                    <span class="ml-auto"><?= date('M j, Y', strtotime($p['created_at'])) ?></span>
                </div>

                <a href="<?= APP_URL ?>/staff/view-property?id=<?= $p['id'] ?>"
                   class="w-full inline-flex items-center justify-center gap-2 bg-violet-600 hover:bg-violet-700 text-white text-sm font-semibold py-2.5 rounded-xl transition-all group-hover:shadow-md">
                    <i class="fas fa-search"></i> Review Property
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
