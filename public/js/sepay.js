// sepay.js — trang chờ thanh toán chuyển khoản QR (SePay).
// Tự động hỏi server trạng thái đơn (do webhook SePay cập nhật) + nút kiểm tra thủ công.
(function () {
  'use strict';

  var box = document.querySelector('.sepay-box');
  if (!box) return;

  var orderId = box.getAttribute('data-order-id');
  var btn = document.getElementById('sepayCheckBtn');
  var statusEl = document.getElementById('sepayStatus');
  var pollTimer = null;
  var stopped = false;

  function showStatus(msg, ok) {
    statusEl.hidden = false;
    statusEl.textContent = msg;
    statusEl.className = 'sepay-status ' + (ok ? 'is-paid' : 'is-pending');
  }

  function onPaid(message) {
    stopped = true;
    if (pollTimer) clearInterval(pollTimer);
    showStatus(message || 'Đã nhận thanh toán! Đang chuyển hướng...', true);
    btn.disabled = true;
    setTimeout(function () {
      window.location.href = '/order/success/' + orderId;
    }, 1200);
  }

  function check(manual) {
    if (stopped) return;
    fetch('/payment/sepay/check/' + orderId, { headers: { 'Accept': 'application/json' } })
      .then(function (r) { return r.json(); })
      .then(function (d) {
        if (d.paid) {
          onPaid(d.message);
        } else if (manual) {
          showStatus(d.message || 'Chưa nhận được chuyển khoản. Vui lòng thử lại sau.', false);
        }
      })
      .catch(function () {
        if (manual) showStatus('Không kết nối được máy chủ. Vui lòng thử lại.', false);
      });
  }

  if (btn) {
    btn.addEventListener('click', function () { check(true); });
  }

  // Tự động kiểm tra mỗi 5 giây (webhook có thể xác nhận bất cứ lúc nào).
  pollTimer = setInterval(function () { check(false); }, 5000);
})();
