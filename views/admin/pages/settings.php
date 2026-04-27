<div class="max-w-6xl mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Platform Settings</h1>
            <p class="text-gray-500 mt-1">Configure and manage your platform's core behavior and integrations.</p>
        </div>
        <div class="bg-blue-50 px-4 py-2 rounded-2xl border border-blue-100">
            <span class="text-blue-600 font-bold text-sm">System Version: 2.1.0</span>
        </div>
    </div>

    <form action="<?= APP_URL ?>/admin/updateSettings" method="POST" enctype="multipart/form-data">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- Sidebar Navigation -->
            <div class="lg:col-span-1 space-y-2">
                <nav class="flex flex-col space-y-1" id="settings-nav">
                    <button type="button" onclick="showTab('general')" class="tab-btn active px-4 py-3 text-left rounded-xl font-bold transition-all flex items-center gap-3 bg-blue-600 text-white shadow-lg shadow-blue-200">
                        <i class="fas fa-cog"></i> General Settings
                    </button>
                    <button type="button" onclick="showTab('branding')" class="tab-btn px-4 py-3 text-left rounded-xl font-bold transition-all flex items-center gap-3 text-gray-600 hover:bg-gray-100">
                        <i class="fas fa-paint-brush"></i> Branding
                    </button>
                    <button type="button" onclick="showTab('payments')" class="tab-btn px-4 py-3 text-left rounded-xl font-bold transition-all flex items-center gap-3 text-gray-600 hover:bg-gray-100">
                        <i class="fas fa-credit-card"></i> Payment Keys
                    </button>
                    <button type="button" onclick="showTab('communication')" class="tab-btn px-4 py-3 text-left rounded-xl font-bold transition-all flex items-center gap-3 text-gray-600 hover:bg-gray-100">
                        <i class="fas fa-envelope"></i> OTP & Comms
                    </button>
                    <button type="button" onclick="showTab('advanced')" class="tab-btn px-4 py-3 text-left rounded-xl font-bold transition-all flex items-center gap-3 text-gray-600 hover:bg-gray-100">
                        <i class="fas fa-microchip"></i> Advanced API
                    </button>
                    <button type="button" onclick="showTab('seo')" class="tab-btn px-4 py-3 text-left rounded-xl font-bold transition-all flex items-center gap-3 text-gray-600 hover:bg-gray-100">
                        <i class="fas fa-search"></i> SEO Settings
                    </button>
                    <button type="button" onclick="showTab('social')" class="tab-btn px-4 py-3 text-left rounded-xl font-bold transition-all flex items-center gap-3 text-gray-600 hover:bg-gray-100">
                        <i class="fas fa-share-alt"></i> Social Links
                    </button>
                </nav>
            </div>

            <!-- Content Area -->
            <div class="lg:col-span-3">
                <!-- General Settings -->
                <div id="tab-general" class="tab-content space-y-8">
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-8 border-b border-gray-50">
                            <h3 class="text-xl font-bold text-gray-900">Brand Identity</h3>
                            <p class="text-sm text-gray-500">How your platform appears to users.</p>
                        </div>
                        <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700">Platform Name</label>
                                <input type="text" name="settings[platform_name]" value="<?= $settings['platform_name'] ?? '' ?>"
                                       class="w-full px-5 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none transition-all placeholder-gray-400">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700">Support Email</label>
                                <input type="email" name="settings[platform_email]" value="<?= $settings['platform_email'] ?? '' ?>"
                                       class="w-full px-5 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none transition-all placeholder-gray-400">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700">Support Phone</label>
                                <input type="text" name="settings[platform_phone]" value="<?= $settings['platform_phone'] ?? '' ?>"
                                       class="w-full px-5 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none transition-all placeholder-gray-400">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700">Commission Rate (%)</label>
                                <input type="number" step="0.1" name="settings[commission_rate]" value="<?= $settings['commission_rate'] ?? '5.0' ?>"
                                       class="w-full px-5 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none transition-all placeholder-gray-400">
                            </div>
                            <div class="space-y-2 md:col-span-2">
                                <label class="text-sm font-bold text-gray-700">Website Base URL</label>
                                <input type="url" name="settings[site_url]" value="<?= htmlspecialchars($settings['site_url'] ?? APP_URL) ?>"
                                       placeholder="https://yourdomain.com/subdir"
                                       class="w-full px-5 py-3 bg-blue-50/50 border-2 border-blue-100 rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none transition-all placeholder-gray-400 font-mono text-sm">
                                <p class="text-[10px] text-blue-500 mt-1 flex items-center gap-1">
                                    <i class="fas fa-exclamation-triangle"></i> Changing this will update all system links and routing. Ensure it matches your live server path.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-8 border-b border-gray-50">
                            <h3 class="text-xl font-bold text-gray-900">System Controls</h3>
                            <p class="text-sm text-gray-500">Manage site availability and user onboarding.</p>
                        </div>
                        <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="flex items-center justify-between p-6 bg-blue-50 rounded-2xl border border-blue-100">
                                <div>
                                    <p class="font-bold text-blue-900">Maintenance Mode</p>
                                    <p class="text-xs text-blue-600">Restrict access to admins only.</p>
                                </div>
                                <select name="settings[maintenance_mode]" class="bg-white px-4 py-2 rounded-xl border-none shadow-sm font-bold text-blue-900 outline-none">
                                    <option value="0" <?= ($settings['maintenance_mode'] ?? '0') === '0' ? 'selected' : '' ?>>Off</option>
                                    <option value="1" <?= ($settings['maintenance_mode'] ?? '0') === '1' ? 'selected' : '' ?>>On</option>
                                </select>
                            </div>
                            <div class="flex items-center justify-between p-6 bg-purple-50 rounded-2xl border border-purple-100">
                                <div>
                                    <p class="font-bold text-purple-900">Public Registration</p>
                                    <p class="text-xs text-purple-600">Allow new users to sign up.</p>
                                </div>
                                <select name="settings[registration_enabled]" class="bg-white px-4 py-2 rounded-xl border-none shadow-sm font-bold text-purple-900 outline-none">
                                    <option value="1" <?= ($settings['registration_enabled'] ?? '1') === '1' ? 'selected' : '' ?>>Enabled</option>
                                    <option value="0" <?= ($settings['registration_enabled'] ?? '0') === '0' ? 'selected' : '' ?>>Disabled</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Branding Settings -->
                <div id="tab-branding" class="tab-content hidden space-y-8">
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-8 border-b border-gray-50">
                            <h3 class="text-xl font-bold text-gray-900">Site Branding</h3>
                            <p class="text-sm text-gray-500">Manage your platform's name, logo, and favicon.</p>
                        </div>
                        <div class="p-8 space-y-8">
                            <!-- Site Name -->
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700 block">Site Name</label>
                                <input type="text" name="settings[site_name]" value="<?= htmlspecialchars($settings['site_name'] ?? $settings['platform_name'] ?? 'ZZY Rental') ?>"
                                       class="w-full px-5 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            </div>

                            <!-- Logo + Favicon -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                            <!-- Logo Upload -->
                            <div class="space-y-4">
                                <label class="text-sm font-bold text-gray-700 block">Main Logo</label>
                                <div class="relative group">
                                    <div class="w-full h-40 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200 flex flex-col items-center justify-center overflow-hidden hover:border-blue-400 transition-all">
                                        <?php if($logo = SiteSetting::get('logo_url')): ?>
                                            <img src="<?= APP_URL . '/' . $logo ?>" class="h-16 object-contain">
                                        <?php else: ?>
                                            <i class="fas fa-image text-3xl text-gray-300 mb-2"></i>
                                            <p class="text-xs text-gray-400">No logo uploaded</p>
                                        <?php endif; ?>
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-all flex items-center justify-center">
                                            <span class="text-white text-xs font-bold bg-white/20 px-4 py-2 rounded-full backdrop-blur-md">Change Logo</span>
                                        </div>
                                    </div>
                                    <input type="file" name="logo" class="absolute inset-0 opacity-0 cursor-pointer">
                                </div>
                                <p class="text-[10px] text-gray-400">Recommended size: 200x50px. PNG or SVG preferred.</p>
                            </div>

                            <!-- Favicon Upload -->
                            <div class="space-y-4">
                                <label class="text-sm font-bold text-gray-700 block">Favicon</label>
                                <div class="relative group">
                                    <div class="w-full h-40 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200 flex flex-col items-center justify-center overflow-hidden hover:border-blue-400 transition-all">
                                        <?php if($fav = SiteSetting::get('favicon_url')): ?>
                                            <img src="<?= APP_URL . '/' . $fav ?>" class="w-12 h-12 object-contain">
                                        <?php else: ?>
                                            <i class="fas fa-square text-3xl text-gray-300 mb-2"></i>
                                            <p class="text-xs text-gray-400">No favicon uploaded</p>
                                        <?php endif; ?>
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-all flex items-center justify-center">
                                            <span class="text-white text-xs font-bold bg-white/20 px-4 py-2 rounded-full backdrop-blur-md">Change Favicon</span>
                                        </div>
                                    </div>
                                    <input type="file" name="favicon" class="absolute inset-0 opacity-0 cursor-pointer">
                                </div>
                                <p class="text-[10px] text-gray-400">Recommended size: 32x32px or 64x64px. .ico or .png</p>
                            </div>
                        </div><!-- end logo+favicon grid -->
                        </div><!-- end space-y-8 -->
                    </div>
                </div>
                <div id="tab-payments" class="tab-content hidden space-y-8">
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-8 bg-blue-600 text-white">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center text-2xl">
                                    <i class="fas fa-credit-card"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold">Paystack Integration</h3>
                                    <p class="text-blue-100 text-sm">Secure your transaction processing keys.</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-8 space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-gray-700">Public Key</label>
                                    <input type="text" name="settings[paystack_public_key]" value="<?= htmlspecialchars($settings['paystack_public_key'] ?? '') ?>" placeholder="pk_test_..."
                                           class="w-full px-5 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none transition-all font-mono text-sm">
                                </div>
                                <div class="space-y-2">
                                    <label class="text-sm font-bold text-gray-700">Secret Key</label>
                                    <input type="password" name="settings[paystack_secret_key]" value="<?= htmlspecialchars($settings['paystack_secret_key'] ?? '') ?>" placeholder="sk_test_..."
                                           class="w-full px-5 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none transition-all font-mono text-sm">
                                </div>
                            </div>
                            <div class="p-4 bg-amber-50 rounded-2xl border border-amber-100 flex gap-4">
                                <i class="fas fa-shield-alt text-amber-500 mt-1"></i>
                                <p class="text-xs text-amber-800">Your secret key is encrypted at rest. Never share your secret keys with anyone, including support staff.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Communication Settings -->
                <div id="tab-communication" class="tab-content hidden space-y-8">
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-8 bg-indigo-600 text-white">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center text-2xl">
                                    <i class="fas fa-paper-plane"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-bold">Brevo Integration (Email & SMS)</h3>
                                    <p class="text-indigo-100 text-sm">Configure your single API key for both transactional Emails and SMS.</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-8 space-y-6">
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700">Brevo API Key</label>
                                <input type="password" name="settings[brevo_api_key]" value="<?= htmlspecialchars($settings['brevo_api_key'] ?? '') ?>" placeholder="xkeysib-..."
                                       class="w-full px-5 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none transition-all font-mono text-sm">
                                <p class="text-[10px] text-gray-400">This key will be used for both system emails and OTP SMS messages.</p>
                            </div>

                            <div class="p-6 bg-blue-50 rounded-2xl border border-blue-100 flex items-start gap-4">
                                <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 flex-shrink-0">
                                    <i class="fas fa-info-circle"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-blue-900">Unified Delivery</p>
                                    <p class="text-xs text-blue-700 leading-relaxed">By using Brevo for both channels, you only need to maintain one set of credits and one API key. Ensure your Brevo account has SMS credits enabled to send OTPs to phone numbers.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Advanced API Settings -->
                <div id="tab-advanced" class="tab-content hidden space-y-8">
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-8 border-b border-gray-50 flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">RTC Configuration (Agora)</h3>
                                <p class="text-sm text-gray-500">Real-time voice and video settings.</p>
                            </div>
                            <i class="fas fa-video text-3xl text-gray-200"></i>
                        </div>
                        <div class="p-8">
                            <div class="space-y-2 max-w-lg">
                                <label class="text-sm font-bold text-gray-700">Agora App ID</label>
                                <input type="text" name="settings[agora_app_id]" value="<?= htmlspecialchars($settings['agora_app_id'] ?? '') ?>" placeholder="Enter Agora App ID"
                                       class="w-full px-5 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none transition-all font-mono text-sm">
                                <p class="text-[10px] text-gray-400 mt-2">Required for the property viewing video call feature.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SEO Settings -->
                <div id="tab-seo" class="tab-content hidden space-y-8">
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-8 border-b border-gray-50 flex items-center justify-between">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900">Search Engine Optimization</h3>
                                <p class="text-sm text-gray-500">Manage how your site appears in Google and other search engines.</p>
                            </div>
                            <i class="fas fa-globe text-3xl text-gray-200"></i>
                        </div>
                        <div class="p-8 space-y-6">
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700">Default Meta Title</label>
                                <input type="text" name="settings[meta_title]" value="<?= htmlspecialchars($settings['meta_title'] ?? 'ZZY Smart Rental - Modern Living in Nigeria') ?>"
                                       class="w-full px-5 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700">Default Meta Description</label>
                                <textarea name="settings[meta_description]" rows="3"
                                          class="w-full px-5 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none transition-all"><?= htmlspecialchars($settings['meta_description'] ?? 'Find your future home with ZZY Smart Rental.') ?></textarea>
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700">Keywords (Comma separated)</label>
                                <input type="text" name="settings[meta_keywords]" value="<?= htmlspecialchars($settings['meta_keywords'] ?? 'rental, housing, nigeria, real estate') ?>"
                                       class="w-full px-5 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Social Media Settings -->
                <div id="tab-social" class="tab-content hidden space-y-8">
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-8 border-b border-gray-50">
                            <h3 class="text-xl font-bold text-gray-900">Social Media Profiles</h3>
                            <p class="text-sm text-gray-500">Connect your platform to your social communities.</p>
                        </div>
                        <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700"><i class="fab fa-facebook text-blue-600 mr-2"></i> Facebook URL</label>
                                <input type="url" name="settings[social_facebook]" value="<?= htmlspecialchars($settings['social_facebook'] ?? '') ?>"
                                       class="w-full px-5 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700"><i class="fab fa-twitter text-blue-400 mr-2"></i> Twitter (X) URL</label>
                                <input type="url" name="settings[social_twitter]" value="<?= htmlspecialchars($settings['social_twitter'] ?? '') ?>"
                                       class="w-full px-5 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700"><i class="fab fa-instagram text-pink-600 mr-2"></i> Instagram URL</label>
                                <input type="url" name="settings[social_instagram]" value="<?= htmlspecialchars($settings['social_instagram'] ?? '') ?>"
                                       class="w-full px-5 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="text-sm font-bold text-gray-700"><i class="fab fa-linkedin text-blue-800 mr-2"></i> LinkedIn URL</label>
                                <input type="url" name="settings[social_linkedin]" value="<?= htmlspecialchars($settings['social_linkedin'] ?? '') ?>"
                                       class="w-full px-5 py-3 bg-gray-50 border-none rounded-2xl focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Global Save Button -->
                <div class="mt-8 flex justify-end">
                    <button type="submit" class="bg-gray-900 text-white px-10 py-4 rounded-2xl font-bold hover:bg-black transition-all shadow-xl shadow-gray-200 flex items-center gap-2">
                        <i class="fas fa-save"></i> Apply Changes
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function showTab(tabId) {
    // Hide all contents
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.add('hidden');
    });
    
    // Show selected content
    document.getElementById('tab-' + tabId).classList.remove('hidden');
    
    // Update navigation styles
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('bg-blue-600', 'text-white', 'shadow-lg', 'shadow-blue-200');
        btn.classList.add('text-gray-600', 'hover:bg-gray-100');
    });
    
    const activeBtn = event.currentTarget;
    activeBtn.classList.remove('text-gray-600', 'hover:bg-gray-100');
    activeBtn.classList.add('bg-blue-600', 'text-white', 'shadow-lg', 'shadow-blue-200');
}
</script>

<style>
/* Custom input styles to complement Tailwind */
input:focus {
    transform: translateY(-1px);
    box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.1);
}
.tab-btn {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
