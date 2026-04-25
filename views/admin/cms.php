<?php 
require_once "../views/layouts/admin_layout_start.php"; 
?>

<div class="p-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Guest Page CMS</h1>
            <p class="text-gray-500">Manage site-wide settings and landing page content.</p>
        </div>
        <?php if(isset($_GET['success'])): ?>
            <div class="bg-green-100 text-green-700 px-4 py-2 rounded-lg text-sm font-bold animate-bounce">
                <?= htmlspecialchars($_GET['success']) ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Site Branding (Admin Only) -->
        <div class="lg:col-span-1 space-y-8">
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <i class="fas fa-palette text-blue-600"></i> Site Branding
                </h3>
                <form action="<?= APP_URL ?>/cms/update-settings" method="POST" enctype="multipart/form-data" class="space-y-6">
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Site Name</label>
                        <input type="text" name="site_name" value="<?= htmlspecialchars($settings['site_name']) ?>" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 outline-none focus:border-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Logo</label>
                        <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                            <?php if($settings['logo_url']): ?>
                                <img src="<?= APP_URL . '/' . $settings['logo_url'] ?>" class="h-10">
                            <?php else: ?>
                                <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center text-white font-bold">Z</div>
                            <?php endif; ?>
                            <input type="file" name="logo" class="text-xs">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Favicon</label>
                        <input type="file" name="favicon" class="text-xs">
                    </div>
                    <button class="w-full py-4 bg-blue-600 text-white font-bold rounded-xl shadow-lg shadow-blue-500/20 hover:bg-blue-700 transition-all">Save Branding</button>
                </form>
            </div>
        </div>

        <!-- Page Content -->
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <i class="fas fa-edit text-blue-600"></i> Page Content
                </h3>
                
                <div class="space-y-10">
                    <?php foreach($contents as $page => $sections): ?>
                        <div class="border-b border-gray-100 pb-10 last:border-0 last:pb-0">
                            <h4 class="text-sm font-black text-blue-600 uppercase tracking-widest mb-6"><?= str_replace('_', ' ', $page) ?> Page</h4>
                            
                            <?php foreach($sections as $section => $keys): ?>
                                <?php foreach($keys as $key => $value): ?>
                                    <form action="<?= APP_URL ?>/cms/update-page" method="POST" class="mb-6 last:mb-0">
                                        <input type="hidden" name="page_name" value="<?= $page ?>">
                                        <input type="hidden" name="section_name" value="<?= $section ?>">
                                        <input type="hidden" name="content_key" value="<?= $key ?>">
                                        
                                        <div class="flex flex-col gap-2">
                                            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest"><?= $section ?> • <?= $key ?></label>
                                            <div class="flex gap-4">
                                                <textarea name="content_value" class="flex-1 bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 outline-none focus:border-blue-500 text-sm min-h-[100px]"><?= htmlspecialchars($value) ?></textarea>
                                                <button class="px-6 bg-slate-900 text-white font-bold rounded-xl hover:bg-slate-800 transition-all text-xs h-fit py-3">Update</button>
                                            </div>
                                        </div>
                                    </form>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once "../views/layouts/admin_layout_end.php"; ?>
