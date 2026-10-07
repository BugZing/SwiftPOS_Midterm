-- Complete POS MIDTERMS MySQL database export
SET NAMES utf8mb4;
SET time_zone = '+00:00';

CREATE DATABASE IF NOT EXISTS `swiftpos`
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE `swiftpos`;

DROP TABLE IF EXISTS `sales`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `customers`;
DROP TABLE IF EXISTS `users`;

CREATE TABLE `products` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `price` DECIMAL(10,2) NOT NULL,
    `stock_quantity` INT NOT NULL DEFAULT 0,
    `image` VARCHAR(255) NULL,
    `created_at` DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `customers` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `full_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `phone` VARCHAR(20) NULL,
    `created_at` DATETIME NOT NULL,
    CONSTRAINT `uq_customers_email` UNIQUE (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `full_name` VARCHAR(100) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `avatar` VARCHAR(255) NULL,
    `created_at` DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sales` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT NOT NULL,
    `customer_id` INT NULL,
    `sold_by` INT NOT NULL,
    `quantity` INT NOT NULL,
    `total_price` DECIMAL(10,2) NOT NULL,
    `created_at` DATETIME NOT NULL,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`),
    FOREIGN KEY (`customer_id`) REFERENCES `customers`(`id`) ON DELETE SET NULL,
    FOREIGN KEY (`sold_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users`
    (`id`, `username`, `full_name`, `password`, `avatar`, `created_at`)
VALUES
    (1, 'admin_reign', 'Adrien Russel Tan', '$2y$12$6XKzQzeYseyy/SqiVpWNxOzHtt9QbPENSKFZkyZ23thuJZdUJunW6', NULL, '2026-09-17 00:00:00'),
    (2, 'mgr_echo', 'Jericho Macarang', '$2y$12$bZdW5ukErvqYbm0Hbxka4.seM1NoF/tbJaVkh8pF8BNOdplSxnE1.', NULL, '2026-09-17 00:00:00'),
    (3, 'cashier_jane', 'Jane Doe', '$2y$12$6XKzQzeYseyy/SqiVpWNxOzHtt9QbPENSKFZkyZ23thuJZdUJunW6', NULL, '2026-09-17 00:00:00');

INSERT INTO `customers`
    (`id`, `full_name`, `email`, `phone`, `created_at`)
VALUES
    (101, 'Maria Santos', 'maria.santos@email.com', '+63 917 123 4567', '2026-09-17 00:00:00'),
    (102, 'Juan Dela Cruz', 'juan.delacruz@email.com', '+63 918 234 5678', '2026-09-17 00:00:00'),
    (103, 'Carla Reyes', 'carla.reyes@email.com', '+63 920 345 6789', '2026-09-17 00:00:00'),
    (104, 'Eduardo Ramos', 'eduardo.ramos@email.com', '+63 922 456 7890', '2026-09-17 00:00:00'),
    (105, 'Patricia Tan', 'patricia.tan@email.com', NULL, '2026-09-17 00:00:00');

INSERT INTO `products`
    (`id`, `name`, `price`, `stock_quantity`, `image`, `created_at`)
VALUES
    (1, 'Espresso Roast', 120.00, 50, NULL, '2026-09-17 00:00:00'),
    (2, 'Caffe Latte', 150.00, 40, NULL, '2026-09-17 00:00:00'),
    (3, 'Cappuccino Classic', 155.00, 35, NULL, '2026-09-17 00:00:00'),
    (4, 'Caramel Macchiato', 175.00, 25, NULL, '2026-09-17 00:00:00'),
    (5, 'Iced Mocha Frappe', 185.00, 20, NULL, '2026-09-17 00:00:00'),
    (6, 'Butter Croissant', 95.00, 15, NULL, '2026-09-17 00:00:00'),
    (7, 'Blueberry Muffin', 85.00, 4, NULL, '2026-09-17 00:00:00'),
    (8, 'Matcha Green Tea Latte', 165.00, 0, NULL, '2026-09-17 00:00:00');

INSERT INTO `sales`
    (`id`, `product_id`, `customer_id`, `sold_by`, `quantity`, `total_price`, `created_at`)
VALUES
    (1, 1, 101, 1, 2, 240.00, '2026-10-01 09:15:00'),
    (2, 4, 102, 1, 1, 175.00, '2026-10-02 11:30:00'),
    (3, 6, NULL, 2, 3, 285.00, '2026-10-03 14:20:00'),
    (4, 2, 103, 1, 2, 300.00, '2026-10-04 16:45:00'),
    (5, 3, NULL, 3, 1, 155.00, '2026-10-05 10:05:00');
