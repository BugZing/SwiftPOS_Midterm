<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Product Inventory</h2>
        <p class="text-secondary mb-0">Manage catalog items, pricing, inventory stock, and product display images.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-white text-secondary border px-3 py-2 rounded-3 shadow-sm">
            <i class="bi bi-box-seam me-1 text-primary"></i> <?= esc(count($products ?? [])) ?> Products
        </span>
        <a class="btn btn-primary" href="<?= esc(site_url('products/new'), 'attr') ?>">
            <i class="bi bi-plus-lg me-1"></i> New Product
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4 py-3" style="width: 80px;">ID</th>
                    <th class="py-3" style="width: 70px;">Image</th>
                    <th class="py-3">Product Name</th>
                    <th class="py-3 text-end">Price</th>
                    <th class="py-3 text-center">Stock</th>
                    <th class="py-3">Created</th>
                    <th class="py-3 text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($products) && is_array($products)): ?>
                    <?php foreach ($products as $product): ?>
                        <?php
                            $stock = (int) $product['stock_quantity'];
                            $hasCustomImage = !empty($product['image']) && is_file(FCPATH . 'uploads/products/' . $product['image']);
                            $imageUrl = $hasCustomImage
                                ? base_url('uploads/products/' . $product['image'])
                                : base_url('images/product-placeholder.svg');
                        ?>
                        <tr>
                            <td class="ps-4">
                                <span class="badge bg-light text-dark border font-monospace">#<?= esc($product['id']) ?></span>
                            </td>
                            <td>
                                <img src="<?= esc($imageUrl, 'attr') ?>"
                                     alt="<?= esc($product['name'], 'attr') ?>"
                                     class="product-thumbnail shadow-sm">
                            </td>
                            <td>
                                <span class="fw-semibold text-dark d-block"><?= esc($product['name']) ?></span>
                            </td>
                            <td class="text-end font-monospace fw-semibold text-dark">
                                ₱<?= number_format((float) $product['price'], 2) ?>
                            </td>
                            <td class="text-center">
                                <?php if ($stock <= 0): ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                        <i class="bi bi-x-circle me-1"></i>Out of Stock (0)
                                    </span>
                                <?php elseif ($stock <= 5): ?>
                                    <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1">
                                        <i class="bi bi-exclamation-triangle me-1"></i>Low Stock (<?= esc($stock) ?>)
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">
                                        <i class="bi bi-check-circle me-1"></i><?= esc($stock) ?> in stock
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-secondary small font-monospace">
                                <?= esc(date('M d, Y', strtotime($product['created_at']))) ?>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <?php if ($stock > 0): ?>
                                        <a href="<?= esc(site_url('sales/new?product_id=' . (int) $product['id']), 'attr') ?>"
                                           class="btn btn-sm btn-outline-info"
                                           title="Sell Product">
                                            <i class="bi bi-cart-plus"></i> Sell
                                        </a>
                                    <?php endif; ?>
                                    <a href="<?= esc(site_url('products/' . (int) $product['id'] . '/edit'), 'attr') ?>"
                                       class="btn btn-sm btn-outline-primary"
                                       title="Edit Product">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="post" action="<?= esc(site_url('products/' . (int) $product['id'] . '/delete'), 'attr') ?>"
                                          class="d-inline m-0"
                                          onsubmit="return confirm('Are you sure you want to delete product '<?= esc($product['name'], 'js') ?>'?');">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Product">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-box-seam fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            <p class="mb-2">No products registered in catalog yet.</p>
                            <a href="<?= esc(site_url('products/new'), 'attr') ?>" class="btn btn-sm btn-primary">
                                Add Your First Product
                            </a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
