    </div><!-- /.container-fluid -->
</div><!-- /.main-content -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>

<script>
// ── DataTables ──
$(document).ready(function() {
    $('.datatable').DataTable({
        pageLength: 25,
        ordering: true,
        searching: true,
        responsive: true
    });
});

// ── Sidebar Toggle ──
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const main = document.getElementById('mainContent');
    if (!sidebar) return;

    // Mobile: slide the off-canvas sidebar in/out
    if (window.innerWidth < 768) {
        sidebar.classList.toggle('show');
        return;
    }

    const isCollapsed = sidebar.classList.toggle('collapsed');
    main.classList.toggle('expanded', isCollapsed);
    localStorage.setItem('sidebar-collapsed', isCollapsed);

    // Rotate submenu arrows
    document.querySelectorAll('.submenu-arrow').forEach(arrow => {
        arrow.style.transform = isCollapsed ? '' : '';
    });
}

// Restore sidebar state on load
(function() {
    if (localStorage.getItem('sidebar-collapsed') === 'true') {
        document.getElementById('sidebar')?.classList.add('collapsed');
        document.getElementById('mainContent')?.classList.add('expanded');
    }
})();

// Mobile: close sidebar on outside click
document.addEventListener('click', function(e) {
    const sidebar = document.getElementById('sidebar');
    if (window.innerWidth < 768 && sidebar && sidebar.classList.contains('show')) {
        if (!sidebar.contains(e.target) && !e.target.closest('[onclick="toggleSidebar()"]')) {
            sidebar.classList.remove('show');
        }
    }
});

// ── Active Nav Link ──
$(document).ready(function() {
    const currentPath = window.location.pathname;
    $('.sidebar .nav-link, .sidebar .submenu-link').each(function() {
        const href = $(this).attr('href');
        if (href && href !== '#' && currentPath.includes(href.split('/').pop().replace('.php', ''))) {
            $(this).addClass('active');
            const collapse = $(this).closest('.submenu-collapse');
            if (collapse.length) {
                collapse.addClass('show');
                collapse.prev('.collapsible-toggle').attr('aria-expanded', 'true');
                collapse.prev('.collapsible-toggle').find('.submenu-arrow').css('transform', 'rotate(180deg)');
            }
        }
    });

    // Submenu arrow rotation
    $('.collapsible-toggle').on('click', function() {
        const arrow = $(this).find('.submenu-arrow');
        const isExpanded = $(this).attr('aria-expanded') === 'true';
        arrow.css('transform', isExpanded ? '' : 'rotate(180deg)');
        const submenuId = $(this).data('bs-target').substring(1);
        localStorage.setItem('submenu-' + submenuId, !isExpanded);
    });

    // Restore submenu states
    $('.collapsible-toggle').each(function() {
        const submenuId = $(this).data('bs-target').substring(1);
        if (localStorage.getItem('submenu-' + submenuId) === 'true') {
            $(this).attr('aria-expanded', 'true');
            $($(this).data('bs-target')).addClass('show');
            $(this).find('.submenu-arrow').css('transform', 'rotate(180deg)');
        }
    });
});

// ── Online Status Check ──
function checkOnlineStatus() {
    const dot = document.getElementById('status-dot');
    const text = document.getElementById('status-text');
    if (!dot || !text) return;
    if (navigator.onLine) {
        dot.className = 'bi bi-circle-fill text-success me-1';
        text.textContent = 'Online';
    } else {
        dot.className = 'bi bi-circle-fill text-danger me-1';
        text.textContent = 'Offline';
    }
}
window.addEventListener('online', checkOnlineStatus);
window.addEventListener('offline', checkOnlineStatus);
checkOnlineStatus();

// ── Theme Toggle ──
(function() {
    const saved = localStorage.getItem('admin-theme') || 'light';
    document.documentElement.setAttribute('data-theme', saved);
    updateThemeIcon(saved);
})();

document.getElementById('themeToggle')?.addEventListener('click', function() {
    const current = document.documentElement.getAttribute('data-theme');
    const next = current === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', next);
    localStorage.setItem('admin-theme', next);
    updateThemeIcon(next);
});

function updateThemeIcon(theme) {
    const icon = document.getElementById('themeIcon');
    if (icon) icon.className = theme === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-stars-fill';
}

// ── Toast ──
function showToast(message, type = 'success') {
    if (!$('#toastContainer').length) {
        $('body').append('<div id="toastContainer" class="toast-container position-fixed top-0 end-0 p-3" style="z-index:9999;"></div>');
    }
    const icons = { success: 'check-circle-fill', danger: 'exclamation-triangle-fill', warning: 'exclamation-circle-fill', info: 'info-circle-fill' };
    const html = `<div class="toast align-items-center text-white bg-${type} border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body"><i class="bi bi-${icons[type]||'info-circle-fill'} me-2"></i>${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>`;
    $('#toastContainer').append(html);
    const toastEl = $('#toastContainer .toast').last()[0];
    new bootstrap.Toast(toastEl, { delay: 4000 }).show();
    setTimeout(() => $(toastEl).remove(), 5000);
}

// ── Auto-hide alerts ──
setTimeout(() => { $('.alert.auto-hide').fadeOut('slow'); }, 5000);

// ── Tooltips (collapsed sidebar) ──
$(document).ready(function() {
    function initTooltips() {
        const sidebar = document.getElementById('sidebar');
        if (!sidebar) return;
        if (sidebar.classList.contains('collapsed')) {
            sidebar.querySelectorAll('.nav-link[title], .sidebar-logout[title]').forEach(el => {
                if (!bootstrap.Tooltip.getInstance(el)) {
                    new bootstrap.Tooltip(el, { placement: 'right', trigger: 'hover' });
                }
            });
        } else {
            sidebar.querySelectorAll('.nav-link[title], .sidebar-logout[title]').forEach(el => {
                bootstrap.Tooltip.getInstance(el)?.dispose();
            });
        }
    }
    initTooltips();
    document.querySelector('[onclick="toggleSidebar()"]')?.addEventListener('click', () => setTimeout(initTooltips, 350));

    // General tooltips
    document.querySelectorAll('[data-bs-toggle="tooltip"]:not(.sidebar *)').forEach(el => {
        new bootstrap.Tooltip(el);
    });
});

// ── Form loading states ──
$('form').on('submit', function(e) {
    if ($(this).hasClass('ajax-form') || $(this).hasClass('no-loading')) return;
    const submitter = e.originalEvent?.submitter || document.activeElement;
    let btn = submitter && (submitter.tagName === 'BUTTON' || submitter.type === 'submit')
        ? $(submitter)
        : $(this).find('button[type="submit"], input[type="submit"]').first();
    if (!btn.length) return;
    const orig = btn.is('input') ? btn.val() : btn.html();
    if (btn.is('input')) btn.val('Processing...').prop('disabled', true);
    else btn.html('<span class="loading me-2"></span>Processing...').prop('disabled', true);
    setTimeout(() => {
        if (btn.is('input')) btn.val(orig).prop('disabled', false);
        else btn.html(orig).prop('disabled', false);
    }, 15000);
});

// ── Delete confirmation ──
$('.delete-btn').on('click', function(e) {
    e.preventDefault();
    const item = $(this).data('item') || 'item';
    if (confirm(`Are you sure you want to delete this ${item}? This cannot be undone.`)) {
        window.location.href = $(this).attr('href');
    }
});

// ── Image preview ──
$('.image-input').on('change', function() {
    const preview = $(this).data('preview');
    const file = this.files[0];
    if (file && preview) {
        const reader = new FileReader();
        reader.onload = e => $(preview).attr('src', e.target.result).show();
        reader.readAsDataURL(file);
    }
});

// ── Settings Modal ──
function openSettings() {
    if (!$('#settingsModal').length) {
        $('body').append(`
        <div class="modal fade" id="settingsModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="bi bi-gear me-2"></i>Admin Settings</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Theme</label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="themeSwitch">
                                <label class="form-check-label" for="themeSwitch">Dark Mode</label>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Dashboard Refresh Rate</label>
                            <select class="form-select" id="refreshRate">
                                <option value="30000">30 seconds</option>
                                <option value="60000">1 minute</option>
                                <option value="300000">5 minutes</option>
                                <option value="0">Manual only</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" onclick="saveSettings()">Save</button>
                    </div>
                </div>
            </div>
        </div>`);
    }
    $('#themeSwitch').prop('checked', document.documentElement.getAttribute('data-theme') === 'dark');
    $('#refreshRate').val(localStorage.getItem('refresh-rate') || '30000');
    new bootstrap.Modal(document.getElementById('settingsModal')).show();
}

function saveSettings() {
    const dark = $('#themeSwitch').is(':checked');
    const theme = dark ? 'dark' : 'light';
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem('admin-theme', theme);
    updateThemeIcon(theme);
    localStorage.setItem('refresh-rate', $('#refreshRate').val());
    bootstrap.Modal.getInstance(document.getElementById('settingsModal')).hide();
    showToast('Settings saved!', 'success');
}

// ── Toggle Status (Brands/Models/Products) ──
function toggleStatus(type, id, checkbox) {
    const is_active = checkbox.checked ? 1 : 0;
    const label = checkbox.closest('.toggle-switch')?.nextElementSibling;
    
    fetch('/api/toggle_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ type, id, is_active })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            if (label) {
                label.textContent = is_active ? 'Active' : 'Inactive';
                label.className = 'toggle-label ' + (is_active ? 'active' : 'inactive');
            }
            // Update status badge if present
            const badge = document.querySelector(`[data-status-id="${id}"]`);
            if (badge) {
                badge.className = `badge ${is_active ? 'bg-success' : 'bg-danger'}`;
                badge.textContent = is_active ? 'Active' : 'Inactive';
            }
            // Reload page after cascade to show updated statuses
            if (data.affected && (data.affected.models > 0 || data.affected.products > 0)) {
                setTimeout(() => location.reload(), 1500);
            }
        } else {
            checkbox.checked = !is_active;
            showToast(data.message || 'Failed to update status', 'danger');
        }
    })
    .catch(err => {
        checkbox.checked = !is_active;
        showToast('Network error. Please try again.', 'danger');
    });
}
</script>
</body>
</html>
