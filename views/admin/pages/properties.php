<h1 class="text-3xl font-bold text-gray-800 mb-8">Property Management</h1>

<!-- Statistics Cards -->
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <p class="text-gray-500 text-xs font-bold uppercase mb-1">Total Listings</p>
        <p class="text-2xl font-bold text-gray-900"><?= $stats['totalListings'] ?? 0 ?></p>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <p class="text-gray-500 text-xs font-bold uppercase mb-1">Pending Verification</p>
        <p class="text-2xl font-bold text-yellow-600"><?= $stats['pendingVerification'] ?? 0 ?></p>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <p class="text-gray-500 text-xs font-bold uppercase mb-1">Active Properties</p>
        <p class="text-2xl font-bold text-green-600"><?= $stats['activeProperties'] ?? 0 ?></p>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <p class="text-gray-500 text-xs font-bold uppercase mb-1">Rejected</p>
        <p class="text-2xl font-bold text-red-600"><?= $stats['rejectedProperties'] ?? 0 ?></p>
    </div>
</div>

<!-- Status Filter Tabs -->
<div class="mb-6 flex gap-2 flex-wrap">
    <a href="<?= APP_URL ?>/admin/properties" class="px-4 py-2 rounded-lg text-sm font-bold transition-all <?= !isset($_GET['status']) || $_GET['status'] === '' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
        <i class="fas fa-list mr-2"></i>All Properties
    </a>
    <a href="<?= APP_URL ?>/admin/properties?status=pending_verification" class="px-4 py-2 rounded-lg text-sm font-bold transition-all <?= (isset($_GET['status']) && $_GET['status'] === 'pending_verification') ? 'bg-yellow-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
        <i class="fas fa-hourglass-half mr-2"></i>Pending
    </a>
    <a href="<?= APP_URL ?>/admin/properties?status=approved" class="px-4 py-2 rounded-lg text-sm font-bold transition-all <?= (isset($_GET['status']) && $_GET['status'] === 'approved') ? 'bg-green-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
        <i class="fas fa-check-circle mr-2"></i>Approved
    </a>
    <a href="<?= APP_URL ?>/admin/properties?status=rejected" class="px-4 py-2 rounded-lg text-sm font-bold transition-all <?= (isset($_GET['status']) && $_GET['status'] === 'rejected') ? 'bg-red-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
        <i class="fas fa-times-circle mr-2"></i>Rejected
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h3 class="font-bold text-gray-800 mb-3">Property Directory</h3>
            <div class="relative">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="searchInput" placeholder="Search by property name or address..."
                       class="pl-10 pr-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none w-full"
                       onkeyup="searchProperties()">
            </div>
        </div>
        <div class="flex gap-2">
            <a href="<?= APP_URL ?>/admin/export-properties<?= isset($_GET['status']) ? '?status=' . htmlspecialchars($_GET['status']) : '' ?>" class="px-4 py-2 bg-blue-600 text-white text-xs font-bold rounded-lg hover:bg-blue-700 transition-all flex items-center gap-2">
                <i class="fas fa-download"></i>Export CSV
            </a>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-400 text-xs uppercase font-bold">
                    <th class="px-6 py-4">Property</th>
                    <th class="px-6 py-4">Landlord</th>
                    <th class="px-6 py-4">Type</th>
                    <th class="px-6 py-4">Price</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100" id="propertyTableBody">
                <?php if (!empty($properties)): ?>
                    <?php foreach($properties as $p): ?>
                    <tr class="hover:bg-gray-50 transition-colors searchable-row" data-property="<?= strtolower($p['title']) ?>" data-address="<?= strtolower($p['address']) ?>">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center">
                                    <i class="fas fa-building text-blue-600 text-lg"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800"><?= htmlspecialchars($p['title']) ?></p>
                                    <p class="text-[10px] text-gray-400 uppercase"><?= htmlspecialchars(substr($p['address'], 0, 40)) ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600"><?= htmlspecialchars($p['landlord_name']) ?></td>
                        <td class="px-6 py-4">
                            <?php
                                $typeColors = [
                                    'apartment' => 'bg-blue-100 text-blue-700',
                                    'house' => 'bg-green-100 text-green-700',
                                    'commercial' => 'bg-purple-100 text-purple-700',
                                    'land' => 'bg-yellow-100 text-yellow-700'
                                ];
                                $typeClass = $typeColors[$p['property_type']] ?? 'bg-gray-100 text-gray-700';
                            ?>
                            <span class="px-2 py-1 text-[10px] font-bold rounded-full <?= $typeClass ?> uppercase">
                                <?= htmlspecialchars($p['property_type']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 font-bold text-gray-800">₦<?= number_format($p['price'], 2) ?>/mo</td>
                        <td class="px-6 py-4">
                            <?php
                                $statusColors = [
                                    'pending_verification' => 'bg-yellow-100 text-yellow-700',
                                    'approved' => 'bg-green-100 text-green-700',
                                    'rejected' => 'bg-red-100 text-red-700',
                                    'rented' => 'bg-blue-100 text-blue-700',
                                    'draft' => 'bg-gray-100 text-gray-700'
                                ];
                                $statusClass = $statusColors[$p['status']] ?? 'bg-gray-100 text-gray-700';
                            ?>
                            <span class="px-2 py-1 text-[10px] font-bold rounded-full <?= $statusClass ?> uppercase">
                                <?= ucfirst(str_replace('_', ' ', $p['status'])) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <?php if ($p['status'] === 'pending_verification'): ?>
                                <form action="<?= APP_URL ?>/admin/approve-property" method="POST" class="inline">
                                    <input type="hidden" name="property_id" value="<?= $p['id'] ?>">
                                    <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded-lg text-xs font-bold hover:bg-green-700 mr-2 transition-all" title="Approve">
                                        <i class="fas fa-check mr-1"></i>Approve
                                    </button>
                                </form>
                                <form action="<?= APP_URL ?>/admin/reject-property" method="POST" class="inline">
                                    <input type="hidden" name="property_id" value="<?= $p['id'] ?>">
                                    <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded-lg text-xs font-bold hover:bg-red-700 transition-all" title="Reject">
                                        <i class="fas fa-times mr-1"></i>Reject
                                    </button>
                                </form>
                            <?php else: ?>
                                <button onclick="viewProperty(<?= $p['id'] ?>)" class="text-blue-600 hover:text-blue-800 font-bold text-xs mr-3 transition-colors" title="View">
                                    <i class="fas fa-eye mr-1"></i>View
                                </button>
                                <button onclick="suspendProperty(<?= $p['id'] ?>)" class="text-red-600 hover:text-red-800 font-bold text-xs transition-colors" title="Suspend">
                                    <i class="fas fa-ban mr-1"></i>Suspend
                                </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <i class="fas fa-inbox text-6xl text-gray-300 mb-4 block"></i>
                            <p class="text-gray-500 text-lg">No properties found</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// Search functionality
function searchProperties() {
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('.searchable-row');
    let visibleCount = 0;

    rows.forEach(row => {
        const property = row.getAttribute('data-property');
        const address = row.getAttribute('data-address');

        if (property.includes(searchInput) || address.includes(searchInput)) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    // Show/hide "no results" message
    const tbody = document.getElementById('propertyTableBody');
    if (visibleCount === 0 && searchInput !== '') {
        const noResults = document.querySelector('.no-results');
        if (!noResults) {
            const tr = document.createElement('tr');
            tr.className = 'no-results';
            tr.innerHTML = '<td colspan="6" class="px-6 py-12 text-center text-gray-500"><i class="fas fa-search text-4xl text-gray-300 mb-2 block"></i><p>No properties match your search</p></td>';
            tbody.appendChild(tr);
        }
    } else {
        const noResults = document.querySelector('.no-results');
        if (noResults) noResults.remove();
    }
}

function viewProperty(id) {
    alert('View property details for ID: ' + id);
}

function suspendProperty(id) {
    if (confirm('Are you sure you want to suspend this property?')) {
        alert('Property suspended. ID: ' + id);
    }
}
</script>
