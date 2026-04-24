<h1 class="text-3xl font-bold text-gray-800 mb-8">User Management</h1>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4">
        <div class="flex items-center gap-2">
            <span class="text-sm font-medium text-gray-500">Showing:</span>
            <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-full uppercase">
                <?= isset($_GET['role']) ? htmlspecialchars($_GET['role']) : 'All Users' ?>
            </span>
        </div>
        <div class="flex gap-3 w-full sm:w-auto">
            <div class="relative flex-grow">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" placeholder="Search users..." class="pl-10 pr-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none w-full">
            </div>
            <button class="bg-white border border-gray-200 p-2 rounded-xl hover:bg-gray-50 text-gray-600">
                <i class="fas fa-filter"></i>
            </button>
        </div>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 text-gray-400 text-xs uppercase font-bold">
                    <th class="px-6 py-4">User</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4">Role</th>
                    <th class="px-6 py-4">Status</th>
                    <th class="px-6 py-4 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php foreach($users as $u): ?>
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600 uppercase">
                                    <?= substr($u['username'], 0, 1) ?>
                                </div>
                                <span class="font-medium text-gray-800"><?= htmlspecialchars($u['username']) ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500"><?= htmlspecialchars($u['email']) ?></td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-[10px] font-bold rounded-full bg-blue-100 text-blue-700 uppercase">
                                <?= htmlspecialchars($u['role_name'] ?? 'No Role') ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="flex items-center gap-1.5 text-xs font-medium <?= $u['status'] === 'verified' ? 'text-green-600' : 'text-orange-500' ?>">
                                <span class="w-1.5 h-1.5 rounded-full <?= $u['status'] === 'verified' ? 'bg-green-600' : 'bg-orange-500' ?>"></span>
                                <?= ucfirst($u['status']) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button onclick="openUserModal(<?= htmlspecialchars(json_encode($u)) ?>)" class="text-gray-400 hover:text-blue-600 transition-colors px-2">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="<?= APP_URL ?>/admin/delete-user" method="POST" class="inline-block">
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors px-2" onclick="return confirm('Are you sure you want to delete this user?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- User Management Modal -->
<div id="userModal" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all scale-95 opacity-0" id="modalContent">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-800">Manage User</h3>
            <button onclick="closeUserModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-6 space-y-6">
            <div class="flex items-center gap-4 p-4 bg-blue-50 rounded-xl border border-blue-100">
                <div id="modalUserAvatar" class="w-12 h-12 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-lg"></div>
                <div>
                    <p id="modalUserName" class="font-bold text-gray-800"></p>
                    <p id="modalUserEmail" class="text-sm text-gray-500"></p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4">
                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">User Status</p>
                <div class="flex flex-wrap gap-2">
                    <form action="<?= APP_URL ?>/admin/approve-user" method="POST" class="inline">
                        <input type="hidden" name="user_id" id="modalUserId">
                        <input type="hidden" name="role" id="modalUserRole">
                        <button class="bg-green-600 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-green-700 transition-all flex items-center gap-2">
                            <i class="fas fa-check-circle"></i> Approve
                        </button>
                    </form>
                    <form action="<?= APP_URL ?>/admin/reject-user" method="POST" class="inline">
                        <input type="hidden" name="user_id" id="modalUserIdReject">
                        <button class="bg-red-600 text-white px-4 py-2 rounded-lg text-xs font-bold hover:bg-red-700 transition-all flex items-center gap-2">
                            <i class="fas fa-ban"></i> Reject/Ban
                        </button>
                    </form>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-100">
                <a href="#" id="modalUserEditLink" class="block w-full text-center bg-gray-100 text-gray-700 py-2 rounded-lg text-sm font-semibold hover:bg-gray-200 transition-all">
                    <i class="fas fa-user-edit mr-2"></i> Full Profile Edit
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function openUserModal(user) {
    const modal = document.getElementById('userModal');
    const content = document.getElementById('modalContent');

    document.getElementById('modalUserName').textContent = user.username;
    document.getElementById('modalUserEmail').textContent = user.email;
    document.getElementById('modalUserAvatar').textContent = user.username.charAt(0).toUpperCase();
    document.getElementById('modalUserId').value = user.id;
    document.getElementById('modalUserIdReject').value = user.id;
    document.getElementById('modalUserRole').value = user.role_name;

    // For the edit link, since we can't put a variable inside an attribute easily in HTML, we handle it via JS
    const editLink = document.getElementById('modalUserEditLink');
    if (editLink) {
        editLink.href = `<?= APP_URL ?>/admin/edit-user?id=${user.id}`;
    }

    modal.classList.remove('hidden');
    setTimeout(() => {
        content.classList.remove('scale-95', 'opacity-0');
        content.classList.add('scale-100', 'opacity-100');
    }, 10);
}

function closeUserModal() {
    const modal = document.getElementById('userModal');
    const content = document.getElementById('modalContent');

    content.classList.remove('scale-100', 'opacity-100');
    content.classList.add('scale-95', 'opacity-0');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 200);
}

// Close on outside click
window.onclick = function(event) {
    const modal = document.getElementById('userModal');
    if (event.target == modal) {
        closeUserModal();
    }
}
</script>
