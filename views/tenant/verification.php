<div class="min-h-screen bg-gray-50 py-12 px-6">
    <div class="max-w-2xl mx-auto">
        <div class="text-center mb-10">
            <div class="bg-blue-100 text-blue-600 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-user-check text-2xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900">Verify Your Identity</h1>
            <p class="text-gray-500 mt-2">To rent a property, you must first complete our identity verification process.</p>
        </div>

        <div class="bg-white p-8 rounded-3xl shadow-xl border border-gray-100">
            <?php if(isset($_SESSION['success'])): ?>
                <div class="mb-6 p-4 bg-green-50 text-green-700 rounded-xl border border-green-100 flex items-center gap-3">
                    <i class="fas fa-check-circle"></i>
                    <span class="text-sm font-medium"><?= $_SESSION['success']; unset($_SESSION['success']); ?></span>
                </div>
            <?php endif; ?>

            <?php if(isset($_SESSION['error'])): ?>
                <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-xl border border-red-100 flex items-center gap-3">
                    <i class="fas fa-exclamation-circle"></i>
                    <span class="text-sm font-medium"><?= $_SESSION['error']; unset($_SESSION['error']); ?></span>
                </div>
            <?php endif; ?>

            <form action="<?= APP_URL ?>/tenant/verify-submit" method="POST" enctype="multipart/form-data" class="space-y-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">ID Number (NIMC/BVN/Passport)</label>
                        <input type="text" name="id_number" required class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Full Residential Address</label>
                        <textarea name="address" required rows="3" class="w-full px-4 py-3 border rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Government ID Upload (PDF/JPG/PNG)</label>
                        <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-xl hover:border-blue-400 transition-colors cursor-pointer bg-gray-50">
                            <div class="space-y-1 text-center">
                                <i class="fas fa-cloud-upload-alt text-gray-400 text-3xl mb-2"></i>
                                <div class="flex text-sm text-gray-600">
                                    <label for="id_doc" class="relative cursor-pointer font-semibold text-blue-600 hover:text-blue-500">
                                        <span>Upload a file</span>
                                        <input id="id_doc" name="id_doc" type="file" class="sr-only" required>
                                    </label>
                                </div>
                                <p class="text-xs text-gray-400">PNG, JPG, PDF up to 10MB</p>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full py-4 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-200">
                    Submit for Verification
                </button>
            </form>
        </div>
    </div>
</div>
