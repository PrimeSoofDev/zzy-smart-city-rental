<h1 class="text-3xl font-bold text-gray-800 mb-8">Verification Center</h1>

<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
    <!-- Tenant Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-800">Tenant KYCs</h3>
            <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full font-bold">All Records</span>
        </div>
        <div class="p-6 space-y-4">
            <?php foreach ($tenants as $tenant): ?>
            <div class="flex items-center justify-between p-4 rounded-xl border border-gray-100 hover:border-blue-300 transition-all group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                        <i class="fas fa-user text-gray-400"></i>
                    </div>
                    <div class="flex flex-col">
                        <p class="text-sm font-bold text-gray-800"><?php echo htmlspecialchars($tenant['username']); ?></p>
                        <div class="flex items-center gap-2">
                            <?php
                                $status = $tenant['verification_status'] ?? 'pending';
                                $badgeClass = 'bg-yellow-100 text-yellow-700';
                                $statusText = 'Pending';
                                if ($status === 'approved') { $badgeClass = 'bg-green-100 text-green-700'; $statusText = 'Approved'; }
                                elseif ($status === 'rejected') { $badgeClass = 'bg-red-100 text-red-700'; $statusText = 'Rejected'; }
                            ?>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full <?= $badgeClass ?> uppercase"><?= $statusText ?></span>
                        </div>
                    </div>
                </div>
                <?php if ($status === 'pending'): ?>
                <form action="admin/approve-user" method="POST" class="flex gap-2">
                    <input type="hidden" name="user_id" value="<?php echo $tenant['user_id']; ?>">
                    <input type="hidden" name="role" value="Tenant">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-blue-700 transition-colors">Verify Now</button>
                    <button name="action" value="reject" class="bg-gray-200 text-gray-600 px-4 py-2 rounded-lg text-xs font-bold hover:bg-gray-300 transition-colors">Reject</button>
                </form>
                <?php else: ?>
                    <div class="text-xs text-gray-400 italic">Processed</div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Landlord Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-800">Landlord KYCs</h3>
            <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full font-bold">All Records</span>
        </div>
        <div class="p-6 space-y-4">
            <?php foreach ($landlords as $landlord): ?>
            <div class="flex items-center justify-between p-4 rounded-xl border border-gray-100 hover:border-blue-300 transition-all group">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center">
                        <i class="fas fa-building text-gray-400"></i>
                    </div>
                    <div class="flex flex-col">
                        <p class="text-sm font-bold text-gray-800"><?php echo htmlspecialchars($landlord['username']); ?></p>
                        <div class="flex items-center gap-2">
                            <?php
                                $status = $landlord['verification_status'] ?? 'pending';
                                $badgeClass = 'bg-yellow-100 text-yellow-700';
                                $statusText = 'Pending';
                                if ($status === 'approved') { $badgeClass = 'bg-green-100 text-green-700'; $statusText = 'Approved'; }
                                elseif ($status === 'rejected') { $badgeClass = 'bg-red-100 text-red-700'; $statusText = 'Rejected'; }
                            ?>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full <?= $badgeClass ?> uppercase"><?= $statusText ?></span>
                        </div>
                    </div>
                </div>
                <?php if ($status === 'pending'): ?>
                <form action="admin/approve-user" method="POST" class="flex gap-2">
                    <input type="hidden" name="user_id" value="<?php echo $landlord['user_id']; ?>">
                    <input type="hidden" name="role" value="Landlord">
                    <button class="bg-blue-600 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-blue-700 transition-colors">Verify Now</button>
                    <button name="action" value="reject" class="bg-gray-200 text-gray-600 px-4 py-2 rounded-lg text-xs font-bold hover:bg-gray-300 transition-colors">Reject</button>
                </form>
                <?php else: ?>
                    <div class="text-xs text-gray-400 italic">Processed</div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
