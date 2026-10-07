<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Point of Sale Dashboard</h2>
        <p class="text-secondary mb-0">Live retail summary, inventory health, client accounts, and recent transactions.</p>
    </div>
    <?php if (session()->get('auth_user_id')): ?>
        <div class="d-flex align-items-center gap-2">
            <a class="btn btn-primary" href="<?= esc(site_url('sales/new'), 'attr') ?>">
                <i class="bi bi-cart-plus me-1"></i> Record Sale
            </a>
            <a class="btn btn-outline-primary" href="<?= esc(site_url('products/new'), 'attr') ?>">
                <i class="bi bi-plus-lg me-1"></i> Add Product
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- Key Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card p-3 rounded-3 bg-white border-0 shadow-sm">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary small fw-semibold text-uppercase">Total Revenue</span>
                    <h3 class="fw-bold my-1 text-success font-monospace">₱<?= number_format((float) ($totalRevenue ?? 0), 2) ?></h3>
                    <span class="text-muted small"><?= esc($totalSales ?? 0) ?> completed transactions</span>
                </div>
                <div class="bg-success-subtle text-success p-3 rounded-3 fs-4">
                    <i class="bi bi-cash-stack"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card p-3 rounded-3 bg-white border-0 shadow-sm">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary small fw-semibold text-uppercase">Products in Stock</span>
                    <h3 class="fw-bold my-1 text-dark"><?= esc($productCount ?? 0) ?></h3>
                    <?php if (($lowStockCount ?? 0) > 0): ?>
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle small">
                            <i class="bi bi-exclamation-triangle me-1"></i><?= esc($lowStockCount) ?> Low/Out of Stock
                        </span>
                    <?php else: ?>
                        <span class="text-success small fw-medium"><i class="bi bi-check-circle"></i> Stock Healthy</span>
                    <?php endif; ?>
                </div>
                <div class="bg-primary-subtle text-primary p-3 rounded-3 fs-4">
                    <i class="bi bi-box-seam"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card p-3 rounded-3 bg-white border-0 shadow-sm">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary small fw-semibold text-uppercase">Customers</span>
                    <h3 class="fw-bold my-1 text-dark"><?= esc($customerCount ?? 0) ?></h3>
                    <span class="text-primary small fw-medium"><i class="bi bi-people"></i> Registered Patrons</span>
                </div>
                <div class="bg-info-subtle text-info p-3 rounded-3 fs-4">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="card stat-card p-3 rounded-3 bg-white border-0 shadow-sm">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-secondary small fw-semibold text-uppercase">Staff Accounts</span>
                    <h3 class="fw-bold my-1 text-dark"><?= esc($userCount ?? 0) ?></h3>
                    <span class="text-secondary small"><i class="bi bi-shield-lock"></i> Authenticated Users</span>
                </div>
                <div class="bg-secondary-subtle text-secondary p-3 rounded-3 fs-4">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Navigation Shortcut Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 p-3 rounded-3 border-0 shadow-sm">
            <div class="d-flex align-items-start gap-3">
                <div class="bg-primary text-white p-3 rounded-3">
                    <i class="bi bi-box-seam fs-3"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1">Products</h5>
                    <p class="text-secondary small mb-3">Inventory catalog, pricing, and prepared product images.</p>
                    <a href="<?= esc(site_url('products'), 'attr') ?>" class="btn btn-outline-primary btn-sm fw-medium">
                        Open Inventory <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 p-3 rounded-3 border-0 shadow-sm">
            <div class="d-flex align-items-start gap-3">
                <div class="bg-success text-white p-3 rounded-3">
                    <i class="bi bi-cart4 fs-3"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1">Record Sale</h5>
                    <p class="text-secondary small mb-3">Process sales, select customer, and auto-decrease inventory.</p>
                    <a href="<?= esc(site_url('sales/new'), 'attr') ?>" class="btn btn-outline-success btn-sm fw-medium">
                        Launch POS <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 p-3 rounded-3 border-0 shadow-sm">
            <div class="d-flex align-items-start gap-3">
                <div class="bg-info text-white p-3 rounded-3">
                    <i class="bi bi-receipt fs-3"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1">Sales History</h5>
                    <p class="text-secondary small mb-3">Comprehensive audit trail with items, staff, customers, and totals.</p>
                    <a href="<?= esc(site_url('sales/history'), 'attr') ?>" class="btn btn-outline-info btn-sm fw-medium">
                        View Audit Log <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-xl-3">
        <div class="card h-100 p-3 rounded-3 border-0 shadow-sm">
            <div class="d-flex align-items-start gap-3">
                <div class="bg-dark text-white p-3 rounded-3">
                    <i class="bi bi-people fs-3"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-1">Customers</h5>
                    <p class="text-secondary small mb-3">Full CRUD patron management and contact records.</p>
                    <a href="<?= esc(site_url('customers'), 'attr') ?>" class="btn btn-outline-dark btn-sm fw-medium">
                        Manage Clients <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions Table -->
<div class="card border-0 shadow-sm rounded-3 overflow-hidden">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
        <h5 class="card-title fw-bold text-dark mb-0">
            <i class="bi bi-clock-history text-primary me-2"></i>Recent Transactions
        </h5>
        <a href="<?= esc(site_url('sales/history'), 'attr') ?>" class="btn btn-sm btn-outline-secondary">
            View All Sales <i class="bi bi-arrow-right ms-1"></i>
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4 py-3" style="width: 80px;">Txn #</th>
                    <th class="py-3">Date</th>
                    <th class="py-3">Product</th>
                    <th class="py-3">Customer</th>
                    <th class="py-3">Staff</th>
                    <th class="py-3 text-center">Qty</th>
                    <th class="py-3 text-end pe-4">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($recentSales) && is_array($recentSales)): ?>
                    <?php foreach ($recentSales as $sale): ?>
                        <tr>
                            <td class="ps-4 font-monospace">
                                <span class="badge bg-light text-secondary border">#<?= esc($sale['id']) ?></span>
                            </td>
                            <td class="font-monospace small text-secondary">
                                <?= esc(date('M d, Y h:i A', strtotime($sale['created_at']))) ?>
                            </td>
                            <td class="fw-semibold text-dark">
                                <?= esc($sale['product_name'] ?? 'Product') ?>
                            </td>
                            <td>
                                <?php if (!empty($sale['customer_name'])): ?>
                                    <span class="text-dark"><?= esc($sale['customer_name']) ?></span>
                                <?php else: ?>
                                    <span class="badge bg-secondary-subtle text-secondary small">Walk-in</span>
                                <?php endif; ?>
                            </td>
                            <td class="small text-secondary">
                                <?= esc($sale['staff_name'] ?? $sale['staff_username'] ?? 'Staff') ?>
                            </td>
                            <td class="text-center font-monospace fw-bold">
                                <?= esc($sale['quantity']) ?>
                            </td>
                            <td class="text-end pe-4 font-monospace fw-bold text-success">
                                ₱<?= number_format((float) $sale['total_price'], 2) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            No recent sales transactions recorded.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
