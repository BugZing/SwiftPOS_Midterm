<?php
    helper('form');
    $errors = $errors ?? [];
    $selectedProductId = (int) ($values['product_id'] ?? 0);
    $selectedCustomerId = (int) ($values['customer_id'] ?? 0);
    $enteredQty = (int) ($values['quantity'] ?? 1);
    if ($enteredQty < 1) $enteredQty = 1;
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Record Sale</h2>
        <p class="text-secondary mb-0">Select a product, optional customer, enter quantity, and record transaction.</p>
    </div>
    <a class="btn btn-outline-secondary" href="<?= esc(site_url('sales/history'), 'attr') ?>">
        <i class="bi bi-clock-history me-1"></i> View Sales History
    </a>
</div>

<?php if ($errors !== []): ?>
    <div class="alert alert-danger shadow-sm mb-4" role="alert">
        <h6 class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Transaction Error:</h6>
        <ul class="mb-0">
            <?php foreach ($errors as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title fw-bold text-dark mb-0">
                    <i class="bi bi-cart4 text-primary me-2"></i>Sale Details
                </h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= esc(site_url('sales'), 'attr') ?>" method="post" id="recordSaleForm" novalidate>
                    <?= csrf_field() ?>

                    <!-- Product Selection -->
                    <div class="mb-4">
                        <label for="product_id" class="form-label fw-bold text-dark">
                            Select Product <span class="text-danger">*</span>
                        </label>
                        <select name="product_id" id="product_id" class="form-select form-select-lg" required>
                            <option value="" disabled <?= $selectedProductId === 0 ? 'selected' : '' ?>>
                                -- Choose a product from catalog --
                            </option>
                            <?php foreach ($products as $p): ?>
                                <?php
                                    $pStock = (int) $p['stock_quantity'];
                                    $pPrice = (float) $p['price'];
                                    $isSel  = ($selectedProductId === (int) $p['id']);
                                ?>
                                <option value="<?= esc($p['id'], 'attr') ?>"
                                        data-price="<?= esc($pPrice, 'attr') ?>"
                                        data-stock="<?= esc($pStock, 'attr') ?>"
                                        data-name="<?= esc($p['name'], 'attr') ?>"
                                        <?= $isSel ? 'selected' : '' ?>
                                        <?= $pStock <= 0 ? 'disabled' : '' ?>>
                                    <?= esc($p['name']) ?> — ₱<?= number_format($pPrice, 2) ?>
                                    (<?= $pStock <= 0 ? 'OUT OF STOCK' : $pStock . ' in stock' ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text small">Select the item being purchased. Only items in stock can be ordered.</div>
                    </div>

                    <!-- Customer Selection (Optional) -->
                    <div class="mb-4">
                        <label for="customer_id" class="form-label fw-bold text-dark">
                            Customer <span class="badge bg-secondary-subtle text-secondary fw-normal">Optional</span>
                        </label>
                        <select name="customer_id" id="customer_id" class="form-select">
                            <option value="" <?= $selectedCustomerId === 0 ? 'selected' : '' ?>>
                                Walk-in Customer (None)
                            </option>
                            <?php foreach ($customers as $c): ?>
                                <option value="<?= esc($c['id'], 'attr') ?>" <?= $selectedCustomerId === (int) $c['id'] ? 'selected' : '' ?>>
                                    <?= esc($c['full_name']) ?> (<?= esc($c['email']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text small">Associate transaction with an existing customer, or leave as Walk-in.</div>
                    </div>

                    <!-- Quantity Input -->
                    <div class="mb-4">
                        <label for="quantity" class="form-label fw-bold text-dark">
                            Quantity <span class="text-danger">*</span>
                        </label>
                        <div class="input-group input-group-lg">
                            <button type="button" class="btn btn-outline-secondary" id="btnMinus" onclick="adjustQty(-1)">
                                <i class="bi bi-dash-lg"></i>
                            </button>
                            <input type="number" id="quantity" name="quantity" min="1" step="1"
                                   class="form-control text-center fw-bold"
                                   value="<?= esc($enteredQty, 'attr') ?>" required>
                            <button type="button" class="btn btn-outline-secondary" id="btnPlus" onclick="adjustQty(1)">
                                <i class="bi bi-plus-lg"></i>
                            </button>
                        </div>
                        <div id="stock-warning" class="text-danger small fw-semibold mt-2 d-none">
                            <i class="bi bi-exclamation-octagon-fill me-1"></i>
                            Requested quantity exceeds available stock!
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" id="btnSubmitSale" class="btn btn-primary btn-lg shadow-sm py-3 fw-bold">
                            <i class="bi bi-check-circle-fill me-2"></i> Complete &amp; Record Sale
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Live Order Summary -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-3 bg-light">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title fw-bold text-dark mb-0">
                    <i class="bi bi-receipt-cutoff text-primary me-2"></i>Order Summary
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                    <span class="text-secondary">Cashier on Duty</span>
                    <span class="fw-semibold text-dark">
                        <i class="bi bi-person-badge text-primary me-1"></i>
                        <?= esc((string) session()->get('auth_username')) ?>
                    </span>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                    <span class="text-secondary">Selected Product</span>
                    <span class="fw-bold text-dark text-end" id="summary-product-name">None Selected</span>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                    <span class="text-secondary">Available Stock</span>
                    <span class="badge bg-secondary-subtle text-secondary" id="summary-stock-badge">0 items</span>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                    <span class="text-secondary">Unit Price</span>
                    <span class="font-monospace fw-semibold text-dark" id="summary-unit-price">₱0.00</span>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                    <span class="text-secondary">Quantity</span>
                    <span class="fw-bold text-dark font-monospace" id="summary-qty">1</span>
                </div>

                <div class="p-3 bg-white rounded-3 border mb-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="h5 fw-bold text-dark mb-0">Total Amount:</span>
                        <span class="h3 fw-bold text-primary font-monospace mb-0" id="summary-total-price">₱0.00</span>
                    </div>
                </div>

                <div class="alert alert-info py-2 small mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-info-circle-fill fs-5"></i>
                    <span>Submitting will immediately record the transaction and deduct inventory stock.</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const productSelect = document.getElementById('product_id');
const quantityInput = document.getElementById('quantity');
const summaryName = document.getElementById('summary-product-name');
const summaryStock = document.getElementById('summary-stock-badge');
const summaryUnitPrice = document.getElementById('summary-unit-price');
const summaryQty = document.getElementById('summary-qty');
const summaryTotal = document.getElementById('summary-total-price');
const stockWarning = document.getElementById('stock-warning');
const btnSubmit = document.getElementById('btnSubmitSale');

function updateSummary() {
    const selected = productSelect.options[productSelect.selectedIndex];
    const qty = parseInt(quantityInput.value) || 0;

    if (!selected || !selected.value) {
        summaryName.textContent = 'None Selected';
        summaryStock.textContent = '0 items';
        summaryStock.className = 'badge bg-secondary-subtle text-secondary';
        summaryUnitPrice.textContent = '₱0.00';
        summaryQty.textContent = qty;
        summaryTotal.textContent = '₱0.00';
        stockWarning.classList.add('d-none');
        btnSubmit.disabled = true;
        return;
    }

    const price = parseFloat(selected.dataset.price) || 0;
    const stock = parseInt(selected.dataset.stock) || 0;
    const name = selected.dataset.name || selected.text;

    summaryName.textContent = name;
    summaryUnitPrice.textContent = '₱' + price.toFixed(2);
    summaryQty.textContent = qty;

    const total = price * qty;
    summaryTotal.textContent = '₱' + total.toFixed(2);

    summaryStock.textContent = stock + ' in stock';
    if (stock <= 0) {
        summaryStock.className = 'badge bg-danger-subtle text-danger border border-danger-subtle';
    } else if (stock <= 5) {
        summaryStock.className = 'badge bg-warning-subtle text-warning-emphasis border border-warning-subtle';
    } else {
        summaryStock.className = 'badge bg-success-subtle text-success border border-success-subtle';
    }

    if (qty > stock) {
        stockWarning.classList.remove('d-none');
        stockWarning.innerHTML = '<i class="bi bi-exclamation-octagon-fill me-1"></i> Requested quantity (' + qty + ') exceeds available stock (' + stock + ')!';
        btnSubmit.disabled = true;
    } else if (qty < 1) {
        stockWarning.classList.remove('d-none');
        stockWarning.innerHTML = '<i class="bi bi-exclamation-octagon-fill me-1"></i> Quantity must be at least 1.';
        btnSubmit.disabled = true;
    } else {
        stockWarning.classList.add('d-none');
        btnSubmit.disabled = false;
    }
}

function adjustQty(amount) {
    let current = parseInt(quantityInput.value) || 1;
    current += amount;
    if (current < 1) current = 1;
    quantityInput.value = current;
    updateSummary();
}

productSelect.addEventListener('change', updateSummary);
quantityInput.addEventListener('input', updateSummary);
document.addEventListener('DOMContentLoaded', updateSummary);
</script>
