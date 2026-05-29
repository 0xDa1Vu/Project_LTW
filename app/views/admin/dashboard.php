<div class="stat-cards">
    <div class="stat-card"><span class="stat-label">Doanh thu</span><span class="stat-value"><?= vnd($stats['revenue']) ?></span></div>
    <div class="stat-card"><span class="stat-label">Đơn hàng</span><span class="stat-value"><?= (int) $stats['orders'] ?></span></div>
    <div class="stat-card"><span class="stat-label">Sản phẩm</span><span class="stat-value"><?= (int) $stats['products'] ?></span></div>
    <div class="stat-card"><span class="stat-label">Khách hàng</span><span class="stat-value"><?= (int) $stats['users'] ?></span></div>
</div>

<div class="chart-grid">
    <div class="chart-box">
        <h3>Doanh thu 14 ngày</h3>
        <canvas id="revenueChart"></canvas>
    </div>
    <div class="chart-box">
        <h3>Đơn theo trạng thái</h3>
        <canvas id="statusChart"></canvas>
    </div>
    <div class="chart-box chart-wide">
        <h3>Top sản phẩm bán chạy</h3>
        <canvas id="topChart"></canvas>
    </div>
</div>
