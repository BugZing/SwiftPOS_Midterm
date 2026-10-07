#!/usr/bin/env python3
"""Verification script for Complete POS MIDTERMS codebase integrity and requirements."""

import os
import sys

BASE_DIR = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))


def check(name, condition, details=""):
    status = "PASS" if condition else "FAIL"
    print(f"[{status}] {name}")
    if not condition and details:
        print(f"       Details: {details}")
    return condition


def verify():
    all_passed = True

    # 1. SQL Schema Check
    sql_path = os.path.join(BASE_DIR, "database", "swiftpos.sql")
    all_passed &= check("Database SQL file exists", os.path.isfile(sql_path))
    if os.path.isfile(sql_path):
        with open(sql_path, "r", encoding="utf-8") as f:
            sql = f.read().lower()
        all_passed &= check("SQL creates products table", "create table `products`" in sql or "create table products" in sql)
        all_passed &= check("SQL creates customers table", "create table `customers`" in sql or "create table customers" in sql)
        all_passed &= check("SQL creates users table", "create table `users`" in sql or "create table users" in sql)
        all_passed &= check("SQL creates sales table", "create table `sales`" in sql or "create table sales" in sql)
        all_passed &= check("SQL defines foreign keys on sales", "foreign key (`product_id`)" in sql and "foreign key (`sold_by`)" in sql)

    # 2. Migrations Check
    mig_dir = os.path.join(BASE_DIR, "app", "Database", "Migrations")
    mig_files = os.listdir(mig_dir) if os.path.isdir(mig_dir) else []
    all_passed &= check("Migration CreateProducts exists", any("CreateProducts" in f for f in mig_files))
    all_passed &= check("Migration CreateCustomers exists", any("CreateCustomers" in f for f in mig_files))
    all_passed &= check("Migration CreateUsers exists", any("CreateUsers" in f for f in mig_files))
    all_passed &= check("Migration CreateSales exists", any("CreateSales" in f for f in mig_files))

    # 3. Models Check
    for model_name in ["ProductModel.php", "CustomerModel.php", "UserModel.php", "SaleModel.php"]:
        m_path = os.path.join(BASE_DIR, "app", "Models", model_name)
        all_passed &= check(f"Model {model_name} exists", os.path.isfile(m_path))

    # Check SaleModel has getSalesHistory
    sale_model_path = os.path.join(BASE_DIR, "app", "Models", "SaleModel.php")
    if os.path.isfile(sale_model_path):
        with open(sale_model_path, "r", encoding="utf-8") as f:
            content = f.read()
        all_passed &= check("SaleModel has getSalesHistory method", "function getSalesHistory" in content)

    # 4. Controllers & CRUD Methods Check
    controller_specs = {
        "Products.php": ["index", "newForm", "create", "edit", "update", "delete"],
        "Customers.php": ["index", "newForm", "create", "edit", "update", "delete"],
        "Users.php": ["index", "newForm", "create", "edit", "update", "delete"],
        "Sales.php": ["index", "newForm", "create"],
        "Auth.php": ["login", "authenticate", "logout"],
    }
    for ctrl, methods in controller_specs.items():
        c_path = os.path.join(BASE_DIR, "app", "Controllers", ctrl)
        exists = os.path.isfile(c_path)
        all_passed &= check(f"Controller {ctrl} exists", exists)
        if exists:
            with open(c_path, "r", encoding="utf-8") as f:
                code = f.read()
            for m in methods:
                all_passed &= check(f"  {ctrl}::{m}() implemented", f"function {m}(" in code)

    # Check Business logic in Sales.php: stock check
    sales_ctrl_path = os.path.join(BASE_DIR, "app", "Controllers", "Sales.php")
    if os.path.isfile(sales_ctrl_path):
        with open(sales_ctrl_path, "r", encoding="utf-8") as f:
            code = f.read()
        all_passed &= check("Sales.php rejects quantity exceeding stock", "exceeds available stock" in code)
        all_passed &= check("Sales.php decrements stock_quantity", "stock_quantity" in code and "-" in code)

    # 5. Route Protection Check
    routes_path = os.path.join(BASE_DIR, "app", "Config", "Routes.php")
    if os.path.isfile(routes_path):
        with open(routes_path, "r", encoding="utf-8") as f:
            r_code = f.read()
        all_passed &= check("Routes group guarded by auth filter", "['filter' => 'auth']" in r_code)
        all_passed &= check("Product routes registered", "Products::index" in r_code and "Products::delete" in r_code)
        all_passed &= check("Customer routes registered", "Customers::index" in r_code and "Customers::delete" in r_code)
        all_passed &= check("User routes registered", "Users::index" in r_code and "Users::delete" in r_code)
        all_passed &= check("Sales routes registered", "Sales::index" in r_code and "Sales::create" in r_code)

    # 6. Views Check
    required_views = [
        "products/index.php", "products/form.php",
        "customers/index.php", "customers/form.php",
        "users/index.php", "users/form.php",
        "sales/form.php", "sales/index.php",
        "pages/landing.php", "pages/about.php",
        "templates/header.php", "templates/footer.php",
        "auth/login.php",
    ]
    views_base = os.path.join(BASE_DIR, "app", "Views")
    if not (os.path.isdir(views_base) and os.path.isdir(os.path.join(views_base, "auth"))):
        views_base = os.path.join(BASE_DIR, "app", "views")
    for view in required_views:
        v_path = os.path.join(views_base, view)
        all_passed &= check(f"View {view} exists", os.path.isfile(v_path))

    # 7. Asset and Upload Directories Check
    all_passed &= check("Product placeholder SVG exists", os.path.isfile(os.path.join(BASE_DIR, "public", "images", "product-placeholder.svg")))
    all_passed &= check("Avatar placeholder SVG exists", os.path.isfile(os.path.join(BASE_DIR, "public", "images", "avatar-placeholder.svg")))
    all_passed &= check("Uploads products dir exists", os.path.isdir(os.path.join(BASE_DIR, "public", "uploads", "products")))
    all_passed &= check("Uploads avatars dir exists", os.path.isdir(os.path.join(BASE_DIR, "public", "uploads", "avatars")))

    print("\n" + ("=" * 40))
    if all_passed:
        print("ALL VERIFICATION CHECKS PASSED!")
    else:
        print("SOME CHECKS FAILED! Please review output above.")
    print("=" * 40)
    return all_passed


if __name__ == "__main__":
    success = verify()
    sys.exit(0 if success else 1)
