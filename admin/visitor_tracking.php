<?php
// Visitor Tracking Dashboard - Admin Panel
include_once 'includes/auth.php';
include_once 'includes/functions.php';
include_once 'header.php';

// Date range filter
$days = isset($_GET['days']) ? (int)$_GET['days'] : 7;
$date_from = date('Y-m-d', strtotime("-{$days} days"));
$date_to = date('Y-m-d');

// Total visitors today
$today_count = countRowsWhere('visitor_tracking', "DATE(created_at) = CURDATE()");

// Unique visitors today
$unique_today = 0;
$r = $conn->query("SELECT COUNT(DISTINCT session_id) as cnt FROM visitor_tracking WHERE DATE(created_at) = CURDATE()");
if ($r) $unique_today = $r->fetch_assoc()['cnt'];

// Total visitors this week
$week_count = countRowsWhere('visitor_tracking', "created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");

// Total visitors all time
$total_count = countRows('visitor_tracking');
$total_unique = 0;
$r = $conn->query("SELECT COUNT(DISTINCT session_id) as cnt FROM visitor_tracking");
if ($r) $total_unique = $r->fetch_assoc()['cnt'];

// Average pages per visit
$avg_pages = 0;
$r = $conn->query("SELECT AVG(pages_viewed) as avg_p FROM visitor_tracking WHERE created_at >= DATE_SUB(NOW(), INTERVAL {$days} DAY)");
if ($r) $avg_pages = round($r->fetch_assoc()['avg_p'] ?? 0, 1);

// Average visit duration
$avg_duration = 0;
$r = $conn->query("SELECT AVG(visit_duration) as avg_d FROM visitor_tracking WHERE visit_duration > 0 AND created_at >= DATE_SUB(NOW(), INTERVAL {$days} DAY)");
if ($r) $avg_duration = round($r->fetch_assoc()['avg_d'] ?? 0, 0);

// Visitors by day (for chart)
$chart_data = [];
$r = $conn->query("SELECT DATE(created_at) as day, COUNT(*) as total, COUNT(DISTINCT session_id) as unique_visitors
                    FROM visitor_tracking 
                    WHERE created_at >= DATE_SUB(NOW(), INTERVAL {$days} DAY)
                    GROUP BY DATE(created_at) ORDER BY day ASC");
if ($r) {
    while ($row = $r->fetch_assoc()) {
        $chart_data[] = $row;
    }
}

// Device breakdown
$devices = [];
$r = $conn->query("SELECT device_type, COUNT(*) as count FROM visitor_tracking 
                    WHERE created_at >= DATE_SUB(NOW(), INTERVAL {$days} DAY)
                    GROUP BY device_type ORDER BY count DESC");
if ($r) {
    while ($row = $r->fetch_assoc()) {
        $devices[] = $row;
    }
}

// Browser breakdown
$browsers = [];
$r = $conn->query("SELECT browser, COUNT(*) as count FROM visitor_tracking 
                    WHERE created_at >= DATE_SUB(NOW(), INTERVAL {$days} DAY)
                    GROUP BY browser ORDER BY count DESC LIMIT 6");
if ($r) {
    while ($row = $r->fetch_assoc()) {
        $browsers[] = $row;
    }
}

// OS breakdown
$operating_systems = [];
$r = $conn->query("SELECT os, COUNT(*) as count FROM visitor_tracking 
                    WHERE created_at >= DATE_SUB(NOW(), INTERVAL {$days} DAY)
                    GROUP BY os ORDER BY count DESC LIMIT 6");
if ($r) {
    while ($row = $r->fetch_assoc()) {
        $operating_systems[] = $row;
    }
}

// Top pages
$top_pages = [];
$r = $conn->query("SELECT page_url, COUNT(*) as views, COUNT(DISTINCT session_id) as unique_visitors
                    FROM visitor_tracking 
                    WHERE created_at >= DATE_SUB(NOW(), INTERVAL {$days} DAY)
                    GROUP BY page_url ORDER BY views DESC LIMIT 10");
if ($r) {
    while ($row = $r->fetch_assoc()) {
        $top_pages[] = $row;
    }
}

// Recent visitors (last 30)
$recent_visitors = [];
$r = $conn->query("SELECT * FROM visitor_tracking 
                    ORDER BY created_at DESC LIMIT 30");
if ($r) {
    while ($row = $r->fetch_assoc()) {
        $recent_visitors[] = $row;
    }
}

// Online now (last 5 minutes)
$online_now = countRowsWhere('visitor_tracking', "updated_at >= DATE_SUB(NOW(), INTERVAL 5 MINUTE)");// Peak hour
$peak_hour = 'N/A';
$r = $conn->query("SELECT HOUR(created_at) as h, COUNT(*) as cnt FROM visitor_tracking 
                    WHERE created_at >= DATE_SUB(NOW(), INTERVAL {$days} DAY)
                    GROUP BY HOUR(created_at) ORDER BY cnt DESC LIMIT 1");
if ($r && $r->num_rows > 0) {
    $peak = $r->fetch_assoc();
    $peak_hour = date('g A', strtotime($peak['h'] . ':00:00'));
}

// Session Navigation Paths — show how visitors navigate through the site
$session_paths = [];
$r = $conn->query("SELECT session_id, page_url, browser, os, device_type, ip_address, created_at
                    FROM visitor_tracking
                    WHERE created_at >= DATE_SUB(NOW(), INTERVAL {$days} DAY)
                    ORDER BY session_id, created_at ASC");
if ($r) {
    $temp = [];
    while ($row = $r->fetch_assoc()) {
        $sid = $row['session_id'];
        if (!isset($temp[$sid])) {
            $temp[$sid] = [
                'session_id' => $sid,
                'browser' => $row['browser'],
                'os' => $row['os'],
                'device_type' => $row['device_type'],
                'ip_address' => $row['ip_address'],
                'pages' => [],
                'started' => $row['created_at'],
            ];
        }
        $temp[$sid]['pages'][] = $row['page_url'];
        $temp[$sid]['ended'] = $row['created_at'];
    }
    // Sort by most recently ended first, show sessions with 2+ pages (actual navigation)
    usort($temp, function($a, $b) {
        return strtotime($b['ended'] ?? $b['started']) - strtotime($a['ended'] ?? $a['started']);
    });
    foreach ($temp as $session) {
        if (count($session['pages']) >= 1) {
            $session_paths[] = $session;
        }
    }
    // Limit to 30 most recent sessions
    $session_paths = array_slice($session_paths, 0, 30);
}

// Page-to-page flow (what pages lead to what)
$page_flows = [];
$r = $conn->query("SELECT vt1.page_url as from_page, vt2.page_url as to_page, COUNT(*) as flow_count
                    FROM visitor_tracking vt1
                    INNER JOIN visitor_tracking vt2 ON vt1.session_id = vt2.session_id
                        AND vt2.created_at > vt1.created_at
                        AND vt2.created_at <= DATE_ADD(vt1.created_at, INTERVAL 5 MINUTE)
                    WHERE vt1.created_at >= DATE_SUB(NOW(), INTERVAL {$days} DAY)
                    GROUP BY vt1.page_url, vt2.page_url
                    HAVING flow_count >= 2
                    ORDER BY flow_count DESC
                    LIMIT 15");
if ($r) {
    while ($row = $r->fetch_assoc()) {
        $page_flows[] = $row;
    }
}?>

<style>
.visitor-stat-card {
    background: var(--card-bg);
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.07);
    border: none;
    padding: 1.25rem;
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}
.visitor-stat-card:hover { transform: translateY(-4px); box-shadow: 0 6px 24px rgba(0,0,0,0.12); }
.visitor-stat-card .stat-icon {
    width: 48px; height: 48px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem;
}
.visitor-stat-card .stat-value { font-size: 1.6rem; font-weight: 700; }
.visitor-stat-card .stat-label { font-size: 0.78rem; color: var(--text-muted); }

.chart-card {
    background: var(--card-bg);
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.07);
    border: none;
    overflow: hidden;
}

.mini-bar {
    height: 6px;
    border-radius: 3px;
    background: #e9ecef;
    overflow: hidden;
}
.mini-bar-fill {
    height: 100%;
    border-radius: 3px;
    transition: width 0.6s ease;
}

.visitor-row {
    padding: 10px 15px;
    border-bottom: 1px solid var(--border-color);
    transition: background 0.2s;
    font-size: 0.85rem;
}
.visitor-row:hover { background: var(--admin-light); }
.visitor-row:last-child { border-bottom: none; }

.device-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 8px;
    border-radius: 6px;
    font-size: 0.72rem;
    font-weight: 600;
}

.page-url {
    max-width: 250px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    font-family: 'SF Mono', monospace;
    font-size: 0.8rem;
    color: var(--admin-primary);
}

@media (max-width: 768px) {
    .visitor-stat-card .stat-value { font-size: 1.3rem; }
    .page-url { max-width: 150px; }
}
</style>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1 fw-bold"><i class="bi bi-graph-up-arrow me-2 text-primary"></i>Visitor Tracking</h4>
        <p class="text-muted mb-0" style="font-size:0.85rem;">Monitor who visits your website in real-time</p>
    </div>
    <div class="d-flex gap-2">
        <?php foreach ([7 => '7 Days', 14 => '14 Days', 30 => '30 Days', 90 => '90 Days'] as $d => $label): ?>
            <a href="?days=<?= $d ?>" class="btn btn-admin <?= $days === $d ? 'btn-primary' : 'btn-outline-secondary' ?>" style="font-size:0.8rem;padding:6px 12px;">
                <?= $label ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="visitor-stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-primary bg-opacity-10 text-primary"><i class="bi bi-eye"></i></div>
                <div>
                    <div class="stat-value"><?= number_format($today_count) ?></div>
                    <div class="stat-label">Visits Today</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="visitor-stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-success bg-opacity-10 text-success"><i class="bi bi-people"></i></div>
                <div>
                    <div class="stat-value"><?= number_format($unique_today) ?></div>
                    <div class="stat-label">Unique Visitors</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="visitor-stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-info bg-opacity-10 text-info"><i class="bi bi-wifi"></i></div>
                <div>
                    <div class="stat-value text-success"><?= $online_now ?></div>
                    <div class="stat-label">Online Now</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="visitor-stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-warning bg-opacity-10 text-warning"><i class="bi bi-clock-history"></i></div>
                <div>
                    <div class="stat-value"><?= $peak_hour ?></div>
                    <div class="stat-label">Peak Hour</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Second Row Stats -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="visitor-stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-danger bg-opacity-10 text-danger"><i class="bi bi-calendar-week"></i></div>
                <div>
                    <div class="stat-value"><?= number_format($week_count) ?></div>
                    <div class="stat-label">This Week</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="visitor-stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon bg-secondary bg-opacity-10 text-secondary"><i class="bi bi-globe"></i></div>
                <div>
                    <div class="stat-value"><?= number_format($total_unique) ?></div>
                    <div class="stat-label">All Unique Visitors</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="visitor-stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(139,92,246,0.1);color:#8b5cf6;"><i class="bi bi-file-earmark-text"></i></div>
                <div>
                    <div class="stat-value"><?= $avg_pages ?></div>
                    <div class="stat-label">Avg Pages/Visit</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="visitor-stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(16,185,129,0.1);color:#10b981;"><i class="bi bi-stopwatch"></i></div>
                <div>
                    <div class="stat-value"><?= $avg_duration ?>s</div>
                    <div class="stat-label">Avg Duration</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row g-3 mb-4">
    <!-- Visitor Chart -->
    <div class="col-lg-8">
        <div class="chart-card p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-bar-chart-line me-2 text-primary"></i>Visitors Over Time</h6>
            <div id="visitorChart" style="height: 280px; position: relative;">
                <!-- Simple bar chart built with CSS -->
                <div class="d-flex align-items-end gap-1" style="height: 240px; padding-bottom: 30px;">
                    <?php
                    $max_val = 1;
                    foreach ($chart_data as $cd) {
                        $max_val = max($max_val, $cd['total']);
                    }
                    foreach ($chart_data as $cd):
                        $pct = $max_val > 0 ? ($cd['total'] / $max_val) * 100 : 0;
                        $unique_pct = $max_val > 0 ? ($cd['unique_visitors'] / $max_val) * 100 : 0;
                    ?>
                        <div class="flex-fill text-center" style="position:relative; height:100%;">
                            <div style="position:absolute;bottom:28px;left:50%;transform:translateX(-50%);width:70%;">
                                <div class="mb-1" style="height:<?= $pct ?>%;min-height:2px;background:linear-gradient(180deg,#3b82f6,#2563eb);border-radius:4px 4px 0 0;transition:height 0.6s ease;" title="<?= $cd['total'] ?> visits"></div>
                                <div style="height:<?= $unique_pct ?>%;min-height:0;background:rgba(245,158,11,0.7);border-radius:4px 4px 0 0;margin-top:-<?= $pct ?>%;position:relative;top:-<?= $unique_pct ?>%;" title="<?= $cd['unique_visitors'] ?> unique"></div>
                            </div>
                            <div style="position:absolute;bottom:0;left:50%;transform:translateX(-50%);font-size:0.65rem;color:var(--text-muted);white-space:nowrap;">
                                <?= date('M d', strtotime($cd['day'])) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="d-flex gap-3 mt-2" style="font-size:0.75rem;">
                    <span><span style="display:inline-block;width:10px;height:10px;background:#3b82f6;border-radius:2px;margin-right:4px;"></span>Total Visits</span>
                    <span><span style="display:inline-block;width:10px;height:10px;background:#f59e0b;border-radius:2px;margin-right:4px;"></span>Unique Visitors</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Device & Browser Breakdown -->
    <div class="col-lg-4">
        <div class="chart-card p-4 mb-3">
            <h6 class="fw-bold mb-3"><i class="bi bi-phone me-2 text-primary"></i>Devices</h6>
            <?php
            $total_device = array_sum(array_column($devices, 'count'));
            $device_colors = ['desktop' => '#3b82f6', 'mobile' => '#10b981', 'tablet' => '#f59e0b', 'unknown' => '#94a3b8'];
            $device_icons = ['desktop' => 'bi-pc-display', 'mobile' => 'bi-phone', 'tablet' => 'bi-tablet', 'unknown' => 'bi-question-circle'];
            foreach ($devices as $d):
                $pct = $total_device > 0 ? round(($d['count'] / $total_device) * 100) : 0;
                $color = $device_colors[$d['device_type']] ?? '#94a3b8';
                $icon = $device_icons[$d['device_type']] ?? 'bi-question-circle';
            ?>
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi <?= $icon ?>" style="color:<?= $color ?>;"></i>
                        <span class="fw-semibold" style="font-size:0.82rem;"><?= ucfirst($d['device_type']) ?></span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span style="font-size:0.82rem;font-weight:600;"><?= $d['count'] ?></span>
                        <span style="font-size:0.72rem;color:var(--text-muted);"><?= $pct ?>%</span>
                    </div>
                </div>
                <div class="mini-bar mb-2">
                    <div class="mini-bar-fill" style="width:<?= $pct ?>%;background:<?= $color ?>;"></div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($devices)): ?>
                <p class="text-muted text-center" style="font-size:0.82rem;">No data yet</p>
            <?php endif; ?>
        </div>

        <div class="chart-card p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-browser-chrome me-2 text-primary"></i>Browsers</h6>
            <?php
            $total_browser = array_sum(array_column($browsers, 'count'));
            $browser_colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4'];
            foreach ($browsers as $i => $b):
                $pct = $total_browser > 0 ? round(($b['count'] / $total_browser) * 100) : 0;
                $color = $browser_colors[$i % count($browser_colors)];
            ?>
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="fw-semibold" style="font-size:0.82rem;"><?= htmlspecialchars($b['browser']) ?></span>
                    <div class="d-flex align-items-center gap-2">
                        <span style="font-size:0.82rem;font-weight:600;"><?= $b['count'] ?></span>
                        <span style="font-size:0.72rem;color:var(--text-muted);"><?= $pct ?>%</span>
                    </div>
                </div>
                <div class="mini-bar mb-2">
                    <div class="mini-bar-fill" style="width:<?= $pct ?>%;background:<?= $color ?>;"></div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($browsers)): ?>
                <p class="text-muted text-center" style="font-size:0.82rem;">No data yet</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Top Pages -->
<div class="row g-3 mb-4">
    <div class="col-lg-7">
        <div class="chart-card">
            <div class="p-4 pb-2">
                <h6 class="fw-bold"><i class="bi bi-file-earmark-bar-graph me-2 text-primary"></i>Most Visited Pages</h6>
            </div>
            <div class="px-4 pb-3">
                <table class="table table-hover mb-0" style="font-size:0.85rem;">
                    <thead>
                        <tr>
                            <th>Page</th>
                            <th class="text-center">Views</th>
                            <th class="text-center">Unique</th>
                            <th class="text-end">Share</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $max_page_views = max(1, ...array_column($top_pages, 'views'));
                        foreach ($top_pages as $tp):
                            $share = $week_count > 0 ? round(($tp['views'] / $week_count) * 100, 1) : 0;
                        ?>
                            <tr>
                                <td>
                                    <span class="page-url"><?= htmlspecialchars($tp['page_url']) ?></span>
                                </td>
                                <td class="text-center fw-semibold"><?= number_format($tp['views']) ?></td>
                                <td class="text-center text-muted"><?= number_format($tp['unique_visitors']) ?></td>
                                <td class="text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-2">
                                        <div class="mini-bar" style="width:60px;">
                                            <div class="mini-bar-fill" style="width:<?= ($tp['views'] / $max_page_views) * 100 ?>%;background:#3b82f6;"></div>
                                        </div>
                                        <span style="font-size:0.75rem;color:var(--text-muted);"><?= $share ?>%</span>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($top_pages)): ?>
                            <tr><td colspan="4" class="text-center text-muted py-4">No page data yet. Visitors will appear here once tracking starts.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- OS Breakdown -->
    <div class="col-lg-5">
        <div class="chart-card p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-cpu me-2 text-primary"></i>Operating Systems</h6>
            <?php
            $total_os = array_sum(array_column($operating_systems, 'count'));
            $os_colors = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4'];
            foreach ($operating_systems as $i => $os):
                $pct = $total_os > 0 ? round(($os['count'] / $total_os) * 100) : 0;
                $color = $os_colors[$i % count($os_colors)];
            ?>
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="fw-semibold" style="font-size:0.82rem;"><?= htmlspecialchars($os['os']) ?></span>
                    <div class="d-flex align-items-center gap-2">
                        <span style="font-size:0.82rem;font-weight:600;"><?= $os['count'] ?></span>
                        <span style="font-size:0.72rem;color:var(--text-muted);"><?= $pct ?>%</span>
                    </div>
                </div>
                <div class="mini-bar mb-2">
                    <div class="mini-bar-fill" style="width:<?= $pct ?>%;background:<?= $color ?>;"></div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($operating_systems)): ?>
                <p class="text-muted text-center" style="font-size:0.82rem;">No data yet</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Session Navigation Paths -->
<div class="row g-3 mb-4">
    <div class="col-lg-8">
        <div class="chart-card">
            <div class="p-4 pb-2">
                <h6 class="fw-bold"><i class="bi bi-signpost-split me-2 text-primary"></i>Visitor Navigation Paths</h6>
                <p class="text-muted" style="font-size:0.78rem;">Shows the pages each visitor browsed through, in order</p>
            </div>
            <div class="px-4 pb-3" style="max-height:450px;overflow-y:auto;">
                <?php foreach ($session_paths as $sp): ?>
                    <?php
                    $device_icons = ['desktop' => 'bi-pc-display', 'mobile' => 'bi-phone', 'tablet' => 'bi-tablet', 'unknown' => 'bi-question-circle'];
                    $device_colors = ['desktop' => 'text-primary', 'mobile' => 'text-success', 'tablet' => 'text-warning', 'unknown' => 'text-secondary'];
                    $d_icon = $device_icons[$sp['device_type']] ?? 'bi-question-circle';
                    $d_color = $device_colors[$sp['device_type']] ?? 'text-secondary';
                    $d_label = ucfirst($sp['device_type']);
                    $diff = strtotime($sp['ended'] ?? $sp['started']) - strtotime($sp['started']);
                    $duration = $diff > 0 ? $diff . 's' : '';
                    $page_count = count($sp['pages']);
                    ?>
                    <div class="mb-3 p-3 rounded" style="background:var(--admin-light);border-left:3px solid #3b82f6;">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi <?= $d_icon ?> <?= $d_color ?>"></i>
                                <span class="fw-semibold" style="font-size:0.8rem;">
                                    <?= htmlspecialchars($sp['browser'] ?? 'Unknown') ?> &middot; <?= htmlspecialchars($sp['os'] ?? 'Unknown') ?>
                                </span>
                                <span style="font-size:0.72rem;color:var(--text-muted);"><?= $d_label ?></span>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span style="font-size:0.72rem;color:var(--text-muted);" title="<?= $sp['ip_address'] ?>">
                                    <i class="bi bi-geo-alt me-1"></i><?= $sp['ip_address'] ?>
                                </span>
                                <span style="font-size:0.72rem;color:var(--text-muted);">
                                    <?= $page_count ?> page<?= $page_count !== 1 ? 's' : '' ?><?= $duration ? ' &middot; ' . $duration : '' ?>
                                </span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center flex-wrap gap-1">
                            <?php foreach ($sp['pages'] as $pi => $page): ?>
                                <?php if ($pi > 0): ?>
                                    <i class="bi bi-chevron-right" style="font-size:0.6rem;color:#94a3b8;"></i>
                                <?php endif; ?>
                                <span style="font-size:0.72rem;padding:2px 8px;border-radius:4px;<?php if ($pi === count($sp['pages']) - 1): ?>background:#3b82f6;color:#fff;font-weight:600;<?php else: ?>background:#e9ecef;color:#495057;<?php endif; ?>" title="<?= htmlspecialchars($page) ?>">
                                    <?= htmlspecialchars(strlen($page) > 30 ? substr($page, 0, 27) . '...' : $page) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                        <div style="font-size:0.7rem;color:#94a3b8;margin-top:4px;">
                            <?= date('M d, g:i A', strtotime($sp['started'])) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($session_paths)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-signpost-split" style="font-size:2rem;"></i>
                        <p class="mt-2">No navigation paths recorded yet</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Page-to-Page Flow -->
    <div class="col-lg-4">
        <div class="chart-card p-4">
            <h6 class="fw-bold mb-3"><i class="bi bi-arrow-left-right me-2 text-primary"></i>Page Flow</h6>
            <p class="text-muted mb-3" style="font-size:0.75rem;">Most common navigation paths between pages</p>
            <?php foreach ($page_flows as $pf): ?>
                <?php
                $short_from = strlen($pf['from_page']) > 20 ? substr($pf['from_page'], 0, 17) . '...' : $pf['from_page'];
                $short_to = strlen($pf['to_page']) > 20 ? substr($pf['to_page'], 0, 17) . '...' : $pf['to_page'];
                $max_flow = max(1, $page_flows[0]['flow_count'] ?? 1);
                $flow_pct = round(($pf['flow_count'] / $max_flow) * 100);
                ?>
                <div class="mb-3">
                    <div class="d-flex align-items-center gap-2 mb-1" style="font-size:0.78rem;">
                        <span class="fw-semibold" style="color:#3b82f6;" title="<?= htmlspecialchars($pf['from_page']) ?>"><?= htmlspecialchars($short_from) ?></span>
                        <i class="bi bi-arrow-right" style="font-size:0.7rem;color:#94a3b8;"></i>
                        <span class="fw-semibold" style="color:#10b981;" title="<?= htmlspecialchars($pf['to_page']) ?>"><?= htmlspecialchars($short_to) ?></span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="mini-bar flex-fill">
                            <div class="mini-bar-fill" style="width:<?= $flow_pct ?>%;background:linear-gradient(90deg,#3b82f6,#8b5cf6);"></div>
                        </div>
                        <span style="font-size:0.72rem;color:var(--text-muted);font-weight:600;white-space:nowrap;">
                            <?= $pf['flow_count'] ?>x
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($page_flows)): ?>
                <p class="text-muted text-center" style="font-size:0.82rem;">No flow data yet</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Recent Visitors Log -->
<div class="chart-card mb-4">
    <div class="p-4 pb-2 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0"><i class="bi bi-list-check me-2 text-primary"></i>Recent Visitors</h6>
        <button class="btn btn-admin btn-sm btn-outline-primary" onclick="location.reload()" title="Refresh">
            <i class="bi bi-arrow-clockwise"></i> Refresh
        </button>
    </div>
    <div style="max-height:500px;overflow-y:auto;">
        <?php foreach ($recent_visitors as $v): ?>
            <?php
            $time_ago = '';
            $diff = time() - strtotime($v['created_at']);
            if ($diff < 60) $time_ago = 'Just now';
            elseif ($diff < 3600) $time_ago = round($diff / 60) . 'm ago';
            elseif ($diff < 86400) $time_ago = round($diff / 3600) . 'h ago';
            else $time_ago = round($diff / 86400) . 'd ago';

            $device_badges = [
                'desktop' => ['bg-primary', 'bi-pc-display', 'Desktop'],
                'mobile' => ['bg-success', 'bi-phone', 'Mobile'],
                'tablet' => ['bg-warning', 'bi-tablet', 'Tablet'],
                'unknown' => ['bg-secondary', 'bi-question-circle', 'Unknown']
            ];
            $badge = $device_badges[$v['device_type']] ?? $device_badges['unknown'];
            ?>
            <div class="visitor-row d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3" style="min-width:0;">
                    <span class="device-badge <?= $badge[0] ?> bg-opacity-10 text-<?= $badge[0] === 'bg-warning' ? 'warning' : explode('-', $badge[0])[1] ?>">
                        <i class="bi <?= $badge[1] ?>"></i> <?= $badge[2] ?>
                    </span>
                    <div style="min-width:0;">
                        <div class="fw-semibold" style="font-size:0.82rem;"><?= htmlspecialchars($v['browser'] ?? 'Unknown') ?> · <?= htmlspecialchars($v['os'] ?? 'Unknown') ?></div>
                        <div class="page-url" style="font-size:0.78rem;"><?= htmlspecialchars($v['page_url'] ?? '/') ?></div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3 text-end" style="white-space:nowrap;">
                    <span class="text-muted" style="font-size:0.78rem;" title="<?= $v['ip_address'] ?>">
                        <i class="bi bi-geo-alt me-1"></i><?= $v['ip_address'] ?>
                    </span>
                    <span class="text-muted" style="font-size:0.78rem;">
                        <?= $v['pages_viewed'] ?> pg<?= $v['pages_viewed'] !== 1 ? 's' : '' ?>
                    </span>
                    <span class="text-muted" style="font-size:0.78rem;"><?= $time_ago ?></span>
                </div>
            </div>
        <?php endforeach; ?>
        <?php if (empty($recent_visitors)): ?>
            <div class="text-center py-5 text-muted">
                <i class="bi bi-people" style="font-size:2.5rem;"></i>
                <p class="mt-2 fw-semibold">No visitors tracked yet</p>
                <p style="font-size:0.82rem;">Visitor data will appear here once people start browsing your website.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Auto-refresh visitor count every 30 seconds
setInterval(function() {
    fetch('/api/track_visitor.php', { method: 'OPTIONS' }).catch(() => {});
}, 30000);
</script>

<?php include 'footer.php'; ?>
