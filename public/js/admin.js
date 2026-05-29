// admin.js — tương tác trang quản trị + biểu đồ dashboard (Chart.js).
// Nhúng ở layouts/admin.php (Chart.js CDN đứng trước).
(function () {
  'use strict';

  // ---- 1. Toggle sidebar (mobile) ----
  var menuToggle = document.getElementById('adminMenuToggle');
  var sidebar = document.getElementById('adminSidebar');
  if (menuToggle && sidebar) {
    menuToggle.addEventListener('click', function (e) {
      e.stopPropagation();
      sidebar.classList.toggle('open');
    });
    document.addEventListener('click', function (e) {
      if (window.innerWidth <= 900 &&
          !sidebar.contains(e.target) && !menuToggle.contains(e.target)) {
        sidebar.classList.remove('open');
      }
    });
  }

  // ---- 2. Tự ẩn toast sau 3s ----
  var stack = document.getElementById('toastStack');
  if (stack) {
    Array.prototype.forEach.call(stack.querySelectorAll('.toast'), function (t) {
      setTimeout(function () {
        t.classList.add('toast-out');
        setTimeout(function () { t.remove(); }, 300);
      }, 3000);
    });
  }

  // ---- 3. "+ Thêm biến thể": nhân bản dòng trong #variantTable ----
  var addRowBtn = document.getElementById('addVariantRow');
  var variantTable = document.getElementById('variantTable');
  if (addRowBtn && variantTable) {
    var tbody = variantTable.querySelector('tbody');
    addRowBtn.addEventListener('click', function () {
      var tr = document.createElement('tr');
      tr.innerHTML =
        '<td><input type="text" name="v_size[]" value=""></td>' +
        '<td><input type="text" name="v_color[]" value=""></td>' +
        '<td><input type="number" name="v_stock[]" value="0"></td>' +
        '<td><button type="button" class="link-btn danger">✕</button></td>';
      tr.querySelector('button').addEventListener('click', function () {
        tr.remove();
      });
      tbody.appendChild(tr);
    });
  }

  // ---- 4. Dashboard: vẽ biểu đồ Chart.js từ /admin/stats ----
  var revenueCanvas = document.getElementById('revenueChart');
  if (revenueCanvas && typeof Chart !== 'undefined') {
    fetch('/admin/stats')
      .then(function (r) { return r.json(); })
      .then(function (d) { drawCharts(d); })
      .catch(function () {});
  }

  function drawCharts(d) {
    var INK = '#1a1a1a';
    var palette = ['#1a1a1a', '#6b7280', '#9ca3af', '#d1d5db', '#374151', '#e5e7eb'];

    Chart.defaults.font.family = "Inter, system-ui, sans-serif";
    Chart.defaults.color = '#555';

    // 4.1 Doanh thu 14 ngày — line
    var rev = d.revenue || [];
    new Chart(document.getElementById('revenueChart'), {
      type: 'line',
      data: {
        labels: rev.map(function (x) { return x.day; }),
        datasets: [{
          label: 'Doanh thu',
          data: rev.map(function (x) { return Number(x.amount); }),
          borderColor: INK,
          backgroundColor: 'rgba(26,26,26,0.08)',
          fill: true,
          tension: 0.3,
          pointRadius: 3
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
          y: {
            beginAtZero: true,
            ticks: { callback: function (v) { return fmtShort(v); } }
          }
        }
      }
    });

    // 4.2 Đơn theo trạng thái — doughnut
    var st = d.byStatus || [];
    var labelMap = {
      pending: 'Chờ xử lý', confirmed: 'Đã xác nhận', shipping: 'Đang giao',
      completed: 'Hoàn tất', cancelled: 'Đã huỷ'
    };
    new Chart(document.getElementById('statusChart'), {
      type: 'doughnut',
      data: {
        labels: st.map(function (x) { return labelMap[x.status] || x.status; }),
        datasets: [{
          data: st.map(function (x) { return Number(x.cnt); }),
          backgroundColor: palette,
          borderWidth: 1,
          borderColor: '#fff'
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } }
      }
    });

    // 4.3 Top sản phẩm bán chạy — bar ngang
    var top = d.topProducts || [];
    new Chart(document.getElementById('topChart'), {
      type: 'bar',
      data: {
        labels: top.map(function (x) { return x.name; }),
        datasets: [{
          label: 'Đã bán',
          data: top.map(function (x) { return Number(x.sold); }),
          backgroundColor: INK,
          borderRadius: 4
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { x: { beginAtZero: true, ticks: { precision: 0 } } }
      }
    });
  }

  function fmtShort(v) {
    if (v >= 1e9) return (v / 1e9).toFixed(1) + ' tỷ';
    if (v >= 1e6) return (v / 1e6).toFixed(1) + ' tr';
    if (v >= 1e3) return (v / 1e3).toFixed(0) + 'k';
    return v;
  }
})();
