// sepay.js — polling trạng thái thanh toán chuyển khoản QR.
(function () {
  'use strict';

  var wrap = document.getElementById('sepayWrap') || document.querySelector('.sepay-box');
  if (!wrap) return;

  var orderId = wrap.getAttribute('data-order-id');
  var btn      = document.getElementById('sepayCheckBtn');
  var statusEl = document.getElementById('sepayStatus');
  var badge    = document.getElementById('sp2Badge');
  var pollTimer = null;
  var stopped   = false;

  function showStatus(msg, ok) {
    if (!statusEl) return;
    statusEl.hidden = false;
    statusEl.textContent = msg;
    statusEl.style.background = ok ? '#e8f5e9' : '#fff8e1';
    statusEl.style.color      = ok ? '#2e7d32' : '#b8860b';
  }

  function onPaid(message) {
    stopped = true;
    if (pollTimer) clearInterval(pollTimer);
    showStatus(message || 'Đã nhận thanh toán! Đang chuyển hướng...', true);
    if (badge) {
      badge.textContent = '✅ Đã thanh toán';
      badge.className = 'sp2-pay-badge sp2-badge-paid';
    }
    if (btn) btn.disabled = true;
    setTimeout(function () {
      window.location.href = '/order/success/' + orderId;
    }, 1500);
  }

  function check(manual) {
    if (stopped) return;
    fetch('/payment/sepay/check/' + orderId, { headers: { 'Accept': 'application/json' } })
      .then(function (r) { return r.json(); })
      .then(function (d) {
        if (d.paid) {
          onPaid(d.message);
        } else if (manual) {
          showStatus('Chưa nhận được chuyển khoản. Vui lòng đợi và thử lại.', false);
        }
      })
      .catch(function () {
        if (manual) showStatus('Không kết nối được máy chủ. Vui lòng thử lại.', false);
      });
  }

  if (btn) {
    btn.addEventListener('click', function () { check(true); });
  }

  // Tự polling mỗi 5 giây
  pollTimer = setInterval(function () { check(false); }, 5000);
})();
