# ATELIER — Website bán hàng thời trang

Đồ án môn **Lập trình Web**. Website thương mại điện tử thời trang gồm **trang khách** và **trang quản trị (Admin)**: CRUD đầy đủ, đăng nhập/đăng xuất/phân quyền, giỏ hàng, đặt hàng, đánh giá và dashboard thống kê. Giao diện tối giản, hiện đại, responsive.

## Công nghệ

| Thành phần | Lựa chọn |
|---|---|
| Backend | **PHP thuần** (8.2), tổ chức **MVC nhẹ** tự xây — không framework |
| Database | **PostgreSQL 16** truy cập qua **PDO** (prepared statements) |
| Frontend | **HTML + CSS + JS thuần**, Chart.js (CDN) cho dashboard |
| Thanh toán | **COD**, **SePay** (chuyển khoản QR + webhook đối soát tự động), **VNPay** (sandbox) |
| Triển khai | **Docker Compose** (web Apache + db Postgres + Adminer) |

> ⚠️ **Lưu ý về đề bài:** đề gợi ý dùng MySQL, nhóm chọn **PostgreSQL**. Vì truy cập qua PDO + SQL chuẩn nên dễ port lại nếu cần. Một vài cú pháp đặc thù Postgres được dùng: `ILIKE`, `ON CONFLICT ... DO UPDATE`, `interval`, `JSONB`, `to_char`.

## Cấu trúc thư mục

```
project_LTW/
├── public/                 # docroot (web chỉ thấy thư mục này)
│   ├── index.php           # front controller — mọi request đi qua đây
│   ├── .htaccess           # rewrite về index.php
│   ├── css/                # style.css (khách), admin.css
│   ├── js/                 # cart.js, main.js, admin.js
│   ├── assets/             # hero-poster.svg (đặt hero.mp4 vào đây nếu có)
│   └── uploads/            # ảnh sản phẩm admin upload (gitignore)
├── app/
│   ├── bootstrap.php       # autoloader PSR-4 + helper e(), vnd(), cfg()
│   ├── routes.php          # định nghĩa route
│   ├── core/               # Router, Database, Model, Controller, Session,
│   │                       # Csrf, Auth, Validator, Str
│   ├── controllers/        # khách + Admin/*
│   ├── models/             # User, Category, Product, Variant, Cart, Order, ...
│   └── views/              # layouts, partials, các trang
├── config/config.php       # đọc .env -> hằng số (DB)
├── database/
│   ├── schema.sql          # 11 bảng + CHECK + index + FK
│   └── seed.sql            # dữ liệu mẫu (chạy tự động lần init đầu)
├── docker/Dockerfile       # php:8.2-apache + pdo_pgsql + mod_rewrite
├── docker-compose.yml
└── .env.example
```

## Chạy nhanh bằng Docker

Yêu cầu: đã cài **Docker Desktop** (có Docker Compose v2).

```bash
# 1. Tạo file cấu hình từ mẫu
cp .env.example .env

# 2. Build & chạy (web + db + adminer)
docker compose up -d --build

# 3. Mở trình duyệt
#    Storefront : http://localhost:8000
#    Adminer    : http://localhost:8080   (server: db, user: shop, pass: shop_secret, db: fashion_shop)
```

`schema.sql` và `seed.sql` được **tự động nạp khi container db khởi tạo lần đầu**.

### Nạp lại dữ liệu mẫu (re-seed)

Init script của Postgres **chỉ chạy khi volume dữ liệu còn trống**. Nếu đã chạy trước đó mà muốn nạp lại schema/seed:

```bash
docker compose down -v      # xoá volume pgdata
docker compose up -d --build
```

## Tài khoản demo

| Vai trò | Email | Mật khẩu |
|---|---|---|
| **Admin** | `admin@shop.test` | `admin123` |
| Khách hàng | `an@shop.test` | `123456` |
| Khách hàng | `binh@shop.test` | `123456` |

Đăng nhập admin rồi vào **http://localhost:8000/admin** để xem dashboard và quản trị.

## Tính năng

**Trang khách**
- Trang chủ: hero full-width (video nếu có `assets/hero.mp4`, không thì poster gradient), danh mục, sản phẩm mới, bán chạy.
- Danh sách sản phẩm: lọc (danh mục / giá / size / màu), sắp xếp, phân trang, tìm kiếm.
- Chi tiết sản phẩm: gallery ảnh, chọn biến thể (size/màu), thêm giỏ (AJAX), đánh giá.
- Giỏ hàng AJAX (cập nhật số lượng / xoá / badge), checkout 3 phương thức (COD / SePay QR / VNPay), đặt hàng (trừ kho theo biến thể, snapshot giá).
- Tài khoản: lịch sử đơn, chi tiết đơn, cập nhật hồ sơ.

**Trang quản trị** (`/admin`, chặn bằng `requireRole('admin')`)
- Dashboard: 3 biểu đồ Chart.js (doanh thu 14 ngày, đơn theo trạng thái, top sản phẩm bán chạy).
- CRUD sản phẩm + biến thể + upload nhiều ảnh; CRUD danh mục; quản lý đơn (đổi trạng thái); người dùng; đánh giá.

## Bảo mật

- **SQLi:** toàn bộ truy vấn dùng PDO prepared statements.
- **XSS:** mọi output HTML qua helper `e()` (`htmlspecialchars`).
- **CSRF:** mọi form POST có `Csrf::field()` và được `Csrf::verify()` (thiếu token → **419**).
- **Phân quyền:** middleware `Auth::requireRole('admin')` trước controller admin (chưa đăng nhập/không phải admin → **403**).
- **Upload:** kiểm tra MIME + giới hạn 5MB, đổi tên file ngẫu nhiên.

## Thanh toán

Khi đặt hàng, khách chọn 1 trong 3 phương thức:

- **COD** — thanh toán khi nhận hàng (mặc định).
- **SePay (chuyển khoản QR)** — sinh mã **VietQR** động (qua `img.vietqr.io`) với đúng số tiền và nội dung `DH<mã đơn>`. Khi khách chuyển khoản, **SePay gọi webhook** `POST /payment/sepay/webhook` (xác thực bằng header `Authorization: Apikey <SEPAY_API_KEY>`); hệ thống đối soát số tiền + nội dung rồi tự cập nhật đơn sang `paid` / `confirmed`. Trang chờ cũng có nút **"Tôi đã chuyển khoản — Kiểm tra"** và tự hỏi trạng thái mỗi 5 giây.
- **VNPay** — cổng sandbox (cần `VNP_TMN_CODE` / `VNP_HASH_SECRET`).

Cấu hình SePay trong `.env` (xem `.env.example`): `SEPAY_ACCOUNT_NUMBER`, `SEPAY_BANK`, `SEPAY_ACCOUNT_NAME`, `SEPAY_API_KEY`, `SEPAY_PREFIX`.

**Thử webhook ở local** (không cần SePay thật) — giả lập một giao dịch tiền vào cho đơn `#<id>`:

```bash
curl -X POST http://localhost:8000/payment/sepay/webhook \
  -H "Authorization: Apikey <SEPAY_API_KEY>" -H "Content-Type: application/json" \
  -d '{"transferType":"in","transferAmount":<SỐ_TIỀN>,"content":"DH<id>","referenceCode":"FT123"}'
```

> File `.env` chứa thông tin nhạy cảm nên **không** được commit (đã có trong `.gitignore`).

## Chạy không cần Docker (tuỳ chọn)

Cần PHP 8.2+ (bật `pdo_pgsql`) và PostgreSQL chạy sẵn:

```bash
createdb fashion_shop
psql -d fashion_shop -f database/schema.sql
psql -d fashion_shop -f database/seed.sql
# sửa DB_HOST/DB_PORT trong .env cho khớp Postgres của bạn
php -S localhost:8000 -t public
```

---

Đồ án môn Lập trình Web — ATELIER · PHP thuần + PostgreSQL.
