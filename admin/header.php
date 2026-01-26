<?php
include_once 'includes/auth.php';
include_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SPARE XPRESS LTD - Admin Panel</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Custom Admin CSS -->
    <link href="/css/admin-style.css" rel="stylesheet">
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.4/css/dataTables.bootstrap5.min.css">

    <style>
        :root {
            --admin-primary: #198754; /* Green */
            --admin-secondary: #6c757d;
            --admin-success: #198754;
            --admin-danger: #dc3545;
            --admin-warning: #ffc107; /* Yellow */
            --admin-info: #0dcaf0; /* Light Blue */
            --admin-dark: #343a40;
            --admin-light: #f8f9fa;
            --admin-white: #ffffff;
            --admin-gray: #e9ecef;
            --sidebar-width: 200px; /* Reduced from 280px for more compact design */
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--admin-gray);
        }

        /* Enhanced Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, #007bff 0%, #0056b3 100%); /* Blue gradient */
            color: #ffc107;
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 4px 0 20px rgba(0,0,0,0.15);
            border-right: 2px solid #004085;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .sidebar::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 193, 7, 0.5);
            border-radius: 2px;
        }

        .sidebar.collapsed {
            width: 60px; /* More compact collapsed state */
        }

        .sidebar .sidebar-header {
            padding: 15px 20px 10px;
            border-bottom: 1px solid rgba(255, 193, 7, 0.2);
            background: rgba(0, 0, 0, 0.1);
        }

        .sidebar .sidebar-nav {
            padding: 10px 0;
        }

        .sidebar .nav-link {
            color: #ffc107 !important;
            padding: 12px 15px;
            margin: 3px 8px;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 0.9rem;
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            text-decoration: none;
            min-height: 44px;
        }

        .sidebar .nav-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background: #ffc107;
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .sidebar .nav-link:hover::before,
        .sidebar .nav-link.active::before {
            transform: scaleY(1);
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255, 193, 7, 0.15);
            color: white !important;
            transform: translateX(3px);
            box-shadow: 0 2px 8px rgba(255, 193, 7, 0.2);
        }

        .sidebar .nav-link i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
            font-size: 1.1rem;
            color: #ffc107;
            transition: color 0.3s ease;
            flex-shrink: 0;
        }

        .sidebar .nav-link:hover i,
        .sidebar .nav-link.active i {
            color: white;
        }

        .sidebar.collapsed .nav-link {
            padding: 14px 8px;
            justify-content: center;
            margin: 3px 4px;
            min-height: 48px;
        }

        .sidebar.collapsed .nav-link span {
            display: none;
        }

        .sidebar.collapsed .nav-link i {
            margin-right: 0;
            font-size: 1.2rem;
        }

        /* Enhanced mobile responsiveness */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
                width: var(--sidebar-width);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .sidebar.collapsed {
                width: 60px;
            }
        }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            transition: margin-left 0.3s ease;
            min-height: 100vh;
        }

        .main-content.expanded {
            margin-left: 70px;
        }

        /* Top Navbar */
        .top-navbar {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%); /* Dark yellow to orange gradient */
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            padding: 8px 20px; /* Reduced padding for more compact design */
            position: sticky;
            top: 0;
            z-index: 999;
            border-bottom: 3px solid #d35400;
        }

        .top-navbar .navbar-brand {
            font-weight: bold;
            color: white !important;
            font-size: 1.1rem;
        }

        .top-navbar .navbar-brand:hover {
            color: #fff3cd !important;
        }

        .top-navbar .nav-link {
            color: white !important;
            font-weight: 500;
        }

        .top-navbar .nav-link:hover {
            color: #fff3cd !important;
        }

        .top-navbar .badge {
            background: rgba(255, 255, 255, 0.2) !important;
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .top-navbar .btn-outline-primary {
            border-color: rgba(255, 255, 255, 0.5);
            color: white;
        }

        .top-navbar .btn-outline-primary:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: white;
            color: white;
        }

        /* Enhanced navbar effects */
        .top-navbar .navbar-brand:hover img {
            transform: scale(1.05);
            transition: transform 0.2s ease;
        }

        .top-navbar .dropdown-menu {
            background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        .top-navbar .dropdown-item {
            color: white;
            transition: all 0.2s ease;
        }

        .top-navbar .dropdown-item:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff3cd;
            transform: translateX(5px);
        }

        .top-navbar .dropdown-divider {
            border-color: rgba(255, 255, 255, 0.2);
        }

        /* Mobile responsive adjustments */
        @media (max-width: 576px) {
            .top-navbar {
                padding: 6px 15px;
            }

            .top-navbar .navbar-brand img {
                height: 24px !important;
                width: 24px !important;
            }

            .top-navbar .navbar-brand span {
                font-size: 0.8rem;
            }
        }

        /* Cards */
        .stats-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            border: none;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        }

        .stats-card .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
        }

        .stats-card .card-title {
            font-size: 0.9rem;
            color: var(--admin-secondary);
            margin-bottom: 5px;
        }

        .stats-card .card-value {
            font-size: 2rem;
            font-weight: bold;
            color: var(--admin-dark);
            margin-bottom: 0;
        }

        /* Tables */
        .table-responsive {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .table thead th {
            background: var(--admin-light);
            border-bottom: 2px solid var(--admin-primary);
            color: var(--admin-dark);
            font-weight: 600;
            padding: 15px;
        }

        .table tbody td {
            padding: 15px;
            vertical-align: middle;
            border-bottom: 1px solid #eee;
        }

        /* Buttons */
        .btn-admin {
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-admin:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        /* Forms */
        .form-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            padding: 30px;
        }

        .form-control {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 12px 15px;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--admin-primary);
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 0;
                transform: translateX(-100%);
            }

            .sidebar.show {
                width: var(--sidebar-width);
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .mobile-menu-toggle {
                display: block !important;
            }
        }

        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--admin-primary);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

<!-- Enhanced Sidebar -->
<nav class="sidebar" id="sidebar">
    <!-- Sidebar Header -->
    <div class="sidebar-header">
        <div class="text-center">
            <h6 class="fw-bold mb-1" style="color: #ffc107; font-size: 1rem;">SPARE XPRESS</h6>
            <small style="color: rgba(255, 193, 7, 0.8); font-size: 0.75rem; letter-spacing: 0.5px;">ADMIN PANEL</small>
        </div>
    </div>

    <!-- Navigation Menu -->
    <div class="sidebar-nav">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="/admin/enhanced_dashboard.php" title="Dashboard">
                    <i class="bi bi-speedometer2-fill"></i>
                    <span>Dashboard</span>
                </a>
            </li>

            <!-- Catalog Management -->
            <li class="nav-item">
                <a class="nav-link collapsible-toggle" href="#" data-bs-toggle="collapse" data-bs-target="#catalogSubmenu" title="Catalog Management">
                    <i class="bi bi-diagram-3-fill"></i>
                    <span>Catalog</span>
                    <i class="bi bi-chevron-down ms-auto submenu-arrow"></i>
                </a>
                <div class="collapse submenu-collapse" id="catalogSubmenu">
                    <ul class="nav flex-column ms-3 submenu">
                        <li class="nav-item">
                            <a class="nav-link submenu-link" href="/admin/brands/enhanced_brand_management.php" title="Manage Brands">
                                <i class="bi bi-tags"></i>
                                <span>Brands</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link submenu-link" href="/admin/models/enhanced_model_management.php" title="Manage Models">
                                <i class="bi bi-car-front"></i>
                                <span>Models</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link submenu-link" href="/admin/categories/enhanced_category_management.php" title="Manage Categories">
                                <i class="bi bi-grid"></i>
                                <span>Categories</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link submenu-link" href="/admin/products/enhanced_product_management.php" title="Manage Products">
                                <i class="bi bi-box-seam"></i>
                                <span>Products</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Order Management -->
            <li class="nav-item">
                <a class="nav-link collapsible-toggle" href="#" data-bs-toggle="collapse" data-bs-target="#ordersSubmenu" title="Order Management">
                    <i class="bi bi-receipt-fill"></i>
                    <span>Orders</span>
                    <i class="bi bi-chevron-down ms-auto submenu-arrow"></i>
                </a>
                <div class="collapse submenu-collapse" id="ordersSubmenu">
                    <ul class="nav flex-column ms-3 submenu">
                        <li class="nav-item">
                            <a class="nav-link submenu-link" href="/admin/orders/enhanced_order_management.php" title="Manage Orders">
                                <i class="bi bi-receipt"></i>
                                <span>All Orders</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link submenu-link" href="/admin/orders/order_demand_list.php" title="On-Demand Requests">
                                <i class="bi bi-star"></i>
                                <span>On-Demand</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </li>

            <!-- Customer Management -->
            <li class="nav-item">
                <a class="nav-link" href="/admin/customers/enhanced_customer_management.php" title="Customer Management">
                    <i class="bi bi-people-fill"></i>
                    <span>Customers</span>
                </a>
            </li>

            <!-- System -->
            <li class="nav-item">
                <a class="nav-link" href="/admin/notifications/notification_manager.php" title="Notifications">
                    <i class="bi bi-bell-fill"></i>
                    <span>Notifications</span>
                    <?php
                    $unread_notifications = countRowsWhere('notifications', 'is_read = 0');
                    if ($unread_notifications > 0) echo "<span class='badge bg-danger ms-auto'>$unread_notifications</span>";
                    ?>
                </a>
            </li>
        </ul>

        <!-- Sidebar Footer -->
        <div class="mt-auto p-3" style="border-top: 1px solid rgba(255, 193, 7, 0.2);">
            <div class="text-center">
                <small style="color: rgba(255, 193, 7, 0.6); font-size: 0.7rem;">
                    <i class="bi bi-shield-check me-1"></i>
                    Secure Admin
                </small>
            </div>
        </div>
    </div>
</nav>

<!-- Main Content Wrapper -->
<div class="main-content" id="mainContent">
    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg top-navbar">
        <div class="container-fluid px-3">
            <button class="btn btn-outline-light d-md-none mobile-menu-toggle me-2" type="button" onclick="toggleSidebar()">
                <i class="bi bi-list"></i>
            </button>

            <a class="navbar-brand d-flex align-items-center flex-shrink-0" href="#">
                <img src="/img/logo/icon.jpg" alt="Icon" class="me-2 rounded" style="height: 28px; width: 28px; object-fit: cover;">
                <div class="d-none d-sm-block">
                    <span class="fw-bold">SPARE XPRESS</span>
                    <small class="d-block opacity-75" style="font-size: 0.7rem; line-height: 1;">Admin Panel</small>
                </div>
                <div class="d-sm-none">
                    <span class="fw-bold" style="font-size: 0.9rem;">SX</span>
                </div>
            </a>

            <div class="navbar-nav ms-auto d-flex align-items-center flex-shrink-0">
                <div class="d-flex align-items-center">
                    <i class="bi bi-circle-fill text-success me-1" style="font-size: 0.6rem;"></i>
                    <small class="fw-semibold">Online</small>
                </div>
                <div class="vr mx-3 d-none d-md-block" style="color: rgba(255,255,255,0.3);"></div>

                <!-- Theme Toggle -->
                <button class="btn btn-link nav-link p-1 me-2" id="themeToggle" title="Toggle Theme">
                    <i class="bi bi-moon-stars-fill" id="themeIcon"></i>
                </button>

                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle d-flex align-items-center p-1" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <img src="/img/logo/icon.jpg" alt="Admin" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover; border: 2px solid rgba(255,255,255,0.3);">
                        <span class="fw-semibold"><?php echo $_SESSION['admin'] ?? 'Admin'; ?></span>
                        <i class="bi bi-chevron-down ms-1" style="font-size: 0.8rem;"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0">
                        <li><a class="dropdown-item py-2" href="../index.php" target="_blank">
                            <i class="bi bi-eye me-2"></i>View Store</a></li>
                        <li><a class="dropdown-item py-2" href="#" onclick="openSettings()">
                            <i class="bi bi-gear me-2"></i>Settings</a></li>
                        <li><hr class="dropdown-divider my-1"></li>
                        <li><a class="dropdown-item text-danger py-2 fw-semibold" href="logout.php">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <div class="container-fluid p-4">