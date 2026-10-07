<?php
    helper('form');
    $isEdit = ($mode ?? 'new') === 'edit';
    $values = array_replace($user ?? [], $values ?? []);
    $errors = $errors ?? [];
    $formAction = $isEdit
        ? site_url('users/' . (int) ($user['id'] ?? 0))
        : site_url('users');

    $currentAvatar = $user['avatar'] ?? null;
    $hasAvatar = is_string($currentAvatar) && is_file(FCPATH . 'uploads/avatars/' . $currentAvatar);
    $avatarUrl = $hasAvatar
        ? base_url('uploads/avatars/' . $currentAvatar)
        : base_url('images/avatar-placeholder.svg');
?>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1"><?= esc($isEdit ? 'Edit Staff Account' : 'New Staff Account') ?></h2>
        <p class="text-secondary mb-0">Set up credentials, account info, and profile avatar.</p>
    </div>
    <a class="btn btn-outline-secondary" href="<?= esc(site_url('users'), 'attr') ?>">
        <i class="bi bi-arrow-left me-1"></i> Back to Staff Accounts
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
                        <label for="username" class="form-label fw-semibold">Username <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">@</span>
                            <input type="text" id="username" name="username" maxlength="50" required
                                   class="form-control<?= isset($errors['username']) ? ' is-invalid' : '' ?>"
                                   value="<?= esc($values['username'] ?? '', 'attr') ?>"
                                   placeholder="e.g. cashier_john">
                            <?php if (isset($errors['username'])): ?>
                                <div class="invalid-feedback"><?= esc($errors['username']) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="full_name" class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                        <input type="text" id="full_name" name="full_name" maxlength="100" required
                               class="form-control<?= isset($errors['full_name']) ? ' is-invalid' : '' ?>"
                               value="<?= esc($values['full_name'] ?? '', 'attr') ?>"
                               placeholder="e.g. John Doe">
                        <?php if (isset($errors['full_name'])): ?>
                            <div class="invalid-feedback"><?= esc($errors['full_name']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-semibold">
                            Password <?= $isEdit ? '<span class="text-muted fw-normal">(Leave blank to keep existing)</span>' : '<span class="text-danger">*</span>' ?>
                        </label>
                        <input type="password" id="password" name="password" minlength="8" maxlength="72"
                               class="form-control<?= isset($errors['password']) ? ' is-invalid' : '' ?>"
                               placeholder="<?= $isEdit ? '••••••••' : 'Minimum 8 characters' ?>"
                               <?= $isEdit ? '' : 'required' ?>>
                        <?php if (isset($errors['password'])): ?>
                            <div class="invalid-feedback"><?= esc($errors['password']) ?></div>
                        <?php endif; ?>
                        <div class="form-text small">Passwords are securely hashed using bcrypt before database storage.</div>
                    </div>
                </div>

                <div class="col-md-5 border-start-md ps-md-4">
                    <label class="form-label fw-semibold">Staff Avatar</label>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="<?= esc($avatarUrl, 'attr') ?>"
                             alt="Avatar preview"
                             id="avatar-preview"
                             class="avatar-preview shadow-sm">
                        <div>
                            <span class="small text-muted d-block mb-1">
                                <?= $hasAvatar ? 'Custom avatar uploaded.' : 'Default placeholder.' ?>
                            </span>
                            <span class="badge bg-light text-secondary border">160x160 Display Ready</span>
                        </div>
                    </div>

                    <div class="mb-2">
                        <label for="avatar" class="form-label small text-muted">
                            <?= $isEdit ? 'Upload New Avatar (Replaces current)' : 'Upload Avatar' ?>
                        </label>
                        <input type="file" id="avatar" name="avatar" accept="image/jpeg,image/png,image/webp"
                               class="form-control<?= isset($errors['avatar']) ? ' is-invalid' : '' ?>"
                               onchange="previewAvatar(this)">
                        <?php if (isset($errors['avatar'])): ?>
                            <div class="invalid-feedback"><?= esc($errors['avatar']) ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-text small">
                        JPG, PNG, or WebP. Max 2MB. Automatically centered and fitted for display.
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex justify-content-end gap-2">
                <a class="btn btn-outline-secondary px-4" href="<?= esc(site_url('users'), 'attr') ?>">Cancel</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-lg me-1"></i> <?= esc($isEdit ? 'Save Changes' : 'Create Staff Account') ?>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function previewAvatar(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatar-preview').src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
