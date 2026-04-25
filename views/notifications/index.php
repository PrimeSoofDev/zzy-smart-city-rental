<div class="max-w-4xl mx-auto py-6 px-4">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800">Notifications</h1>
            <p class="text-gray-500 mt-1">Stay updated on your account activity and requests.</p>
        </div>
        <?php
        $unreadCount = 0;
        foreach ($notifications as $n) {
            if (!$n['is_read']) $unreadCount++;
        }
        ?>
        <?php if ($unreadCount > 0): ?>
        <span class="bg-blue-100 text-blue-700 font-bold px-3 py-1 rounded-full text-xs">
            <?= $unreadCount ?> New
        </span>
        <?php endif; ?>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <?php if (empty($notifications)): ?>
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-bell-slash text-2xl text-gray-300"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-700">All caught up!</h3>
                <p class="text-gray-500 text-sm mt-1">You don't have any notifications right now.</p>
            </div>
        <?php else: ?>
            <ul class="divide-y divide-gray-50">
                <?php foreach ($notifications as $n): ?>
                <li class="p-5 hover:bg-gray-50 transition-colors <?= $n['is_read'] ? 'opacity-70' : 'bg-blue-50/30' ?>">
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 mt-1">
                            <div class="w-10 h-10 rounded-full flex items-center justify-center <?= $n['is_read'] ? 'bg-gray-100 text-gray-400' : 'bg-blue-100 text-blue-600' ?>">
                                <i class="fas fa-bell"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 mb-1 leading-snug">
                                <?= htmlspecialchars($n['message']) ?>
                            </p>
                            <p class="text-xs text-gray-400">
                                <i class="far fa-clock mr-1"></i>
                                <?= date('M j, Y \a\t g:i A', strtotime($n['created_at'])) ?>
                            </p>
                        </div>
                        <?php if (!$n['is_read']): ?>
                        <div class="flex-shrink-0">
                            <span class="w-2.5 h-2.5 bg-blue-600 rounded-full inline-block mt-2"></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </div>
</div>
