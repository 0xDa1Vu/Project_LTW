<?php
use App\Core\Router;
use App\Controllers\HomeController;
use App\Controllers\PageController;
use App\Controllers\ProductController;
use App\Controllers\CartController;
use App\Controllers\OrderController;
use App\Controllers\AuthController;
use App\Controllers\AccountController;
use App\Controllers\ReviewController;
use App\Controllers\PaymentController;
use App\Controllers\CouponController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\ProductController as AdminProduct;
use App\Controllers\Admin\CategoryController as AdminCategory;
use App\Controllers\Admin\OrderController as AdminOrder;
use App\Controllers\Admin\UserController as AdminUser;
use App\Controllers\Admin\ReviewController as AdminReview;

$router = new Router();

// ---- Khách ----
$router->get('/', [HomeController::class, 'index']);

// ---- Trang tĩnh ----
$router->get('/about', [PageController::class, 'about']);
$router->get('/care', [PageController::class, 'care']);
$router->get('/faq', [PageController::class, 'faq']);

$router->get('/products', [ProductController::class, 'index']);
$router->get('/product/{slug}', [ProductController::class, 'show']);

$router->get('/cart', [CartController::class, 'index']);
$router->post('/cart/add', [CartController::class, 'add']);
$router->post('/cart/update', [CartController::class, 'update']);
$router->post('/cart/remove', [CartController::class, 'remove']);
$router->get('/cart/count', [CartController::class, 'count']);

$router->get('/checkout', [OrderController::class, 'checkout']);
$router->post('/checkout', [OrderController::class, 'place']);
$router->get('/order/success/{id}', [OrderController::class, 'success']);

// ---- Auth ----
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->post('/logout', [AuthController::class, 'logout']);

// ---- Tài khoản ----
$router->get('/account', [AccountController::class, 'index']);
$router->get('/account/orders', [AccountController::class, 'orders']);
$router->get('/account/order/{id}', [AccountController::class, 'orderDetail']);
$router->post('/account/profile', [AccountController::class, 'updateProfile']);

// ---- Đánh giá ----
$router->post('/review', [ReviewController::class, 'store']);

// ---- Thanh toán SePay (chuyển khoản QR) ----
$router->get('/payment/sepay/{id}', [PaymentController::class, 'sepayShow']);
$router->get('/payment/sepay/check/{id}', [PaymentController::class, 'sepayCheck']);
$router->post('/payment/sepay/webhook', [PaymentController::class, 'sepayWebhook']);
$router->post('/payment/method/{id}', [PaymentController::class, 'changeMethod']);
$router->post('/coupon/apply', [CouponController::class, 'apply']);

// ---- Admin ----
$router->get('/admin', [DashboardController::class, 'index']);
$router->get('/admin/stats', [DashboardController::class, 'stats']);

$router->get('/admin/products', [AdminProduct::class, 'index']);
$router->get('/admin/products/create', [AdminProduct::class, 'create']);
$router->post('/admin/products/store', [AdminProduct::class, 'store']);
$router->get('/admin/products/edit/{id}', [AdminProduct::class, 'edit']);
$router->post('/admin/products/update/{id}', [AdminProduct::class, 'update']);
$router->post('/admin/products/delete/{id}', [AdminProduct::class, 'destroy']);
$router->post('/admin/products/image/reorder', [AdminProduct::class, 'reorderImages']);
$router->post('/admin/products/image/delete/{id}', [AdminProduct::class, 'deleteImage']);
$router->post('/admin/products/image/primary/{id}', [AdminProduct::class, 'setPrimaryImage']);

$router->get('/admin/categories', [AdminCategory::class, 'index']);
$router->post('/admin/categories/store', [AdminCategory::class, 'store']);
$router->post('/admin/categories/update/{id}', [AdminCategory::class, 'update']);
$router->post('/admin/categories/delete/{id}', [AdminCategory::class, 'destroy']);

$router->get('/admin/orders', [AdminOrder::class, 'index']);
$router->get('/admin/orders/{id}', [AdminOrder::class, 'show']);
$router->post('/admin/orders/status/{id}', [AdminOrder::class, 'updateStatus']);

$router->get('/admin/users', [AdminUser::class, 'index']);
$router->post('/admin/users/role/{id}', [AdminUser::class, 'updateRole']);
$router->post('/admin/users/delete/{id}', [AdminUser::class, 'destroy']);

$router->get('/admin/reviews', [AdminReview::class, 'index']);
$router->post('/admin/reviews/delete/{id}', [AdminReview::class, 'destroy']);

return $router;
