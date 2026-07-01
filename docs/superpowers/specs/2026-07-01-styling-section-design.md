# Styling Section — Design Spec

## Mục tiêu

Thêm một section "STYLING" trên trang chủ, layout và cách hoạt động giống section New Arrival/Best Seller hiện có, hiển thị các ảnh lookbook full-body (phối đồ mẫu) để khách tham khảo. Đây là ảnh minh họa thuần túy — không liên kết tới sản phẩm cụ thể.

Tham khảo thiết kế: section "STYLING" trên levents.asia (dùng làm mẫu tham khảo bố cục, không sao chép nội dung/ảnh).

## 1. Database

Bảng mới `stylings`:

| Cột | Kiểu | Ghi chú |
|---|---|---|
| id | INT PK AUTO_INCREMENT | |
| title | VARCHAR | Tên hiển thị dưới ảnh |
| image | VARCHAR | Đường dẫn ảnh, lưu tại `public/uploads/` |
| sort_order | INT | Thứ tự hiển thị |
| created_at | TIMESTAMP | |
| updated_at | TIMESTAMP | |

Không có bảng liên kết sản phẩm — mỗi look chỉ là ảnh + tiêu đề.

## 2. Backend

- `app/Models/Styling.php` — CRUD cơ bản theo pattern các Model hiện có (vd. `Category.php`)
- `app/Controllers/Admin/StylingController.php` — index/create/store/edit/update/destroy, theo pattern `ProductController.php`
- Route admin: `/admin/stylings` (list, create, edit, delete)
- HomeController: lấy danh sách styling (theo `sort_order`) để truyền vào view trang chủ

## 3. Frontend — Trang chủ

Vị trí: ngay sau section carousel New Arrival/Best Seller, trước các lifestyle grid (ÁO/QUẦN/PHỤ KIỆN), trong `app/Views/home/index.php`.

**Cấu trúc:**
- Header: tiêu đề "STYLING" bên trái, nút "Xem tất cả" bên phải → dẫn tới `/stylings` (trang danh sách đầy đủ, tương tự `/products`)
- Danh sách ảnh: tái dùng cơ chế carousel JS đã có (giống New Arrival) nếu số lượng look nhiều hơn số hiển thị cùng lúc

**Styling / CSS (theo spec đã chốt):**
- Nền trắng full-screen, khoảng trắng lớn trên dưới section
- 4 ảnh full-body xếp ngang một hàng (desktop)
- `display:flex; justify-content:space-between; align-items:flex-end; gap:48px`
- `max-width:1700px; margin:120px auto`
- Chiều cao ảnh đồng nhất ~700px desktop, ảnh nền trắng/transparent
- Không border, không card, không shadow, không nền container
- Tiêu đề dưới mỗi ảnh: căn giữa, `font-family: Inter, Helvetica, Arial, sans-serif`, `font-size:13px`, `color:#111`, `line-height:2`, `letter-spacing:0.2px`
- Responsive: 4 cột desktop → 2 cột tablet → 1 cột mobile
- Animation: fade-up khi scroll vào viewport, 0.6s, opacity 0→1, translateY(20px)→0

**Tương tác:** click vào ảnh mở modal/lightbox phóng to ảnh + tiêu đề trên trang chủ (không cần trang chi tiết riêng). Modal tái dùng pattern search-drawer hiện có: overlay, nút đóng (X), phím Esc, khóa scroll nền.

## 4. Admin — Quản lý Styling

Trang `/admin/stylings`, theo pattern các trang admin hiện có (products, coupons):

- **Danh sách:** bảng gồm ảnh thumbnail, tiêu đề, thứ tự, nút Sửa/Xóa, nút "Thêm mới"
- **Form tạo/sửa:** `title` (text), `image` (upload file, lưu `public/uploads/` theo cách upload ảnh sản phẩm hiện tại), `sort_order` (số, tùy chọn)
- **Xóa:** xác nhận trước khi xóa; xóa cả file ảnh liên quan trên server

Không cần chọn sản phẩm liên kết, không cần mô tả dài — chỉ ảnh + tiêu đề.

## Ngoài phạm vi (out of scope)

- Không có trang chi tiết riêng cho từng look (route `/styling/{id}`) — dùng modal thay thế
- Không liên kết sản phẩm cụ thể trong ảnh
- Không có mô tả/nội dung dài dạng blog
