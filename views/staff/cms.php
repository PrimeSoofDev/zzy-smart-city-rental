<?php 
require_once "../views/layouts/staff_layout_start.php"; 
?>

<div class="p-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Content Management</h1>
            <p class="text-gray-500">Update the text and content of the guest-facing pages.</p>
        </div>
        <?php if(isset($_GET['success'])): ?>
            <div class="bg-green-100 text-green-700 px-4 py-2 rounded-lg text-sm font-bold">
                <?= htmlspecialchars($_GET['success']) ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="max-w-4xl space-y-8">
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
            <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                <i class="fas fa-edit text-blue-600"></i> Page Sections
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

<?php require_once "../views/layouts/staff_layout_end.php"; ?>
