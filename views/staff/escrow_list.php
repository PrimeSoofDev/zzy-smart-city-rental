<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Escrow Management</h1>
            <p class="text-muted small">Monitor and release funds for verified tenant move-ins.</p>
        </div>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fas fa-check-circle me-2"></i><?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm border-0 rounded-lg overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="px-4 py-3 border-0">Reference</th>
                        <th class="py-3 border-0">Property</th>
                        <th class="py-3 border-0">Tenant</th>
                        <th class="py-3 border-0">Landlord</th>
                        <th class="py-3 border-0 text-end">Amount</th>
                        <th class="py-3 border-0 text-center">Status</th>
                        <th class="px-4 py-3 border-0 text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transactions)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-box-open d-block fs-1 mb-3 opacity-25"></i>
                                No active escrow holds found.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($transactions as $t): ?>
                            <tr>
                                <td class="px-4">
                                    <span class="fw-bold text-primary">#<?php echo $t['paystack_reference']; ?></span>
                                    <div class="small text-muted"><?php echo date('M d, Y H:i', strtotime($t['created_at'])); ?></div>
                                </td>
                                <td>
                                    <div class="fw-bold text-gray-900"><?php echo htmlspecialchars($t['property_title']); ?></div>
                                </td>
                                <td><?php echo htmlspecialchars($t['tenant_name']); ?></td>
                                <td>
                                    <div class="fw-bold"><?php echo htmlspecialchars($t['landlord_name']); ?></div>
                                    <div class="small text-muted"><?php echo $t['bank_name']; ?> (<?php echo $t['account_number']; ?>)</div>
                                </td>
                                <td class="text-end fw-bold">₦<?php echo number_format($t['amount'], 2); ?></td>
                                <td class="text-center">
                                    <span class="badge bg-info-soft text-info rounded-pill px-3 py-2 uppercase tracking-wider small">
                                        <i class="fas fa-lock me-1"></i>ESCROW HOLD
                                    </span>
                                </td>
                                <td class="px-4 text-end">
                                    <form action="<?php echo APP_URL; ?>/staff/escrow-release" method="POST" class="d-inline" onsubmit="return confirm('Confirm tenant move-in and release funds to landlord?');">
                                        <input type="hidden" name="transaction_id" value="<?php echo $t['id']; ?>">
                                        <button type="submit" class="btn btn-success btn-sm rounded-pill px-3">
                                            <i class="fas fa-check me-1"></i>Confirm Move-in
                                        </button>
                                    </form>
                                    <form action="<?php echo APP_URL; ?>/staff/escrow-refund" method="POST" class="d-inline" onsubmit="return confirm('Refund funds to tenant? This will cancel the booking.');">
                                        <input type="hidden" name="transaction_id" value="<?php echo $t['id']; ?>">
                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-3">
                                            <i class="fas fa-undo me-1"></i>Refund
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .bg-info-soft { background-color: rgba(13, 202, 240, 0.1); }
    .text-info { color: #0dcaf0 !important; }
</style>
