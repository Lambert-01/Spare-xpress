<?php
// Technical Specifications View for SPARE XPRESS LTD
include '../includes/auth.php';
include '../includes/functions.php';
include '../header.php';

// Get model ID from URL
$model_id = (int)($_GET['id'] ?? 0);
if (!$model_id) {
    header('Location: enhanced_model_management.php');
    exit;
}

// Fetch model details
$model_query = "SELECT vm.*, vb.brand_name
                FROM vehicle_models_enhanced vm
                LEFT JOIN vehicle_brands_enhanced vb ON vm.brand_id = vb.id
                WHERE vm.id = ?";
$stmt = $conn->prepare($model_query);
$stmt->bind_param("i", $model_id);
$stmt->execute();
$model = $stmt->get_result()->fetch_assoc();

if (!$model) {
    $_SESSION['error'] = 'Model not found';
    header('Location: enhanced_model_management.php');
    exit;
}

// Parse technical specifications
$technical_specs = [];
if ($model['technical_specs']) {
    $specs_json = json_decode($model['technical_specs'], true);
    if (is_array($specs_json)) {
        $technical_specs = $specs_json;
    }
}

// Parse other specifications
$engine_types = $model['engine_types'] ? json_decode($model['engine_types'], true) : [];
$fuel_types = $model['fuel_types'] ? json_decode($model['fuel_types'], true) : [];
$transmission_types = $model['transmission_types'] ? json_decode($model['transmission_types'], true) : [];
$body_types = $model['body_types'] ? json_decode($model['body_types'], true) : [];

function formatArrayField($array) {
    if (empty($array)) return 'Not specified';
    return implode(', ', array_map('ucfirst', $array));
}

function formatSpecValue($value) {
    if ($value === null || $value === '') return '<span class="text-muted">Not specified</span>';
    return htmlspecialchars($value);
}

function getSpecCategory($key) {
    $categories = [
        'engine' => ['engine', 'displacement', 'horsepower', 'torque', 'cylinders', 'valves'],
        'performance' => ['top_speed', 'acceleration', 'fuel_consumption', 'emissions'],
        'dimensions' => ['length', 'width', 'height', 'wheelbase', 'weight', 'capacity'],
        'mechanical' => ['transmission', 'drivetrain', 'suspension', 'brakes', 'steering'],
        'electrical' => ['battery', 'alternator', 'electrical_system'],
        'fuel' => ['fuel_tank', 'fuel_type', 'fuel_economy']
    ];
    
    $key_lower = strtolower($key);
    foreach ($categories as $category => $keywords) {
        foreach ($keywords as $keyword) {
            if (stripos($key_lower, $keyword) !== false) {
                return $category;
            }
        }
    }
    return 'general';
}

// Group specs by category
$specs_by_category = [];
foreach ($technical_specs as $key => $value) {
    $category = getSpecCategory($key);
    if (!isset($specs_by_category[$category])) {
        $specs_by_category[$category] = [];
    }
    $specs_by_category[$category][$key] = $value;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Technical Specifications - <?php echo htmlspecialchars($model['model_name']); ?> | SPARE XPRESS LTD</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
        }
        
        .specs-header {
            background: linear-gradient(135deg, var(--primary-color), #0056b3);
            color: white;
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }
        
        .specs-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .model-badge {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .spec-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
            border-left: 4px solid var(--primary-color);
        }
        
        .spec-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0,0,0,0.15);
        }
        
        .spec-card.engine { border-left-color: var(--danger-color); }
        .spec-card.performance { border-left-color: var(--success-color); }
        .spec-card.dimensions { border-left-color: var(--warning-color); }
        .spec-card.mechanical { border-left-color: var(--info-color); }
        .spec-card.electrical { border-left-color: var(--secondary-color); }
        .spec-card.fuel { border-left-color: var(--primary-color); }
        .spec-card.general { border-left-color: var(--secondary-color); }
        
        .spec-item {
            border-bottom: 1px solid #e9ecef;
            padding: 1rem;
            transition: background-color 0.2s ease;
        }
        
        .spec-item:hover {
            background-color: #f8f9fa;
        }
        
        .spec-item:last-child {
            border-bottom: none;
        }
        
        .spec-label {
            font-weight: 600;
            color: var(--secondary-color);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .spec-value {
            font-size: 1.1rem;
            font-weight: 500;
            color: #333;
        }
        
        .spec-category-header {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 4px solid var(--primary-color);
        }
        
        .spec-category-header h5 {
            margin: 0;
            color: var(--primary-color);
        }
        
        .spec-category-header.engine { border-left-color: var(--danger-color); }
        .spec-category-header.performance { border-left-color: var(--success-color); }
        .spec-category-header.dimensions { border-left-color: var(--warning-color); }
        .spec-category-header.mechanical { border-left-color: var(--info-color); }
        .spec-category-header.electrical { border-left-color: var(--secondary-color); }
        .spec-category-header.fuel { border-left-color: var(--primary-color); }
        .spec-category-header.general { border-left-color: var(--secondary-color); }
        
        .specs-summary {
            background: linear-gradient(135deg, #fff, #f8f9fa);
            border: 1px solid #e9ecef;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .summary-item {
            background: white;
            border-radius: 8px;
            padding: 1rem;
            border: 1px solid #e9ecef;
            margin-bottom: 1rem;
        }
        
        .summary-item h6 {
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .back-btn {
            background: linear-gradient(135deg, var(--primary-color), #0056b3);
            border: none;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .back-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,123,255,0.3);
            color: white;
            text-decoration: none;
        }
        
        .no-specs {
            background: #f8f9fa;
            border: 2px dashed #dee2e6;
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            color: #6c757d;
        }
        
        .specs-stats {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 2rem;
        }
        
        .stat-badge {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 50px;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--secondary-color);
        }
        
        .stat-badge.primary { border-color: var(--primary-color); color: var(--primary-color); }
        .stat-badge.success { border-color: var(--success-color); color: var(--success-color); }
        .stat-badge.warning { border-color: var(--warning-color); color: var(--warning-color); }

        .model-hero-image {
            width: 120px;
            height: 80px;
            object-fit: cover;
            border: 3px solid rgba(255, 255, 255, 0.8);
            transition: transform 0.3s ease;
        }

        .model-hero-image:hover {
            transform: scale(1.05);
        }

        .model-hero-placeholder {
            width: 120px;
            height: 80px;
            background: linear-gradient(135deg, rgba(255,255,255,0.2), rgba(255,255,255,0.1));
            border: 3px solid rgba(255, 255, 255, 0.8);
        }

        .specs-header {
            min-height: 200px;
            display: flex;
            align-items: center;
        }

        .enhanced-spec-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .enhanced-spec-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 45px rgba(0,0,0,0.15);
        }

        .spec-category-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-right: 1rem;
        }

        .spec-category-icon.engine { background: linear-gradient(135deg, #dc3545, #b02a37); color: white; }
        .spec-category-icon.performance { background: linear-gradient(135deg, #28a745, #1e7e34); color: white; }
        .spec-category-icon.dimensions { background: linear-gradient(135deg, #ffc107, #e0a800); color: white; }
        .spec-category-icon.mechanical { background: linear-gradient(135deg, #0dcaf0, #0aa2c0); color: white; }
        .spec-category-icon.electrical { background: linear-gradient(135deg, #6c757d, #545b62); color: white; }
        .spec-category-icon.fuel { background: linear-gradient(135deg, #007bff, #0056b3); color: white; }
        .spec-category-icon.general { background: linear-gradient(135deg, #6c757d, #545b62); color: white; }

        .spec-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .spec-metric {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .spec-metric:hover {
            background: linear-gradient(135deg, #e9ecef, #dee2e6);
            transform: translateY(-2px);
        }

        .metric-value {
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 0.25rem;
        }

        .metric-label {
            font-size: 0.9rem;
            color: var(--secondary-color);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }


        .bg-gradient {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .enhanced-spec-card .card-header {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%) !important;
            color: white;
        }

        .enhanced-spec-card .card-header h5,
        .enhanced-spec-card .card-header h6 {
            color: white !important;
        }

        .summary-item {
            padding: 1rem;
            border-bottom: 1px solid #f0f0f0;
            transition: background-color 0.2s ease;
        }

        .summary-item:hover {
            background-color: #f8f9fa;
        }

        .summary-item:last-child {
            border-bottom: none;
        }

        .no-specs-icon {
            opacity: 0.5;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container-fluid py-4">
        <!-- Header Section -->
        <div class="specs-header position-relative">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-4">
                            <?php if (!empty($model['model_image'])): ?>
                                <img src="<?php echo htmlspecialchars($model['model_image']); ?>"
                                     alt="<?php echo htmlspecialchars($model['model_name']); ?>"
                                     class="model-hero-image rounded shadow">
                            <?php else: ?>
                                <div class="model-hero-placeholder rounded shadow d-flex align-items-center justify-content-center">
                                    <i class="bi bi-car-front-fill display-4 text-white opacity-75"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h1 class="mb-1 display-5 fw-bold"><?php echo htmlspecialchars($model['model_name']); ?></h1>
                            <p class="mb-0 lead opacity-75"><?php echo htmlspecialchars($model['brand_name']); ?> Technical Specifications</p>
                        </div>
                    </div>
                    <div class="model-badge d-inline-block px-3 py-2">
                        <span class="me-3"><i class="bi bi-calendar-event me-1"></i>
                            <?php echo $model['year_from'] ?: '?'; ?> - <?php echo $model['year_to'] ?: 'Present'; ?>
                        </span>
                        <span><i class="bi bi-gear me-1"></i><?php echo formatArrayField($transmission_types); ?></span>
                    </div>
                </div>
                <div class="col-lg-6 text-lg-end">
                    <div class="specs-stats mb-3">
                        <span class="stat-badge primary">
                            <i class="bi bi-engine me-1"></i>
                            <?php echo count($engine_types); ?> Engine Types
                        </span>
                        <span class="stat-badge success">
                            <i class="bi bi-fuel-pump me-1"></i>
                            <?php echo count($fuel_types); ?> Fuel Types
                        </span>
                        <span class="stat-badge warning">
                            <i class="bi bi-gear-wide me-1"></i>
                            <?php echo count($transmission_types); ?> Transmissions
                        </span>
                    </div>
                    <div class="d-flex justify-content-lg-end gap-2">
                        <a href="enhanced_model_management.php" class="back-btn">
                            <i class="bi bi-arrow-left"></i> Back to Models
                        </a>
                        <a href="enhanced_model_management.php?edit=<?php echo $model['id']; ?>" class="btn btn-outline-light">
                            <i class="bi bi-pencil me-1"></i>Edit Model
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Section -->
        <div class="row">
            <div class="col-lg-4">
                <div class="specs-summary enhanced-spec-card mb-4">
                    <div class="card-header bg-gradient border-0 py-3">
                        <h6 class="mb-0 fw-bold text-primary">
                            <i class="bi bi-info-circle me-2"></i>Model Overview
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="summary-item">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-tags me-3 text-primary fs-5"></i>
                                <div>
                                    <div class="fw-semibold text-primary">Brand</div>
                                    <div class="text-muted"><?php echo htmlspecialchars($model['brand_name']); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="summary-item">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar-event me-3 text-success fs-5"></i>
                                <div>
                                    <div class="fw-semibold text-success">Production Years</div>
                                    <div class="text-muted">
                                        <?php echo $model['year_from'] ?: 'Not specified'; ?> -
                                        <?php echo $model['year_to'] ?: 'Present'; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="summary-item">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-gear me-3 text-warning fs-5"></i>
                                <div>
                                    <div class="fw-semibold text-warning">Engine Types</div>
                                    <div class="text-muted"><?php echo formatArrayField($engine_types); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="summary-item">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-fuel-pump me-3 text-info fs-5"></i>
                                <div>
                                    <div class="fw-semibold text-info">Fuel Types</div>
                                    <div class="text-muted"><?php echo formatArrayField($fuel_types); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="summary-item">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-gear-wide me-3 text-danger fs-5"></i>
                                <div>
                                    <div class="fw-semibold text-danger">Transmission Types</div>
                                    <div class="text-muted"><?php echo formatArrayField($transmission_types); ?></div>
                                </div>
                            </div>
                        </div>

                        <div class="summary-item">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-car-front me-3 text-secondary fs-5"></i>
                                <div>
                                    <div class="fw-semibold text-secondary">Body Types</div>
                                    <div class="text-muted"><?php echo formatArrayField($body_types); ?></div>
                                </div>
                            </div>
                        </div>

                        <?php if ($model['compatibility_info']): ?>
                        <div class="summary-item">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-shield-check me-3 text-success fs-5"></i>
                                <div>
                                    <div class="fw-semibold text-success">Compatibility</div>
                                    <div class="text-muted small"><?php echo htmlspecialchars($model['compatibility_info']); ?></div>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

            </div>
            
            <!-- Key Metrics & Technical Specifications -->
            <div class="col-lg-8">
                <!-- Key Metrics -->
                <?php
                $key_metrics = [];
                if (!empty($technical_specs)) {
                    // Extract some key metrics
                    $metric_keys = ['engine_displacement', 'horsepower', 'top_speed', 'fuel_consumption', 'length', 'width', 'height', 'weight'];
                    foreach ($metric_keys as $key) {
                        if (isset($technical_specs[$key])) {
                            $key_metrics[$key] = $technical_specs[$key];
                        }
                    }
                }
                if (!empty($key_metrics)): ?>
                <div class="mb-4">
                    <h4 class="mb-3 fw-bold text-primary">
                        <i class="bi bi-graph-up me-2"></i>Key Performance Metrics
                    </h4>
                    <div class="row g-3">
                        <?php foreach ($key_metrics as $key => $value): ?>
                        <div class="col-md-6 col-lg-3">
                            <div class="spec-metric">
                                <div class="metric-value"><?php echo htmlspecialchars($value); ?></div>
                                <div class="metric-label"><?php echo htmlspecialchars(str_replace('_', ' ', ucwords($key))); ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <?php if (!empty($specs_by_category)): ?>
                    <div class="spec-grid">
                        <?php foreach ($specs_by_category as $category => $specs): ?>
                            <div class="enhanced-spec-card">
                                <div class="card-header bg-gradient border-0 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="spec-category-icon <?php echo $category; ?>">
                                            <i class="bi <?php
                                                echo match($category) {
                                                    'engine' => 'bi-engine',
                                                    'performance' => 'bi-speedometer',
                                                    'dimensions' => 'bi-rulers',
                                                    'mechanical' => 'bi-gear',
                                                    'electrical' => 'bi-lightning',
                                                    'fuel' => 'bi-fuel-pump',
                                                    default => 'bi-list-ul'
                                                };
                                            ?>"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-0 fw-bold"><?php echo ucfirst($category); ?> Specifications</h5>
                                            <small class="text-muted"><?php echo count($specs); ?> specifications</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <?php foreach ($specs as $key => $value): ?>
                                        <div class="spec-item">
                                            <div class="row align-items-center g-3">
                                                <div class="col-sm-5">
                                                    <div class="spec-label">
                                                        <?php echo htmlspecialchars(str_replace('_', ' ', ucwords($key))); ?>
                                                    </div>
                                                </div>
                                                <div class="col-sm-7">
                                                    <div class="spec-value">
                                                        <?php echo formatSpecValue($value); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="enhanced-spec-card">
                        <div class="card-body text-center py-5">
                            <div class="no-specs-icon mb-4">
                                <i class="bi bi-info-circle display-1 text-muted"></i>
                            </div>
                            <h4 class="text-muted mb-3">No Technical Specifications</h4>
                            <p class="text-muted mb-4">This model does not have detailed technical specifications stored yet.</p>
                            <a href="enhanced_model_management.php?edit=<?php echo $model['id']; ?>" class="btn btn-primary btn-lg">
                                <i class="bi bi-pencil me-2"></i>Add Specifications
                            </a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Additional Actions -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <a href="enhanced_model_management.php" class="btn btn-outline-primary">
                            <i class="bi bi-arrow-left me-2"></i>Back to Models
                        </a>
                        <a href="enhanced_model_management.php?edit=<?php echo $model['id']; ?>" class="btn btn-outline-secondary ms-2">
                            <i class="bi bi-pencil me-2"></i>Edit Model
                        </a>
                    </div>
                    <div>
                        <span class="text-muted small">
                            <i class="bi bi-clock me-1"></i>
                            Last updated: <?php echo date('M d, Y \a\t H:i', strtotime($model['updated_at'] ?? $model['created_at'])); ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add some interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            // Animate spec cards on scroll
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);
            
            // Apply animation to spec cards
            document.querySelectorAll('.spec-card').forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(card);
            });
            
            // Add hover effects to spec items
            document.querySelectorAll('.spec-item').forEach(item => {
                item.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = '#f8f9fa';
                    this.style.transform = 'translateX(5px)';
                    this.style.transition = 'all 0.2s ease';
                });

                item.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = 'transparent';
                    this.style.transform = 'translateX(0)';
                });
            });
        });

    </script>
</body>
</html>