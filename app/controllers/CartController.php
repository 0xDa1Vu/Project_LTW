<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Core\Csrf;
use App\Models\Cart;
use App\Models\Variant;

class CartController extends Controller
{
    private Cart $cart;

    public function __construct()
    {
        $this->cart = new Cart();
    }

    private function currentCart(): array
    {
        return $this->cart->current(Auth::id(), session_id());
    }

    public function index(): void
    {
        $cart = $this->currentCart();
        $items = $this->cart->items((int) $cart['id']);
        $total = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $items));
        $this->view('cart/index', [
            'title' => 'Giỏ hàng',
            'items' => $items,
            'total' => $total,
        ]);
    }

    public function add(): void
    {
        Csrf::verify();
        $variantId = (int) $this->input('variant_id');
        $qty = max(1, (int) $this->input('quantity', 1));

        $variant = (new Variant())->withProduct($variantId);
        if (!$variant) {
            $this->json(['ok' => false, 'message' => 'Sản phẩm không tồn tại.'], 404);
        }
        if ($variant['stock'] < $qty) {
            $this->json(['ok' => false, 'message' => 'Không đủ hàng trong kho.'], 422);
        }

        $cart = $this->currentCart();
        $this->cart->addItem((int) $cart['id'], $variantId, $qty);
        $this->json([
            'ok' => true,
            'message' => 'Đã thêm vào giỏ hàng.',
            'count' => $this->cart->count((int) $cart['id']),
        ]);
    }

    public function update(): void
    {
        Csrf::verify();
        $cartItemId = (int) $this->input('cart_item_id');
        $qty = max(1, (int) $this->input('quantity', 1));
        $this->cart->updateItem($cartItemId, $qty);

        $cart = $this->currentCart();
        $items = $this->cart->items((int) $cart['id']);
        $total = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $items));
        $line = null;
        foreach ($items as $i) {
            if ((int) $i['cart_item_id'] === $cartItemId) {
                $line = $i['price'] * $i['quantity'];
            }
        }
        $this->json(['ok' => true, 'line_total' => $line, 'total' => $total]);
    }

    public function remove(): void
    {
        Csrf::verify();
        $cartItemId = (int) $this->input('cart_item_id');
        $this->cart->removeItem($cartItemId);
        $cart = $this->currentCart();
        $items = $this->cart->items((int) $cart['id']);
        $total = array_sum(array_map(fn($i) => $i['price'] * $i['quantity'], $items));
        $this->json([
            'ok' => true,
            'total' => $total,
            'count' => $this->cart->count((int) $cart['id']),
        ]);
    }

    public function count(): void
    {
        $cart = $this->currentCart();
        $this->json(['count' => $this->cart->count((int) $cart['id'])]);
    }
}
