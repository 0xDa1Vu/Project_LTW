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

  // ---- 5b. Trang chủ: đổi nền navbar khi cuộn xuống ----
  var siteHeader = document.getElementById('siteHeader');
  if (siteHeader && document.body.classList.contains('home')) {
    var onScroll = function () {
      if (window.scrollY > 40) siteHeader.classList.add('scrolled');
      else siteHeader.classList.remove('scrolled');
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();
  }

  // ---- 6. Carousel tabs (New Arrival / Best Seller) ----
  var carouselTabs = document.querySelectorAll('.carousel-tab');
  if (carouselTabs.length) {
    carouselTabs.forEach(function (tab) {
      tab.addEventListener('click', function () {
        carouselTabs.forEach(function (t) { t.classList.remove('active'); });
        tab.classList.add('active');
        var target = tab.getAttribute('data-tab');
        document.getElementById('tabNew').hidden = (target !== 'new');
        document.getElementById('tabBest').hidden = (target !== 'best');
      });
    });
  }

  // ---- 7. Carousel prev/next ----
  function initCarousel(wrapperId, trackId) {
    var wrapper = document.getElementById(wrapperId);
    var track = document.getElementById(trackId);
    if (!wrapper || !track) return;
    var cards = track.querySelectorAll('.product-card');
    if (!cards.length) return;
    var idx = 0;

    function visibleCount() {
      if (window.innerWidth <= 680) return 1;
      if (window.innerWidth <= 980) return 2;
      return 4;
    }

    function slide() {
      var pct = (100 / visibleCount()) * idx;
      track.style.transform = 'translateX(-' + pct + '%)';
    }

    var prev = wrapper.querySelector('.carousel-prev');
    var next = wrapper.querySelector('.carousel-next');
    var max = cards.length - visibleCount();

    if (prev) prev.addEventListener('click', function () {
      idx = Math.max(0, idx - 1);
      slide();
    });
    if (next) next.addEventListener('click', function () {
      max = cards.length - visibleCount();
      idx = Math.min(max, idx + 1);
      slide();
    });
    window.addEventListener('resize', function () { idx = 0; slide(); }, { passive: true });
  }
  initCarousel('tabNew', 'trackNew');
  initCarousel('tabBest', 'trackBest');

  // ---- 5. Form lọc: submit ngay khi đổi select ----
  var filterForm = document.getElementById('filterForm');
  if (filterForm) {
    Array.prototype.forEach.call(filterForm.querySelectorAll('select'), function (sel) {
      sel.addEventListener('change', function () { filterForm.submit(); });
    });
  }
})();
