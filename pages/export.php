<?php
session_start();
include_once '../includes/config.php';
include_once '../includes/header.php';
include_once '../includes/navigation.php';
?>

<style>
.export-card {
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 1.5rem;
    transition: all 0.3s ease;
    background: white;
}

.export-card:hover {
    border-color: #667eea;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
    transform: translateY(-3px);
}

.export-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    color: #667eea;
}

.download-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    padding: 12px 30px;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.download-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    color: white;
}

.export-info {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    margin-top: 1rem;
}
</style>

<!-- Page Header -->
<div class="container-fluid bg-mesh-gradient py-5">
    <div class="container py-4">
        <div class="row">
            <div class="col-12 text-center">
                <h1 class="display-5 fw-bold text-white mb-3">
                    <i class="fas fa-file-excel me-3"></i>Export Inventory
                </h1>
                <p class="lead text-white" style="opacity: 0.95;">
                    Download complete inventory data in Excel or CSV format
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Export Options -->
<div class="container py-5">
    <div class="row justify-content-center">
        <!-- Excel Export (XML Format) -->
        <div class="col-lg-5 col-md-6">
            <div class="export-card text-center">
                <div class="export-icon">
                    <i class="fas fa-file-excel text-success"></i>
                </div>
                <h3 class="mb-3">Excel Format</h3>
                <p class="text-muted mb-4">
                    Professional Excel file with formatting, colors, and formulas. Best for viewing and analysis in Microsoft Excel.
                </p>
                
                <a href="/export_inventory.php" class="btn download-btn w-100 mb-3" target="_blank">
                    <i class="fas fa-download me-2"></i>Download Excel File
                </a>
                
                <div class="export-info">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Includes: All products, brands, models, prices, stock levels, and descriptions
                    </small>
                </div>
            </div>
        </div>

        <!-- CSV Export -->
        <div class="col-lg-5 col-md-6">
            <div class="export-card text-center">
                <div class="export-icon">
                    <i class="fas fa-file-csv text-primary"></i>
                </div>
                <h3 class="mb-3">CSV Format</h3>
                <p class="text-muted mb-4">
                    Simple comma-separated values file. Compatible with Excel, Google Sheets, and database imports.
                </p>
                
                <a href="/export_inventory_csv.php" class="btn download-btn w-100 mb-3" target="_blank">
                    <i class="fas fa-download me-2"></i>Download CSV File
                </a>
                
                <div class="export-info">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Includes: Same data as Excel, in plain text format
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Information Section -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="glass-card p-4 rounded-xl">
                <h4 class="mb-3"><i class="fas fa-list-check me-2 text-primary"></i>What's Included</h4>
                <div class="row">
                    <div class="col-md-6">
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Product ID & Name</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Brand & Model Information</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Category Classification</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Part Number & SKU</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-unstyled">
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Regular & Sale Prices</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Stock Quantity & Status</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Compatible Year Range</li>
                            <li class="mb-2"><i class="fas fa-check text-success me-2"></i>Product Descriptions</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tips Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="alert alert-info">
                <h5 class="alert-heading"><i class="fas fa-lightbulb me-2"></i>Tips</h5>
                <ul class="mb-0">
                    <li>The Excel format includes total product count and inventory value calculations</li>
                    <li>Files are generated in real-time with the latest database information</li>
                    <li>Both formats can be opened in Microsoft Excel, Google Sheets, or LibreOffice</li>
                    <li>The CSV format is best for importing into other systems or databases</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Back Button -->
    <div class="row mt-4">
        <div class="col-12 text-center">
            <a href="/" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Back to Home
            </a>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>
