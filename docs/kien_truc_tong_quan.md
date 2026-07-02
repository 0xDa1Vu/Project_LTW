# Kiến trúc tổng quan — ATELIER (dành cho người mới / thuyết trình)

Tài liệu này giải thích **hệ thống hoạt động như thế nào**, dùng cho người chưa biết gì về dự án. Muốn xem danh sách route/tính năng đầy đủ → xem [README.md](../README.md). Muốn xem ERD/sequence diagram → xem [phan_tich_thiet_ke.md](phan_tich_thiet_ke.md).

## 1. Bài toán & lựa chọn công nghệ

Website bán hàng thời trang (đồ án môn Lập trình Web), gồm 2 phần:
- **Storefront**: khách xem sản phẩm, mua hàng.
- **Admin**: quản trị viên quản lý sản phẩm/đơn hàng.

Điểm đặc biệt: **không dùng framework** (không Laravel/Symfony). Cả nhóm tự viết một MVC "nhẹ" (~9 file trong `app/core/`) để hiểu rõ framework hoạt động ra sao bên dưới, thay vì chỉ gọi API có sẵn.

| Lớp | Công nghệ | Vì sao |
|---|---|---|
| Ngôn ngữ | PHP 8.2 thuần | Không framework, dễ debug từng bước |
| DB | PostgreSQL 16 qua PDO | Prepared statements chống SQLi; PDO là chuẩn, dễ port sang MySQL nếu cần |
| Frontend | HTML/CSS/JS thuần + Chart.js (CDN) | Không cần build step (Webpack/Vite), mở file là chạy |
| Triển khai | Docker Compose (web + db + adminer) | Đồng bộ môi trường giữa các máy trong nhóm |

## 2. MVC tự viết hoạt động như thế nào

```
Browser
   │  gõ URL, ví dụ GET /product/ao-thun-basic
   ▼
public/index.php   ← Front Controller — MỌI request đều chui qua 1 file này
   │  (Apache .htaccess rewrite tất cả URL không phải file thật về đây)
   ▼
app/bootstrap.php  ← nạp autoload, session, config, các hàm helper e()/vnd()/cfg()
   ▼
app/routes.php     ← danh sách route: $router->get('/product/{slug}', [ProductController::class, 'show'])
   ▼
Router::dispatch()      (app/core/Router.php)
   │  so khớp URL với pattern route bằng regex, tách ra tham số ({id}, {slug})
   ▼
Controller (VD ProductController::show($slug))
   │  gọi Model để lấy dữ liệu
   ▼
Model (VD Product::findBySlug($slug))   — chạy PDO prepared statement lên PostgreSQL
   │  trả về mảng dữ liệu (array), KHÔNG phải object ORM
   ▼
Controller nhận dữ liệu → gọi $this->view('product/show', ['product' => $product])
   ▼
View (app/views/product/show.php)  — PHP thuần trộn HTML, dùng e($x) để escape chống XSS
   ▼
Layout (app/views/layouts/main.php)  — header/footer bọc quanh nội dung view
   ▼
HTML trả về Browser
```

**4 khái niệm cốt lõi cần nhớ khi thuyết trình:**

1. **Front Controller** (`public/index.php`): điểm vào duy nhất. Không có file PHP nào khác được truy cập trực tiếp từ web — bảo mật hơn vì code nghiệp vụ nằm ngoài `public/`.
2. **Router** (`app/core/Router.php`): map `(HTTP method, URL pattern) → [Controller, action]`. Tự viết regex đơn giản, không cần thư viện ngoài.
3. **Model = 1 class/1 bảng**, kế thừa `App\Core\Model` (`app/core/Model.php`) để có sẵn `find()`, `all()`, `insert()`, `update()`, `delete()`. Model con chỉ cần khai `$table` và viết thêm query đặc thù.
4. **Controller = 1 class/1 nhóm chức năng**, kế thừa `App\Core\Controller` (`app/core/Controller.php`) để có sẵn `view()` (render HTML), `json()` (trả AJAX), `redirect()`.

## 3. Sơ đồ thư mục — vai trò từng phần

```
public/            ← DOCROOT (chỉ thư mục này lộ ra web)
  index.php          front controller
  css/ js/ uploads/  tài nguyên tĩnh + ảnh admin upload

app/               ← toàn bộ code nghiệp vụ, KHÔNG truy cập trực tiếp được từ web
  bootstrap.php      autoload + session + helpers toàn cục
  routes.php         khai báo route (danh sách URL hợp lệ)
  core/              "framework mini" tự viết: Router, Model, Controller,
                      Database (kết nối PDO singleton), Auth (đăng nhập/phân quyền),
                      Session, Csrf (chống CSRF), Validator, Str (helper chuỗi)
  controllers/       xử lý logic theo từng nhóm (Cart, Order, Payment...) + admin/
  models/            1 class / 1 bảng DB
  views/             template HTML lồng PHP, có layouts/ (khung sườn) dùng chung

config/config.php   đọc file .env → mảng cấu hình (đọc qua hàm cfg('key.path'))
database/
  schema.sql         định nghĩa 11 bảng + FK + CHECK constraint
  seed.sql           dữ liệu mẫu, tự nạp khi container DB khởi tạo lần đầu
docker/, docker-compose.yml   môi trường chạy đồng nhất
```

## 4. Vòng đời một request điển hình — ví dụ "Thêm vào giỏ hàng"

1. Người dùng bấm nút "Thêm giỏ" trên trang sản phẩm → JS (`cart.js`) gửi `POST /cart/add` bằng `fetch()`.
2. `Router` khớp route, gọi `CartController::add()`.
3. Controller đọc `$this->input('variant_id')`, `$this->input('quantity')`, kiểm tra `Csrf::verify()`.
4. Controller gọi `Cart` model → `INSERT/UPDATE cart_items` qua PDO prepared statement.
5. Controller trả JSON (`$this->json(['ok' => true, 'count' => ...])`) — không phải HTML, vì đây là AJAX.
6. JS phía client nhận JSON, cập nhật badge số lượng giỏ hàng trên giao diện — không reload trang.

## 5. Bảo mật — áp dụng ở đâu trong code

| Nguy cơ | Cách chặn | Vị trí |
|---|---|---|
| SQL Injection | PDO prepared statements — không bao giờ nối chuỗi SQL trực tiếp | mọi Model (`app/models/*.php`) |
| XSS | Helper `e()` = `htmlspecialchars()`, dùng khi in dữ liệu ra view | `app/bootstrap.php` → dùng trong `app/views/*` |
| CSRF | Token ẩn trong form (`Csrf::field()`), server kiểm tra (`Csrf::verify()`) trước khi xử lý POST | `app/core/Csrf.php` |
| Truy cập trái phép trang admin | Middleware `Auth::requireRole('admin')` chạy đầu mỗi controller admin | `app/core/Auth.php`, `app/controllers/Admin/*` |
| Upload file độc hại | Kiểm tra MIME type, giới hạn dung lượng, đổi tên file random | controller xử lý upload ảnh sản phẩm |

## 6. Điểm hay để nhấn khi thuyết trình cho newbie

- **"Tự viết MVC" không phải để phát minh lại bánh xe** — mà để hiểu Laravel/Symfony đang làm gì bên dưới lớp vỏ "magic". Sau khi hiểu Router/Model/Controller tự viết, học framework thật sẽ dễ hơn nhiều.
- **Model trả về `array`, không phải object** — đơn giản hóa vì không cần ORM đầy đủ (không lazy-loading, không relationship tự động). Đây là đánh đổi hợp lý cho quy mô đồ án.
- **`order_items` lưu snapshot** (tên, giá tại thời điểm mua) thay vì chỉ lưu `product_id` — để đơn hàng cũ không bị sai lệch khi sản phẩm gốc bị sửa/xóa sau này. Đây là một pattern thực tế hay gặp trong hệ thống e-commerce thật.
- **3 phương thức thanh toán minh họa 3 kiểu tích hợp khác nhau**: COD (không cần gateway), VNPay (redirect + callback đồng bộ), SePay (webhook bất đồng bộ + polling từ client) — dùng để nói về sự khác biệt giữa flow đồng bộ và bất đồng bộ khi tích hợp thanh toán.
