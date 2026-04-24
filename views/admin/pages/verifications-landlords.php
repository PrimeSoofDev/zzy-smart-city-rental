<?php
RbacMiddleware::check(['Admin']);
$db = Database::getInstance()->getConnection();

$users = $db->query("
    SELECT u.id, u.username, u.email, u.status, r.role_name
    FROM users u
    LEFT JOIN user_roles ur ON u.id = ur.user_id
    LEFT JOIN roles r ON ur.role_id = r.id
    WHERE r.role_name = 'Landlord'
")->fetchAll();

require_once "../views/layouts/admin_layout_start.php";
?>

<h1 class="text-3xl font-bold text-gray-800 mb-8">Approve Landlords</h1>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">User</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Email</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Role</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Status</th>
                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            <?php foreach ($users as $user): ?>
            <tr class="hover:bg-gray-50 transition-colors">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm">
                            <?php echo strtoupper(substr($user['username'], 0, 1)); ?>
                        </div>
                        <span class="font-semibold text-gray-800"><?php echo htmlspecialchars($user['username']); ?></span>
                    </div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600"><?php echo htmlspecialchars($user['email']); ?></td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 rounded-md bg-gray-100 text-gray-600 text-[10px] font-bold uppercase">
                        <?php echo $user['role_name']; ?>
                    </span>
                </td>
                <td class="px-6 py-4">
                    <?php if ($user['status'] === 'verified'): ?>
                        <span class="px-2 py-1 rounded-full bg-green-100 text-green-700 text-[10px] font-bold uppercase">Verified</span>
                    <?php else: ?>
                        <span class="px-2 py-1 rounded-full bg-orange-100 text-orange-700 text-[10px] font-bold uppercase">Pending</span>
                    <?php endif; ?>
                </td>
                <td class="px-6 py-4 text-right space-x-2">
                    <?php if ($user['status'] !== 'verified'): ?>
                        <form action="admin/approve-user" method="POST" class="inline">
                            <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                            <input type="hidden" name="role" value="Landlord">
                            <button class="bg-green-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-green-700 transition-colors">Approve</button>
                        </form>
                    <?php endif; ?>
                    <form action="admin/reject-user" method="POST" class="inline">
                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                        <button class="bg-red-600 text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-red-700 transition-colors">Reject/Ban</button>
                    </form>
                    <a href="admin/edit-user?id=<?php echo $user['id']; ?>" class="bg-gray-200 text-gray-700 px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-gray-300 transition-colors inline-block">Edit</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php require_once "../views/layouts/admin_layout_end.php"; ?>
