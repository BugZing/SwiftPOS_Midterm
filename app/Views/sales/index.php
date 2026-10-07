<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Sales History</h2>
        <p class="text-secondary mb-0">Audit log of completed point-of-sale transactions and item sales.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a class="btn btn-primary" href="<?= esc(site_url('sales/new'), 'attr') ?>">
            <i class="bi bi-cart-plus me-1"></i> Record New Sale
        </a>
    </div>
</div>

<!-- Transaction Metrics Summary -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 stat-card p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-success-subtle text-success p-3 rounded-3 fs-4">
                    <i class="bi bi-cash-stack"></i>
                </span>
                <div>
                    <span class="text-secondary small fw-semibold text-uppercase">Total Revenue</span>
                    <h3 class="fw-bold text-dark font-monospace mb-0">₱<?= number_format((float) ($totalRevenue ?? 0), 2) ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 stat-card p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-primary-subtle text-primary p-3 rounded-3 fs-4">
                    <i class="bi bi-receipt"></i>
                </span>
                <div>
                    <span class="text-secondary small fw-semibold text-uppercase">Total Transactions</span>
                    <h3 class="fw-bold text-dark font-monospace mb-0"><?= esc($totalSales ?? 0) ?></h3>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card border-0 shadow-sm rounded-3 stat-card p-3 bg-white">
            <div class="d-flex align-items-center gap-3">
                <span class="badge bg-info-subtle text-info p-3 rounded-3 fs-4">
                    <i class="bi bi-boxes"></i>
                </span>
                <div>
                    <span class="text-secondary small fw-semibold text-uppercase">Units Sold</span>
                    <h3 class="fw-bold text-dark font-monospace mb-0"><?= esc($totalItems ?? 0) ?></h3>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4 py-3" style="width: 90px;">Txn #</th>
                    <th class="py-3">Date &amp; Time</th>
                    <th class="py-3">Product</th>
                    <th class="py-3">Customer</th>
                    <th class="py-3">Staff (Sold By)</th>
                    <th class="py-3 text-center">Qty</th>
                    <th class="py-3 text-end pe-4">Total Price</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($sales) && is_array($sales)): ?>
                    <?php foreach ($sales as $sale): ?>
                        <?php
                            $hasCustomImage = !empty($sale['product_image']) && is_file(FCPATH . 'uploads/products/' . $sale['product_image']);
                            $prodImgUrl = $hasCustomImage
                                ? base_url('uploads/products/' . $sale['product_image'])
                                : base_url('images/product-placeholder.svg');

                            $hasAvatar = !empty($sale['staff_avatar']) && is_file(FCPATH . 'uploads/avatars/' . $sale['staff_avatar']);
                            $avatarUrl = $hasAvatar
                                ? base_url('uploads/avatars/' . $sale['staff_avatar'])
                                : base_url('images/avatar-placeholder.svg');
                        ?>
                        <tr>
                            <td class="ps-4 font-monospace">
                                <span class="badge bg-light text-secondary border">#<?= esc($sale['id']) ?></span>
                            </td>
                            <td class="font-monospace small text-secondary">
                                <?= esc(date('M d, Y h:i A', strtotime($sale['created_at']))) ?>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="<?= esc($prodImgUrl, 'attr') ?>"
                                         alt="<?= esc($sale['product_name'] ?? 'Product', 'attr') ?>"
                                         class="product-thumbnail shadow-sm"
                                         style="width: 36px; height: 36px;">
                                    <div>
                                        <span class="fw-semibold text-dark d-block"><?= esc($sale['product_name'] ?? 'Deleted Product') ?></span>
                                        <span class="small text-muted font-monospace">₱<?= number_format((float) ($sale['product_price'] ?? 0), 2) ?> each</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <?php if (!empty($sale['customer_name'])): ?>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-person-check text-primary"></i>
                                        <span class="fw-semibold text-dark"><?= esc($sale['customer_name']) ?></span>
                                    </div>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">
                                        <i class="bi bi-person-slash me-1"></i>Walk-in
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <img src="<?= esc($avatarUrl, 'attr') ?>"
                                         alt="<?= esc($sale['staff_username'] ?? 'Staff', 'attr') ?>"
                                         class="avatar-image shadow-sm"
                                         style="width: 28px; height: 28px;">
                                    <span class="text-dark small fw-medium"><?= esc($sale['staff_name'] ?? $sale['staff_username'] ?? 'Staff') ?></span>
                                </div>
                            </td>
                            <td class="text-center font-monospace fw-bold text-dark">
                                <?= esc($sale['quantity']) ?>
                            </td>
                            <td class="text-end pe-4 font-monospace fw-bold text-success fs-6">
                                ₱<?= number_format((float) $sale['total_price'], 2) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-receipt fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            <p class="mb-2">No sales transactions have been recorded yet.</p>
                            <a href="<?= esc(site_url('sales/new'), 'attr') ?>" class="btn btn-sm btn-primary">
                                Record Your First Sale
                            </a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
