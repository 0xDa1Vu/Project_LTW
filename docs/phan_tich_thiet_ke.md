# Phân tích & Thiết kế hệ thống — Website bán hàng thời trang

## 1. ERD (Entity Relationship Diagram)

```mermaid
erDiagram
    USERS ||--o{ ORDERS : "đặt"
    USERS ||--o{ CARTS : "sở hữu"
    USERS ||--o{ REVIEWS : "viết"

    CATEGORIES ||--o{ CATEGORIES : "danh mục cha/con"
    CATEGORIES ||--o{ PRODUCTS : "chứa"

    PRODUCTS ||--o{ PRODUCT_IMAGES : "có ảnh"
    PRODUCTS ||--o{ VARIANTS : "có biến thể"
    PRODUCTS ||--o{ REVIEWS : "được đánh giá"

    CARTS ||--o{ CART_ITEMS : "gồm"
    VARIANTS ||--o{ CART_ITEMS : "được chọn"
    VARIANTS ||--o{ ORDER_ITEMS : "được mua"

    ORDERS ||--o{ ORDER_ITEMS : "gồm"
    ORDERS ||--o{ PAYMENTS : "thanh toán"

    STYLINGS ||--o{ STYLING_IMAGES : "có ảnh"

    USERS {
        int id PK
        varchar name
        varchar email UK
        varchar password_hash
        varchar role "customer|admin"
        varchar phone
        text address
        timestamp created_at
    }

    CATEGORIES {
        int id PK
        varchar name
        varchar slug UK
        int parent_id FK
    }

    PRODUCTS {
        int id PK
        int category_id FK
        varchar name
        varchar slug UK
        text description
        numeric price
        numeric sale_price
        varchar brand
        varchar status "active|hidden"
        bool is_featured
        timestamp created_at
    }

    PRODUCT_IMAGES {
        int id PK
        int product_id FK
        varchar image_url
        bool is_primary
        int sort_order
    }

    VARIANTS {
        int id PK
        int product_id FK
        varchar size
        varchar color
        int stock
        varchar sku UK
    }

    CARTS {
        int id PK
        int user_id FK
        varchar session_id
        timestamp created_at
    }

    CART_ITEMS {
        int id PK
        int cart_id FK
        int variant_id FK
        int quantity
    }

    ORDERS {
        int id PK
        int user_id FK
        numeric total
        varchar status "pending|confirmed|shipping|completed|cancelled"
        varchar payment_method "cod|vnpay|sepay"
        varchar payment_status "unpaid|paid|failed"
        text shipping_address
        varchar phone
        varchar customer_name
        text note
        timestamp created_at
    }

    ORDER_ITEMS {
        int id PK
        int order_id FK
        int variant_id FK
        varchar product_name
        varchar variant_label
        numeric price
        int quantity
    }

    REVIEWS {
        int id PK
        int product_id FK
        int user_id FK
        int rating "1-5"
        text comment
        timestamp created_at
    }

    PAYMENTS {
        int id PK
        int order_id FK
        varchar provider
        varchar txn_ref
        numeric amount
        varchar status
        jsonb raw_response
        timestamp created_at
    }

    COUPONS {
        int id PK
        varchar code UK
        varchar type "percent|fixed"
        numeric value
        numeric min_order
        int max_uses
        int used_count
        timestamp expires_at
        bool is_active
    }

    STYLINGS {
        int id PK
        varchar title
        text model_info
        int sort_order
        timestamp created_at
        timestamp updated_at
    }

    STYLING_IMAGES {
        int id PK
        int styling_id FK
        varchar image_url
        bool is_cover
        int sort_order
    }
```

---

## 2. Use Case Diagram (tổng quan chức năng)

```mermaid
flowchart LR
    Customer((Khách hàng))
    Admin((Quản trị viên))

    subgraph "Hệ thống bán hàng thời trang"
        UC1[Xem sản phẩm / danh mục]
        UC2[Tìm kiếm & lọc sản phẩm]
        UC3[Quản lý giỏ hàng]
        UC4[Đặt hàng / Checkout]
        UC5[Thanh toán COD/VNPay/SePay]
        UC6[Đánh giá sản phẩm]
        UC7[Quản lý tài khoản]
        UC8[Áp dụng mã giảm giá]
        UC9[Xem phối đồ - Styling]

        UC10[Quản lý sản phẩm]
        UC11[Quản lý danh mục]
        UC12[Quản lý đơn hàng]
        UC13[Quản lý người dùng]
        UC14[Quản lý mã giảm giá]
        UC15[Quản lý Styling]
        UC16[Xem báo cáo / thống kê]
    end

    Customer --> UC1
    Customer --> UC2
    Customer --> UC3
    Customer --> UC4
    Customer --> UC5
    Customer --> UC6
    Customer --> UC7
    Customer --> UC8
    Customer --> UC9

    Admin --> UC10
    Admin --> UC11
    Admin --> UC12
    Admin --> UC13
    Admin --> UC14
    Admin --> UC15
    Admin --> UC16
```

---

## 3. Sequence Diagram — Luồng đặt hàng & thanh toán

```mermaid
sequenceDiagram
    actor KH as Khách hàng
    participant CartCtl as CartController
    participant OrderCtl as OrderController
    participant PayCtl as PaymentController
    participant DB as PostgreSQL
    participant Gateway as Cổng thanh toán (VNPay/SePay)

    KH->>CartCtl: Thêm sản phẩm vào giỏ (add)
    CartCtl->>DB: INSERT/UPDATE cart_items
    DB-->>CartCtl: OK
    CartCtl-->>KH: Cập nhật giỏ hàng

    KH->>OrderCtl: Checkout (xem giỏ + nhập thông tin giao hàng)
    OrderCtl->>DB: SELECT cart_items, variants, products
    DB-->>OrderCtl: Danh sách sản phẩm trong giỏ
    OrderCtl-->>KH: Trang checkout

    KH->>OrderCtl: Đặt hàng (place)
    OrderCtl->>DB: INSERT orders (status=pending)
    OrderCtl->>DB: INSERT order_items (snapshot giá, tên)
    OrderCtl->>DB: DELETE cart_items đã đặt
    DB-->>OrderCtl: order_id

    alt Thanh toán COD
        OrderCtl-->>KH: Trang xác nhận đơn (chờ giao hàng)
    else Thanh toán VNPay
        KH->>PayCtl: vnpayCreate(order_id)
        PayCtl->>Gateway: Tạo URL thanh toán
        Gateway-->>KH: Redirect sang VNPay
        KH->>Gateway: Thanh toán
        Gateway->>PayCtl: vnpayReturn (callback)
        PayCtl->>DB: UPDATE orders.payment_status, INSERT payments
        PayCtl-->>KH: Trang kết quả thanh toán
    else Thanh toán SePay (chuyển khoản QR)
        OrderCtl-->>KH: Trang hiển thị QR (sepayShow)
        Gateway->>PayCtl: sepayWebhook (khi nhận được tiền)
        PayCtl->>DB: UPDATE orders.payment_status='paid', INSERT payments
        KH->>PayCtl: sepayCheck (poll kiểm tra trạng thái)
        PayCtl->>DB: SELECT payment_status
        DB-->>PayCtl: Trạng thái
        PayCtl-->>KH: Cập nhật giao diện realtime
    end
```

---

## 4. Kiến trúc hệ thống (MVC)

```mermaid
flowchart TB
    subgraph Client["Trình duyệt (Client)"]
        UI[HTML/CSS/JS - Views]
    end

    subgraph Server["Server PHP - Kiến trúc MVC"]
        Router[Router / index.php]
        Controllers["Controllers\n(Home, Product, Cart, Order,\nPayment, Auth, Admin...)"]
        Models["Models\n(User, Product, Order,\nVariant, Payment...)"]
        Views["Views (templates)"]
    end

    subgraph Data["Tầng dữ liệu"]
        DB[(PostgreSQL)]
    end

    subgraph External["Dịch vụ ngoài"]
        VNPay[VNPay Gateway]
        SePay[SePay Webhook/QR]
    end

    UI -->|HTTP Request| Router
    Router --> Controllers
    Controllers --> Models
    Models --> DB
    Controllers --> Views
    Views -->|HTML Response| UI
    Controllers -.->|redirect/callback| VNPay
    Controllers -.->|webhook| SePay
```

---

## 5. Trạng thái đơn hàng (State Diagram)

```mermaid
stateDiagram-v2
    [*] --> pending: Khách đặt hàng
    pending --> confirmed: Admin xác nhận
    confirmed --> shipping: Bắt đầu giao hàng
    shipping --> completed: Giao thành công
    pending --> cancelled: Hủy đơn
    confirmed --> cancelled: Hủy đơn
    completed --> [*]
    cancelled --> [*]
```

---

### Ghi chú khi thuyết trình
- **ERD**: nhấn mạnh quan hệ 1-n giữa `products` → `variants`/`product_images`, và việc `order_items` lưu **snapshot** (product_name, price) để không bị ảnh hưởng khi sản phẩm gốc thay đổi/xóa.
- **Sequence diagram**: làm rõ 3 phương thức thanh toán khác nhau (COD đơn giản, VNPay dùng redirect + callback, SePay dùng webhook + polling).
- **Kiến trúc MVC**: giải thích tách biệt Controller (xử lý logic), Model (truy vấn DB), View (giao diện).
