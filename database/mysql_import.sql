-- =====================================================================
-- Schema + dữ liệu: Website bán hàng thời trang (MySQL / MariaDB)
-- Chuyển đổi từ PostgreSQL (database/dump_20260703.sql) để chạy trên XAMPP.
-- Import file này bằng phpMyAdmin hoặc:
--   mysql -u root -p fashion_shop < database/mysql_import.sql
-- (Nhớ tạo database fashion_shop trước, charset utf8mb4)
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS cart_items;
DROP TABLE IF EXISTS carts;
DROP TABLE IF EXISTS variants;
DROP TABLE IF EXISTS product_images;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS coupons;
DROP TABLE IF EXISTS styling_images;
DROP TABLE IF EXISTS stylings;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;

-- ---------------------------------------------------------------------
CREATE TABLE users (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    name          VARCHAR(120) NOT NULL,
    email         VARCHAR(160) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role          VARCHAR(20)  NOT NULL DEFAULT 'customer'
                  CHECK (role IN ('customer', 'admin')),
    phone         VARCHAR(20),
    address       TEXT,
    created_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
CREATE TABLE categories (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    name      VARCHAR(120) NOT NULL,
    slug      VARCHAR(140) NOT NULL UNIQUE,
    parent_id INT NULL,
    CONSTRAINT categories_parent_id_fkey FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
CREATE TABLE products (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NULL,
    name        VARCHAR(200) NOT NULL,
    slug        VARCHAR(220) NOT NULL UNIQUE,
    description TEXT,
    price       DECIMAL(12,2) NOT NULL DEFAULT 0,
    sale_price  DECIMAL(12,2),
    brand       VARCHAR(120),
    status      VARCHAR(20) NOT NULL DEFAULT 'active'
                CHECK (status IN ('active', 'hidden')),
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT products_category_id_fkey FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_products_status   ON products(status);

-- ---------------------------------------------------------------------
CREATE TABLE product_images (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_url  VARCHAR(255) NOT NULL,
    is_primary TINYINT(1) NOT NULL DEFAULT 0,
    sort_order INT NOT NULL DEFAULT 0,
    CONSTRAINT product_images_product_id_fkey FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE INDEX idx_images_product ON product_images(product_id);

-- ---------------------------------------------------------------------
CREATE TABLE variants (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    size       VARCHAR(20)  NOT NULL,
    color      VARCHAR(40)  NOT NULL,
    stock      INT NOT NULL DEFAULT 0 CHECK (stock >= 0),
    sku        VARCHAR(60) UNIQUE,
    UNIQUE (product_id, size, color),
    CONSTRAINT variants_product_id_fkey FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE INDEX idx_variants_product ON variants(product_id);

-- ---------------------------------------------------------------------
CREATE TABLE carts (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT NULL,
    session_id VARCHAR(120),
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT carts_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE INDEX idx_carts_user    ON carts(user_id);
CREATE INDEX idx_carts_session ON carts(session_id);

-- ---------------------------------------------------------------------
CREATE TABLE cart_items (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    cart_id    INT NOT NULL,
    variant_id INT NOT NULL,
    quantity   INT NOT NULL DEFAULT 1 CHECK (quantity > 0),
    UNIQUE (cart_id, variant_id),
    CONSTRAINT cart_items_cart_id_fkey FOREIGN KEY (cart_id) REFERENCES carts(id) ON DELETE CASCADE,
    CONSTRAINT cart_items_variant_id_fkey FOREIGN KEY (variant_id) REFERENCES variants(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
CREATE TABLE orders (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    user_id          INT NULL,
    total            DECIMAL(12,2) NOT NULL DEFAULT 0,
    status           VARCHAR(20) NOT NULL DEFAULT 'pending'
                     CHECK (status IN ('pending','confirmed','shipping','completed','cancelled')),
    payment_method   VARCHAR(20) NOT NULL DEFAULT 'cod'
                     CHECK (payment_method IN ('cod','vnpay','sepay')),
    payment_status   VARCHAR(20) NOT NULL DEFAULT 'unpaid'
                     CHECK (payment_status IN ('unpaid','paid','failed')),
    shipping_address TEXT NOT NULL,
    phone            VARCHAR(20) NOT NULL,
    customer_name    VARCHAR(120) NOT NULL,
    note             TEXT,
    created_at       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT orders_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE INDEX idx_orders_user   ON orders(user_id);
CREATE INDEX idx_orders_status ON orders(status);

-- ---------------------------------------------------------------------
CREATE TABLE order_items (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    order_id     INT NOT NULL,
    variant_id   INT NULL,
    product_name VARCHAR(200) NOT NULL,
    variant_label VARCHAR(80),
    price        DECIMAL(12,2) NOT NULL,
    quantity     INT NOT NULL CHECK (quantity > 0),
    CONSTRAINT order_items_order_id_fkey FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    CONSTRAINT order_items_variant_id_fkey FOREIGN KEY (variant_id) REFERENCES variants(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE INDEX idx_order_items_order ON order_items(order_id);

-- ---------------------------------------------------------------------
CREATE TABLE reviews (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id    INT NOT NULL,
    rating     INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment    TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (product_id, user_id),
    CONSTRAINT reviews_product_id_fkey FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    CONSTRAINT reviews_user_id_fkey FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE INDEX idx_reviews_product ON reviews(product_id);

-- ---------------------------------------------------------------------
CREATE TABLE payments (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    order_id     INT NOT NULL,
    provider     VARCHAR(20) NOT NULL,
    txn_ref      VARCHAR(100),
    amount       DECIMAL(12,2) NOT NULL,
    status       VARCHAR(20) NOT NULL DEFAULT 'pending',
    raw_response JSON,
    created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT payments_order_id_fkey FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE INDEX idx_payments_order ON payments(order_id);

-- ---------------------------------------------------------------------
CREATE TABLE coupons (
    id           INT AUTO_INCREMENT PRIMARY KEY,
    code         VARCHAR(50) NOT NULL UNIQUE,
    type         VARCHAR(10) NOT NULL DEFAULT 'percent' CHECK (type IN ('percent','fixed')),
    value        DECIMAL(12,2) NOT NULL,
    min_order    DECIMAL(12,2) NOT NULL DEFAULT 0,
    max_uses     INT,
    used_count   INT NOT NULL DEFAULT 0,
    expires_at   TIMESTAMP NULL,
    is_active    TINYINT(1) NOT NULL DEFAULT 1,
    created_at   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------
CREATE TABLE stylings (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    title      VARCHAR(150) NOT NULL,
    model_info TEXT,
    sort_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE styling_images (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    styling_id  INT NOT NULL,
    image_url   VARCHAR(255) NOT NULL,
    is_cover    TINYINT(1) NOT NULL DEFAULT 0,
    sort_order  INT NOT NULL DEFAULT 0,
    CONSTRAINT styling_images_styling_id_fkey FOREIGN KEY (styling_id) REFERENCES stylings(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE INDEX idx_styling_images_styling ON styling_images(styling_id);

-- =====================================================================
-- Dữ liệu (theo thứ tự phụ thuộc khóa ngoại)
-- =====================================================================

INSERT INTO users (id, name, email, password_hash, role, phone, address, created_at) VALUES
(1, 'Quan tri vien', 'admin@shop.test', '$2y$10$vcbmDrTl/vr9eYeKveJLmeVigj6jg2FZugFP7Ve5lsERaeCleJHQi', 'admin', '0900000001', 'Van phong ATELIER, Ha Noi', '2026-05-29 20:04:48'),
(2, 'Nguyen Van An', 'an@shop.test', '$2y$10$xGVQFDzQQwwaEft29cqpBexzIigt9TmHByPZdeQIRDyohQn.IlcHa', 'customer', '0911111111', '12 Le Loi, Quan 1, TP.HCM', '2026-05-29 20:04:48'),
(3, 'Tran Thi Binh', 'binh@shop.test', '$2y$10$xGVQFDzQQwwaEft29cqpBexzIigt9TmHByPZdeQIRDyohQn.IlcHa', 'customer', '0922222222', '34 Cau Giay, Ha Noi', '2026-05-29 20:04:48'),
(4, '123456', 'daivu9c@gmail.com', '$2y$10$OSPcE/kmZhOfo5tqcvo7MeiDS6ufGqraXHy0nqHuqD1vYSbFCQePa', 'customer', '12345678', NULL, '2026-06-25 07:06:27'),
(5, 'Mai Vũ Đại Vũ', 'daivu912.dev@gmail.com', '$2y$10$mI/av4vXdqO4o5duT98ztOa1GbhVjOtpDQr3fKQbvzJYKQ5Vioc1u', 'customer', '0336008578', NULL, '2026-07-02 07:01:31');

INSERT INTO categories (id, name, slug, parent_id) VALUES
(1, 'Áo', 'ao', NULL),
(2, 'Quần', 'quan', NULL),
(4, 'Phụ kiện', 'phu-kien', NULL),
(9, 'Hoodie', 'hoodie', 1),
(13, 'Mũ', 'mu', 4),
(14, 'Túi', 'tui', 4);

INSERT INTO coupons (id, code, type, value, min_order, max_uses, used_count, expires_at, is_active, created_at) VALUES
(1, 'WELCOME10', 'percent', 10.00, 0.00, NULL, 0, NULL, 1, '2026-06-26 08:59:23'),
(2, 'SALE50K', 'fixed', 50000.00, 200000.00, NULL, 0, NULL, 1, '2026-06-26 08:59:23');

INSERT INTO products (id, category_id, name, slug, description, price, sale_price, brand, status, created_at, is_featured) VALUES
(32, 2, 'Raw Denim Stitch Baggy Jeans', 'raw-denim-stitch-baggy-jeans-b75c', 'kh con gi de noi..', 360000.00, NULL, '', 'active', '2026-07-01 10:33:07', 0),
(10, 1, 'Travel Cities Long Sleeve Boxy Tee', 'travel-cities-long-sleeve-boxy-tee-1940', 'Quần kaki chino mềm, dễ phối, đủ màu cơ bản.', 389000.00, 329000.00, '', 'active', '2026-05-29 20:04:48', 0),
(34, 14, 'Stripe Classic Backpack', 'stripe-classic-backpack-eccb', 'dung ca? the gioi', 789000.00, NULL, '', 'active', '2026-07-01 10:42:44', 0),
(35, 4, 'Triple Star Small Wallet', 'triple-star-small-wallet-aa9c', 'muoi hai doi dep mat cung mot dem', 363636000.00, NULL, '', 'active', '2026-07-01 10:44:57', 0),
(11, 13, 'Joy Icon Classic Cap', 'joy-icon-classic-cap-9a77', 'hip to the hop', 259000.00, NULL, '', 'active', '2026-05-29 20:04:48', 0),
(12, 2, 'Emoji Pattern Shortpants', 'emoji-pattern-shortpants-d616', 'dep thi vl luon', 299000.00, NULL, '', 'active', '2026-05-29 20:04:48', 0),
(31, 1, 'Basic Slub Long Sleeve Relaxed Tee', 'levents-basic-slub-long-sleeve-relaxed-tee-ec7b', '', 360000.00, NULL, '', 'active', '2026-06-25 06:37:42', 1),
(33, 9, 'Classic Triple Star Zipper Hoodie Boxy', 'classic-triple-star-zipper-hoodie-boxy-8295', 'nong lam dung mua', 6789000.00, NULL, '', 'active', '2026-07-01 10:39:46', 0),
(18, 1, 'Seasonal Slub Semi-Oversized Tee', 'seasonal-slub-semi-oversized-tee-e10d', 'Đầm sơ mi dáng midi thanh lịch, có đai eo.', 649000.00, NULL, 'ATELIER', 'active', '2026-05-29 20:04:48', 1),
(17, 1, 'Furry Heart Semi-Oversized Tee', 'furry-heart-semi-oversized-tee-c8de', 'Maxi voan mỏng nhẹ, thoáng mát cho mùa hè.', 559000.00, 459000.00, '', 'active', '2026-05-29 20:04:48', 1),
(16, 1, 'Blink Blink Signature Logo Semi-Oversized Tee/ White', 'blink-blink-signature-logo-semi-oversized-tee-white-54cb', 'dep thi thoi roi', 899000.00, NULL, 'Bloom', 'active', '2026-05-29 20:04:48', 1),
(15, 1, 'XL Logo Star Shark Semi-Oversized', 'xl-logo-star-shark-semi-oversized-93d7', ':D', 689000.00, 589000.00, '', 'active', '2026-05-29 20:04:48', 1),
(14, 1, 'Signature Logo Long Sleeve Boxy Tee', 'signature-logo-long-sleeve-boxy-tee-24da', 'dep thi vl', 629000.00, NULL, 'ATELIER', 'active', '2026-05-29 20:04:48', 1);

INSERT INTO product_images (id, product_id, image_url, is_primary, sort_order) VALUES
(85, 34, '/uploads/3f9c629723986556.webp', 1, 0),
(87, 34, '/uploads/da589ccf6e34ffaa.webp', 0, 1),
(86, 34, '/uploads/97e2df04dec79633.webp', 0, 2),
(88, 35, '/uploads/c7c38679ca10b7da.webp', 1, 0),
(89, 35, '/uploads/12e186597ad53c20.webp', 0, 0),
(90, 35, '/uploads/2ee4454c592c4b07.webp', 0, 0),
(72, 12, '/uploads/edec8aca507d6b3d.webp', 1, 0),
(73, 12, '/uploads/e2dabd686eb1f1e4.webp', 0, 0),
(75, 11, '/uploads/928367dfea1dba36.webp', 0, 0),
(76, 11, '/uploads/fbf7a59a85337305.webp', 0, 0),
(74, 11, '/uploads/4dd3fe2be434c4be.webp', 1, 0),
(77, 32, '/uploads/dfc1596e916d77d5.webp', 1, 0),
(78, 32, '/uploads/0ecf54fcbd6606b3.webp', 0, 0),
(81, 10, '/uploads/f9031978d5e127a6.webp', 1, 0),
(80, 10, '/uploads/fd897f6955d99d37.webp', 0, 1),
(79, 10, '/uploads/358010f88d835bfa.webp', 0, 2),
(63, 31, '/uploads/2924be21042850b1.webp', 1, 0),
(64, 31, '/uploads/4d427cc035f0b338.webp', 0, 1),
(65, 31, '/uploads/d4b26c127bb31d2c.webp', 0, 2),
(66, 31, '/uploads/7ae81eb56a8c06a6.webp', 0, 3),
(67, 18, '/uploads/2fc9e9059eea1226.webp', 0, 0),
(68, 17, '/uploads/60ea37ec316e3aaa.webp', 1, 0),
(83, 33, '/uploads/726f9b9be62645e8.webp', 0, 1),
(82, 33, '/uploads/15e3ea15c00e1fea.webp', 0, 2),
(69, 16, '/uploads/917959e0c0238168.webp', 1, 0),
(84, 33, '/uploads/3458f12e7fdacfcc.webp', 1, 0),
(70, 15, '/uploads/13843acb8badc1d2.webp', 1, 0),
(71, 14, '/uploads/000db1aba3115984.webp', 1, 0);

INSERT INTO variants (id, product_id, size, color, stock, sku) VALUES
(290, 18, 'L', 'Xanh navy', 10, '18-l-xanh-navy'),
(430, 11, 'onesize', 'Trắng', 5, '11-onesize-trang'),
(435, 12, '1', 'Xanh navy', 12, '12-1-xanh-navy'),
(436, 12, '2', 'Xanh navy', 17, '12-2-xanh-navy'),
(437, 12, '3', 'Xanh navy', 15, '12-3-xanh-navy'),
(438, 12, '4', 'Xanh navy', 20, '12-4-xanh-navy'),
(440, 33, '1', 'Đen', 11, '33-1-den'),
(398, 32, '1', 'Đen', 13, '32-1-den'),
(399, 32, '2', 'Đen', 14, '32-2-den'),
(400, 32, '3', 'Đen', 15, '32-3-den'),
(401, 32, '4', 'Đen', 13, '32-4-den'),
(441, 33, '2', 'Đen', 11, '33-2-den'),
(442, 33, '3', 'Đen', 11, '33-3-den'),
(443, 33, '4', 'Đen', 11, '33-4-den'),
(291, 18, 'L', 'Đen', 15, '18-l-den'),
(427, 34, 'onesize', 'xanh', 77, '34-onesize-xanh'),
(429, 35, 'onesize', 'Đen', 44, '35-onesize-den'),
(293, 18, 'M', 'Đen', 12, '18-m-den'),
(302, 17, 'L', 'Be', 3, '17-l-be'),
(303, 17, 'L', 'Xám', 8, '17-l-xam'),
(304, 17, 'M', 'Be', 25, '17-m-be'),
(305, 17, 'M', 'Xám', 5, '17-m-xam'),
(306, 17, 'S', 'Be', 22, '17-s-be'),
(307, 17, 'S', 'Xám', 27, '17-s-xam'),
(334, 15, '1', 'Trắng', 18, '15-1-trang'),
(294, 18, 'S', 'Xanh navy', 3, '18-s-xanh-navy'),
(410, 10, '1', 'Trắng', 7, '10-1-trang'),
(411, 10, '2', 'Trắng', 9, '10-2-trang'),
(412, 10, '3', 'Trắng', 10, '10-3-trang'),
(413, 10, '4', 'Trắng', 14, '10-4-trang'),
(295, 18, 'S', 'Đen', 8, '18-s-den'),
(292, 18, 'M', 'Xanh navy', 6, '18-m-xanh-navy'),
(439, 31, '38', 'Be', 9, '31-38-be'),
(320, 16, '1', 'Trắng', 21, '16-1-trang'),
(321, 16, '2', 'Xanh navy', 26, '16-2-xanh-navy'),
(322, 16, '3', 'Trắng', 18, '16-3-trang'),
(323, 16, '4', 'Xanh navy', 23, '16-4-xanh-navy'),
(324, 16, '2', 'Trắng', 15, '16-2-trang'),
(325, 16, '3', 'Xanh navy', 20, '16-3-xanh-navy'),
(335, 15, '2', 'Trắng', 14, '15-2-trang'),
(336, 15, '3', 'Trắng', 16, '15-3-trang'),
(337, 15, '4', 'Trắng', 11, '15-4-trang'),
(356, 14, '1', 'Trắng', 12, '14-1-trang'),
(357, 14, '2', 'Xám', 7, '14-2-xam'),
(358, 14, '3', 'Trắng', 9, '14-3-trang'),
(359, 14, '4', 'Xám', 4, '14-4-xam');

INSERT INTO carts (id, user_id, session_id, created_at) VALUES
(1, NULL, 'e8754e2d8b3eea5d8c8d78477ae1660d', '2026-05-29 20:07:44'),
(3, 2, NULL, '2026-05-29 20:16:59'),
(4, NULL, '9fea663a33b20a6927facf8a36f8f258', '2026-05-30 18:23:18'),
(5, 1, NULL, '2026-06-25 06:34:31'),
(7, 4, NULL, '2026-06-25 07:06:27'),
(15, 5, NULL, '2026-07-02 07:01:31');

INSERT INTO orders (id, user_id, total, status, payment_method, payment_status, shipping_address, phone, customer_name, note, created_at) VALUES
(1, 2, 717000.00, 'completed', 'cod', 'paid', '12 Le Loi, Quan 1, TP.HCM', '0911111111', 'Nguyen Van An', 'Giao gio hanh chinh', '2026-05-18 20:04:48'),
(2, 3, 828000.00, 'completed', 'vnpay', 'paid', '34 Cau Giay, Ha Noi', '0922222222', 'Tran Thi Binh', NULL, '2026-05-19 20:04:48'),
(3, 2, 1217000.00, 'completed', 'cod', 'paid', '12 Le Loi, Quan 1, TP.HCM', '0911111111', 'Nguyen Van An', NULL, '2026-05-21 20:04:48'),
(4, 3, 589000.00, 'shipping', 'cod', 'unpaid', '34 Cau Giay, Ha Noi', '0922222222', 'Tran Thi Binh', 'Goi truoc khi giao', '2026-05-23 20:04:48'),
(5, 2, 948000.00, 'confirmed', 'vnpay', 'paid', '12 Le Loi, Quan 1, TP.HCM', '0911111111', 'Nguyen Van An', NULL, '2026-05-25 20:04:48'),
(6, 3, 259000.00, 'pending', 'cod', 'unpaid', '34 Cau Giay, Ha Noi', '0922222222', 'Tran Thi Binh', NULL, '2026-05-27 20:04:48'),
(7, 2, 549000.00, 'cancelled', 'cod', 'unpaid', '12 Le Loi, Quan 1, TP.HCM', '0911111111', 'Nguyen Van An', 'Khach doi y', '2026-05-28 20:04:48'),
(8, 3, 1027000.00, 'completed', 'vnpay', 'paid', '34 Cau Giay, Ha Noi', '0922222222', 'Tran Thi Binh', NULL, '2026-05-20 20:04:48'),
(9, 2, 318000.00, 'confirmed', 'sepay', 'paid', '12 Le Loi Q1', '0911111111', 'Nguyen Van An', 'test', '2026-05-29 20:16:59'),
(11, NULL, 549000.00, 'pending', 'sepay', 'unpaid', 'Gia Nghĩa', '0336008578', 'Mai Vũ Đại Vũ', '', '2026-06-25 06:19:00'),
(12, NULL, 279000.00, 'pending', 'sepay', 'unpaid', '1', '2', '1', '1', '2026-06-25 06:19:28'),
(13, NULL, 549000.00, 'pending', 'sepay', 'unpaid', '2', '2', '1', '1', '2026-06-25 06:21:57'),
(14, NULL, 159000.00, 'pending', 'sepay', 'unpaid', '1', '2', '1', '', '2026-06-25 06:31:33'),
(15, 1, 12000.00, 'pending', 'sepay', 'unpaid', 'Van phong ATELIER, Ha Noi', '0900000001', 'Quan tri vien', '', '2026-06-25 06:40:12'),
(16, 1, 459000.00, 'pending', 'sepay', 'unpaid', 'Van phong ATELIER, Ha Noi', '0900000001', 'Quan tri vien', '', '2026-06-25 06:41:06'),
(17, 1, 42000.00, 'confirmed', 'sepay', 'paid', 'Van phong ATELIER, Ha Noi', '0900000001', 'Quan tri vien', '', '2026-06-25 06:54:03'),
(18, 1, 42000.00, 'confirmed', 'sepay', 'paid', 'Van phong ATELIER, Ha Noi', '0900000001', 'Quan tri vien', '', '2026-06-25 06:57:49'),
(19, 1, 42000.00, 'pending', 'cod', 'unpaid', 'Van phong ATELIER, Ha Noi', '0900000001', 'Quan tri vien', '', '2026-06-25 06:59:22'),
(20, 1, 390000.00, 'pending', 'sepay', 'unpaid', 'Van phong ATELIER, Ha Noi', '0900000001', 'Quan tri vien', '', '2026-06-26 08:19:48'),
(21, 1, 390000.00, 'pending', 'sepay', 'unpaid', 'Van phong ATELIER, Ha Noi', '0900000001', 'Quan tri vien', '', '2026-06-26 08:20:18'),
(22, 1, 390000.00, 'pending', 'cod', 'unpaid', 'Van phong ATELIER, Ha Noi', '0900000001', 'Quan tri vien', '', '2026-06-26 08:22:22'),
(23, 1, 390000.00, 'pending', 'sepay', 'unpaid', 'Van phong ATELIER, Ha Noi', '0900000001', 'Quan tri vien', '', '2026-06-26 08:22:47'),
(24, 1, 390000.00, 'pending', 'sepay', 'unpaid', 'Van phong ATELIER, Ha Noi', '0900000001', 'Quan tri vien', '', '2026-06-26 08:23:15'),
(25, 1, 489000.00, 'pending', 'sepay', 'unpaid', 'Van phong ATELIER, Ha Noi', '0900000001', 'Quan tri vien', '', '2026-06-26 08:25:15'),
(26, 1, 309000.00, 'pending', 'sepay', 'unpaid', 'Van phong ATELIER, Ha Noi', '0900000001', 'Quan tri vien', '', '2026-06-26 08:26:31'),
(27, 1, 309000.00, 'pending', 'cod', 'unpaid', 'Van phong ATELIER, Ha Noi', '0900000001', 'Quan tri vien', '', '2026-06-26 08:27:31'),
(28, 1, 429000.00, 'pending', 'sepay', 'unpaid', 'Van phong ATELIER, Ha Noi', '0900000001', 'Quan tri vien', '', '2026-06-26 08:28:04'),
(29, 1, 579000.00, 'pending', 'cod', 'unpaid', 'Van phong ATELIER, Ha Noi', '0900000001', 'Quan tri vien', '', '2026-06-26 08:29:45'),
(30, 1, 390000.00, 'pending', 'sepay', 'unpaid', 'Van phong ATELIER, Ha Noi', '0900000001', 'Quan tri vien', '', '2026-06-26 08:34:43'),
(31, 1, 489000.00, 'pending', 'cod', 'unpaid', 'adsdads, Phường Bình Hòa, Thành phố Hồ Chí Minh', '0900000001', 'Quan tri vien', '', '2026-06-26 08:51:08'),
(32, 1, 489000.00, 'pending', 'sepay', 'unpaid', '1, Xã Ngọc Đường, Tỉnh Tuyên Quang', '0336008578', 'Quan tri vien', '', '2026-06-26 08:51:43'),
(33, 1, 619000.00, 'pending', 'sepay', 'unpaid', '1234, Phường Ba Đình, Thành phố Hà Nội', '0900000001', 'aaaa', '', '2026-07-01 11:10:35'),
(34, NULL, 679000.00, 'pending', 'cod', 'unpaid', 'abcd, Xã Bảo Lâm, Tỉnh Cao Bằng', '6853779', 'Mai Vũ Đại Vũ', '', '2026-07-02 05:22:19'),
(35, NULL, 679000.00, 'pending', 'sepay', 'unpaid', 'Gia Nghĩa, Phường Giảng Võ, Thành phố Hà Nội', '0336008578', 'Mai Vũ Đại Vũ', '', '2026-07-02 06:25:47'),
(36, 1, 679000.00, 'pending', 'sepay', 'unpaid', 'kiikkkk, Phường Ngọc Hà, Thành phố Hà Nội', '0900000001', 'Quan tri vien', '', '2026-07-02 06:54:42'),
(37, 5, 390000.00, 'pending', 'cod', 'unpaid', 'Gia Nghĩa, Xã Phú Linh, Tỉnh Tuyên Quang', '0336008578', 'Mai Vũ Đại Vũ', '', '2026-07-02 07:18:24');

INSERT INTO order_items (id, order_id, variant_id, product_name, variant_label, price, quantity) VALUES
(3, 2, NULL, 'Đầm suông linen', 'M / Be', 629000.00, 1),
(7, 4, NULL, 'Đầm xòe hoa nhí', 'M / Trắng', 589000.00, 1),
(8, 5, NULL, 'Áo khoác bomber', 'L / Xanh navy', 649000.00, 1),
(9, 5, NULL, 'Thắt lưng da bò', 'Free / Đen', 299000.00, 1),
(10, 6, NULL, 'Quần short kaki', '30 / Be', 259000.00, 1),
(11, 7, NULL, 'Áo hoodie nỉ bông', 'XL / Đen', 549000.00, 1),
(13, 8, NULL, 'Khăn lụa vuông', 'Free / Trắng', 249000.00, 2),
(12, 8, NULL, 'Giày cao gót 7cm', '39 / Đen', 529000.00, 1),
(5, 3, NULL, 'Giày sneaker trắng', '40 / Trắng', 659000.00, 1),
(4, 2, NULL, 'Túi tote canvas', 'Free / Đen', 199000.00, 1),
(20, 15, NULL, 'aaaa', '38 / Be', 12000.00, 1),
(22, 17, NULL, 'aaaa', '38 / Be', 12000.00, 1),
(23, 18, NULL, 'aaaa', '38 / Be', 12000.00, 1),
(24, 19, NULL, 'aaaa', '38 / Be', 12000.00, 1),
(25, 20, NULL, 'Levents® Basic Slub Long Sleeve Relaxed Tee', '38 / Be', 360000.00, 1),
(26, 21, NULL, 'Levents® Basic Slub Long Sleeve Relaxed Tee', '38 / Be', 360000.00, 1),
(27, 22, NULL, 'Levents® Basic Slub Long Sleeve Relaxed Tee', '38 / Be', 360000.00, 1),
(28, 23, NULL, 'Levents® Basic Slub Long Sleeve Relaxed Tee', '38 / Be', 360000.00, 1),
(29, 24, NULL, 'Levents® Basic Slub Long Sleeve Relaxed Tee', '38 / Be', 360000.00, 1),
(35, 30, NULL, 'Levents® Basic Slub Long Sleeve Relaxed Tee', '38 / Be', 360000.00, 1),
(1, 1, NULL, 'Áo thun cotton basic', 'M / Trắng', 159000.00, 2),
(19, 14, NULL, 'Áo thun cotton basic', 'L / Xanh navy', 159000.00, 1),
(14, 9, NULL, 'Áo thun cotton basic', 'S / Trắng', 159000.00, 2),
(21, 16, NULL, 'Áo sơ mi linen dài tay', 'L / Xám', 459000.00, 1),
(36, 31, NULL, 'Áo sơ mi linen dài tay', 'L / Xám', 459000.00, 1),
(37, 32, NULL, 'Áo sơ mi linen dài tay', 'L / Xám', 459000.00, 1),
(30, 25, NULL, 'Áo sơ mi linen dài tay', 'L / Be', 459000.00, 1),
(6, 3, NULL, 'Áo polo pique', 'L / Đen', 279000.00, 2),
(17, 12, NULL, 'Áo polo pique', 'L / Đen', 279000.00, 1),
(31, 26, NULL, 'Áo polo pique', 'L / Xanh navy', 279000.00, 1),
(32, 27, NULL, 'Áo polo pique', 'L / Xanh navy', 279000.00, 1),
(16, 11, NULL, 'Áo hoodie nỉ bông', 'L / Xám', 549000.00, 1),
(18, 13, NULL, 'Áo hoodie nỉ bông', 'L / Xám', 549000.00, 1),
(34, 29, NULL, 'Áo hoodie nỉ bông', 'L / Xám', 549000.00, 1),
(33, 28, NULL, 'Áo len cổ tròn', 'L / Xanh navy', 399000.00, 1),
(2, 1, NULL, 'Quần jean slim fit', '30 / Đen', 399000.00, 1),
(38, 33, 334, 'XL Logo Star Shark Semi-Oversized', '1 / Trắng', 589000.00, 1),
(39, 34, 294, 'Seasonal Slub Semi-Oversized Tee', 'S / Xanh navy', 649000.00, 1),
(40, 35, 295, 'Seasonal Slub Semi-Oversized Tee', 'S / Đen', 649000.00, 1),
(41, 36, 292, 'Seasonal Slub Semi-Oversized Tee', 'M / Xanh navy', 649000.00, 1),
(42, 37, 439, 'Basic Slub Long Sleeve Relaxed Tee', '38 / Be', 360000.00, 1);

INSERT INTO payments (id, order_id, provider, txn_ref, amount, status, raw_response, created_at) VALUES
(1, 2, 'vnpay', 'TX1002', 828000.00, 'success', NULL, '2026-05-29 20:04:48'),
(2, 5, 'vnpay', 'TX1005', 948000.00, 'success', NULL, '2026-05-29 20:04:48'),
(3, 8, 'vnpay', 'TX1008', 1027000.00, 'success', NULL, '2026-05-29 20:04:48'),
(4, 9, 'sepay', 'FT123456', 318000.00, 'paid', '{"id": 999, "content": "DH9 thanh toan", "transferType": "in", "referenceCode": "FT123456", "transferAmount": 318000}', '2026-05-29 20:18:15'),
(6, 17, 'sepay', 'FT26176242820359', 42000.00, 'paid', '{"id": 64973047, "code": null, "content": "134865022846-DH17-CHUYEN TIEN-OQCH000EHvA1-MOMO134865022846MOMO", "gateway": "MBBank", "subAccount": null, "accumulated": 0, "description": "BankAPINotify 134865022846-DH17-CHUYEN TIEN-OQCH000EHvA1-MOMO134865022846MOMO", "transferType": "in", "accountNumber": "64579797977777", "referenceCode": "FT26176242820359", "transferAmount": 42000, "transactionDate": "2026-06-25 13:54:00"}', '2026-06-25 06:54:49'),
(7, 18, 'sepay', 'FT26176502693040', 42000.00, 'paid', '{"id": 64973513, "code": null, "content": "DH18", "gateway": "MBBank", "subAccount": null, "accumulated": 0, "description": "BankAPINotify DH18", "transferType": "in", "accountNumber": "64579797977777", "referenceCode": "FT26176502693040", "transferAmount": 42000, "transactionDate": "2026-06-25 13:58:00"}', '2026-06-25 06:58:34');

INSERT INTO reviews (id, product_id, user_id, rating, comment, created_at) VALUES
(4, 14, 3, 5, 'Đầm xinh, vải mềm, sẽ ủng hộ tiếp.', '2026-05-29 20:04:48'),
(7, 15, 2, 4, 'Đầm hoa dễ thương, đúng mô tả.', '2026-05-29 20:04:48'),
(9, 35, 1, 5, 'dep', '2026-07-01 11:01:08');

INSERT INTO stylings (id, title, sort_order, created_at, updated_at, model_info) VALUES
(7, 'Striped Oversized Jersey Tee', 2, '2026-07-01 09:16:51', '2026-07-01 09:23:13', 'Model:\r\n\r\nHeight 1m68, Weight 51kg\r\n\r\nWearing: Size 0'),
(2, 'Logo Zipper Hoodie Boxy', 1, '2026-07-01 08:26:43', '2026-07-01 09:24:31', 'Model:\r\n\r\nHeight 1m68, Weight 51kg\r\nWearing: Size 3'),
(8, 'XL Logo Boxy Sweater', 3, '2026-07-01 09:23:59', '2026-07-01 09:26:14', 'Model:\r\nHeight 1m68, Weight 51kg\r\nWearing: Size 3'),
(9, 'Raglan Long Sleeve Boxy Tee', 4, '2026-07-01 09:35:50', '2026-07-01 09:36:48', ''),
(10, 'Striped Fur Knit Boxy Sweater', 0, '2026-07-01 09:44:33', '2026-07-01 09:44:33', '');

INSERT INTO styling_images (id, styling_id, image_url, is_cover, sort_order) VALUES
(3, 2, '/uploads/912ef64f1028c413.webp', 1, 0),
(4, 2, '/uploads/cc23cdd428f6bc08.webp', 0, 0),
(5, 2, '/uploads/5803c8cf8a9dd7ec.webp', 0, 0),
(6, 2, '/uploads/cbdb9993142f2096.webp', 0, 0),
(10, 7, '/uploads/40477b16aa1f06b1.webp', 1, 0),
(12, 7, '/uploads/9ad18f8aaded4e42.webp', 0, 0),
(13, 7, '/uploads/633cb359948f4f50.webp', 0, 0),
(14, 7, '/uploads/62d7ef09eba205da.webp', 0, 0),
(16, 8, '/uploads/cec56b64830da252.webp', 1, 0),
(17, 8, '/uploads/1f48f5955b3cff33.webp', 0, 0),
(18, 8, '/uploads/1f9fe84db73a3b8c.webp', 0, 0),
(19, 8, '/uploads/f71af44dee78a131.webp', 0, 0),
(23, 9, '/uploads/46d58a92e32f82c8.webp', 1, 0),
(22, 9, '/uploads/cf0fe6a2dd8059cb.webp', 0, 1),
(21, 9, '/uploads/78c3c01353f9ffc5.webp', 0, 2),
(20, 9, '/uploads/39ccd92b4c10d011.webp', 0, 3),
(24, 10, '/uploads/4815833df4d90008.webp', 1, 0),
(25, 10, '/uploads/57bca55035a6bbc8.webp', 0, 0),
(26, 10, '/uploads/a31bf8c3dfe76879.webp', 0, 0),
(27, 10, '/uploads/502d8ae4de293a4a.webp', 0, 0);

-- Đặt lại AUTO_INCREMENT theo giá trị sequence cũ của Postgres
ALTER TABLE users AUTO_INCREMENT = 6;
ALTER TABLE categories AUTO_INCREMENT = 16;
ALTER TABLE coupons AUTO_INCREMENT = 3;
ALTER TABLE products AUTO_INCREMENT = 36;
ALTER TABLE product_images AUTO_INCREMENT = 91;
ALTER TABLE variants AUTO_INCREMENT = 444;
ALTER TABLE carts AUTO_INCREMENT = 16;
ALTER TABLE orders AUTO_INCREMENT = 38;
ALTER TABLE order_items AUTO_INCREMENT = 43;
ALTER TABLE payments AUTO_INCREMENT = 8;
ALTER TABLE reviews AUTO_INCREMENT = 10;
ALTER TABLE stylings AUTO_INCREMENT = 11;
ALTER TABLE styling_images AUTO_INCREMENT = 28;

SET FOREIGN_KEY_CHECKS = 1;
