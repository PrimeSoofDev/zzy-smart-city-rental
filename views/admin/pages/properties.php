<h1 class="text-3xl font-bold text-gray-800 mb-8">Property Management</h1>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <p class="text-gray-500 text-xs font-bold uppercase mb-1">Total Listings</p>
        <p class="text-2xl font-bold text-gray-900">1,240</p>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <p class="text-gray-500 text-xs font-bold uppercase mb-1">Pending Verification</p>
        <p class="text-2xl font-bold text-yellow-600">42</p>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <p class="text-gray-500 text-xs font-bold uppercase mb-1">Active Properties</p>
        <p class="text-2xl font-bold text-green-600">1,198</p>
    </div>
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <p class="text-gray-500 text-xs font-bold uppercase mb-1">Rejected</p>
        <p class="text-2xl font-bold text-red-600">12</p>
    </div>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <h3 class="font-bold text-gray-800">Property Directory</h3>
        <div class="flex gap-2">
            <button class="px-3 py-1.5 bg-blue-600 text-white text-xs font-bold rounded-lg">Export CSV</button>
            <button class="px-3 py-1.5 bg-gray-100 text-gray-600 text-xs font-bold rounded-lg hover:bg-gray-200">Filter</button>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-400 text-xs uppercase font-bold">
                    <th class="px-6 py-4">Property</th>
                    <th class="px-6 py-4">Landlord</th>
                    <th class="px-6 py-4">Price</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach($properties as $p): ?>
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-lg bg-gray-200 overflow-hidden">
                                <img src="https://via.placeholder.com/48" alt="Prop">
                            </div>
                            <div>
                                <p class="font-medium text-gray-800"><?= htmlspecialchars($p['title']) ?></p>
                                <p class="text-[10px] text-gray-400 uppercase"><?= htmlspecialchars($p['address']) ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-600"><?= htmlspecialchars($p['landlord_name']) ?></td>
                    <td class="px-6 py-4 font-bold text-gray-800">$<?= number_format($p['price'], 2) ?>/mo</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-[10px] font-bold rounded-full <?= $p['status'] === 'approved' ? 'bg-green-100 text-green-700' : ($p['status'] === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') ?> uppercase">
                            <?= ucfirst($p['status']) ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <?php if ($p['status'] === 'pending_verification'): ?>
                            <form action="admin/approve-property" method="POST" class="inline">
                                <input type="hidden" name="property_id" value="<?= $p['id'] ?>">
                                <button class="bg-green-600 text-white px-3 py-1 rounded-lg text-xs font-bold hover:bg-green-700 mr-2">Approve</button>
                            </form>
                            <form action="admin/reject-property" method="POST" class="inline">
                                <input type="hidden" name="property_id" value="<?= $p['id'] ?>">
                                <button class="bg-red-600 text-white px-3 py-1 rounded-lg text-xs font-bold hover:bg-red-700">Reject</button>
                            </form>
                        <?php else: ?>
                            <button class="text-blue-600 hover:text-blue-800 font-bold text-xs mr-3">View</button>
                            <button class="text-red-600 hover:text-red-800 font-bold text-xs">Suspend</button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
