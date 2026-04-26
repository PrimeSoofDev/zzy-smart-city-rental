<div class="max-w-4xl mx-auto px-4 py-12">
    <div class="bg-white rounded-[2.5rem] shadow-2xl shadow-slate-200 border border-slate-100 overflow-hidden">
        <div class="flex flex-col md:flex-row">
            <!-- Sidebar/Info -->
            <div class="md:w-1/3 bg-slate-900 p-10 text-white flex flex-col justify-between">
                <div>
                    <div class="w-16 h-16 bg-blue-600 rounded-2xl flex items-center justify-center mb-8 shadow-lg shadow-blue-900/50">
                        <i class="fas fa-university text-2xl"></i>
                    </div>
                    <h2 class="text-3xl font-black tracking-tight mb-4 leading-tight">Payout Configuration</h2>
                    <p class="text-slate-400 text-sm leading-relaxed mb-8">
                        Securely set up your bank account details. This is where your rental income will be deposited once released from escrow.
                    </p>
                    
                    <ul class="space-y-4">
                        <li class="flex items-center gap-3 text-xs font-bold text-slate-300">
                            <i class="fas fa-check-circle text-green-500"></i>
                            Instant Subaccount Creation
                        </li>
                        <li class="flex items-center gap-3 text-xs font-bold text-slate-300">
                            <i class="fas fa-check-circle text-green-500"></i>
                            All Nigeria Banks Supported
                        </li>
                        <li class="flex items-center gap-3 text-xs font-bold text-slate-300">
                            <i class="fas fa-check-circle text-green-500"></i>
                            Secure Payment Splitting
                        </li>
                    </ul>
                </div>
                
                <div class="mt-12 pt-8 border-t border-slate-800">
                    <p class="text-[10px] uppercase font-black tracking-widest text-slate-500">Powered by</p>
                    <div class="flex items-center gap-2 mt-2">
                        <i class="fab fa-cc-paystack text-xl text-slate-300"></i>
                        <span class="font-bold text-slate-300">Paystack</span>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="md:w-2/3 p-10 md:p-16">
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="mb-8 p-4 bg-green-50 border-l-4 border-green-500 text-green-800 rounded-r-2xl shadow-sm flex items-center gap-3 animate-bounce">
                        <i class="fas fa-check-circle text-green-500 text-xl"></i>
                        <p class="text-sm font-bold"><?= $_SESSION['success']; unset($_SESSION['success']); ?></p>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="mb-8 p-4 bg-red-50 border-l-4 border-red-500 text-red-800 rounded-r-2xl shadow-sm flex items-center gap-3">
                        <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                        <p class="text-sm font-bold"><?= $_SESSION['error']; unset($_SESSION['error']); ?></p>
                    </div>
                <?php endif; ?>

                <form action="<?= APP_URL ?>/landlord/bank-save" method="POST" class="space-y-8">
                    <div class="relative group">
                        <label for="bank" class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 transition-colors group-focus-within:text-blue-600">
                            Select Your Bank
                        </label>
                        <div class="relative">
                            <select name="bank_code" id="bank" class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-6 py-4 text-slate-900 font-bold outline-none focus:border-blue-600 focus:bg-white transition-all appearance-none" required>
                                <option value="">-- Select Bank --</option>
                                <?php foreach ($banks as $bank): ?>
                                    <option value="<?= $bank['code']; ?>" <?= (isset($profile['bank_code']) && $profile['bank_code'] == $bank['code']) ? 'selected' : ''; ?>>
                                        <?= $bank['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                                <i class="fas fa-chevron-down"></i>
                            </div>
                        </div>
                    </div>

                    <div class="relative group">
                        <label for="account_number" class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 transition-colors group-focus-within:text-blue-600">
                            Account Number
                        </label>
                        <input type="text" name="account_number" id="account_number" maxlength="10" 
                               class="w-full bg-slate-50 border-2 border-slate-100 rounded-2xl px-6 py-4 text-slate-900 font-bold outline-none focus:border-blue-600 focus:bg-white transition-all placeholder:text-slate-300"
                               placeholder="e.g. 0123456789" required
                               value="<?= $profile['account_number'] ?? ''; ?>">
                        <p class="mt-2 text-[10px] font-bold text-slate-400 italic">Must be 10 digits</p>
                    </div>

                    <div class="pt-4">
                        <button type="submit" class="w-full bg-blue-600 text-white rounded-2xl py-5 px-8 font-black uppercase tracking-widest text-sm hover:bg-blue-700 hover:scale-[1.02] active:scale-[0.98] transition-all shadow-xl shadow-blue-100 flex items-center justify-center gap-3">
                            <i class="fas fa-save text-lg"></i>
                            Save Payout Details
                        </button>
                    </div>
                </form>

                <?php if (!empty($profile['subaccount_code'])): ?>
                    <div class="mt-12 pt-12 border-t border-slate-100">
                        <div class="bg-blue-50/50 rounded-3xl p-6 border border-blue-100/50">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-green-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-green-200">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div>
                                    <h4 class="font-black text-slate-900 uppercase tracking-tight">Account Active</h4>
                                    <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Linked to Paystack Subaccount</p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-white p-4 rounded-2xl border border-slate-100">
                                    <p class="text-[9px] font-black uppercase text-slate-400 mb-1">Bank Name</p>
                                    <p class="text-xs font-bold text-slate-900"><?= $profile['bank_name']; ?></p>
                                </div>
                                <div class="bg-white p-4 rounded-2xl border border-slate-100">
                                    <p class="text-[9px] font-black uppercase text-slate-400 mb-1">Account No</p>
                                    <p class="text-xs font-bold text-slate-900"><?= $profile['account_number']; ?></p>
                                </div>
                            </div>
                            <div class="mt-4 bg-slate-900 p-3 rounded-xl flex justify-between items-center">
                                <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest px-2">Subaccount Code</span>
                                <span class="text-[10px] font-mono font-bold text-blue-400 bg-blue-900/30 px-3 py-1 rounded-lg"><?= $profile['subaccount_code']; ?></span>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="mt-12 text-center">
        <a href="<?= APP_URL ?>/landlord/dashboard" class="inline-flex items-center gap-2 text-slate-400 hover:text-slate-900 font-bold transition-colors">
            <i class="fas fa-long-arrow-alt-left"></i>
            <span>Return to Dashboard</span>
        </a>
    </div>
</div>
