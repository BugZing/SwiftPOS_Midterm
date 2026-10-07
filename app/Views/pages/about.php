<div class="mb-4">
    <h2 class="fw-bold text-dark mb-1">About Complete POS (Midterms)</h2>
    <p class="text-secondary">Architectural specification, database relational design, and feature documentation.</p>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm p-4 rounded-3 mb-4">
            <h4 class="fw-bold text-dark mb-3">Project Scope &amp; Architecture</h4>
            <p class="text-secondary lh-lg mb-3">
                <strong>Complete POS MIDTERMS</strong> is a full-featured Point-of-Sale web application built on the <strong>CodeIgniter 4</strong> MVC framework with a relational <strong>MySQL</strong> backend.
                It provides complete inventory product catalog management, customer relationship management, secure staff account administration, a live sales checkout terminal, and a comprehensive sales audit log.
            </p>
            <div class="row g-2">
                <div class="col-sm-6">
                    <div class="p-2 border rounded-2 bg-light small">
                        <i class="bi bi-shield-lock-fill text-primary me-1"></i>
                        <strong>Strict Authentication:</strong> All management pages guarded behind authentication filter.
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="p-2 border rounded-2 bg-light small">
                        <i class="bi bi-currency-exchange text-success me-1"></i>
                        <strong>Stock Integrity:</strong> Auto stock deduction &amp; strict rejection when quantity exceeds stock.
                    </div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm p-4 rounded-3 mb-4">
            <h4 class="fw-bold text-dark mb-3">Database Relational Schema</h4>
            <div class="table-responsive">
                <table class="table table-bordered table-sm small mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Table</th>
                            <th>Primary Key</th>
                            <th>Key Attributes</th>
                            <th>Relationships &amp; Constraints</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-bold font-monospace">products</td>
                            <td class="font-monospace">id</td>
                            <td>name, price, stock_quantity, image, created_at</td>
                            <td>Inventory items referenced by <code>sales.product_id</code></td>
                        </tr>
                        <tr>
                            <td class="fw-bold font-monospace">customers</td>
                            <td class="font-monospace">id</td>
                            <td>full_name, email (UNIQUE), phone, created_at</td>
                            <td>Patrons optionally linked in <code>sales.customer_id</code></td>
                        </tr>
                        <tr>
                            <td class="fw-bold font-monospace">users</td>
                            <td class="font-monospace">id</td>
                            <td>username (UNIQUE), full_name, password (hash), avatar, created_at</td>
                            <td>Staff users referenced by <code>sales.sold_by</code></td>
                        </tr>
                        <tr>
                            <td class="fw-bold font-monospace">sales</td>
                            <td class="font-monospace">id</td>
                            <td>quantity, total_price, created_at</td>
                            <td>
                                <code>FK: product_id &rarr; products(id)</code><br>
                                <code>FK: customer_id &rarr; customers(id) [Nullable]</code><br>
                                <code>FK: sold_by &rarr; users(id)</code>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card border-0 shadow-sm p-4 rounded-3">
            <h4 class="fw-bold text-dark mb-3">Core Modules</h4>
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 border h-100">
                        <div class="text-primary fw-bold mb-1"><i class="bi bi-box-seam me-1"></i> 1. Product Management</div>
                        <small class="text-muted">Full catalog CRUD with display image upload (resized &amp; fitted), real-time stock tracking, and pricing.</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 border h-100">
                        <div class="text-success fw-bold mb-1"><i class="bi bi-cart-check me-1"></i> 2. Record Sale Terminal</div>
                        <small class="text-muted">Interactive POS checkout. Select product, optional customer, quantity validation, stock decrement, and total price calculation.</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 border h-100">
                        <div class="text-info fw-bold mb-1"><i class="bi bi-clock-history me-1"></i> 3. Sales Audit Trail</div>
                        <small class="text-muted">Complete historical transaction ledger joined with products, customers (or walk-ins), and the processing cashier.</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 border h-100">
                        <div class="text-dark fw-bold mb-1"><i class="bi bi-people me-1"></i> 4. Customer &amp; Staff Administration</div>
                        <small class="text-muted">Full CRUD for customers and staff accounts with display-ready avatar processing and bcrypt password encryption.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm p-4 rounded-3 mb-4">
            <h5 class="fw-bold text-dark mb-3">System Specification</h5>
            <ul class="list-group list-group-flush small">
                <li class="list-group-item d-flex justify-content-between px-0 py-2">
                    <span class="text-muted">Project Name</span>
                    <span class="fw-semibold">Complete POS MIDTERMS</span>
                </li>
                <li class="list-group-item d-flex justify-content-between px-0 py-2">
                    <span class="text-muted">Framework</span>
                    <span class="fw-semibold">CodeIgniter 4</span>
                </li>
                <li class="list-group-item d-flex justify-content-between px-0 py-2">
                    <span class="text-muted">Architecture</span>
                    <span class="fw-semibold">Model-View-Controller (MVC)</span>
                </li>
                <li class="list-group-item d-flex justify-content-between px-0 py-2">
                    <span class="text-muted">Database</span>
                    <span class="badge bg-success-subtle text-success">MySQL (InnoDB)</span>
                </li>
                <li class="list-group-item d-flex justify-content-between px-0 py-2">
                    <span class="text-muted">Password Security</span>
                    <span class="badge bg-primary-subtle text-primary">Bcrypt Hash</span>
                </li>
                <li class="list-group-item d-flex justify-content-between px-0 py-2">
                    <span class="text-muted">Image Processing</span>
                    <span class="badge bg-info-subtle text-info">GD Image Service</span>
                </li>
                <li class="list-group-item d-flex justify-content-between px-0 py-2">
                    <span class="text-muted">CSS / UI</span>
                    <span class="fw-semibold">Bootstrap 5.3.3</span>
                </li>
            </ul>
        </div>

        <div class="card border-0 shadow-sm p-4 rounded-3 bg-light">
            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-key-fill text-warning me-1"></i> Default Staff Login</h6>
            <div class="small text-secondary mb-2">Use these initial credentials to sign in and test the system:</div>
            <div class="p-2 bg-white rounded border font-monospace small">
                <div><strong>Username:</strong> admin_reign</div>
                <div><strong>Password:</strong> Admin123!</div>
            </div>
        </div>
    </div>
</div>
