# Complete POS MIDTERMS

Point-of-Sale web application built on **CodeIgniter 4** (MVC), **MySQL**, and **Bootstrap 5.3**.

---

## Website Login Credentials

- **Username:** `admin_reign`
- **Password:** `Admin123!`

*(Alternative staff accounts seeded: `mgr_echo` / `Admin123!`, `cashier_jane` / `Admin123!`)*

---

## Database Relational Design & Schema

The database utilizes the following relational schema with InnoDB foreign key integrity constraints:

```sql
CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  stock_quantity INT NOT NULL DEFAULT 0,
  image VARCHAR(255),
  created_at DATETIME NOT NULL
);

CREATE TABLE customers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  phone VARCHAR(20),
  created_at DATETIME NOT NULL,
  CONSTRAINT uq_customers_email UNIQUE (email)
);

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  full_name VARCHAR(100) NOT NULL,
  password VARCHAR(255) NOT NULL,
  avatar VARCHAR(255),
  created_at DATETIME NOT NULL
);

CREATE TABLE sales (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  customer_id INT,
  sold_by INT NOT NULL,
  quantity INT NOT NULL,
  total_price DECIMAL(10,2) NOT NULL,
  created_at DATETIME NOT NULL,
  FOREIGN KEY (product_id) REFERENCES products(id),
  FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL,
  FOREIGN KEY (sold_by) REFERENCES users(id)
);
```

---

## Core Implemented Modules

### 1. Product Management (`/products`)
- **Catalog List:** View all products with display thumbnails, unit prices, formatted stock status badges (In Stock, Low Stock < 5, Out of Stock = 0), and created timestamps.
- **Add Product (`/products/new`):** Input name, price, stock quantity, and optional product image upload.
- **Image Preparation for Display:** Uploaded product images are automatically cropped and fitted to 300x300 center using CodeIgniter's GD Image Service (`service('image')->withFile(...)->fit(300, 300, 'center')`), saved to `public/uploads/products/`, with safe fallback to `public/images/product-placeholder.svg`.
- **Edit Product (`/products/{id}/edit`):** Update catalog details, adjust inventory stock, or replace product display image (safely cleans up orphaned image files).
- **Delete Product (`POST /products/{id}/delete`):** CSRF-protected deletion with confirmation. If a product has recorded sales history, deletion is blocked with a clear warning explaining that transaction history must be preserved.

### 2. Customer Management with Full CRUD (`/customers`)
- **Customer Directory (Read):** Lists all registered customers with Account ID, Avatar initial circle, Full Name, Email, Contact Phone, and Registration Date.
- **Add Customer (Create):** Register new patron with full name, unique email validation, and phone number.
- **Edit Customer (Update):** Modify customer details with uniqueness validation ignoring current ID.
- **Delete Customer (Delete):** Remove customer account with confirmation. Foreign key references in sales history are safely set to `NULL` to retain transaction totals while removing the customer profile.

### 3. Staff (User) Management with Full CRUD (`/users`)
- **Staff Directory (Read):** Displays staff list with display avatars, full names, usernames, and account creation dates.
- **Add Staff Member (Create):** Create account with unique username, full name, secure password, and optional avatar upload.
- **Avatar Preparation for Display:** Uploaded avatars are automatically resized and fitted into 160x160 circular format using CodeIgniter's image service (`service('image')->withFile(...)->fit(160, 160, 'center')`).
- **Hashed Passwords:** Passwords are encrypted before database insertion using PHP's standard `password_hash($password, PASSWORD_DEFAULT)` (Bcrypt).
- **Edit Staff Member (Update):** Update username, full name, optional password change (leaves hash unchanged if blank), and upload a new avatar (deleting previous avatar file).
- **Delete Staff Member (Delete):** Full deletion support with security guards: prevents deleting the currently authenticated staff account, and prevents deleting staff members who have recorded sales in sales history.

### 4. Authentication & Route Protection (`/login`, `AuthFilter`)
- **Authentication Filter (`app/Filters/AuthFilter.php`):** Inspects the active server-side session. Unauthenticated visitors attempting to access ANY management page (`/products`, `/customers`, `/users`, `/sales/new`, `/sales/history`) are immediately redirected to `/login` with an informative error flash message.
- **Login & Logout:** Secure login verifying Bcrypt password hashes, session ID regeneration upon sign-in to prevent session fixation, and complete session destruction upon POST logout.

### 5. Record Sale Page (`/sales/new`)
- **Interactive POS Terminal:** Logged-in staff member selects a product from the catalog and optionally chooses a registered customer (or defaults to Walk-in Customer).
- **Live Order Calculation:** JavaScript dynamically updates unit price, stock availability badge, and total price (`Unit Price × Quantity`).
- **Stock Integrity Check:** Server strictly validates requested quantity against `products.stock_quantity`. If `quantity > stock_quantity`, the sale is rejected with a clear message:
  > *"Sale rejected: Requested quantity (X) exceeds available stock (Y) for [Product]."*
- **Inventory Deduction:** On valid submission within a database transaction, `products.stock_quantity` decreases by the sold quantity, and the transaction is inserted into `sales` with `sold_by = session('auth_user_id')`.

### 6. Sales History Page (`/sales/history`)
- **Audit Ledger:** Lists all historical sales transactions joined across `products`, `customers`, and `users`.
- **Transaction Details:** Displays Transaction ID (#), Date & Time, Product Name & Thumbnail, Customer (Full Name or Walk-in badge), Cashier (avatar and staff name), Quantity sold, and Total Price.
- **Dashboard & Summary KPIs:** Aggregated cards showing Total Revenue (₱), Total Completed Transactions, and Total Units Sold.

---

## Requirements

- PHP 8.2+
- PHP extensions: `intl`, `mbstring`, `mysqli`, `gd` (for avatar and product image preparation)
- Composer 2
- MySQL or MariaDB

---

## Installation & Setup

1. **Install dependencies:**
   ```bash
   composer install
   ```

2. **Configure Environment:**
   Copy `env` to `.env` and set your database credentials:
   ```ini
   app.baseURL = 'http://localhost:8080/'
   database.default.hostname = localhost
   database.default.database = swiftpos
   database.default.username = root
   database.default.password = your_password
   database.default.DBDriver = MySQLi
   ```

3. **Initialize Database (Choose either Method A or Method B):**

   **Method A: Using the SQL file (Recommended for clean setup):**
   ```bash
   mysql -u root -p swiftpos < database/swiftpos.sql
   ```

   **Method B: Using CodeIgniter Migrations and Seeders:**
   ```bash
   php spark migrate
   php spark db:seed DatabaseSeeder
   ```

4. **Start Development Server:**
   ```bash
   php spark serve
   ```
   Open `http://localhost:8080/` in your browser.

---

## Automated Smoke & Route Verification

To verify that all management routes are protected, authentication works, and access control functions correctly, run:

```bash
SWIFTPOS_TEST_USERNAME='admin_reign' SWIFTPOS_TEST_PASSWORD='Admin123!'   python3 scripts/smoke_auth.py http://localhost:8080/
```
