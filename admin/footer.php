    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
// Initialize DataTables
$(document).ready(function() {
    $('.datatable').DataTable({
        "pageLength": 25,
        "ordering": true,
        "searching": true,
        "responsive": true,
        "language": {
            "search": "Search:",
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ entries",
            "paginate": {
                "first": "First",
                "last": "Last",
                "next": "Next",
                "previous": "Previous"
            }
        }
    });
});

// Sidebar toggle functionality
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');

    sidebar.classList.toggle('collapsed');
    mainContent.classList.toggle('expanded');
}

// Auto-collapse sidebar on mobile
$(window).resize(function() {
    if ($(window).width() < 768) {
        $('#sidebar').removeClass('collapsed');
        $('#mainContent').removeClass('expanded');
    }
});

// Enhanced loading states for forms and buttons
$('form').on('submit', function(e) {
    const form = $(this);
    const submitBtn = form.find('button[type="submit"], input[type="submit"]');
    const originalText = submitBtn.html();

    // Don't show loading for forms with ajax-form class (handled separately)
    if (!form.hasClass('ajax-form')) {
        submitBtn.html('<span class="loading me-2"></span>Processing...').prop('disabled', true);

        // Add loading overlay to form
        form.addClass('loading-active');
        form.append('<div class="form-loading-overlay"><div class="loading-spinner"></div></div>');

        // Re-enable after 15 seconds as fallback
        setTimeout(() => {
            submitBtn.html(originalText).prop('disabled', false);
            form.removeClass('loading-active').find('.form-loading-overlay').remove();
        }, 15000);
    }
});

// Loading states for AJAX buttons
$('.btn-loading').on('click', function() {
    const btn = $(this);
    const originalText = btn.html();

    btn.html('<span class="loading me-2"></span>Loading...').prop('disabled', true);

    // Re-enable after 10 seconds as fallback
    setTimeout(() => {
        btn.html(originalText).prop('disabled', false);
    }, 10000);
});

// Enhanced button hover effects
$('.btn').on('mouseenter', function() {
    $(this).addClass('btn-hover');
}).on('mouseleave', function() {
    $(this).removeClass('btn-hover');
});

// Micro-interactions for cards
$('.stats-card, .form-card, .brand-card').on('mouseenter', function() {
    $(this).addClass('card-hover');
}).on('mouseleave', function() {
    $(this).removeClass('card-hover');
});

// Smooth scrolling for anchor links
$('a[href^="#"]').on('click', function(e) {
    const target = $(this.getAttribute('href'));
    if (target.length) {
        e.preventDefault();
        $('html, body').animate({
            scrollTop: target.offset().top - 100
        }, 500);
    }
});

// Enhanced modal animations
$('.modal').on('show.bs.modal', function() {
    $(this).addClass('modal-fade-in');
}).on('hide.bs.modal', function() {
    $(this).removeClass('modal-fade-in');
});

// Table sorting functionality
$(document).ready(function() {
    $('.table th.sortable').on('click', function() {
        const table = $(this).closest('table');
        const column = $(this).data('column');
        const currentSort = $(this).hasClass('sort-asc') ? 'asc' : $(this).hasClass('sort-desc') ? 'desc' : 'none';

        // Reset all sort indicators
        table.find('th.sortable').removeClass('sort-asc sort-desc');

        // Determine new sort order
        let newSort = 'asc';
        if (currentSort === 'asc') {
            newSort = 'desc';
        }

        // Add sort indicator
        $(this).addClass('sort-' + newSort);

        // Sort table rows
        const tbody = table.find('tbody');
        const rows = tbody.find('tr').toArray();

        rows.sort(function(a, b) {
            const aVal = getCellValue(a, column);
            const bVal = getCellValue(b, column);

            if (newSort === 'asc') {
                return aVal.localeCompare(bVal, undefined, {numeric: true, sensitivity: 'base'});
            } else {
                return bVal.localeCompare(aVal, undefined, {numeric: true, sensitivity: 'base'});
            }
        });

        // Re-append sorted rows
        tbody.empty();
        tbody.append(rows);
    });

    function getCellValue(row, column) {
        const cell = $(row).find(`td[data-column="${column}"], td`).first();
        return cell.text().trim() || cell.find('input').val() || '';
    }
});

// Table search and filter enhancements
$(document).ready(function() {
    // Live search in tables
    $('.table-search').on('input', function() {
        const searchTerm = $(this).val().toLowerCase();
        const table = $(this).data('table') ? $('#' + $(this).data('table')) : $(this).closest('.table-responsive').find('table');

        table.find('tbody tr').each(function() {
            const rowText = $(this).text().toLowerCase();
            $(this).toggle(rowText.includes(searchTerm));
        });
    });

    // Table pagination (if needed)
    $('.table-paginate').on('click', function() {
        const page = $(this).data('page');
        const table = $(this).data('table') ? $('#' + $(this).data('table')) : $(this).closest('.table-responsive').find('table');

        // Simple pagination logic
        const rowsPerPage = 25;
        const startIndex = (page - 1) * rowsPerPage;
        const endIndex = startIndex + rowsPerPage;

        table.find('tbody tr').each(function(index) {
            $(this).toggle(index >= startIndex && index < endIndex);
        });
    });
});

// Confirmation dialogs
$('.delete-btn').on('click', function(e) {
    e.preventDefault();
    const href = $(this).attr('href');
    const item = $(this).data('item') || 'item';

    if (confirm(`Are you sure you want to delete this ${item}? This action cannot be undone.`)) {
        window.location.href = href;
    }
});

// Toast notifications
function showToast(message, type = 'success') {
    const toastHtml = `
        <div class="toast align-items-center text-white bg-${type} border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    `;

    if (!$('#toastContainer').length) {
        $('body').append('<div id="toastContainer" class="toast-container position-fixed top-0 end-0 p-3"></div>');
    }

    $('#toastContainer').append(toastHtml);
    const toast = new bootstrap.Toast($('#toastContainer .toast').last()[0]);
    toast.show();

    // Remove toast after shown
    setTimeout(() => {
        $('#toastContainer .toast').last().remove();
    }, 5000);
}

// Auto-hide alerts after 5 seconds
setTimeout(() => {
    $('.alert').fadeOut('slow');
}, 5000);

// Initialize tooltips
$(document).ready(function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Sidebar submenu functionality
$(document).ready(function() {
    // Load saved submenu states
    $('.collapsible-toggle').each(function() {
        const submenuId = $(this).data('bs-target').substring(1);
        const isExpanded = localStorage.getItem(`submenu-${submenuId}`) === 'true';
        if (isExpanded) {
            $(this).attr('aria-expanded', 'true');
            $($(this).data('bs-target')).addClass('show');
        }
    });

    // Save submenu states when toggled
    $('.collapsible-toggle').on('click', function() {
        const submenuId = $(this).data('bs-target').substring(1);
        const isExpanded = $(this).attr('aria-expanded') === 'true';
        localStorage.setItem(`submenu-${submenuId}`, !isExpanded);
    });

    // Highlight active menu item
    const currentPath = window.location.pathname.split('/').pop();
    $('.sidebar .nav-link').each(function() {
        const href = $(this).attr('href');
        if (href && href.includes(currentPath)) {
            $(this).addClass('active');
            // Also expand parent submenu if this is a submenu item
            $(this).closest('.submenu-collapse').prev('.collapsible-toggle').attr('aria-expanded', 'true');
            $(this).closest('.submenu-collapse').addClass('show');
        }
    });
});

// Theme Toggle Functionality
$(document).ready(function() {
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    const html = document.documentElement;

    // Load saved theme
    const savedTheme = localStorage.getItem('admin-theme') || 'light';
    html.setAttribute('data-theme', savedTheme);
    updateThemeIcon(savedTheme);

    // Theme toggle click handler
    themeToggle.addEventListener('click', function() {
        const currentTheme = html.getAttribute('data-theme');
        const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

        html.setAttribute('data-theme', newTheme);
        localStorage.setItem('admin-theme', newTheme);
        updateThemeIcon(newTheme);

        // Add transition class for smooth animation
        document.body.classList.add('theme-transition');
        setTimeout(() => {
            document.body.classList.remove('theme-transition');
        }, 300);
    });

    function updateThemeIcon(theme) {
        if (theme === 'dark') {
            themeIcon.className = 'bi bi-sun-fill';
        } else {
            themeIcon.className = 'bi bi-moon-stars-fill';
        }
    }
});

// Settings Modal
function openSettings() {
    // Create settings modal if it doesn't exist
    if (!$('#settingsModal').length) {
        const settingsModal = `
            <div class="modal fade" id="settingsModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-gear me-2"></i>Admin Settings
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Theme Preference</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="themeSwitch">
                                    <label class="form-check-label" for="themeSwitch">
                                        Dark Mode
                                    </label>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Dashboard Refresh Rate</label>
                                <select class="form-select" id="refreshRate">
                                    <option value="30000">30 seconds</option>
                                    <option value="60000">1 minute</option>
                                    <option value="300000">5 minutes</option>
                                    <option value="0">Manual only</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Items Per Page</label>
                                <select class="form-select" id="itemsPerPage">
                                    <option value="10">10</option>
                                    <option value="25">25</option>
                                    <option value="50">50</option>
                                    <option value="100">100</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-primary" onclick="saveSettings()">Save Settings</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        $('body').append(settingsModal);
    }

    // Load current settings
    const currentTheme = document.documentElement.getAttribute('data-theme');
    $('#themeSwitch').prop('checked', currentTheme === 'dark');
    $('#refreshRate').val(localStorage.getItem('refresh-rate') || '30000');
    $('#itemsPerPage').val(localStorage.getItem('items-per-page') || '25');

    new bootstrap.Modal(document.getElementById('settingsModal')).show();
}

function saveSettings() {
    const themeChecked = $('#themeSwitch').is(':checked');
    const newTheme = themeChecked ? 'dark' : 'light';
    const refreshRate = $('#refreshRate').val();
    const itemsPerPage = $('#itemsPerPage').val();

    // Apply theme
    document.documentElement.setAttribute('data-theme', newTheme);
    localStorage.setItem('admin-theme', newTheme);
    updateThemeIcon(newTheme);

    // Save other settings
    localStorage.setItem('refresh-rate', refreshRate);
    localStorage.setItem('items-per-page', itemsPerPage);

    // Close modal
    bootstrap.Modal.getInstance(document.getElementById('settingsModal')).hide();

    showToast('Settings saved successfully!', 'success');
}

function updateThemeIcon(theme) {
    const themeIcon = document.getElementById('themeIcon');
    if (themeIcon) {
        if (theme === 'dark') {
            themeIcon.className = 'bi bi-sun-fill';
        } else {
            themeIcon.className = 'bi bi-moon-stars-fill';
        }
    }
}

// Form validation enhancement
$('form').on('submit', function(e) {
    const requiredFields = $(this).find('[required]');
    let isValid = true;

    requiredFields.each(function() {
        if (!$(this).val().trim()) {
            $(this).addClass('is-invalid');
            isValid = false;
        } else {
            $(this).removeClass('is-invalid');
        }
    });

    if (!isValid) {
        e.preventDefault();
        showToast('Please fill in all required fields.', 'warning');
    }
});

// Image preview for file inputs
$('.image-input').on('change', function() {
    const file = this.files[0];
    const preview = $(this).data('preview');

    if (file && preview) {
        const reader = new FileReader();
        reader.onload = function(e) {
            $(preview).attr('src', e.target.result).show();
        };
        reader.readAsDataURL(file);
    }
});

// AJAX form submissions for better UX
$('.ajax-form').on('submit', function(e) {
    e.preventDefault();

    const form = $(this);
    const submitBtn = form.find('button[type="submit"]');
    const originalText = submitBtn.html();

    submitBtn.html('<span class="loading"></span> Saving...').prop('disabled', true);

    $.ajax({
        url: form.attr('action'),
        method: form.attr('method'),
        data: new FormData(this),
        processData: false,
        contentType: false,
        success: function(response) {
            showToast('Operation completed successfully!', 'success');
            setTimeout(() => location.reload(), 1500);
        },
        error: function() {
            showToast('An error occurred. Please try again.', 'danger');
            submitBtn.html(originalText).prop('disabled', false);
        }
    });
});
</script>

</body>
</html>