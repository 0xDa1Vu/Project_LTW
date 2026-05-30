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

  // ---- 4. Nút SEARCH: mở/đóng drawer trượt từ phải ----
  var searchToggle = document.getElementById('searchToggle');
  var searchDrawer = document.getElementById('searchDrawer');
  var searchOverlay = document.getElementById('searchOverlay');
  var searchClose = document.getElementById('searchClose');
  if (searchToggle && searchDrawer && searchOverlay) {
    var openSearch = function () {
      searchDrawer.hidden = false;
      searchOverlay.hidden = false;
      // đợi 1 frame để transition chạy từ trạng thái ẩn
      requestAnimationFrame(function () {
        searchDrawer.classList.add('show');
        searchOverlay.classList.add('show');
      });
      document.body.style.overflow = 'hidden';
      var inp = searchDrawer.querySelector('input');
      if (inp) setTimeout(function () { inp.focus(); }, 350);
    };
    var closeSearch = function () {
      searchDrawer.classList.remove('show');
      searchOverlay.classList.remove('show');
      document.body.style.overflow = '';
      setTimeout(function () {
        searchDrawer.hidden = true;
        searchOverlay.hidden = true;
      }, 350);
    };
    searchToggle.addEventListener('click', openSearch);
    searchOverlay.addEventListener('click', closeSearch);
    if (searchClose) searchClose.addEventListener('click', closeSearch);
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && !searchDrawer.hidden) closeSearch();
    });
  }

  // ---- 5. Form lọc: submit ngay khi đổi select ----
  var filterForm = document.getElementById('filterForm');
  if (filterForm) {
    Array.prototype.forEach.call(filterForm.querySelectorAll('select'), function (sel) {
      sel.addEventListener('change', function () { filterForm.submit(); });
    });
  }
})();
