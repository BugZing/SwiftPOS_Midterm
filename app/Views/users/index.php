<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
    <div>
        <h2 class="fw-bold text-dark mb-1">Staff Accounts</h2>
        <p class="text-secondary mb-0">System operators, cashiers, and management accounts.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-white text-secondary border px-3 py-2 rounded-3 shadow-sm">
            <i class="bi bi-person-badge me-1 text-primary"></i> <?= esc(count($users ?? [])) ?> Staff Accounts
        </span>
        <a class="btn btn-primary" href="<?= esc(site_url('users/new'), 'attr') ?>">
            <i class="bi bi-plus-lg me-1"></i> New Staff Member
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th class="ps-4 py-3" style="width: 80px;">ID</th>
                    <th class="py-3" style="width: 60px;">Avatar</th>
                    <th class="py-3">Staff Name</th>
                    <th class="py-3">Username</th>
                    <th class="py-3">Created</th>
                    <th class="py-3 text-end pe-4">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($users) && is_array($users)): ?>
                    <?php foreach ($users as $user): ?>
                        <?php
                            $isCurrent = (int) $user['id'] === (int) session()->get('auth_user_id');
                            $hasAvatar = !empty($user['avatar']) && is_file(FCPATH . 'uploads/avatars/' . $user['avatar']);
                            $avatarUrl = $hasAvatar
                                ? base_url('uploads/avatars/' . $user['avatar'])
                                : base_url('images/avatar-placeholder.svg');
                        ?>
                        <tr>
                            <td class="ps-4">
                                <span class="badge bg-light text-dark border font-monospace">#<?= esc($user['id']) ?></span>
                            </td>
                            <td>
                                <img src="<?= esc($avatarUrl, 'attr') ?>"
                                     alt="<?= esc($user['username'], 'attr') ?>"
                                     class="avatar-image shadow-sm">
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-semibold text-dark"><?= esc($user['full_name']) ?></span>
                                    <?php if ($isCurrent): ?>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle small">You</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <span class="font-monospace text-secondary small">
                                    @<?= esc($user['username']) ?>
                                </span>
                            </td>
                            <td class="text-secondary small font-monospace">
                                <?= esc(date('M d, Y', strtotime($user['created_at']))) ?>
                            </td>
                            <td class="text-end pe-4">
                                <div class="d-inline-flex align-items-center gap-1">
                                    <a href="<?= esc(site_url('users/' . (int) $user['id'] . '/edit'), 'attr') ?>"
                                       class="btn btn-sm btn-outline-primary"
                                       title="Edit Staff Member">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <?php if (!$isCurrent): ?>
                                        <form method="post" action="<?= esc(site_url('users/' . (int) $user['id'] . '/delete'), 'attr') ?>"
                                              class="d-inline m-0"
                                              onsubmit="return confirm('Are you sure you want to delete staff account '<?= esc($user['username'], 'js') ?>'?');">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Staff Account">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-outline-secondary opacity-50" disabled title="Cannot delete your active account">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-person-badge fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            <p class="mb-2">No staff accounts found.</p>
                            <a href="<?= esc(site_url('users/new'), 'attr') ?>" class="btn btn-sm btn-primary">
                                Add Staff Member
                            </a>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
