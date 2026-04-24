<h1 class="text-3xl font-bold mb-8">Legal Agreements</h1>
<div class="overflow-x-auto bg-white rounded-2xl shadow-sm border">
    <table class="w-full text-left">
        <thead>
            <tr class="bg-gray-100 text-gray-600 text-sm uppercase font-bold">
                <th class="p-4">Property</th>
                <th class="p-4">Tenant</th>
                <th class="p-4">Status</th>
                <th class="p-4">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($requests as $r): ?>
                <tr class="border-t">
                    <td class="p-4 font-medium"><?= $r['title'] ?></td>
                    <td class="p-4 text-sm text-gray-500">Paid User</td>
                    <td class="p-4 text-xs font-bold uppercase text-green-600"><?= $r['status'] ?></td>
                    <td class="p-4"><a href="#" class="bg-blue-600 text-white px-3 py-1 rounded-lg text-xs font-bold hover:bg-blue-700">Draft Agreement</a></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
