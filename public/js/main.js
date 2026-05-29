// main.js — tương tác chung cho trang khách.
// Được nhúng ở layouts/main.php (sau cart.js).
(function () {
  'use strict';

  // ---- 1. Hamburger menu (mobile) ----
  var navToggle = document.getElementById('navToggle');
  var mainNav = document.getElementById('mainNav');
  if (navToggle && mainNav) {
    navToggle.addEventListener('click', function (e) {
      e.stopPropagation();
      var open = mainNav.classList.toggle('open');
      navToggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
    // Đóng menu khi bấm ra ngoài
    document.addEventListener('click', function (e) {
      if (!mainNav.contains(e.target) && !navToggle.contains(e.target)) {
        mainNav.classList.remove('open');
        navToggle.setAttribute('aria-expanded', 'false');
      }
    });
  }

  // ---- 2. Tự ẩn toast (flash từ server) sau 3s ----
  var stack = document.getElementById('toastStack');
  if (stack) {
    Array.prototype.forEach.call(stack.querySelectorAll('.toast'), function (t) {
      setTimeout(function () {
        t.classList.add('toast-out');
        setTimeout(function () { t.remove(); }, 300);
      }, 3000);
    });
  }

  // ---- 3. Dropdown tài khoản (toggle khi bấm, đóng khi click ngoài) ----
  var dropBtn = document.querySelector('.dropdown-btn');
  if (dropBtn) {
    var dropdown = dropBtn.closest('.dropdown');
    dropBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      dropdown.classList.toggle('open');
    });
    document.addEventListener('click', function () {
      dropdown.classList.remove('open');
    });
  }

  // ---- 4. Form lọc: submit ngay khi đổi select ----
  var filterForm = document.getElementById('filterForm');
  if (filterForm) {
    Array.prototype.forEach.call(filterForm.querySelectorAll('select'), function (sel) {
      sel.addEventListener('change', function () { filterForm.submit(); });
    });
  }
})();
