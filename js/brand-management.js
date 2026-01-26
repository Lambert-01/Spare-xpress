// Automatic filtering functionality for brands
let filterTimeout;
const currentFilters = {
    search: '',
    status: 'all',
    country: 'all'
};

document.addEventListener('DOMContentLoaded', function() {
    // Initialize filters from current page state
    initializeFiltersFromPage();
    
    // Initialize filters
    initializeFilters();
});

function initializeFiltersFromPage() {
    // Get current filter values from the page
    const searchInput = document.getElementById('searchFilter');
    const statusSelect = document.getElementById('statusFilter');
    const countrySelect = document.getElementById('countryFilter');
    
    if (searchInput) currentFilters.search = searchInput.value || '';
    if (statusSelect) currentFilters.status = statusSelect.value || 'all';
    if (countrySelect) currentFilters.country = countrySelect.value || 'all';
}

function initializeFilters() {
    // Add change event listeners to all filter selects
    document.querySelectorAll('.filter-select').forEach(select => {
        select.addEventListener('change', function() {
            const filterType = this.name;
            const filterValue = this.value;

            // Update current filters
            currentFilters[filterType] = filterValue;

            // Auto-submit form after delay
            clearTimeout(filterTimeout);
            filterTimeout = setTimeout(() => {
                document.getElementById('filterForm').submit();
            }, 500);
        });
    });

    // Add input event listener for search with debounce
    const searchInput = document.getElementById('searchFilter');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            const filterValue = this.value.trim();

            // Update current filters
            currentFilters.search = filterValue;

            // Clear previous timeout
            clearTimeout(searchTimeout);
            
            // Auto-submit form after delay (longer for search)
            searchTimeout = setTimeout(() => {
                document.getElementById('filterForm').submit();
            }, 800);
        });
    }
}

function applyFilters() {
    // For now, use regular form submission for better compatibility
    // This ensures the search works even if AJAX fails
    const params = new URLSearchParams();
    Object.keys(currentFilters).forEach(key => {
        if (currentFilters[key] !== 'all' && currentFilters[key] !== '') {
            params.append(key, currentFilters[key]);
        }
    });

    // Build URL and redirect
    const url = params.toString() ? `?${params}` : window.location.pathname;
    window.location.href = url;
}

// Uncomment this function if you want to use AJAX filtering instead
/*
function applyFilters() {
    // Show loading state
    showLoadingState();

    // Build query string
    const params = new URLSearchParams();
    Object.keys(currentFilters).forEach(key => {
        if (currentFilters[key] !== 'all' && currentFilters[key] !== '') {
            params.append(key, currentFilters[key]);
        }
    });

    // Make AJAX request
    fetch(`../api/get_filtered_brands.php?${params}`)
        .then(response => response.json())
        .then(data => {
            hideLoadingState();
            if (data.success) {
                updateBrandsGrid(data.brands);
                updateStatistics(data.stats);
            } else {
                showToast('Error loading brands: ' + data.error, 'danger');
            }
        })
        .catch(error => {
            console.error('Error filtering brands:', error);
            hideLoadingState();
            showToast('Error loading brands. Please try again.', 'danger');
        });
}
*/

function clearFilters() {
    // Reset the form
    const form = document.getElementById('filterForm');
    if (form) {
        form.reset();
        
        // Reset current filters
        currentFilters.search = '';
        currentFilters.status = 'all';
        currentFilters.country = 'all';
        
        // Submit the form to reload with cleared filters
        form.submit();
    } else {
        // Fallback: redirect to page without parameters
        window.location.href = window.location.pathname;
    }
}

function showLoadingState() {
    // Add loading overlay to brands container
    const container = document.getElementById('brands-container');
    if (container) {
        container.style.opacity = '0.5';
        container.style.pointerEvents = 'none';
        // Add loading spinner
        if (!container.querySelector('.loading-overlay')) {
            const loadingOverlay = document.createElement('div');
            loadingOverlay.className = 'loading-overlay d-flex justify-content-center align-items-center';
            loadingOverlay.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
            container.appendChild(loadingOverlay);
        }
    }
}

function hideLoadingState() {
    const container = document.getElementById('brands-container');
    if (container) {
        container.style.opacity = '1';
        container.style.pointerEvents = 'auto';
        // Remove loading overlay
        const loadingOverlay = container.querySelector('.loading-overlay');
        if (loadingOverlay) {
            loadingOverlay.remove();
        }
    }
}

function updateBrandsGrid(brands) {
    const container = document.getElementById('brands-container');
    const emptyState = container.querySelector('.empty-state');

    if (!brands || brands.length === 0) {
        container.innerHTML = '<div class="empty-state-container"><div class="empty-state text-center py-5"><i class="bi bi-tags display-1 text-muted mb-3"></i><h4>No Brands Found</h4><p class="text-muted">No brands match your current filters.</p><a href="enhanced_brand_management.php" class="btn btn-primary"><i class="bi bi-arrow-counterclockwise me-2"></i>Clear Filters</a></div></div>';
        return;
    }

    // Remove empty state if it exists
    const existingEmptyState = container.querySelector('.empty-state');
    if (existingEmptyState) {
        existingEmptyState.remove();
    }

    // Clear existing content
    container.innerHTML = '';

    // Brand mappings for images (same as PHP)
    const uploadedLogos = {
        'Mercedes-Benz': 'mercedes-benz-9.svg',
        'Renault': 'renault-2.svg',
        'Changan': 'changan-automobile-logo-1.svg',
        'BYD': 'byd-1.svg',
        'Geely': 'geely-logo-2.svg',
        'Chery': 'chery-3.svg',
        'Citroën': 'citroen-racing-2009-2016-logo.svg',
        'BAIC': 'BAIC.png',
        'Dongfeng': 'DONGFENG.png',
        'Great Wall': 'great-wall-seeklogo.png',
        'JAC': 'jac-motors-seeklogo.png'
    };

    const simpleIconBrands = [
        'toyota', 'nissan', 'hyundai', 'kia', 'mitsubishi', 'suzuki',
        'volkswagen', 'bmw', 'audi', 'honda', 'ford', 'mazda', 'subaru',
        'infiniti', 'acura', 'cadillac', 'jeep', 'chrysler', 'dodge', 'ram',
        'tesla', 'porsche', 'ferrari', 'lamborghini', 'bentley', 'jaguar',
        'volvo', 'saab', 'scania', 'iveco', 'daf', 'man', 'renault', 'peugeot',
        'fiat', 'alfaromeo', 'maserati', 'lancia', 'abarth', 'skoda', 'seat',
        'opel', 'vauxhall', 'mini', 'smart', 'maybach', 'lotus', 'mclaren',
        'yamaha', 'kawasaki', 'suzuki', 'ducati', 'triumph', 'aprilia', 'ktm', 'husqvarna',
        'mvagusta', 'indian', 'royalenfield', 'vespa', 'piaggio', 'trek',
        'specialized', 'cannondale', 'giant', 'johndeere', 'caseih', 'newholland',
        'claas', 'fendt', 'kubota', 'yanmar', 'komatsu', 'caterpillar', 'liebherr',
        'jcb', 'bobcat'
    ];

    const customLogoBrands = {
        'byd': 'https://www.carlogos.org/car-logos/bYD-logo.png',
        'mercedes': 'https://www.carlogos.org/car-logos/mercedes-benz-logo.png',
        'landrover': 'https://www.carlogos.org/car-logos/land-rover-logo.png',
        'lexus': 'https://www.carlogos.org/car-logos/lexus-logo.png',
        'mg': 'https://www.carlogos.org/car-logos/mg-logo.png',
        'wuling': 'https://www.carlogos.org/car-logos/wuling-logo.png',
        'isuzu': 'https://www.carlogos.org/car-logos/isuzu-logo.png',
        'chevrolet': 'https://www.carlogos.org/car-logos/chevrolet-logo.png',
        'citroen': 'https://www.carlogos.org/car-logos/citroen-logo.png'
    };

    let html = '';
    brands.forEach((brand, index) => {
        const brandSlug = brand.brand_name.toLowerCase().replace(/[\s-]/g, '');
        const animationDelay = (index * 0.1) + 's';
        let heroImageHtml = '';
        let floatingLogoHtml = '';

        // Determine hero image
        if (uploadedLogos[brand.brand_name]) {
            heroImageHtml = `<img src="/uploads/brands/${uploadedLogos[brand.brand_name]}" alt="${brand.brand_name} logo" class="brand-hero-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <div class="brand-hero-placeholder" style="display: none;">
                                <i class="bi bi-building display-4 text-white opacity-75"></i>
                                <div class="image-overlay"></div>
                            </div>`;
        } else if (simpleIconBrands.includes(brandSlug)) {
            heroImageHtml = `<img src="https://cdn.simpleicons.org/${brandSlug}/007bff" alt="${brand.brand_name} logo" class="brand-hero-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <div class="brand-hero-placeholder" style="display: none;">
                                <i class="bi bi-building display-4 text-white opacity-75"></i>
                                <div class="image-overlay"></div>
                            </div>`;
        } else if (customLogoBrands[brandSlug]) {
            heroImageHtml = `<img src="${customLogoBrands[brandSlug]}" alt="${brand.brand_name} logo" class="brand-hero-image" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                            <div class="brand-hero-placeholder" style="display: none;">
                                <i class="bi bi-building display-4 text-white opacity-75"></i>
                                <div class="image-overlay"></div>
                            </div>`;
        } else {
            heroImageHtml = `<div class="brand-hero-placeholder">
                                <i class="bi bi-building display-4 text-white opacity-75"></i>
                                <div class="image-overlay"></div>
                            </div>`;
        }

        // Determine floating logo
        if (uploadedLogos[brand.brand_name]) {
            floatingLogoHtml = `<img src="/uploads/brands/${uploadedLogos[brand.brand_name]}" alt="${brand.brand_name} Logo" class="brand-logo-floating" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                              <div class="brand-logo-placeholder-floating" style="display: none;">
                                  <i class="bi bi-tag text-white fs-3"></i>
                              </div>`;
        } else if (simpleIconBrands.includes(brandSlug)) {
            floatingLogoHtml = `<img src="https://cdn.simpleicons.org/${brandSlug}/ffffff" alt="${brand.brand_name} Logo" class="brand-logo-floating" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                              <div class="brand-logo-placeholder-floating" style="display: none;">
                                  <i class="bi bi-tag text-white fs-3"></i>
                              </div>`;
        } else if (customLogoBrands[brandSlug]) {
            floatingLogoHtml = `<img src="${customLogoBrands[brandSlug]}" alt="${brand.brand_name} Logo" class="brand-logo-floating" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                              <div class="brand-logo-placeholder-floating" style="display: none;">
                                  <i class="bi bi-tag text-white fs-3"></i>
                              </div>`;
        } else {
            floatingLogoHtml = `<div class="brand-logo-placeholder-floating">
                                  <i class="bi bi-tag text-white fs-3"></i>
                              </div>`;
        }

        html += `
            <div class="col-xl-3 col-lg-4 col-md-6 col-sm-6 fade-in-up" style="animation-delay: ${animationDelay};">
                <div class="brand-card enhanced-card h-100">
                    <div class="card-image-header">
                        ${heroImageHtml}
                        <div class="image-overlay"></div>

                        <div class="floating-logo">
                            ${floatingLogoHtml}
                        </div>

                        <div class="card-controls">
                            <div class="form-check me-2">
                                <input class="form-check-input bulk-select" type="checkbox" value="${brand.id}" id="brand_${brand.id}">
                                <label class="form-check-label visually-hidden" for="brand_${brand.id}">
                                    Select ${brand.brand_name}
                                </label>
                            </div>
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm dropdown-toggle opacity-75" type="button" data-bs-toggle="dropdown">
                                    <i class="bi bi-three-dots"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="#" onclick="editBrand(${brand.id})">
                                        <i class="bi bi-pencil me-2"></i>Edit Brand</a></li>
                                    <li><a class="dropdown-item" href="#" onclick="viewAnalytics(${brand.id})">
                                        <i class="bi bi-graph-up me-2"></i>View Analytics</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="#" onclick="deleteBrand(${brand.id}, '${brand.brand_name.replace(/'/g, "\\'")}')">
                                        <i class="bi bi-trash me-2"></i>Delete Brand</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="card-content">
                        <!-- Brand Header -->
                        <div class="brand-header mb-3">
                            <h4 class="brand-name mb-2">${brand.brand_name}</h4>
                            <div class="brand-meta">
                                ${brand.country ? `
                                    <div class="meta-item">
                                        <i class="bi bi-geo-alt-fill me-1"></i>
                                        <span>${brand.country}</span>
                                    </div>
                                ` : ''}
                                ${brand.founded_year ? `
                                    <div class="meta-item">
                                        <i class="bi bi-calendar-event me-1"></i>
                                        <span>${brand.founded_year}</span>
                                    </div>
                                ` : ''}
                            </div>
                        </div>

                        ${brand.description ? `<div class="brand-description mb-3"><p class="text-muted small mb-0">${brand.description.substring(0, 100)}${brand.description.length > 100 ? '...' : ''}</p></div>` : ''}

                        <div class="analytics-grid mb-3">
                            <div class="analytics-item">
                                <div class="analytics-value text-primary fw-bold">${brand.model_count}</div>
                                <div class="analytics-label small text-muted">Models</div>
                            </div>
                            <div class="analytics-item">
                                <div class="analytics-value text-success fw-bold">${brand.active_product_count}</div>
                                <div class="analytics-label small text-muted">Products</div>
                            </div>
                            <div class="analytics-item">
                                <div class="analytics-value text-info fw-bold">${Number(brand.total_sales).toLocaleString()}</div>
                                <div class="analytics-label small text-muted">Sales</div>
                            </div>
                        </div>

                        <div class="card-footer-section">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="status-badge ${brand.is_active ? 'status-active' : 'status-inactive'}">
                                    <i class="bi bi-circle-fill me-1"></i>
                                    ${brand.is_active ? 'Active' : 'Inactive'}
                                </span>
                                <div class="action-buttons">
                                    <a href="/admin/models/enhanced_model_management.php?brand=${brand.id}" class="btn btn-primary btn-sm action-btn" title="View Models">
                                        <i class="bi bi-car-front-fill"></i>
                                    </a>
                                    <a href="/admin/products/enhanced_product_management.php?brand=${brand.id}" class="btn btn-success btn-sm action-btn" title="View Products">
                                        <i class="bi bi-box-seam-fill"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}

function updateStatistics(stats) {
    // Update statistics cards with filtered data
    if (stats) {
        // Update Total Brands card
        const totalBrandsElement = document.getElementById('totalBrands');
        if (totalBrandsElement) {
            totalBrandsElement.textContent = Number(stats.total_brands || 0).toLocaleString();
        }

        // Update Total Models card
        const totalModelsElement = document.getElementById('totalModels');
        if (totalModelsElement) {
            totalModelsElement.textContent = Number(stats.total_models || 0).toLocaleString();
        }

        // Update Active Products card
        const activeProductsElement = document.getElementById('activeProducts');
        if (activeProductsElement) {
            activeProductsElement.textContent = Number(stats.active_products || 0).toLocaleString();
        }

        // Update Total Sales card
        const totalSalesElement = document.getElementById('totalSales');
        if (totalSalesElement) {
            totalSalesElement.textContent = Number(stats.total_sales || 0).toLocaleString();
        }
    }
}

function showLoadingState() {
    // Add loading overlay to brands container
    const container = document.getElementById('brands-container');
    if (container) {
        container.style.opacity = '0.5';
        container.style.pointerEvents = 'none';
        // Add loading spinner
        if (!container.querySelector('.loading-overlay')) {
            const loadingOverlay = document.createElement('div');
            loadingOverlay.className = 'loading-overlay d-flex justify-content-center align-items-center';
            loadingOverlay.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
            container.appendChild(loadingOverlay);
        }
    }
}

function hideLoadingState() {
    const container = document.getElementById('brands-container');
    if (container) {
        container.style.opacity = '1';
        container.style.pointerEvents = 'auto';
        // Remove loading overlay
        const loadingOverlay = container.querySelector('.loading-overlay');
        if (loadingOverlay) {
            loadingOverlay.remove();
        }
    }
}

// Show toast notification function
function showToast(message, type = 'info') {
    // Create toast element if it doesn't exist
    let toastContainer = document.querySelector('.toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
        toastContainer.style.zIndex = '9999';
        document.body.appendChild(toastContainer);
    }

    // Create toast element
    const toastId = 'toast-' + Date.now();
    const toastElement = document.createElement('div');
    toastElement.id = toastId;
    toastElement.className = `toast align-items-center text-white bg-${type} border-0`;
    toastElement.setAttribute('role', 'alert');
    toastElement.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;

    toastContainer.appendChild(toastElement);

    // Initialize and show toast
    const toast = new bootstrap.Toast(toastElement);
    toast.show();

    // Remove toast element after it's hidden
    toastElement.addEventListener('hidden.bs.toast', function () {
        toastElement.remove();
    });
}

// Edit brand function
function editBrand(brandId) {
    fetch(`../api/get_brand.php?id=${brandId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const brand = data.brand;
                document.getElementById('editBrandId').value = brand.id;

                const content = `
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Brand Name *</label>
                            <input type="text" class="form-control" name="brand_name" value="${brand.brand_name}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Country</label>
                            <input type="text" class="form-control" name="country" value="${brand.country || ''}" placeholder="e.g., Japan, Germany">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Founded Year</label>
                            <input type="number" class="form-control" name="founded_year" value="${brand.founded_year || ''}" min="1900" max="${new Date().getFullYear()}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Website</label>
                            <input type="url" class="form-control" name="website" value="${brand.website || ''}" placeholder="https://...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contact Email</label>
                            <input type="email" class="form-control" name="contact_email" value="${brand.contact_email || ''}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Contact Phone</label>
                            <input type="tel" class="form-control" name="contact_phone" value="${brand.contact_phone || ''}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Logo Image</label>
                            <input type="file" class="form-control" name="logo_image" accept="image/*">
                            <input type="hidden" name="existing_logo" value="${brand.logo_image || ''}">
                            ${brand.logo_image ? `<small class="text-muted">Current: ${brand.logo_image.split('/').pop()}</small>` : ''}
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Brand Image</label>
                            <input type="file" class="form-control" name="brand_image" accept="image/*">
                            <input type="hidden" name="existing_brand_image" value="${brand.brand_image || ''}">
                            ${brand.brand_image ? `<small class="text-muted">Current: ${brand.brand_image.split('/').pop()}</small>` : ''}
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea class="form-control" name="description" rows="3">${brand.description || ''}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Manufacturer Details</label>
                            <textarea class="form-control" name="manufacturer_details" rows="2">${brand.manufacturer_details || ''}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">SEO Title</label>
                            <input type="text" class="form-control" name="seo_title" value="${brand.seo_title || ''}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">SEO Description</label>
                            <textarea class="form-control" name="seo_description" rows="2">${brand.seo_description || ''}</textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="is_active" id="editBrandActive" ${brand.is_active ? 'checked' : ''}>
                                <label class="form-check-label" for="editBrandActive">
                                    Brand is active
                                </label>
                            </div>
                        </div>
                    </div>
                `;

                document.getElementById('editBrandContent').innerHTML = content;
                new bootstrap.Modal(document.getElementById('editBrandModal')).show();
            }
        })
        .catch(error => {
            console.error('Error loading brand data:', error);
            alert('Error loading brand data. Please try again.');
        });
}

// Delete brand function
function deleteBrand(brandId, brandName) {
    if (confirm(`Are you sure you want to delete "${brandName}"? This action cannot be undone.`)) {
        // Show loading
        const btn = event ? event.target.closest('.dropdown-item') : null;
        const originalText = btn ? btn.innerHTML : '';
        if (btn) {
            btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Deleting...';
            btn.style.pointerEvents = 'none';
        }

        fetch(`?delete=${brandId}`, {
            method: 'GET'
        })
        .then(response => {
            if (response.ok) {
                showToast('Brand deleted successfully!', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                throw new Error('Delete failed');
            }
        })
        .catch(error => {
            console.error('Error deleting brand:', error);
            showToast('Error deleting brand. Please try again.', 'danger');
            if (btn) {
                btn.innerHTML = originalText;
                btn.style.pointerEvents = 'auto';
            }
        });
    }
}

// View analytics function
function viewAnalytics(brandId) {
    showToast('Analytics feature coming soon!', 'info');
}

// Export brands function
function exportBrands() {
    // Create a temporary link to trigger download
    const link = document.createElement('a');
    link.href = '../api/export_brands.php';
    link.download = 'brands_export_' + new Date().toISOString().slice(0, 19).replace(/:/g, '-') + '.csv';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    showToast('Export started! File will download automatically.', 'success');
}