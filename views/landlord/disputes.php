<div class="mb-8">
    <h1 class="text-3xl font-bold mb-2 flex items-center gap-2">
        <i class="fas fa-gavel text-amber-500"></i> Dispute Center
    </h1>
    <p class="text-gray-600">Manage disputes for your properties and escrow payouts.</p>
</div>

<?php if(empty($payoutItems)): ?>
    <div class="bg-white p-12 rounded-3xl shadow-sm border border-gray-100 text-center">
        <i class="fas fa-check-circle text-5xl text-gray-300 mb-4"></i>
        <h3 class="text-xl font-bold text-gray-800 mb-2">No Active Escrow Payouts</h3>
        <p class="text-gray-500">You don't have any properties currently in escrow to dispute.</p>
    </div>
<?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach($payoutItems as $item): ?>
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-md transition-all flex flex-col justify-between h-full">
                <div>
                    <div class="flex justify-between items-start mb-4">
                        <div class="bg-blue-50 text-blue-600 p-3 rounded-2xl">
                            <i class="fas fa-hand-holding-usd text-xl"></i>
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full 
                            <?= $item['status'] == 'escrow_hold' ? 'bg-amber-50 text-amber-600 border border-amber-100' : 'bg-green-50 text-green-600 border border-green-100' ?>">
                            <?= str_replace('_', ' ', $item['status']) ?>
                        </span>
                    </div>
                    <h3 class="font-bold text-gray-900 mb-1"><?= htmlspecialchars($item['property_title']) ?></h3>
                    <p class="text-xs text-gray-500 mb-4 line-clamp-1">Tenant: <?= htmlspecialchars($item['tenant_name']) ?></p>
                    
                    <div class="pt-4 border-t border-gray-50 mb-4">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Total Rent</p>
                        <p class="font-black text-gray-900 text-xl">₦<?= number_format($item['amount'], 2) ?></p>
                    </div>
                </div>

                <div class="mt-auto pt-4 flex flex-col gap-2">
                    <?php if($item['status'] == 'escrow_hold'): ?>
                        <?php if($item['request_status'] === 'disputed'): ?>
                            <a href="<?= APP_URL ?>/dispute/portal?request_id=<?= $item['request_id'] ?>" class="w-full text-center px-4 py-3 bg-amber-50 text-amber-600 rounded-xl text-sm font-bold hover:bg-amber-100 transition-all shadow-sm border border-amber-100">
                                <i class="fas fa-gavel mr-1"></i> View Dispute Case
                            </a>
                        <?php else: ?>
                            <div class="flex flex-col gap-2">
                                <button onclick="openDisputeModal(<?= $item['request_id'] ?>, '<?= htmlspecialchars($item['property_title']) ?>')" class="w-full px-4 py-3 bg-red-50 text-red-600 rounded-xl text-sm font-bold hover:bg-red-100 transition-all border border-red-100 shadow-sm">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> Raise a Dispute
                                </button>
                                <button onclick="openReviewModal(<?= $item['request_id'] ?>, <?= $item['tenant_id'] ?>, '<?= htmlspecialchars($item['tenant_name']) ?>')" class="w-full px-4 py-3 bg-blue-50 text-blue-600 rounded-xl text-sm font-bold hover:bg-blue-100 transition-all border border-blue-100 shadow-sm">
                                    <i class="fas fa-star mr-1"></i> Write Review
                                </button>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="px-4 py-3 bg-gray-50 text-gray-400 rounded-xl text-sm font-bold text-center border border-gray-100">
                            Disputes not available
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- Dispute Modal -->
<div id="dispute-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-[2.5rem] w-full max-w-md overflow-hidden shadow-2xl border border-slate-100 animate-in fade-in zoom-in duration-300">
        <div class="p-8">
            <div class="w-16 h-16 bg-red-50 text-red-600 rounded-3xl flex items-center justify-center mb-6">
                <i class="fas fa-exclamation-triangle text-2xl"></i>
            </div>
            <h3 class="text-2xl font-black text-slate-900 mb-2">Raise a Dispute</h3>
            <p class="text-slate-500 text-sm mb-6">Tell us why you're disputing this escrow. This will freeze the funds.</p>
            
            <form action="<?= APP_URL ?>/dispute/raise" method="POST">
                <input type="hidden" name="request_id" id="modal-request-id">
                <div class="mb-6">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Reason for Dispute</label>
                    <textarea name="reason" rows="4" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm focus:ring-2 focus:ring-red-500 outline-none transition-all placeholder:text-slate-300" placeholder="Describe the issue..." required></textarea>
                </div>
                
                <div class="flex gap-3">
                    <button type="button" onclick="closeDisputeModal()" class="flex-1 py-4 bg-slate-50 text-slate-500 font-bold rounded-2xl hover:bg-slate-100 transition-all">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 py-4 bg-red-600 text-white font-bold rounded-2xl shadow-lg shadow-red-600/20 hover:bg-red-700 transition-all">
                        Submit Dispute
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Review Modal -->
<div id="review-modal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-[2.5rem] w-full max-w-md overflow-hidden shadow-2xl border border-slate-100 animate-in fade-in zoom-in duration-300">
        <div class="p-8">
            <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-3xl flex items-center justify-center mb-6">
                <i class="fas fa-star text-2xl"></i>
            </div>
            <h3 class="text-2xl font-black text-slate-900 mb-2">Write Review</h3>
            <p class="text-slate-500 text-sm mb-6">Share your experience with <span id="modal-tenant-name" class="font-bold text-slate-900"></span>.</p>
            
            <form action="<?= APP_URL ?>/review/submit" method="POST">
                <input type="hidden" name="request_id" id="review-request-id">
                <input type="hidden" name="reviewee_id" id="review-tenant-id">
                
                <div class="mb-6">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Rating</label>
                    <div class="flex gap-2" id="star-rating">
                        <?php for($i=1; $i<=5; $i++): ?>
                            <i class="far fa-star text-2xl text-amber-400 cursor-pointer star-btn" data-value="<?= $i ?>"></i>
                        <?php endfor; ?>
                    </div>
                    <input type="hidden" name="rating" id="rating-value" value="5" required>
                </div>

                <div class="mb-6">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2">Your Review</label>
                    <textarea name="comment" rows="4" class="w-full px-4 py-3 bg-slate-50 border border-slate-100 rounded-2xl text-sm focus:ring-2 focus:ring-blue-500 outline-none transition-all placeholder:text-slate-300" placeholder="e.g. Respectful tenant, paid on time, and kept the property clean..." required></textarea>
                </div>
                
                <div class="flex gap-3">
                    <button type="button" onclick="closeReviewModal()" class="flex-1 py-4 bg-slate-50 text-slate-500 font-bold rounded-2xl hover:bg-slate-100 transition-all">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 py-4 bg-blue-600 text-white font-bold rounded-2xl shadow-lg shadow-blue-600/20 hover:bg-blue-700 transition-all">
                        Submit Review
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openDisputeModal(requestId, title) {
        document.getElementById('modal-request-id').value = requestId;
        const titleEl = document.getElementById('modal-property-title');
        if (titleEl) titleEl.innerText = title;
        document.getElementById('dispute-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeDisputeModal() {
        document.getElementById('dispute-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function openReviewModal(requestId, tenantId, tenantName) {
        document.getElementById('review-request-id').value = requestId;
        document.getElementById('review-tenant-id').value = tenantId;
        document.getElementById('modal-tenant-name').innerText = tenantName;
        document.getElementById('review-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeReviewModal() {
        document.getElementById('review-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Star rating logic
    document.querySelectorAll('.star-btn').forEach(star => {
        star.addEventListener('click', () => {
            const val = star.dataset.value;
            document.getElementById('rating-value').value = val;
            
            document.querySelectorAll('.star-btn').forEach(s => {
                if (s.dataset.value <= val) {
                    s.classList.remove('far');
                    s.classList.add('fas');
                } else {
                    s.classList.remove('fas');
                    s.classList.add('far');
                }
            });
        });
    });
</script>
