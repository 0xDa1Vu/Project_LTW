// Giỏ hàng: thêm (ajax), cập nhật số lượng, xoá, đồng bộ badge.
(function () {
  function toast(msg, type) {
    let stack = document.getElementById('toastStack');
    if (!stack) {
      stack = document.createElement('div');
      stack.id = 'toastStack';
      stack.className = 'toast-stack';
      document.body.appendChild(stack);
    }
    const t = document.createElement('div');
    t.className = 'toast toast-' + (type || 'info');
    t.textContent = msg;
    stack.appendChild(t);
    setTimeout(() => t.remove(), 3000);
  }
  window.toast = toast;

  function fmtVND(n) {
    return new Intl.NumberFormat('vi-VN').format(Math.round(n)) + '₫';
  }

  // Badge số lượng giỏ
  function refreshCount() {
    fetch('/cart/count')
      .then(r => r.json())
      .then(d => {
        const el = document.getElementById('cartCount');
        if (el) el.textContent = d.count;
      })
      .catch(() => {});
  }
  refreshCount();

  // Thêm vào giỏ (trang chi tiết sản phẩm)
  const addForm = document.getElementById('addToCartForm');
  if (addForm) {
    addForm.addEventListener('submit', function (e) {
      e.preventDefault();
      const fd = new FormData(addForm);
      if (!fd.get('variant_id')) {
        toast('Vui lòng chọn size/màu.', 'error');
        return;
      }
      fetch('/cart/add', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(d => {
          toast(d.message, d.ok ? 'success' : 'error');
          if (d.ok) {
            const el = document.getElementById('cartCount');
            if (el) el.textContent = d.count;
          }
        })
        .catch(() => toast('Có lỗi xảy ra.', 'error'));
    });
  }

  // Trang giỏ hàng: cập nhật & xoá
  const cartWrap = document.querySelector('.cart-items');
  if (cartWrap) {
    const csrf = cartWrap.dataset.csrf;

    cartWrap.addEventListener('change', function (e) {
      if (!e.target.classList.contains('cart-qty-input')) return;
      const row = e.target.closest('.cart-row');
      const qty = Math.max(1, parseInt(e.target.value) || 1);
      e.target.value = qty;
      const fd = new FormData();
      fd.append('_csrf', csrf);
      fd.append('cart_item_id', row.dataset.id);
      fd.append('quantity', qty);
      fetch('/cart/update', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(d => {
          if (d.ok) {
            row.querySelector('.cart-line').textContent = fmtVND(d.line_total);
            document.getElementById('cartTotal').textContent = fmtVND(d.total);
          }
        });
    });

    cartWrap.addEventListener('click', function (e) {
      if (!e.target.classList.contains('cart-remove')) return;
      const row = e.target.closest('.cart-row');
      const fd = new FormData();
      fd.append('_csrf', csrf);
      fd.append('cart_item_id', row.dataset.id);
      fetch('/cart/remove', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(d => {
          if (d.ok) {
            row.remove();
            document.getElementById('cartTotal').textContent = fmtVND(d.total);
            const el = document.getElementById('cartCount');
            if (el) el.textContent = d.count;
            toast('Đã xoá khỏi giỏ.', 'info');
            if (!document.querySelector('.cart-row')) location.reload();
          }
        });
    });
  }
})();
