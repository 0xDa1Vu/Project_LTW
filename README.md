# ATELIER — Website bán hàng thời trang

Đồ án môn **Lập trình Web**. Website thương mại điện tử thời trang gồm **trang khách** và **trang quản trị (Admin)**: CRUD đầy đủ, đăng nhập/đăng xuất/phân quyền, giỏ hàng, đặt hàng, đánh giá và dashboard thống kê. Giao diện tối giản, hiện đại, responsive.

## Công nghệ

| Thành phần | Lựa chọn |
|---|---|
| Backend | **PHP thuần** (8.2), tổ chức **MVC nhẹ** tự xây — không framework |
| Database | **MySQL** (8.x hoặc MariaDB đi kèm XAMPP) truy cập qua **PDO** (prepared statements) |
| Frontend | **HTML + CSS + JS thuần**, Chart.js (CDN) cho dashboard |
| Thanh toán | **COD**, **SePay** (chuyển khoản QR + webhook đối soát tự động), **VNPay** (sandbox) |
| Triển khai | **XAMPP** (Apache + MySQL) hoặc **Docker Compose** (web Apache + db MySQL + Adminer) — cả 2 dùng chung code, chạy độc lập không xung đột |

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
│   ├── mysql_import.sql    # schema + dữ liệu MySQL — dùng file này để import
│   └── dump_20260703.sql   # backup dump gốc (PostgreSQL) — chỉ để đối chiếu, không dùng để import
├── docker/Dockerfile       # php:8.2-apache + pdo_mysql + mod_rewrite
├── docker-compose.yml      # web Apache + db MySQL 8.0 + Adminer
└── .env.example
```

## Chạy bằng XAMPP

Yêu cầu: đã cài **XAMPP** (Apache + MySQL + phpMyAdmin).

> ⚠️ **Bắt buộc: Virtual Host.** App được viết để chạy ở **docroot gốc** (route `/`, `/products`, `/cart`... không có tiền tố). Nếu bạn mở thẳng `http://localhost/project_LTW/public/`, các link/route sẽ **404** vì URI thực tế có tiền tố `/project_LTW/public`. Bắt buộc phải tạo Virtual Host trỏ `DocumentRoot` thẳng vào thư mục `public/` (xem bước 5 bên dưới) — đây không phải bước tuỳ chọn.

### Các bước cài đặt

1. **Copy project vào `htdocs`** của XAMPP, ví dụ `C:\xampp\htdocs\project_LTW` (Windows) hoặc `/Applications/XAMPP/xamppfiles/htdocs/project_LTW` (Mac).

2. **Tạo file cấu hình:**
   ```bash
   cp .env.example .env
   ```

3. Mở **XAMPP Control Panel**, start **Apache** và **MySQL**.
   > Nếu MySQL không khởi động được (đứng ở "Starting..." rồi tắt), rất có thể máy bạn đã có sẵn MySQL/MariaDB khác chiếm cổng 3306 (MAMP, Homebrew, cài native...). Cách kiểm tra: `lsof -nP -iTCP:3306 -sTCP:LISTEN` (Mac/Linux). Nếu có tiến trình khác đang giữ cổng, đổi cổng MySQL của XAMPP sang cổng khác (vd 3307) trong `xampp/etc/my.cnf` (sửa cả `port` ở mục `[client]` và `[mysqld]`), rồi cập nhật `.env` (`DB_PORT`) cho khớp.

4. Mở **phpMyAdmin** (`http://localhost/phpmyadmin`), tạo database mới tên `fashion_shop`, collation `utf8mb4_general_ci`. Vào database đó → tab **Import** → chọn file `database/mysql_import.sql` → **Go**.

   (Hoặc dòng lệnh, thay `<port>` bằng cổng MySQL thật của bạn — 3306 nếu mặc định:)
   ```bash
   mysql -u root -P <port> -h 127.0.0.1 fashion_shop < database/mysql_import.sql
   ```

5. **Tạo Virtual Host** (bắt buộc — xem cảnh báo ở trên). Thêm vào `xampp/etc/extra/httpd-vhosts.conf`:
   ```apache
   <VirtualHost *:80>
       DocumentRoot "/duong/dan/toi/project_LTW/public"
       ServerName project-ltw.local
       <Directory "/duong/dan/toi/project_LTW/public">
           Options Indexes FollowSymLinks
           AllowOverride All
           Require all granted
       </Directory>
   </VirtualHost>
   ```
   Bỏ comment dòng `Include etc/extra/httpd-vhosts.conf` trong `xampp/etc/httpd.conf` nếu đang bị comment.
   Thêm vào file hosts của hệ điều hành (`/etc/hosts` trên Mac/Linux, `C:\Windows\System32\drivers\etc\hosts` trên Windows — cần quyền admin):
   ```
   127.0.0.1 project-ltw.local
   ```
   Restart Apache trong XAMPP Control Panel.

6. Kiểm tra `.env` đã đúng `DB_HOST`, `DB_PORT` (khớp với cổng MySQL thật của bạn), `DB_USER=root`, `DB_PASS=` (để trống nếu không đặt mật khẩu).

7. Truy cập **`http://project-ltw.local/`**.

### Nạp lại dữ liệu (re-import)

`mysql_import.sql` tự `DROP TABLE` trước khi tạo lại, nên chạy lại file này bất cứ lúc nào là an toàn — chỉ cần import lại là schema + dữ liệu về đúng trạng thái ban đầu.

## Chạy bằng Docker

Yêu cầu: đã cài **Docker Desktop** (có Docker Compose v2). Cách này chạy ở docroot gốc nên **không cần** Virtual Host — route hoạt động ngay, giống hệt cách chạy PHP built-in server.

```bash
# 1. Tạo file cấu hình từ mẫu
cp .env.example .env

# 2. Build & chạy (web + db + adminer)
docker compose up -d --build

# 3. Mở trình duyệt
#    Storefront : http://localhost:8000
#    Adminer    : http://localhost:8081   (server: db, user: shop, pass: shop_secret, db: fashion_shop)
```

`docker-compose.yml` dùng image `mysql:8.0` và tự động import `database/mysql_import.sql` khi container `db` khởi tạo **lần đầu** (volume rỗng). Cổng host của DB là `3308:3306` (khác cổng MySQL của XAMPP/máy host) nên **chạy song song với XAMPP không xung đột** — bạn có thể bật cả 2 cùng lúc, mỗi bên có database riêng độc lập.

### Nạp lại dữ liệu (re-seed) trong Docker

Init script chỉ chạy khi volume dữ liệu còn trống. Muốn nạp lại từ đầu:

```bash
docker compose down -v      # xoá volume mysqldata
docker compose up -d --build
```

## Tài khoản demo

| Vai trò | Email | Mật khẩu |
|---|---|---|
| **Admin** | `admin@shop.test` | `admin123` |
| Khách hàng | `an@shop.test` | `123456` |
| Khách hàng | `binh@shop.test` | `123456` |

Đăng nhập admin rồi vào **http://project-ltw.local/admin** để xem dashboard và quản trị.

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
curl -X POST http://project-ltw.local/payment/sepay/webhook \
  -H "Authorization: Apikey <SEPAY_API_KEY>" -H "Content-Type: application/json" \
  -d '{"transferType":"in","transferAmount":<SỐ_TIỀN>,"content":"DH<id>","referenceCode":"FT123"}'
```

> File `.env` chứa thông tin nhạy cảm nên **không** được commit (đã có trong `.gitignore`).

## Chạy nhanh bằng PHP built-in server (tuỳ chọn, không cần Apache)

Cần PHP 8.2+ (bật extension `pdo_mysql`) và MySQL/MariaDB chạy sẵn (XAMPP MySQL hoặc cài riêng):

```bash
mysql -u root -P <port> -h 127.0.0.1 -e "CREATE DATABASE fashion_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
mysql -u root -P <port> -h 127.0.0.1 fashion_shop < database/mysql_import.sql
# sửa DB_HOST/DB_PORT trong .env cho khớp MySQL của bạn
php -S localhost:8000 -t public
```
Cách này chạy đúng ở docroot gốc (`localhost:8000/`) nên route hoạt động bình thường mà không cần Virtual Host.

## Danh sách chức năng

### Khách (chưa đăng nhập)

| Chức năng | Route |
|---|---|
| Trang chủ (hero, danh mục, sản phẩm nổi bật) | `GET /` |
| Danh sách sản phẩm (lọc / sắp xếp / phân trang / tìm kiếm) | `GET /products` |
| Chi tiết sản phẩm (gallery, chọn biến thể, đánh giá) | `GET /product/{slug}` |
| Trang tĩnh About / Care / FAQ | `GET /about`, `/care`, `/faq` |
| Xem giỏ hàng | `GET /cart` |
| Thêm / cập nhật / xoá sản phẩm trong giỏ (AJAX) | `POST /cart/add`, `/cart/update`, `/cart/remove` |
| Badge số lượng giỏ hàng | `GET /cart/count` |
| Đăng ký tài khoản | `GET /register`, `POST /register` |
| Đăng nhập | `GET /login`, `POST /login` |
| Đăng xuất | `POST /logout` |

### Người dùng đã đăng nhập

| Chức năng | Route |
|---|---|
| Trang tài khoản (thông tin cá nhân) | `GET /account` |
| Cập nhật hồ sơ | `POST /account/profile` |
| Lịch sử đơn hàng | `GET /account/orders` |
| Chi tiết đơn hàng | `GET /account/order/{id}` |
| Đặt hàng (checkout) | `GET /checkout` → `POST /checkout` |
| Trang xác nhận đơn thành công | `GET /order/success/{id}` |
| Gửi đánh giá sản phẩm | `POST /review` |

### Thanh toán

| Phương thức | Flow |
|---|---|
| **COD** | Đặt hàng → trạng thái `pending`, thu tiền khi giao |
| **SePay QR** | `POST /checkout` → `GET /payment/sepay/{id}` (hiện QR) → webhook `POST /payment/sepay/webhook` tự cập nhật `paid` |
| **VNPay** | `POST /payment/vnpay/create` → redirect cổng VNPay → `GET /payment/vnpay/return` |

### Admin (`/admin` — yêu cầu role `admin`)

| Module | Chức năng | Route |
|---|---|---|
| Dashboard | Biểu đồ doanh thu 14 ngày, đơn theo trạng thái, top sản phẩm | `GET /admin`, `/admin/stats` |
| Sản phẩm | Xem danh sách, tạo mới, sửa, xoá, upload ảnh, quản lý biến thể | `GET|POST /admin/products/...` |
| Danh mục | Xem, tạo, sửa, xoá | `GET|POST /admin/categories/...` |
| Đơn hàng | Xem danh sách, chi tiết, cập nhật trạng thái | `GET|POST /admin/orders/...` |
| Người dùng | Xem danh sách, đổi role, xoá | `GET|POST /admin/users/...` |
| Đánh giá | Xem danh sách, xoá | `GET|POST /admin/reviews/...` |

---

## Flow hoạt động

### Mua hàng (khách)

```
Xem sản phẩm (/products → /product/{slug})
        ↓
Chọn biến thể → Thêm giỏ hàng (AJAX)
        ↓
/cart  →  /checkout  →  POST /checkout
        ↓
  ┌─────┴──────┬──────────────┐
 COD        SePay QR       VNPay
  │      /payment/sepay    /payment/vnpay/create
  │       (QR + polling)      (redirect)
  │       webhook auto     /payment/vnpay/return
  └─────┬──────┴──────────────┘
        ↓
 /order/success/{id}  →  /account/orders
        ↓
 POST /review  (đánh giá sản phẩm)
```

### Vòng đời đơn hàng

```
pending  →  confirmed  →  shipped  →  delivered
                                           ↑
  (COD)  hoặc  paid (SePay/VNPay)  →  confirmed  →  shipped  →  delivered
                                           ↑
                              Admin cập nhật trạng thái tại /admin/orders
```

### Luồng xác thực & phân quyền

```
Request đến bất kỳ /admin/*
        ↓
Auth::requireRole('admin')
        ├── Chưa đăng nhập  → redirect /login (401)
        ├── Đã đăng nhập nhưng không phải admin → 403
        └── Admin hợp lệ → vào controller
```

### Kiến trúc request

```
Browser  →  public/index.php  →  Router  →  Controller
                                                 ↓
                                            Model (PDO/MySQL)
                                                 ↓
                                          View (PHP template)  →  Response HTML
```

---

Đồ án môn Lập trình Web — ATELIER · PHP thuần + MySQL.
