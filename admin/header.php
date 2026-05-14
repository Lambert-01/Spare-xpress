<?php
if (!defined('ADMIN_AUTH_LOADED')) {
    require_once __DIR__ . '/includes/auth.php';
}
if (!defined('ADMIN_FUNCTIONS_LOADED')) {
    require_once __DIR__ . '/includes/functions.php';
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SPARE XPRESS LTD - Admin Panel</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="/css/admin-style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">

    <style>
        :root {
            --admin-primary: #0d6efd;
            --admin-secondary: #6c757d;
            --admin-success: #198754;
            --admin-danger: #dc3545;
            --admin-warning: #ffc107;
            --admin-info: #0dcaf0;
            --admin-dark: #212529;
            --admin-light: #f8f9fa;
            --admin-white: #ffffff;
            --admin-gray: #e9ecef;
            --sidebar-width: 220px;
            --sidebar-bg: linear-gradient(180deg, #1a237e 0%, #0d47a1 60%, #1565c0 100%);
            --navbar-bg: linear-gradient(135deg, #1565c0 0%, #0d47a1 100%);
            --body-bg: #f0f2f5;
            --card-bg: #ffffff;
            --text-color: #212529;
            --text-muted: #6c757d;
            --border-color: #dee2e6;
        }

        [data-theme="dark"] {
            --body-bg: #0f1117;
            --card-bg: #1e2130;
            --text-color: #e2e8f0;
            --text-muted: #94a3b8;
            --border-color: #2d3748;
            --admin-gray: #1e2130;
            --admin-light: #2d3748;
            --admin-dark: #e2e8f0;
        }

        * { transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease; }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--body-bg);
            color: var(--text-color);
        }

        /* ── Sidebar ── */
        .sidebar {
            position: fixed;
            top: 0; left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            color: #fff;
            z-index: 1000;
            transition: width 0.3s cubic-bezier(0.4,0,0.2,1);
            box-shadow: 4px 0 24px rgba(0,0,0,0.18);
            overflow-y: auto;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
        }

        .sidebar::-webkit-scrollbar { width: 3px; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 2px; }

        .sidebar.collapsed { width: 60px; }

        /* Header */
        .sidebar-header {
            padding: 16px 14px 12px;
            border-bottom: 1px solid rgba(255,255,255,0.12);
            background: rgba(0,0,0,0.15);
            flex-shrink: 0;
        }

        .sidebar-header .brand-full { display: flex; align-items: center; gap: 10px; }
        .sidebar-header .brand-icon { display: none; }
        .sidebar.collapsed .brand-full { display: none; }
        .sidebar.collapsed .brand-icon { display: flex; justify-content: center; }

        /* Nav */
        .sidebar-nav { padding: 8px 0; flex: 1; }

        .nav-section-label {
            padding: 10px 16px 4px;
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: rgba(255,255,255,0.4);
            white-space: nowrap;
            overflow: hidden;
        }
        .sidebar.collapsed .nav-section-label { opacity: 0; height: 0; padding: 0; }

        .sidebar .nav-link {
            color: rgba(255,255,255,0.8) !important;
            padding: 10px 14px;
            margin: 2px 8px;
            border-radius: 8px;
            transition: all 0.25s ease;
            font-weight: 500;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            text-decoration: none;
            white-space: nowrap;
            position: relative;
        }

        .sidebar .nav-link::before {
            content: '';
            position: absolute;
            left: 0; top: 50%;
            transform: translateY(-50%) scaleY(0);
            height: 60%; width: 3px;
            background: #fff;
            border-radius: 0 3px 3px 0;
            transition: transform 0.25s ease;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.15) !important;
            color: #fff !important;
        }

        .sidebar .nav-link:hover::before,
        .sidebar .nav-link.active::before { transform: translateY(-50%) scaleY(1); }

        .sidebar .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 1rem;
            flex-shrink: 0;
            margin-right: 10px;
        }

        .sidebar.collapsed .nav-link {
            padding: 12px 0;
            justify-content: center;
            margin: 2px 6px;
        }
        .sidebar.collapsed .nav-link i { margin-right: 0; font-size: 1.1rem; }
        .sidebar.collapsed .nav-link span,
        .sidebar.collapsed .nav-link .submenu-arrow,
        .sidebar.collapsed .nav-link .badge { display: none; }
        .sidebar.collapsed .submenu-collapse { display: none !important; }

        /* Submenu */
        .submenu { padding: 0; }
        .submenu .nav-link {
            font-size: 0.82rem;
            padding: 8px 12px;
            margin: 1px 4px;
            color: rgba(255,255,255,0.65) !important;
        }
        .submenu .nav-link:hover,
        .submenu .nav-link.active {
            background: rgba(255,255,255,0.1) !important;
            color: #fff !important;
        }

        /* Sidebar footer */
        .sidebar-footer {
            padding: 12px 14px;
            border-top: 1px solid rgba(255,255,255,0.12);
            flex-shrink: 0;
        }
        .sidebar-footer .admin-info { display: flex; align-items: center; gap: 10px; }
        .sidebar-footer .admin-avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
            display: flex; align-items: center; justify-content: center;
            font-size: 0.9rem; flex-shrink: 0;
        }
        .sidebar-footer .admin-name { font-size: 0.8rem; font-weight: 600; color: #fff; }
        .sidebar-footer .admin-role { font-size: 0.7rem; color: rgba(255,255,255,0.5); }
        .sidebar.collapsed .admin-info .admin-text { display: none; }
        .sidebar.collapsed .sidebar-footer { padding: 12px 6px; }
        .sidebar.collapsed .admin-info { justify-content: center; }

        /* Logout link */
        .sidebar-logout {
            display: flex; align-items: center;
            padding: 8px 14px;
            margin: 4px 8px;
            border-radius: 8px;
            color: rgba(255,100,100,0.85) !important;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.25s ease;
        }
        .sidebar-logout:hover { background: rgba(220,53,69,0.2) !important; color: #ff6b6b !important; }
        .sidebar-logout i { width: 20px; text-align: center; margin-right: 10px; flex-shrink: 0; }
        .sidebar.collapsed .sidebar-logout { justify-content: center; padding: 10px 0; margin: 2px 6px; }
        .sidebar.collapsed .sidebar-logout span { display: none; }
        .sidebar.collapsed .sidebar-logout i { margin-right: 0; }

        /* Mobile */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); width: var(--sidebar-width) !important; }
            .sidebar.show { transform: translateX(0); }
        }

        /* ── Main Content ── */
        .main-content {
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s ease;
            min-height: 100vh;
        }
        .main-content.expanded { margin-left: 60px; }
        @media (max-width: 768px) { .main-content { margin-left: 0; } }

        /* ── Top Navbar ── */
        .top-navbar {
            background: var(--navbar-bg);
            box-shadow: 0 2px 12px rgba(0,0,0,0.15);
            padding: 8px 20px;
            position: sticky; top: 0; z-index: 999;
        }

        .top-navbar .navbar-brand { font-weight: 700; color: #fff !important; font-size: 1rem; }
        .top-navbar .nav-link { color: rgba(255,255,255,0.9) !important; font-weight: 500; }
        .top-navbar .nav-link:hover { color: #fff !important; }

        .top-navbar .dropdown-menu {
            background: #1565c0;
            border: 1px solid rgba(255,255,255,0.15);
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        }
        .top-navbar .dropdown-item { color: rgba(255,255,255,0.85); transition: all 0.2s ease; }
        .top-navbar .dropdown-item:hover { background: rgba(255,255,255,0.12); color: #fff; }
        .top-navbar .dropdown-divider { border-color: rgba(255,255,255,0.15); }

        /* ── Dark Mode ── */
        [data-theme="dark"] body { background-color: var(--body-bg); color: var(--text-color); }
        [data-theme="dark"] .stats-card,
        [data-theme="dark"] .form-card,
        [data-theme="dark"] .table-responsive { background: var(--card-bg) !important; color: var(--text-color); }
        [data-theme="dark"] .card-title,
        [data-theme="dark"] .text-muted { color: var(--text-muted) !important; }
        [data-theme="dark"] .card-value,
        [data-theme="dark"] .fw-bold { color: var(--text-color); }
        [data-theme="dark"] .table { color: var(--text-color); }
        [data-theme="dark"] .table thead th { background: #2d3748; color: var(--text-color); border-color: var(--border-color); }
        [data-theme="dark"] .table tbody td { border-color: var(--border-color); }
        [data-theme="dark"] .form-control,
        [data-theme="dark"] .form-select {
            background: #2d3748; color: var(--text-color);
            border-color: var(--border-color);
        }
        [data-theme="dark"] .form-control:focus,
        [data-theme="dark"] .form-select:focus { background: #374151; color: var(--text-color); }
        [data-theme="dark"] .modal-content { background: var(--card-bg); color: var(--text-color); }
        [data-theme="dark"] .modal-header,
        [data-theme="dark"] .modal-footer { border-color: var(--border-color); }
        [data-theme="dark"] .dropdown-menu:not(.top-navbar .dropdown-menu) {
            background: var(--card-bg); border-color: var(--border-color);
        }
        [data-theme="dark"] .dropdown-item:not(.top-navbar .dropdown-item) {
            color: var(--text-color);
        }
        [data-theme="dark"] .dropdown-item:not(.top-navbar .dropdown-item):hover {
            background: var(--admin-light);
        }
        [data-theme="dark"] .alert { border-color: var(--border-color); }
        [data-theme="dark"] .border-bottom,
        [data-theme="dark"] .border-top { border-color: var(--border-color) !important; }
        [data-theme="dark"] .bg-white { background: var(--card-bg) !important; }
        [data-theme="dark"] .card-header.bg-white { background: var(--card-bg) !important; }
        [data-theme="dark"] .progress { background: #2d3748; }
        [data-theme="dark"] input::placeholder,
        [data-theme="dark"] textarea::placeholder { color: var(--text-muted); }

        /* ── Cards ── */
        .stats-card {
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            border: none;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }
        .stats-card:hover { transform: translateY(-4px); box-shadow: 0 6px 24px rgba(0,0,0,0.12); }

        .stats-card .card-icon {
            width: 48px; height: 48px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem;
        }

        .stats-card .card-title { font-size: 0.8rem; color: var(--text-muted); margin-bottom: 2px; }
        .stats-card .card-value { font-size: 1.6rem; font-weight: 700; color: var(--text-color); margin-bottom: 0; }

        /* ── Tables ── */
        .table-responsive {
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
            overflow: hidden;
        }
        .table thead th {
            background: var(--admin-light);
            border-bottom: 2px solid var(--admin-primary);
            color: var(--text-color);
            font-weight: 600;
            padding: 12px 15px;
        }
        .table tbody td { padding: 12px 15px; vertical-align: middle; border-bottom: 1px solid var(--border-color); }

        /* ── Form Card ── */
        .form-card {
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        }
        .form-card > .card-body { padding: 20px; }

        .form-control, .form-select {
            border-radius: 8px;
            border: 1.5px solid var(--border-color);
            padding: 10px 14px;
            transition: border-color 0.2s ease;
            background: var(--card-bg);
            color: var(--text-color);
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--admin-primary);
            box-shadow: 0 0 0 0.2rem rgba(13,110,253,0.15);
        }

        /* ── Buttons ── */
        .btn-admin { border-radius: 8px; padding: 8px 16px; font-weight: 500; transition: all 0.25s ease; }
        .btn-admin:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }

        /* ── Loading ── */
        .loading {
            display: inline-block; width: 16px; height: 16px;
            border: 2px solid rgba(255,255,255,0.3);
            border-radius: 50%; border-top-color: white;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #cbd5e0; border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: #a0aec0; }
    </style>
</head>
<body>

<!-- Sidebar -->
<nav class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <div class="brand-full">
            <img src="/img/logo/icon.jpg" alt="Logo" style="width:32px;height:32px;border-radius:6px;object-fit:cover;flex-shrink:0;">
            <div>
                <div style="font-weight:700;font-size:0.9rem;color:#fff;line-height:1.1;">SPARE XPRESS</div>
                <div style="font-size:0.65rem;color:rgba(255,255,255,0.5);letter-spacing:0.8px;">ADMIN PANEL</div>
            </div>
        </div>
        <div class="brand-icon">
            <img src="/img/logo/icon.jpg" alt="Logo" style="width:32px;height:32px;border-radius:6px;object-fit:cover;">
        </div>
    </div>

    <div class="sidebar-nav">
        <ul class="nav flex-column">

            <!-- Overview -->
            <li class="nav-section-label">Overview</li>
            <li class="nav-item">
                <a class="nav-link" href="/admin/enhanced_dashboard.php" title="Dashboard" data-bs-toggle="tooltip" data-bs-placement="right">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Catalog -->
            <li class="nav-section-label">Catalog</li>
            <li class="nav-item">
                <a class="nav-link collapsible-toggle" href="#" data-bs-toggle="collapse" data-bs-target="#catalogSubmenu" title="Catalog" data-bs-placement="right">
                    <i class="bi bi-diagram-3"></i>
                    <span>Catalog</span>
                    <i class="bi bi-chevron-down ms-auto submenu-arrow" style="font-size:0.7rem;transition:transform 0.2s;"></i>
                </a>
                <div class="collapse submenu-collapse" id="catalogSubmenu">
                    <ul class="nav flex-column ms-2 submenu">
                        <li class="nav-item">
                            <a class="nav-link submenu-link" href="/admin/brands/enhanced_brand_management.php">
                                <i class="bi bi-tags"></i><span>Brands</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link submenu-link" href="/admin/models/enhanced_model_management.php">
                                <i class="bi bi-car-front"></i><span>Models</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link submenu-link" href="/admin/categories/enhanced_category_management.php">
                                <i class="bi bi-grid"></i><span>Categories</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link submenu-link" href="/admin/products/enhanced_product_management.php">
                                <i class="bi bi-box-seam"></i><span>Products</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Orders -->
            <li class="nav-section-label">Orders</li>
            <li class="nav-item">
                <a class="nav-link collapsible-toggle" href="#" data-bs-toggle="collapse" data-bs-target="#ordersSubmenu" title="Orders" data-bs-placement="right">
                    <i class="bi bi-receipt"></i>
                    <span>Orders</span>
                    <i class="bi bi-chevron-down ms-auto submenu-arrow" style="font-size:0.7rem;transition:transform 0.2s;"></i>
                </a>
                <div class="collapse submenu-collapse" id="ordersSubmenu">
                    <ul class="nav flex-column ms-2 submenu">
                        <li class="nav-item">
                            <a class="nav-link submenu-link" href="/admin/orders/enhanced_order_management.php">
                                <i class="bi bi-list-ul"></i><span>All Orders</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link submenu-link" href="/admin/orders/price_requests.php">
                                <i class="bi bi-currency-exchange"></i><span>Price Requests</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link submenu-link" href="/admin/orders/order_demand_list.php">
                                <i class="bi bi-star"></i><span>On-Demand</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link submenu-link" href="/admin/orders/analytics.php">
                                <i class="bi bi-bar-chart-line"></i><span>Analytics</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Customers -->
            <li class="nav-section-label">People</li>
            <li class="nav-item">
                <a class="nav-link" href="/admin/customers/enhanced_customer_management.php" title="Customers" data-bs-placement="right">
                    <i class="bi bi-people"></i>
                    <span>Customers</span>
                </a>
            </li>

            <!-- System -->
            <li class="nav-section-label">System</li>
            <li class="nav-item">
                <a class="nav-link" href="/admin/notifications/notification_manager.php" title="Notifications" data-bs-placement="right">
                    <i class="bi bi-bell"></i>
                    <span>Notifications</span>
                    <?php
                    $unread_notifications = countRowsWhere('notifications', 'is_read = 0');
                    if ($unread_notifications > 0) echo "<span class='badge bg-danger ms-auto' style='font-size:0.65rem;'>$unread_notifications</span>";
                    ?>
                </a>
            </li>
        </ul>

        <!-- Logout -->
        <a href="/admin/logout.php" class="sidebar-logout" title="Logout">
            <i class="bi bi-box-arrow-right"></i>
            <span>Logout</span>
        </a>
    </div>

    <!-- Sidebar Footer: Admin Info -->
    <div class="sidebar-footer">
        <div class="admin-info">
            <div class="admin-avatar">
                <i class="bi bi-person-fill" style="color:rgba(255,255,255,0.8);"></i>
            </div>
            <div class="admin-text">
                <div class="admin-name"><?php echo htmlspecialchars($_SESSION['admin_name'] ?? $_SESSION['admin'] ?? 'Admin'); ?></div>
                <div class="admin-role"><?php echo htmlspecialchars($_SESSION['admin_role'] ?? 'Administrator'); ?></div>
            </div>
        </div>
    </div>
</nav>

<!-- Main Content Wrapper -->
<div class="main-content" id="mainContent">
    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg top-navbar">
        <div class="container-fluid px-3">
            <button class="btn btn-link text-white p-1 me-2" type="button" onclick="toggleSidebar()" title="Toggle Sidebar">
                <i class="bi bi-list fs-5"></i>
            </button>

            <a class="navbar-brand d-flex align-items-center" href="/admin/enhanced_dashboard.php">
                <img src="/img/logo/icon.jpg" alt="Icon" class="me-2 rounded" style="height:26px;width:26px;object-fit:cover;">
                <span class="d-none d-sm-inline">SPARE XPRESS <small class="opacity-75 fw-normal">Admin</small></span>
            </a>

            <div class="navbar-nav ms-auto d-flex align-items-center gap-2">
                <!-- Online status (real check via JS) -->
                <div class="d-flex align-items-center" id="online-status">
                    <i class="bi bi-circle-fill text-success me-1" style="font-size:0.55rem;" id="status-dot"></i>
                    <small class="fw-semibold text-white" id="status-text">Online</small>
                </div>

                <div class="vr mx-1" style="color:rgba(255,255,255,0.3);"></div>

                <!-- Theme Toggle -->
                <button class="btn btn-link text-white p-1" id="themeToggle" title="Toggle Dark Mode">
                    <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
                </button>

                <!-- Admin Dropdown -->
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center p-1" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <img src="/img/logo/icon.jpg" alt="Admin" class="rounded-circle me-2" style="width:30px;height:30px;object-fit:cover;border:2px solid rgba(255,255,255,0.3);">
                        <span class="fw-semibold d-none d-md-inline"><?php echo htmlspecialchars($_SESSION['admin_name'] ?? $_SESSION['admin'] ?? 'Admin'); ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0">
                        <li><a class="dropdown-item py-2" href="../index.php" target="_blank">
                            <i class="bi bi-eye me-2"></i>View Store</a></li>
                        <li><a class="dropdown-item py-2" href="#" onclick="openSettings()">
                            <i class="bi bi-gear me-2"></i>Settings</a></li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li><a class="dropdown-item text-danger py-2 fw-semibold" href="/admin/logout.php">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <div class="container-fluid p-4">
