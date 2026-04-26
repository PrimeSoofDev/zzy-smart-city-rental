<h1 class="text-3xl font-bold text-gray-800 mb-8">User Management</h1>

<!-- Role Filter Tabs -->
<div class="mb-6 flex gap-2 flex-wrap">
    <a href="<?= APP_URL ?>/admin/users" class="px-4 py-2 rounded-lg text-sm font-bold transition-all <?= !isset($_GET['role']) || $_GET['role'] === '' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
        <i class="fas fa-users mr-2"></i>All Users
    </a>
    <a href="<?= APP_URL ?>/admin/users?role=Tenant" class="px-4 py-2 rounded-lg text-sm font-bold transition-all <?= (isset($_GET['role']) && $_GET['role'] === 'Tenant') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
        <i class="fas fa-home mr-2"></i>Tenants
    </a>
    <a href="<?= APP_URL ?>/admin/users?role=Landlord" class="px-4 py-2 rounded-lg text-sm font-bold transition-all <?= (isset($_GET['role']) && $_GET['role'] === 'Landlord') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
        <i class="fas fa-building mr-2"></i>Landlords
    </a>
    <a href="<?= APP_URL ?>/admin/users?role=Staff" class="px-4 py-2 rounded-lg text-sm font-bold transition-all <?= (isset($_GET['role']) && $_GET['role'] === 'Staff') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
        <i class="fas fa-user-tie mr-2"></i>Staff
    </a>
    <a href="<?= APP_URL ?>/admin/users?role=Lawyer" class="px-4 py-2 rounded-lg text-sm font-bold transition-all <?= (isset($_GET['role']) && $_GET['role'] === 'Lawyer') ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
        <i class="fas fa-gavel mr-2"></i>Lawyers
    </a>
</div>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex flex-col sm:flex-row justify-between items-center gap-4">
        <div class="flex items-center gap-2">
            <span class="text-sm font-medium text-gray-500">Showing:</span>
            <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-full uppercase">
                <?php
                    if (isset($_GET['role']) && $_GET['role'] !== '') {
                        echo htmlspecialchars($_GET['role']) . 's';
                    } else {
                        echo 'All Users';
                    }
                ?>
            </span>
        </div>
        <div class="flex gap-3 w-full sm:w-auto">
            <div class="relative flex-grow">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" id="searchInput" placeholder="Search by name or email..." class="pl-10 pr-4 py-2 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 outline-none w-full" onkeyup="searchUsers()">
            </div>
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
            <tbody class="divide-y divide-gray-100" id="userTableBody">
                <?php foreach($users as $u): ?>
                    <tr class="hover:bg-gray-50 transition-colors searchable-row" data-username="<?= strtolower($u['username']) ?>" data-email="<?= strtolower($u['email']) ?>">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center text-xs font-bold text-white uppercase">
                                    <?= substr($u['username'], 0, 1) ?>
                                </div>
                                <span class="font-medium text-gray-800"><?= htmlspecialchars($u['username']) ?></span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500"><?= htmlspecialchars($u['email']) ?></td>
                        <td class="px-6 py-4">
                            <?php
                                $roleColors = [
                                    'Tenant' => 'bg-blue-100 text-blue-700',
                                    'Landlord' => 'bg-purple-100 text-purple-700',
                                    'Staff' => 'bg-green-100 text-green-700',
                                    'Lawyer' => 'bg-indigo-100 text-indigo-700',
                                    'Admin' => 'bg-red-100 text-red-700'
                                ];
                                $roleClass = $roleColors[$u['role_name']] ?? 'bg-gray-100 text-gray-700';
                            ?>
                            <span class="px-2 py-1 text-[10px] font-bold rounded-full <?= $roleClass ?> uppercase">
                                <?= htmlspecialchars($u['role_name'] ?? 'No Role') ?>
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <?php
                                $statusColors = [
                                    'verified' => 'text-green-600 bg-green-50',
                                    'pending' => 'text-orange-500 bg-orange-50',
                                    'rejected' => 'text-red-600 bg-red-50',
                                    'banned' => 'text-red-700 bg-red-100',
                                    'suspended' => 'text-gray-600 bg-gray-50'
                                ];
                                $statusClass = $statusColors[$u['status']] ?? 'text-gray-600 bg-gray-50';
                                $statusDot = [
                                    'verified' => 'bg-green-600',
                                    'pending' => 'bg-orange-500',
                                    'rejected' => 'bg-red-600',
                                    'banned' => 'bg-red-700',
                                    'suspended' => 'bg-gray-600'
                                ];
                                $dotClass = $statusDot[$u['status']] ?? 'bg-gray-600';
                            ?>
                            <span class="flex items-center gap-1.5 text-xs font-medium <?= $statusClass ?> px-2.5 py-1 rounded-full">
                                <span class="w-1.5 h-1.5 rounded-full <?= $dotClass ?>"></span>
                                <?= ucfirst(str_replace('_', ' ', $u['status'])) ?>
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button onclick="openUserModal(<?= htmlspecialchars(json_encode($u)) ?>)" class="text-gray-400 hover:text-blue-600 transition-colors px-2" title="Edit">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="<?= APP_URL ?>/admin/delete-user" method="POST" class="inline-block">
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                <button type="submit" class="text-gray-400 hover:text-red-600 transition-colors px-2" onclick="return confirm('Are you sure you want to delete this user?')" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php if (empty($users)): ?>
            <div class="text-center py-12">
                <i class="fas fa-users text-6xl text-gray-300 mb-4 block"></i>
                <p class="text-gray-500 text-lg">No users found</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- User Management Modal -->
<div id="userModal" class="fixed inset-0 z-[100] hidden overflow-y-auto bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden transform transition-all scale-95 opacity-0" id="modalContent">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-800" id="modalTitle">Manage User</h3>
            <button onclick="closeUserModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="p-6 space-y-6 max-h-[70vh] overflow-y-auto">
            <!-- User Info Section -->
            <div class="flex items-center gap-4 p-4 bg-blue-50 rounded-xl border border-blue-100">
                <div id="modalUserAvatar" class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 text-white flex items-center justify-center font-bold text-lg"></div>
                <div>
                    <p id="modalUserName" class="font-bold text-gray-800"></p>
                    <p id="modalUserEmail" class="text-sm text-gray-500"></p>
                </div>
            </div>

            <!-- Tabs for different sections -->
            <div class="flex gap-2 border-b border-gray-200">
                <button onclick="switchTab(event, 'edit')" class="tabBtn px-4 py-2 text-sm font-bold border-b-2 border-transparent text-gray-600 hover:text-blue-600 transition-colors border-blue-600 text-blue-600" data-tab="edit">
                    <i class="fas fa-user-edit mr-2"></i>Edit Profile
                </button>
                <button onclick="switchTab(event, 'actions')" class="tabBtn px-4 py-2 text-sm font-bold border-b-2 border-transparent text-gray-600 hover:text-blue-600 transition-colors" data-tab="actions">
                    <i class="fas fa-sliders-h mr-2"></i>Actions
                </button>
            </div>

            <!-- Edit Profile Tab -->
            <div id="editTab" class="tabContent space-y-4">
                <form action="<?= APP_URL ?>/admin/update-user-profile" method="POST">
                    <input type="hidden" name="user_id" id="modalUserId">
                    <div class="space-y-3">
                        <div>
                            <label class="text-xs font-semibold text-gray-600 uppercase">Full Name</label>
                            <input type="text" name="username" id="modalUsername" required
                                   class="w-full mt-1 px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600 uppercase">Email</label>
                            <input type="email" name="email" id="modalEmail" required
                                   class="w-full mt-1 px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-600 uppercase">Phone</label>
                            <input type="text" name="phone" id="modalPhone"
                                   class="w-full mt-1 px-4 py-2 border border-gray-200 rounded-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                        </div>
                        <button type="submit" class="w-full mt-4 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition-all">
                            <i class="fas fa-save mr-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <!-- Actions Tab -->
            <div id="actionsTab" class="tabContent space-y-4" style="display:none;">
                <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider">User Status Actions</p>

                <!-- Approve Button -->
                <form action="<?= APP_URL ?>/admin/approve-user" method="POST">
                    <input type="hidden" name="user_id" id="modalUserIdApprove">
                    <input type="hidden" name="role" id="modalUserRoleApprove">
                    <button type="submit" class="w-full flex items-center justify-between px-4 py-3 bg-green-50 border border-green-200 rounded-xl text-sm font-bold text-green-700 hover:bg-green-100 transition-all">
                        <span><i class="fas fa-check-circle mr-2"></i>Approve User</span>
                        <i class="fas fa-chevron-right"></i>
                    </button>
                </form>

                <!-- Reject Section -->
                <div class="p-4 border border-orange-200 rounded-xl bg-orange-50">
                    <label class="text-xs font-semibold text-gray-600 uppercase block mb-2">Reject User (Fake/Blur Documents)</label>
                    <form action="<?= APP_URL ?>/admin/reject-user-with-reason" method="POST" class="space-y-2">
                        <input type="hidden" name="user_id" id="modalUserIdReject">
                        <textarea name="reason" placeholder="Reason for rejection (e.g., Blurred ID, Fake document, etc.)" rows="2"
                                  class="w-full px-3 py-2 border border-orange-200 rounded-lg text-sm focus:ring-2 focus:ring-orange-500 outline-none" required></textarea>
                        <button type="submit" class="w-full flex items-center justify-between px-4 py-2 bg-orange-600 text-white rounded-lg text-sm font-bold hover:bg-orange-700 transition-all">
                            <span><i class="fas fa-times-circle mr-2"></i>Reject User</span>
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </form>
                </div>

                <!-- Ban Section (Admin Only) -->
                <div id="banSection" class="p-4 border border-red-200 rounded-xl bg-red-50" style="display:none;">
                    <label class="text-xs font-semibold text-gray-600 uppercase block mb-2">Ban User (Cheating/Fraud)</label>
                    <form action="<?= APP_URL ?>/admin/ban-user" method="POST" class="space-y-2">
                        <input type="hidden" name="user_id" id="modalUserIdBan">
                        <textarea name="reason" placeholder="Reason for ban (e.g., Fraudulent activity, Multiple fake accounts, etc.)" rows="2"
                                  class="w-full px-3 py-2 border border-red-200 rounded-lg text-sm focus:ring-2 focus:ring-red-500 outline-none" required></textarea>
                        <button type="submit" class="w-full flex items-center justify-between px-4 py-2 bg-red-600 text-white rounded-lg text-sm font-bold hover:bg-red-700 transition-all">
                            <span><i class="fas fa-ban mr-2"></i>Ban User</span>
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let isAdmin = <?php echo (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Admin') ? 'true' : 'false'; ?>;

// Search functionality
function searchUsers() {
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    const rows = document.querySelectorAll('.searchable-row');
    let visibleCount = 0;

    rows.forEach(row => {
        const username = row.getAttribute('data-username');
        const email = row.getAttribute('data-email');

        if (username.includes(searchInput) || email.includes(searchInput)) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });

    // Show/hide "no results" message
    const tbody = document.getElementById('userTableBody');
    const noResults = document.querySelector('.no-results');

    if (visibleCount === 0 && searchInput !== '') {
        if (!noResults) {
            const tr = document.createElement('tr');
            tr.className = 'no-results';
            tr.innerHTML = '<td colspan="5" class="px-6 py-12 text-center text-gray-500"><i class="fas fa-search text-4xl text-gray-300 mb-2 block"></i><p>No users match your search</p></td>';
            tbody.appendChild(tr);
        }
    } else if (noResults) {
        noResults.remove();
    }
}

function switchTab(event, tab) {
    event.preventDefault();

    // Hide all tabs
    document.querySelectorAll('.tabContent').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.tabBtn').forEach(el => {
        el.classList.remove('border-blue-600', 'text-blue-600');
        el.classList.add('border-transparent', 'text-gray-600');
    });

    // Show selected tab
    if (tab === 'edit') {
        document.getElementById('editTab').style.display = 'block';
    } else if (tab === 'actions') {
        document.getElementById('actionsTab').style.display = 'block';
    }

    // Mark active tab button
    event.target.closest('.tabBtn').classList.remove('border-transparent', 'text-gray-600');
    event.target.closest('.tabBtn').classList.add('border-blue-600', 'text-blue-600');
}

function openUserModal(user) {
    const modal = document.getElementById('userModal');
    const content = document.getElementById('modalContent');

    // Populate user info
    document.getElementById('modalUserName').textContent = user.username;
    document.getElementById('modalUserEmail').textContent = user.email;
    document.getElementById('modalUserAvatar').textContent = user.username.charAt(0).toUpperCase();
    document.getElementById('modalUsername').value = user.username;
    document.getElementById('modalEmail').value = user.email;
    document.getElementById('modalPhone').value = user.phone || '';

    // Set hidden IDs
    document.getElementById('modalUserId').value = user.id;
    document.getElementById('modalUserIdApprove').value = user.id;
    document.getElementById('modalUserRoleApprove').value = user.role_name;
    document.getElementById('modalUserIdReject').value = user.id;
    document.getElementById('modalUserIdBan').value = user.id;

    // Show ban section only for admin users
    const banSection = document.getElementById('banSection');
    if (banSection) {
        banSection.style.display = isAdmin ? 'block' : 'none';
    }

    // Set active tab to edit
    document.getElementById('editTab').style.display = 'block';
    document.getElementById('actionsTab').style.display = 'none';
    document.querySelectorAll('.tabBtn').forEach(el => {
        el.classList.remove('border-blue-600', 'text-blue-600');
        el.classList.add('border-transparent', 'text-gray-600');
    });
    document.querySelectorAll('.tabBtn')[0].classList.remove('border-transparent', 'text-gray-600');
    document.querySelectorAll('.tabBtn')[0].classList.add('border-blue-600', 'text-blue-600');

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
