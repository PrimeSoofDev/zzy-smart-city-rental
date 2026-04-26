<h1 class="text-3xl font-bold text-gray-800 mb-8">Platform Settings</h1>

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden max-w-4xl">
    <div class="p-6 border-b border-gray-100">
        <h3 class="font-bold text-gray-800">System Configuration</h3>
        <p class="text-sm text-gray-500">Update core platform parameters and behavioral settings.</p>
    </div>

    <form action="<?= APP_URL ?>/admin/updateSettings" method="POST" class="p-6 space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Platform Name -->
            <div class="space-y-2">
                <label class="text-sm font-semibold text-gray-600">Platform Name</label>
                <input type="text" name="settings[platform_name]" value="<?= $settings['platform_name'] ?? '' ?>"
                       class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                <p class="text-[10px] text-gray-400">Displayed in the header and emails.</p>
            </div>

            <!-- Platform Email -->
            <div class="space-y-2">
                <label class="text-sm font-semibold text-gray-600">Support Email</label>
                <input type="email" name="settings[platform_email]" value="<?= $settings['platform_email'] ?? '' ?>"
                       class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                <p class="text-[10px] text-gray-400">Used for system notifications.</p>
            </div>

            <!-- Platform Phone -->
            <div class="space-y-2">
                <label class="text-sm font-semibold text-gray-600">Support Phone</label>
                <input type="text" name="settings[platform_phone]" value="<?= $settings['platform_phone'] ?? '' ?>"
                       class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all">
            </div>

            <!-- Commission Rate -->
            <div class="space-y-2">
                <label class="text-sm font-semibold text-gray-600">Commission Rate (%)</label>
                <input type="number" step="0.1" name="settings[commission_rate]" value="<?= $settings['commission_rate'] ?? '5.0' ?>"
                       class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                <p class="text-[10px] text-gray-400">Percentage taken from each successful rental.</p>
            </div>
        </div>

        <div class="pt-6 border-t border-gray-100 grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Maintenance Mode -->
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200">
                <div>
                    <p class="text-sm font-bold text-gray-800">Maintenance Mode</p>
                    <p class="text-xs text-gray-500">Disable public access to the site.</p>
                </div>
                <select name="settings[maintenance_mode]" class="px-3 py-1 border rounded-lg text-sm">
                    <option value="0" <?= ($settings['maintenance_mode'] ?? '0') === '0' ? 'selected' : '' ?>>Off</option>
                    <option value="1" <?= ($settings['maintenance_mode'] ?? '0') === '1' ? 'selected' : '' ?>>On</option>
                </select>
            </div>

            <!-- Registration Enabled -->
            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-200">
                <div>
                    <p class="text-sm font-bold text-gray-800">Allow Registration</p>
                    <p class="text-xs text-gray-500">Enable/Disable new user signups.</p>
                </div>
                <select name="settings[registration_enabled]" class="px-3 py-1 border rounded-lg text-sm">
                    <option value="1" <?= ($settings['registration_enabled'] ?? '1') === '1' ? 'selected' : '' ?>>Enabled</option>
                    <option value="0" <?= ($settings['registration_enabled'] ?? '0') === '0' ? 'selected' : '' ?>>Disabled</option>
                </select>
            </div>
        </div>

        <!-- Payment API Configuration -->
        <div class="mt-8">
            <div class="p-6 border-b border-gray-100 bg-gray-50 rounded-t-2xl">
                <h3 class="font-bold text-gray-800"><i class="fas fa-credit-card text-blue-500 mr-2"></i>Payment API Configuration</h3>
                <p class="text-sm text-gray-500">Manage your Paystack integration keys. If left blank, the system will use the default keys in the configuration file.</p>
            </div>
            <div class="p-6 bg-white border border-t-0 border-gray-100 rounded-b-2xl grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Paystack Public Key -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-600">Paystack Public Key</label>
                    <input type="text" name="settings[paystack_public_key]" value="<?= htmlspecialchars($settings['paystack_public_key'] ?? '') ?>" placeholder="pk_test_..."
                           class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all font-mono text-sm">
                </div>

                <!-- Paystack Secret Key -->
                <div class="space-y-2">
                    <label class="text-sm font-semibold text-gray-600">Paystack Secret Key</label>
                    <input type="password" name="settings[paystack_secret_key]" value="<?= htmlspecialchars($settings['paystack_secret_key'] ?? '') ?>" placeholder="sk_test_..."
                           class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all font-mono text-sm">
                    <p class="text-[10px] text-gray-400">Keep this key secure. It is required for verifying transactions and payouts.</p>
                </div>
            </div>
        </div>

        <!-- Agora API Configuration -->
        <div class="mt-8">
            <div class="p-6 border-b border-gray-100 bg-gray-50 rounded-t-2xl">
                <h3 class="font-bold text-gray-800"><i class="fas fa-video text-red-500 mr-2"></i>Agora API Configuration</h3>
                <p class="text-sm text-gray-500">Enter your Agora App ID to enable voice and video calling features.</p>
            </div>
            <div class="p-6 bg-white border border-t-0 border-gray-100 rounded-b-2xl">
                <div class="space-y-2 max-w-lg">
                    <label class="text-sm font-semibold text-gray-600">Agora App ID</label>
                    <input type="text" name="settings[agora_app_id]" value="<?= htmlspecialchars($settings['agora_app_id'] ?? '') ?>" placeholder="Enter Agora App ID"
                           class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all font-mono text-sm">
                    <p class="text-[10px] text-gray-400">Required for Real-Time Communication (RTC) features.</p>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-4">
            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-200">
                Save Settings
            </button>
        </div>
    </form>
</div>
