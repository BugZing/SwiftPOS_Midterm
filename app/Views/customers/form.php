<?php
    helper('form');
    $isEdit = ($mode ?? 'new') === 'edit';
    $values = array_replace($customer ?? [], $values ?? []);
    $errors = $errors ?? [];
    $formAction = $isEdit
        ? site_url('customers/' . (int) ($customer['id'] ?? 0))
        : site_url('customers');
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1"><?= esc($isEdit ? 'Edit Customer' : 'New Customer') ?></h2>
        <p class="text-secondary mb-0">Enter the customer profile details below.</p>
    </div>
    <a class="btn btn-outline-secondary" href="<?= esc(site_url('customers'), 'attr') ?>">
        <i class="bi bi-arrow-left me-1"></i> Back to Customers
    </a>
</div>

<div class="card border-0 shadow-sm rounded-3">
    <div class="card-body p-4">
        <?php if ($errors !== []): ?>
            <div class="alert alert-danger" role="alert">
                <p class="fw-semibold mb-1">Please correct the following:</p>
                <ul class="mb-0">
                    <?php foreach ($errors as $message): ?>
                        <li><?= esc($message) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= esc($formAction, 'attr') ?>" method="post" novalidate>
            <?= csrf_field() ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="full_name" class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                    <input type="text" id="full_name" name="full_name" maxlength="100" required
                           class="form-control<?= isset($errors['full_name']) ? ' is-invalid' : '' ?>"
                           value="<?= esc($values['full_name'] ?? '', 'attr') ?>"
                           placeholder="e.g. Maria Santos">
                    <?php if (isset($errors['full_name'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['full_name']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label for="email" class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                    <input type="email" id="email" name="email" maxlength="100" required
                           class="form-control<?= isset($errors['email']) ? ' is-invalid' : '' ?>"
                           value="<?= esc($values['email'] ?? '', 'attr') ?>"
                           placeholder="e.g. maria.santos@email.com">
                    <?php if (isset($errors['email'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['email']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="col-md-6">
                    <label for="phone" class="form-label fw-semibold">Phone Number</label>
                    <input type="tel" id="phone" name="phone" maxlength="20"
                           class="form-control<?= isset($errors['phone']) ? ' is-invalid' : '' ?>"
                           value="<?= esc($values['phone'] ?? '', 'attr') ?>"
                           placeholder="e.g. +63 917 123 4567">
                    <?php if (isset($errors['phone'])): ?>
                        <div class="invalid-feedback"><?= esc($errors['phone']) ?></div>
                    <?php endif; ?>
                    <div class="form-text small">Optional mobile or landline contact number.</div>
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex justify-content-end gap-2">
                <a class="btn btn-outline-secondary px-4" href="<?= esc(site_url('customers'), 'attr') ?>">Cancel</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-lg me-1"></i> <?= esc($isEdit ? 'Save Changes' : 'Create Customer') ?>
                </button>
            </div>
        </form>
    </div>
</div>
