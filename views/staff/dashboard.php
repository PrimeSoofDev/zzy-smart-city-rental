<h1 class="text-3xl font-bold mb-8">Property Verifications</h1>
<div class="overflow-x-auto bg-white rounded-2xl shadow-sm border">
    <table class="w-full text-left">
        <thead>
            <tr class="bg-gray-100 text-gray-600 text-sm uppercase font-bold">
                <th class="p-4">Property</th>
                <th class="p-4">Address</th>
                <th class="p-4">Price</th>
                <th class="p-4">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($properties as $p): ?>
                <tr class="border-t">
                    <td class="p-4 font-medium"><?= $p['title'] ?></td>
                    <td class="p-4 text-sm text-gray-500"><?= $p['address'] ?></td>
                    <td class="p-4 font-bold">$<?= number_format($p['price'], 2) ?></td>
                    <td class="p-4 flex gap-2">
                        <a href="<?= APP_URL ?>/staff/verify?id=<?= $p['id'] ?>&status=approved" class="bg-green-600 text-white px-3 py-1 rounded-lg text-xs font-bold hover:bg-green-700">Approve</a>
                        <a href="<?= APP_URL ?>/staff/verify?id=<?= $p['id'] ?>&status=rejected" class="bg-red-600 text-white px-3 py-1 rounded-lg text-xs font-bold hover:bg-red-700">Reject</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
