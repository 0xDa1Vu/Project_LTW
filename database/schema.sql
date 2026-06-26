-- =====================================================================
-- Schema: Website bán hàng thời trang (PostgreSQL)
-- Tự động chạy bởi Docker khi container db khởi tạo lần đầu.
-- =====================================================================

DROP TABLE IF EXISTS payments      CASCADE;
DROP TABLE IF EXISTS reviews       CASCADE;
DROP TABLE IF EXISTS order_items   CASCADE;
DROP TABLE IF EXISTS orders        CASCADE;
DROP TABLE IF EXISTS cart_items    CASCADE;
DROP TABLE IF EXISTS carts         CASCADE;
DROP TABLE IF EXISTS variants      CASCADE;
DROP TABLE IF EXISTS product_images CASCADE;
DROP TABLE IF EXISTS products      CASCADE;
DROP TABLE IF EXISTS categories    CASCADE;
DROP TABLE IF EXISTS users         CASCADE;

-- ---------------------------------------------------------------------
CREATE TABLE users (
    id            SERIAL PRIMARY KEY,
    name          VARCHAR(120) NOT NULL,
    email         VARCHAR(160) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role          VARCHAR(20)  NOT NULL DEFAULT 'customer'
                  CHECK (role IN ('customer', 'admin')),
    phone         VARCHAR(20),
    address       TEXT,
    created_at    TIMESTAMP NOT NULL DEFAULT now()
);

-- ---------------------------------------------------------------------
CREATE TABLE categories (
    id        SERIAL PRIMARY KEY,
    name      VARCHAR(120) NOT NULL,
    slug      VARCHAR(140) NOT NULL UNIQUE,
    parent_id INT REFERENCES categories(id) ON DELETE SET NULL
);

-- ---------------------------------------------------------------------
CREATE TABLE products (
    id          SERIAL PRIMARY KEY,
    category_id INT REFERENCES categories(id) ON DELETE SET NULL,
    name        VARCHAR(200) NOT NULL,
    slug        VARCHAR(220) NOT NULL UNIQUE,
    description TEXT,
    price       NUMERIC(12,2) NOT NULL DEFAULT 0,
    sale_price  NUMERIC(12,2),
    brand       VARCHAR(120),
    status      VARCHAR(20) NOT NULL DEFAULT 'active'
                CHECK (status IN ('active', 'hidden')),
    is_featured BOOLEAN NOT NULL DEFAULT false,
    created_at  TIMESTAMP NOT NULL DEFAULT now()
);
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_products_status   ON products(status);

-- ---------------------------------------------------------------------
CREATE TABLE product_images (
    id         SERIAL PRIMARY KEY,
    product_id INT NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    image_url  VARCHAR(255) NOT NULL,
    is_primary BOOLEAN NOT NULL DEFAULT false
);
CREATE INDEX idx_images_product ON product_images(product_id);

-- ---------------------------------------------------------------------
CREATE TABLE variants (
    id         SERIAL PRIMARY KEY,
    product_id INT NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    size       VARCHAR(20)  NOT NULL,
    color      VARCHAR(40)  NOT NULL,
    stock      INT NOT NULL DEFAULT 0 CHECK (stock >= 0),
    sku        VARCHAR(60) UNIQUE,
    UNIQUE (product_id, size, color)
);
CREATE INDEX idx_variants_product ON variants(product_id);

-- ---------------------------------------------------------------------
CREATE TABLE carts (
    id         SERIAL PRIMARY KEY,
    user_id    INT REFERENCES users(id) ON DELETE CASCADE,
    session_id VARCHAR(120),
    created_at TIMESTAMP NOT NULL DEFAULT now()
);
CREATE INDEX idx_carts_user    ON carts(user_id);
CREATE INDEX idx_carts_session ON carts(session_id);

-- ---------------------------------------------------------------------
CREATE TABLE cart_items (
    id         SERIAL PRIMARY KEY,
    cart_id    INT NOT NULL REFERENCES carts(id) ON DELETE CASCADE,
    variant_id INT NOT NULL REFERENCES variants(id) ON DELETE CASCADE,
    quantity   INT NOT NULL DEFAULT 1 CHECK (quantity > 0),
    UNIQUE (cart_id, variant_id)
);

-- ---------------------------------------------------------------------
CREATE TABLE orders (
    id               SERIAL PRIMARY KEY,
    user_id          INT REFERENCES users(id) ON DELETE SET NULL,
    total            NUMERIC(12,2) NOT NULL DEFAULT 0,
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
    created_at       TIMESTAMP NOT NULL DEFAULT now()
);
CREATE INDEX idx_orders_user   ON orders(user_id);
CREATE INDEX idx_orders_status ON orders(status);

-- ---------------------------------------------------------------------
CREATE TABLE order_items (
    id           SERIAL PRIMARY KEY,
    order_id     INT NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    variant_id   INT REFERENCES variants(id) ON DELETE SET NULL,
    product_name VARCHAR(200) NOT NULL,   -- snapshot tên tại thời điểm mua
    variant_label VARCHAR(80),            -- vd "M / Đen"
    price        NUMERIC(12,2) NOT NULL,  -- snapshot giá
    quantity     INT NOT NULL CHECK (quantity > 0)
);
CREATE INDEX idx_order_items_order ON order_items(order_id);

-- ---------------------------------------------------------------------
CREATE TABLE reviews (
    id         SERIAL PRIMARY KEY,
    product_id INT NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    user_id    INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    rating     INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment    TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT now(),
    UNIQUE (product_id, user_id)   -- mỗi user 1 đánh giá / sản phẩm
);
CREATE INDEX idx_reviews_product ON reviews(product_id);

-- ---------------------------------------------------------------------
CREATE TABLE payments (
    id           SERIAL PRIMARY KEY,
    order_id     INT NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    provider     VARCHAR(20) NOT NULL,
    txn_ref      VARCHAR(100),
    amount       NUMERIC(12,2) NOT NULL,
    status       VARCHAR(20) NOT NULL DEFAULT 'pending',
    raw_response JSONB,
    created_at   TIMESTAMP NOT NULL DEFAULT now()
);
CREATE INDEX idx_payments_order ON payments(order_id);

-- ---------------------------------------------------------------------
CREATE TABLE coupons (
    id           SERIAL PRIMARY KEY,
    code         VARCHAR(50) NOT NULL UNIQUE,
    type         VARCHAR(10) NOT NULL DEFAULT 'percent' CHECK (type IN ('percent','fixed')),
    value        NUMERIC(12,2) NOT NULL,
    min_order    NUMERIC(12,2) NOT NULL DEFAULT 0,
    max_uses     INT,
    used_count   INT NOT NULL DEFAULT 0,
    expires_at   TIMESTAMP,
    is_active    BOOLEAN NOT NULL DEFAULT true,
    created_at   TIMESTAMP NOT NULL DEFAULT now()
);
