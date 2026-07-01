# Guest Checkout — Design Spec
**Date:** 2026-06-25

## Goal
Cho phép khách (chưa đăng nhập) đặt hàng và thanh toán mà không cần tạo tài khoản, tương tự flow của Levents.asia.

## Scope
- Bỏ bắt buộc đăng nhập ở trang checkout và đặt hàng.
- Guest vẫn xem được trang success/QR trong cùng phiên trình duyệt (qua session).
- Không thay đổi DB schema (`user_id` đã nullable).
- Không thêm trang tra cứu đơn cho guest.

## Các thay đổi

### OrderController.php
| Method | Thay đổi |
|---|---|
| `checkout()` | Bỏ `Auth::require()`. Lấy cart bằng `Auth::id()` (nếu đăng nhập) hoặc `session_id()` (guest). Pre-fill form nếu đã login, để trống nếu guest. |
| `place()` | Bỏ `Auth::require()`. Dùng `Auth::id() ?? null` cho `user_id`. Sau khi tạo đơn, lưu `$_SESSION['guest_order_id'] = $orderId`. |
| `success()` | Bỏ `Auth::require()`. Nếu đã login: dùng `findForUser()`. Nếu guest: kiểm tra `$_SESSION['guest_order_id'] === $id`. Nếu không khớp → 404. |

### checkout/index.php
- Thay `$user['name']` → `$user['name'] ?? ''` (tương tự cho phone, address) để pre-fill khi login, trống khi guest.

## Không thay đổi
- DB schema
- CartController (cart đã hỗ trợ guest qua session_id)
- PaymentController (VNPay/SePay redirect không đổi)
- Admin order management
