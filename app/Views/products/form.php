<?php
    helper('form');
    $isEdit = ($mode ?? 'new') === 'edit';
    $values = array_replace($product ?? [], $values ?? []);
    $errors = $errors ?? [];
    $formAction = $isEdit
        ? site_url('products/' . (int) ($product['id'] ?? 0))
        : site_url('products');

    $currentImage = $product['image'] ?? null;
    $hasImage = is_string($currentImage) && is_file(FCPATH . 'uploads/products/' . $currentImage);
    $imageUrl = $hasImage
        ? base_url('uploads/products/' . $currentImage)
        : base_url('images/product-placeholder.svg');
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1"><?= esc($isEdit ? 'Edit Product' : 'New Product') ?></h2>
        <p class="text-secondary mb-0">Enter product information, pricing, stock levels, and upload display image.</p>
    </div>
    <a class="btn btn-outline-secondary" href="<?= esc(site_url('products'), 'attr') ?>">
        <i class="bi bi-arrow-left me-1"></i> Back to Inventory
    </a>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-4">
        <?php if ($errors !== []): ?>
            <div class="alert alert-danger" role="alert">
                <p class="fw-semibold mb-1">Please correct the following errors:</p>
                <ul class="mb-0">
                    <?php foreach ($errors as $message): ?>
                        <li><?= esc($message) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= esc($formAction, 'attr') ?>" method="post" enctype="multipart/form-data" novalidate>
            <?= csrf_field() ?>

            <div class="row g-4">
                <div class="col-md-7">
                    <div class="mb-3">
                        <label for="name" class="form-label fw-semibold">Product Name <span class="text-danger">*</span></label>
                        <input type="text" id="name" name="name" maxlength="100" required
                               class="form-control<?= isset($errors['name']) ? ' is-invalid' : '' ?>"
                               value="<?= esc($values['name'] ?? '', 'attr') ?>"
                               placeholder="e.g. Caramel Macchiato">
                        <?php if (isset($errors['name'])): ?>
                            <div class="invalid-feedback"><?= esc($errors['name']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="row g-3">
                        <div class="col-sm-6 mb-3">
                            <label for="price" class="form-label fw-semibold">Price (₱) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">₱</span>
                                <input type="number" id="price" name="price" step="0.01" min="0" required
                                       class="form-control<?= isset($errors['price']) ? ' is-invalid' : '' ?>"
                                       value="<?= esc($values['price'] ?? '', 'attr') ?>"
                                       placeholder="0.00">
                                <?php if (isset($errors['price'])): ?>
                                    <div class="invalid-feedback"><?= esc($errors['price']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="col-sm-6 mb-3">
                            <label for="stock_quantity" class="form-label fw-semibold">Stock Quantity <span class="text-danger">*</span></label>
                            <input type="number" id="stock_quantity" name="stock_quantity" min="0" required
                                   class="form-control<?= isset($errors['stock_quantity']) ? ' is-invalid' : '' ?>"
                                   value="<?= esc($values['stock_quantity'] ?? '0', 'attr') ?>"
                                   placeholder="0">
                            <?php if (isset($errors['stock_quantity'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['stock_quantity']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-5 border-start-md ps-md-4">
                    <label class="form-label fw-semibold">Product Display Image</label>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="<?= esc($imageUrl, 'attr') ?>"
                             alt="Product preview"
                             id="image-preview"
                             class="product-preview shadow-sm">
                        <div>
                            <span class="small text-muted d-block mb-1">
                                <?= $hasImage ? 'Custom image uploaded.' : 'Default placeholder.' ?>
                            </span>
                            <span class="badge bg-light text-secondary border">Prepared for Display</span>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label for="image" class="form-label small text-muted">
                            <?= $isEdit ? 'Upload New Image (Replaces current)' : 'Upload Product Image' ?>
                        </label>
                        <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/webp"
                               class="form-control<?= isset($errors['image']) ? ' is-invalid' : '' ?>"
                               onchange="previewImage(this)">
                        <?php if (isset($errors['image'])): ?>
                            <div class="invalid-feedback"><?= esc($errors['image']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-text small">
                        JPG, PNG, or WebP. Max 3MB. Images are automatically cropped and prepared for display in thumbnail and POS views.
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex justify-content-end gap-2">
                <a class="btn btn-outline-secondary px-4" href="<?= esc(site_url('products'), 'attr') ?>">Cancel</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-lg me-1"></i> <?= esc($isEdit ? 'Save Changes' : 'Create Product') ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('image-preview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
