<?php
/**
 * SPARE XPRESS LTD — Sales & Inventory Dashboard
 * Dark "Electric Modern" analytics view wired to live order/inventory data.
 */
require_once __DIR__ . '/includes/auth.php';   // enforces admin session, provides $conn

/* ---------- helpers ---------- */
function q_all($conn, $sql) {
    $rows = [];
    if ($res = $conn->query($sql)) {
        while ($r = $res->fetch_assoc()) { $rows[] = $r; }
    }
    return $rows;
}
function q_one($conn, $sql) {
    $res = $conn->query($sql);
    return $res ? $res->fetch_assoc() : null;
}
function rwf($n) { return 'RWF ' . number_format((float)$n); }

$ok_orders = "order_status NOT IN ('cancelled','failed','refunded')";

/* ---------- KPIs ---------- */
$kpi = q_one($conn, "SELECT
    (SELECT COALESCE(SUM(total_amount),0) FROM orders_enhanced WHERE $ok_orders) AS revenue,
    (SELECT COUNT(*) FROM orders_enhanced) AS orders,
    (SELECT COUNT(*) FROM orders_enhanced WHERE $ok_orders) AS valid_orders,
    (SELECT COALESCE(SUM(quantity),0) FROM order_items_enhanced) AS units,
    (SELECT COUNT(*) FROM products_enhanced) AS products,
    (SELECT COALESCE(SUM(COALESCE(regular_price,price,0)*stock_quantity),0) FROM products_enhanced) AS inv_value,
    (SELECT COUNT(*) FROM products_enhanced WHERE stock_quantity > 0 AND stock_quantity <= low_stock_threshold) AS low_stock,
    (SELECT COUNT(*) FROM products_enhanced WHERE stock_quantity <= 0) AS out_stock,
    (SELECT COUNT(*) FROM products_enhanced WHERE stock_quantity > low_stock_threshold) AS in_stock
");
$aov = $kpi['valid_orders'] > 0 ? $kpi['revenue'] / $kpi['valid_orders'] : 0;

/* month-over-month revenue delta */
$this_month = (float) (q_one($conn, "SELECT COALESCE(SUM(total_amount),0) v FROM orders_enhanced WHERE $ok_orders AND DATE_FORMAT(created_at,'%Y-%m')=DATE_FORMAT(CURDATE(),'%Y-%m')")['v'] ?? 0);
$last_month = (float) (q_one($conn, "SELECT COALESCE(SUM(total_amount),0) v FROM orders_enhanced WHERE $ok_orders AND DATE_FORMAT(created_at,'%Y-%m')=DATE_FORMAT(CURDATE() - INTERVAL 1 MONTH,'%Y-%m')")['v'] ?? 0);
$rev_delta = $last_month > 0 ? (($this_month - $last_month) / $last_month) * 100 : ($this_month > 0 ? 100 : 0);

/* ---------- revenue trend (last 12 months) ---------- */
$rows = q_all($conn, "SELECT DATE_FORMAT(created_at,'%Y-%m') ym, ROUND(SUM(total_amount)) rev, COUNT(*) cnt
                      FROM orders_enhanced WHERE $ok_orders GROUP BY ym");
$by_month = [];
foreach ($rows as $r) { $by_month[$r['ym']] = $r; }
$trend_labels = $trend_rev = $trend_orders = [];
for ($i = 11; $i >= 0; $i--) {
    $ym = date('Y-m', strtotime("first day of -$i month"));
    $trend_labels[]  = date('M', strtotime($ym . '-01'));
    $trend_rev[]     = isset($by_month[$ym]) ? (float)$by_month[$ym]['rev'] : 0;
    $trend_orders[]  = isset($by_month[$ym]) ? (int)$by_month[$ym]['cnt'] : 0;
}

/* ---------- inventory value by category ---------- */
$cat = q_all($conn, "SELECT COALESCE(c.category_name,'Uncategorized') name,
                            ROUND(SUM(COALESCE(p.regular_price,p.price,0)*p.stock_quantity)) val
                     FROM products_enhanced p
                     LEFT JOIN categories_enhanced c ON p.category_id=c.id
                     GROUP BY c.category_name HAVING val > 0 ORDER BY val DESC");
$cat_top = array_slice($cat, 0, 5);
$cat_other = 0; foreach (array_slice($cat, 5) as $c) { $cat_other += (float)$c['val']; }
$cat_labels = array_map(fn($c) => $c['name'], $cat_top);
$cat_vals   = array_map(fn($c) => (float)$c['val'], $cat_top);
if ($cat_other > 0) { $cat_labels[] = 'Other'; $cat_vals[] = $cat_other; }

/* ---------- top brands by sales ---------- */
$brands = q_all($conn, "SELECT COALESCE(NULLIF(product_brand,''),'—') name, ROUND(SUM(subtotal)) val
                        FROM order_items_enhanced GROUP BY product_brand HAVING val > 0 ORDER BY val DESC LIMIT 6");

/* ---------- order status funnel ---------- */
$status_rows = q_all($conn, "SELECT order_status s, COUNT(*) n FROM orders_enhanced GROUP BY order_status");
$status_map = []; foreach ($status_rows as $r) { $status_map[$r['s']] = (int)$r['n']; }
$status_order = ['pending','confirmed','processing','shipped','delivered'];

/* ---------- recent orders ---------- */
$recent = q_all($conn, "SELECT o.order_number, o.total_amount, o.order_status, o.created_at,
                               COALESCE(CONCAT(c.first_name,' ',c.last_name),'Guest') customer
                        FROM orders_enhanced o
                        LEFT JOIN customers_enhanced c ON o.customer_id=c.id
                        ORDER BY o.created_at DESC LIMIT 7");

/* ---------- inventory at risk ---------- */
$risk = q_all($conn, "SELECT p.product_name, COALESCE(c.category_name,'—') cat,
                             p.stock_quantity stock, p.low_stock_threshold thr,
                             ROUND(COALESCE(p.regular_price,p.price,0)*p.stock_quantity) val
                      FROM products_enhanced p
                      LEFT JOIN categories_enhanced c ON p.category_id=c.id
                      WHERE p.stock_quantity <= p.low_stock_threshold
                      ORDER BY p.stock_quantity ASC, val DESC LIMIT 12");

$status_badge = function($s) {
    $map = [
        'pending'=>['Pending','warn'], 'confirmed'=>['Confirmed','info'],
        'processing'=>['Processing','info'], 'shipped'=>['Shipped','info'],
        'delivered'=>['Delivered','good'], 'cancelled'=>['Cancelled','crit'],
        'refunded'=>['Refunded','crit'], 'failed'=>['Failed','crit'],
    ];
    return $map[$s] ?? [ucfirst($s), 'muted'];
};
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Sales &amp; Inventory — SPARE XPRESS LTD</title>
<link rel="icon" type="image/jpeg" href="/img/logo/icon.jpg">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" rel="stylesheet">
<script src="/js/vendor/chart.umd.min.js"></script>
<style>
:root{
  --bg:#0B1220; --panel:#111C32; --panel-2:#16233E; --border:rgba(148,163,184,.14);
  --text:#E8EEF7; --muted:#94A3B8; --accent:#2DD4FF; --accent-2:#38BDF8;
  --good:#0ca30c; --warn:#c98500; --crit:#e66767;
  --s1:#3987e5; --s2:#d95926; --s3:#199e70; --s4:#c98500; --s5:#d55181; --s6:#64748B;
}
*{box-sizing:border-box}
body{margin:0;background:var(--bg);color:var(--text);font-family:'Inter',system-ui,sans-serif;font-size:14px}
h1,h2,h3,h4,.num{font-family:'Space Grotesk','Inter',sans-serif;letter-spacing:-.01em}
a{color:inherit;text-decoration:none}
.layout{display:flex;min-height:100vh}
/* sidebar */
.side{width:230px;flex-shrink:0;background:linear-gradient(180deg,#0E1A30,#0B1220);border-right:1px solid var(--border);padding:20px 14px;position:sticky;top:0;height:100vh;overflow-y:auto}
.brand{display:flex;align-items:center;gap:10px;padding:4px 8px 20px}
.brand .logo{width:34px;height:34px;border-radius:9px;background:var(--accent);display:grid;place-items:center;color:#06283b;font-weight:800;font-family:'Space Grotesk'}
.brand b{font-size:15px}.brand span{color:var(--accent)}
.nav-group{color:var(--muted);font-size:10.5px;text-transform:uppercase;letter-spacing:1px;margin:16px 10px 6px}
.nav-item{display:flex;align-items:center;gap:11px;padding:9px 11px;border-radius:9px;color:#C4D0E0;font-weight:500;margin-bottom:2px}
.nav-item i{width:16px;text-align:center;color:var(--muted)}
.nav-item.active{background:rgba(45,212,255,.12);color:#fff}
.nav-item.active i{color:var(--accent)}
.nav-item:hover:not(.active){background:rgba(255,255,255,.04)}
.side-user{display:flex;align-items:center;gap:10px;margin-top:22px;padding:10px;border-top:1px solid var(--border)}
.side-user .av{width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,var(--accent),#7C3AED);display:grid;place-items:center;font-weight:700;color:#06283b}
/* main */
.main{flex:1;padding:22px 26px;min-width:0}
.top{display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:20px;flex-wrap:wrap}
.top h1{margin:0;font-size:22px}
.top .sub{color:var(--muted);font-size:12.5px;margin-top:2px}
.pill{background:var(--panel);border:1px solid var(--border);border-radius:10px;padding:8px 14px;color:var(--text);font-size:13px;display:inline-flex;gap:8px;align-items:center}
.grid{display:grid;gap:16px}
.kpis{grid-template-columns:repeat(6,1fr);margin-bottom:16px}
.card{background:var(--panel);border:1px solid var(--border);border-radius:16px;padding:16px 18px}
.kpi .label{color:var(--muted);font-size:12px;font-weight:500;display:flex;align-items:center;gap:7px}
.kpi .val{font-size:23px;font-weight:700;margin:8px 0 4px}
.kpi .delta{font-size:11.5px;font-weight:600}
.delta.up{color:#34d399}.delta.down{color:var(--crit)}.delta.flat{color:var(--muted)}
.cols-3{grid-template-columns:1.6fr 1fr 1fr}
.cols-2{grid-template-columns:1fr 1fr}
.cols-risk{grid-template-columns:1.1fr 1fr 1fr}
.panel-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px}
.panel-head h3{margin:0;font-size:15px}
.panel-head .tag{color:var(--muted);font-size:11.5px}
.chart-wrap{position:relative;height:230px}
.donut-wrap{position:relative;height:200px}
/* bar list */
.barlist{display:flex;flex-direction:column;gap:12px}
.barrow .brow-top{display:flex;justify-content:space-between;font-size:12.5px;margin-bottom:5px}
.barrow .brow-top b{font-weight:600}.barrow .brow-top span{color:var(--muted)}
.track{height:8px;border-radius:99px;background:rgba(255,255,255,.06);overflow:hidden}
.track i{display:block;height:100%;border-radius:99px;background:linear-gradient(90deg,var(--s1),var(--accent))}
/* funnel */
.funnel{display:flex;flex-direction:column;gap:9px}
.fstep{display:flex;align-items:center;gap:10px}
.fstep .fbar{height:30px;border-radius:8px;background:linear-gradient(90deg,var(--accent),var(--s1));display:flex;align-items:center;padding:0 10px;color:#06283b;font-weight:700;font-size:12px;min-width:34px}
.fstep .fname{width:92px;color:var(--muted);font-size:12px;text-transform:capitalize}
/* lists & tables */
.list{display:flex;flex-direction:column}
.lrow{display:flex;align-items:center;justify-content:space-between;padding:10px 0;border-bottom:1px solid var(--border)}
.lrow:last-child{border-bottom:0}
.lrow .lmeta b{font-size:13px}.lrow .lmeta span{color:var(--muted);font-size:11.5px;display:block}
table{width:100%;border-collapse:collapse}
th{text-align:left;color:var(--muted);font-weight:600;font-size:11px;text-transform:uppercase;letter-spacing:.5px;padding:10px 12px;border-bottom:1px solid var(--border)}
td{padding:11px 12px;border-bottom:1px solid var(--border);font-size:13px}
tr:last-child td{border-bottom:0}
.badge{padding:3px 10px;border-radius:99px;font-size:11px;font-weight:600;display:inline-block}
.badge.good{background:rgba(12,163,12,.16);color:#4ade80}
.badge.warn{background:rgba(201,133,0,.18);color:#fbbf24}
.badge.crit{background:rgba(230,103,103,.16);color:#fca5a5}
.badge.info{background:rgba(57,135,229,.18);color:#7db3f5}
.badge.muted{background:rgba(148,163,184,.16);color:#cbd5e1}
.mb16{margin-bottom:16px}
@media(max-width:1100px){.kpis{grid-template-columns:repeat(3,1fr)}.cols-3,.cols-2,.cols-risk{grid-template-columns:1fr}.side{display:none}}
</style>
</head>
<body>
<div class="layout">
  <!-- Sidebar -->
  <aside class="side">
    <div class="brand"><div class="logo">SX</div><div><b>SPARE <span>XPRESS</span></b></div></div>
    <div class="nav-group">Analytics</div>
    <a class="nav-item active"><i class="fas fa-chart-pie"></i> Overview</a>
    <a class="nav-item" href="enhanced_dashboard.php"><i class="fas fa-gauge"></i> Admin Home</a>
    <a class="nav-item" href="orders/enhanced_order_management.php"><i class="fas fa-receipt"></i> Orders</a>
    <a class="nav-item" href="orders/analytics.php"><i class="fas fa-chart-line"></i> Order Analytics</a>
    <div class="nav-group">Inventory</div>
    <a class="nav-item" href="products/enhanced_product_management.php"><i class="fas fa-boxes-stacked"></i> Products</a>
    <a class="nav-item" href="brands/enhanced_brand_management.php"><i class="fas fa-trademark"></i> Brands</a>
    <a class="nav-item" href="categories/enhanced_category_management.php"><i class="fas fa-layer-group"></i> Categories</a>
    <div class="nav-group">Account</div>
    <a class="nav-item" href="../index.php"><i class="fas fa-store"></i> View Store</a>
    <a class="nav-item" href="logout.php"><i class="fas fa-right-from-bracket"></i> Logout</a>
    <div class="side-user">
      <div class="av"><?php echo strtoupper(substr($_SESSION['admin_name'] ?? $_SESSION['admin'] ?? 'A',0,1)); ?></div>
      <div><b style="font-size:13px"><?php echo htmlspecialchars($_SESSION['admin_name'] ?? $_SESSION['admin'] ?? 'Admin'); ?></b>
      <span style="color:var(--muted);font-size:11px"><?php echo htmlspecialchars(ucfirst($_SESSION['admin_role'] ?? 'admin')); ?></span></div>
    </div>
  </aside>

  <!-- Main -->
  <main class="main">
    <div class="top">
      <div>
        <h1>Sales &amp; Inventory</h1>
        <div class="sub">Live overview of orders, revenue and stock · SPARE XPRESS LTD</div>
      </div>
      <div class="pill"><i class="far fa-calendar"></i> As of <?php echo date('d M Y, H:i'); ?></div>
    </div>

    <!-- KPI row -->
    <div class="grid kpis">
      <?php
      $delta_cls = $rev_delta > 0.5 ? 'up' : ($rev_delta < -0.5 ? 'down' : 'flat');
      $delta_ic  = $rev_delta > 0.5 ? '▲' : ($rev_delta < -0.5 ? '▼' : '■');
      $tiles = [
        ['Total Revenue','fa-sack-dollar', rwf($kpi['revenue']), '<span class="delta '.$delta_cls.'">'.$delta_ic.' '.number_format(abs($rev_delta),1).'% vs last month</span>'],
        ['Orders','fa-receipt', number_format($kpi['orders']), '<span class="delta flat">'.$kpi['valid_orders'].' active</span>'],
        ['Units Sold','fa-cubes', number_format($kpi['units']), '<span class="delta flat">across all orders</span>'],
        ['Avg Order Value','fa-tags', rwf($aov), '<span class="delta flat">per valid order</span>'],
        ['Inventory Value','fa-warehouse', rwf($kpi['inv_value']), '<span class="delta flat">'.$kpi['products'].' products</span>'],
        ['Low / Out of Stock','fa-triangle-exclamation', number_format($kpi['low_stock'] + $kpi['out_stock']), '<span class="delta down">'.$kpi['out_stock'].' out of stock</span>'],
      ];
      foreach ($tiles as $t): ?>
        <div class="card kpi">
          <div class="label"><i class="fas <?php echo $t[1]; ?>" style="color:var(--accent)"></i><?php echo $t[0]; ?></div>
          <div class="val num"><?php echo $t[2]; ?></div>
          <?php echo $t[3]; ?>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Row 2: trend + donut + stock health -->
    <div class="grid cols-3 mb16">
      <div class="card">
        <div class="panel-head"><h3>Revenue Trend</h3><span class="tag">Last 12 months</span></div>
        <div class="chart-wrap"><canvas id="revChart"></canvas></div>
      </div>
      <div class="card">
        <div class="panel-head"><h3>Inventory Value</h3><span class="tag">by category</span></div>
        <div class="donut-wrap"><canvas id="catChart"></canvas></div>
      </div>
      <div class="card">
        <div class="panel-head"><h3>Stock Health</h3><span class="tag"><?php echo $kpi['products']; ?> SKUs</span></div>
        <div class="donut-wrap"><canvas id="stockChart"></canvas></div>
      </div>
    </div>

    <!-- Row 3: brands + order status + recent -->
    <div class="grid cols-3 mb16">
      <div class="card">
        <div class="panel-head"><h3>Top Brands by Sales</h3><span class="tag">revenue</span></div>
        <div class="barlist">
          <?php
          $bmax = 0; foreach ($brands as $b) { $bmax = max($bmax,(float)$b['val']); }
          if (!$brands) echo '<div style="color:var(--muted);font-size:13px">No sales recorded yet.</div>';
          foreach ($brands as $b): $w = $bmax>0 ? round(100*(float)$b['val']/$bmax) : 0; ?>
            <div class="barrow">
              <div class="brow-top"><b><?php echo htmlspecialchars($b['name']); ?></b><span><?php echo rwf($b['val']); ?></span></div>
              <div class="track"><i style="width:<?php echo max($w,4); ?>%"></i></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="card">
        <div class="panel-head"><h3>Order Status</h3><span class="tag">pipeline</span></div>
        <div class="funnel">
          <?php
          $smax = 0; foreach ($status_order as $s) { $smax = max($smax, $status_map[$s] ?? 0); }
          foreach ($status_order as $s): $n = $status_map[$s] ?? 0; $w = $smax>0 ? round(100*$n/$smax) : 0; ?>
            <div class="fstep">
              <div class="fname"><?php echo $s; ?></div>
              <div class="fbar" style="width:<?php echo max($w,8); ?>%"><?php echo $n; ?></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="card">
        <div class="panel-head"><h3>Recent Orders</h3><a class="tag" href="orders/enhanced_order_management.php" style="color:var(--accent)">View all</a></div>
        <div class="list">
          <?php if (!$recent) echo '<div style="color:var(--muted);font-size:13px">No orders yet.</div>';
          foreach ($recent as $o): [$lbl,$cl] = $status_badge($o['order_status']); ?>
            <div class="lrow">
              <div class="lmeta"><b><?php echo htmlspecialchars($o['order_number']); ?></b>
                <span><?php echo htmlspecialchars($o['customer']); ?> · <?php echo date('d M', strtotime($o['created_at'])); ?></span></div>
              <div style="text-align:right"><b class="num"><?php echo rwf($o['total_amount']); ?></b><br>
                <span class="badge <?php echo $cl; ?>" style="margin-top:4px"><?php echo $lbl; ?></span></div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- Inventory at risk -->
    <div class="card">
      <div class="panel-head"><h3>Inventory at Risk</h3><a class="tag" href="products/enhanced_product_management.php" style="color:var(--accent)">Manage stock</a></div>
      <div style="overflow-x:auto">
      <table>
        <thead><tr><th>Product</th><th>Category</th><th>Stock</th><th>Threshold</th><th>Stock Value</th><th>Status</th></tr></thead>
        <tbody>
        <?php if (!$risk) echo '<tr><td colspan="6" style="color:var(--muted)">All products above their low-stock threshold. 🎉</td></tr>';
        foreach ($risk as $p):
          $out = (int)$p['stock'] <= 0;
          [$lbl,$cl] = $out ? ['Out of stock','crit'] : ['Low stock','warn']; ?>
          <tr>
            <td style="max-width:280px"><?php echo htmlspecialchars($p['product_name']); ?></td>
            <td style="color:var(--muted)"><?php echo htmlspecialchars($p['cat']); ?></td>
            <td class="num"><?php echo (int)$p['stock']; ?></td>
            <td class="num" style="color:var(--muted)"><?php echo (int)$p['thr']; ?></td>
            <td class="num"><?php echo rwf($p['val']); ?></td>
            <td><span class="badge <?php echo $cl; ?>"><?php echo $lbl; ?></span></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
      </div>
    </div>

    <div style="color:var(--muted);font-size:11.5px;text-align:center;margin:22px 0 8px">
      SPARE XPRESS LTD · figures from live database · generated <?php echo date('Y-m-d H:i'); ?>
    </div>
  </main>
</div>

<script>
const css = getComputedStyle(document.documentElement);
const c = n => css.getPropertyValue(n).trim();
Chart.defaults.color = c('--muted');
Chart.defaults.font.family = "'Inter',sans-serif";
Chart.defaults.borderColor = 'rgba(148,163,184,.12)';

const fmtRWF = v => 'RWF ' + Number(v).toLocaleString();

/* Revenue trend — single measure, one axis */
const rc = document.getElementById('revChart').getContext('2d');
const grad = rc.createLinearGradient(0,0,0,230);
grad.addColorStop(0,'rgba(45,212,255,.35)'); grad.addColorStop(1,'rgba(45,212,255,0)');
new Chart(rc,{type:'line',
  data:{labels:<?php echo json_encode($trend_labels); ?>,
    datasets:[{label:'Revenue',data:<?php echo json_encode($trend_rev); ?>,
      borderColor:c('--accent'),backgroundColor:grad,borderWidth:2,fill:true,tension:.4,
      pointRadius:0,pointHoverRadius:5,pointHoverBackgroundColor:c('--accent')}]},
  options:{responsive:true,maintainAspectRatio:false,
    plugins:{legend:{display:false},tooltip:{callbacks:{label:x=>fmtRWF(x.parsed.y)}}},
    scales:{y:{beginAtZero:true,ticks:{callback:v=>v>=1000?(v/1000)+'k':v}},x:{grid:{display:false}}}}});

/* Inventory value by category — categorical (validated hues, fixed order) */
new Chart(document.getElementById('catChart'),{type:'doughnut',
  data:{labels:<?php echo json_encode($cat_labels); ?>,
    datasets:[{data:<?php echo json_encode($cat_vals); ?>,
      backgroundColor:[c('--s1'),c('--s2'),c('--s3'),c('--s4'),c('--s5'),c('--s6')],
      borderColor:c('--panel'),borderWidth:3,hoverOffset:6}]},
  options:{responsive:true,maintainAspectRatio:false,cutout:'62%',
    plugins:{legend:{position:'bottom',labels:{boxWidth:10,boxHeight:10,padding:10,font:{size:11}}},
      tooltip:{callbacks:{label:x=>x.label+': '+fmtRWF(x.parsed)}}}}});

/* Stock health — reserved status colors */
new Chart(document.getElementById('stockChart'),{type:'doughnut',
  data:{labels:['In stock','Low stock','Out of stock'],
    datasets:[{data:[<?php echo (int)$kpi['in_stock']; ?>,<?php echo (int)$kpi['low_stock']; ?>,<?php echo (int)$kpi['out_stock']; ?>],
      backgroundColor:[c('--good'),c('--warn'),c('--crit')],
      borderColor:c('--panel'),borderWidth:3,hoverOffset:6}]},
  options:{responsive:true,maintainAspectRatio:false,cutout:'62%',
    plugins:{legend:{position:'bottom',labels:{boxWidth:10,boxHeight:10,padding:10,font:{size:11}}},
      tooltip:{callbacks:{label:x=>x.label+': '+x.parsed+' SKUs'}}}}});
</script>
</body>
</html>
