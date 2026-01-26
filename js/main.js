(function ($) {
    "use strict";

    // Spinner
    var spinner = function () {
        setTimeout(function () {
            if ($('#spinner').length > 0) {
                $('#spinner').removeClass('show');
            }
        }, 1);
    };
    spinner(0);
    
    
    // Initiate the wowjs
    new WOW().init();


    // Sticky Navbar
    $(window).scroll(function () {
        if ($(this).scrollTop() > 45) {
            $('.nav-bar').addClass('sticky-top shadow-sm');
        } else {
            $('.nav-bar').removeClass('sticky-top shadow-sm');
        }
    });


    // Hero Header carousel
    $(".header-carousel").owlCarousel({
        items: 1,
        autoplay: true,
        smartSpeed: 2000,
        center: false,
        dots: false,
        loop: true,
        margin: 0,
        nav : true,
        navText : [
            '<i class="bi bi-arrow-left"></i>',
            '<i class="bi bi-arrow-right"></i>'
        ]
    });


    // ProductList carousel
    $(".productList-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 2000,
        dots: false,
        loop: true,
        margin: 25,
        nav : true,
        navText : [
            '<i class="fas fa-chevron-left"></i>',
            '<i class="fas fa-chevron-right"></i>'
        ],
        responsiveClass: true,
        responsive: {
            0:{
                items:1
            },
            576:{
                items:1
            },
            768:{
                items:2
            },
            992:{
                items:2
            },
            1200:{
                items:3
            }
        }
    });

    // ProductList categories carousel
    $(".productImg-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 1500,
        dots: false,
        loop: true,
        items: 1,
        margin: 25,
        nav : true,
        navText : [
            '<i class="bi bi-arrow-left"></i>',
            '<i class="bi bi-arrow-right"></i>'
        ]
    });


    // Single Products carousel
    $(".single-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 1500,
        dots: true,
        dotsData: true,
        loop: true,
        items: 1,
        nav : true,
        navText : [
            '<i class="bi bi-arrow-left"></i>',
            '<i class="bi bi-arrow-right"></i>'
        ]
    });


    // ProductList carousel
    $(".related-carousel").owlCarousel({
        autoplay: true,
        smartSpeed: 1500,
        dots: false,
        loop: true,
        margin: 25,
        nav : true,
        navText : [
            '<i class="fas fa-chevron-left"></i>',
            '<i class="fas fa-chevron-right"></i>'
        ],
        responsiveClass: true,
        responsive: {
            0:{
                items:1
            },
            576:{
                items:1
            },
            768:{
                items:2
            },
            992:{
                items:3
            },
            1200:{
                items:4
            }
        }
    });



    // Product Quantity
    $('.quantity button').on('click', function () {
        var button = $(this);
        var oldValue = button.parent().parent().find('input').val();
        if (button.hasClass('btn-plus')) {
            var newVal = parseFloat(oldValue) + 1;
        } else {
            if (oldValue > 0) {
                var newVal = parseFloat(oldValue) - 1;
            } else {
                newVal = 0;
            }
        }
        button.parent().parent().find('input').val(newVal);
    });


    
   // Back to top button
   $(window).scroll(function () {
    if ($(this).scrollTop() > 300) {
        $('.back-to-top').fadeIn('slow');
    } else {
        $('.back-to-top').fadeOut('slow');
    }
    });
    $('.back-to-top').click(function () {
        $('html, body').animate({scrollTop: 0}, 1500, 'easeInOutExpo');
        return false;
    });

    // Vehicle Selector Functionality
    $('#make').change(function() {
        var make = $(this).val();
        var models = {
            'Toyota': ['Corolla', 'Camry', 'Hilux'],
            'Nissan': ['Altima', 'Sentra', 'Patrol'],
            'BMW': ['3 Series', '5 Series', 'X3'],
            'Mercedes': ['C-Class', 'E-Class', 'GLC']
        };
        var modelSelect = $('#model');
        modelSelect.empty().append('<option selected>Select Model</option>');
        if (models[make]) {
            models[make].forEach(function(model) {
                modelSelect.append('<option value="' + model + '">' + model + '</option>');
            });
        }
    });

    // Quick Search Functionality
    let searchFilters = {};

    // Load filter data on page load
    $(document).ready(function() {
        loadFilterData();
        initializeYearRange();
    });

    // Load brands, categories, and models from API
    function loadFilterData() {
        $.ajax({
            url: '/api/get_filters.php',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    populateBrands(response.filters.brands);
                    populateCategories(response.filters.categories);
                    populateModels(response.filters.models);
                    searchFilters = response.filters;
                } else {
                    console.error('Failed to load filters:', response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                // Fallback to static data if API fails
                loadFallbackData();
            }
        });
    }

    // Populate brand dropdown
    function populateBrands(brands) {
        const brandSelect = $('#brand');
        brandSelect.empty().append('<option value="">All Brands</option>');

        brands.forEach(function(brand) {
            const option = `<option value="${brand.name}" data-count="${brand.count}">${brand.name} (${brand.count})</option>`;
            brandSelect.append(option);
        });
    }

    // Populate category dropdown
    function populateCategories(categories) {
        const categorySelect = $('#category');
        categorySelect.empty().append('<option value="">All Categories</option>');

        categories.forEach(function(category) {
            const option = `<option value="${category.name}" data-count="${category.count}">${category.name} (${category.count})</option>`;
            categorySelect.append(option);
        });
    }

    // Populate models based on selected brand
    function populateModels(modelsData) {
        $('#brand').change(function() {
            const selectedBrand = $(this).val();
            const modelSelect = $('#model');

            if (!selectedBrand) {
                modelSelect.prop('disabled', true).empty().append('<option value="">Select Brand First</option>');
                return;
            }

            modelSelect.prop('disabled', false).empty().append('<option value="">All Models</option>');

            if (modelsData[selectedBrand]) {
                modelsData[selectedBrand].forEach(function(model) {
                    const option = `<option value="${model.name}" data-count="${model.count}">${model.name} (${model.count})</option>`;
                    modelSelect.append(option);
                });
            }
        });
    }

    // Initialize year range dropdown
    function initializeYearRange() {
        const yearSelect = $('#year_from');
        const currentYear = new Date().getFullYear();
        const startYear = 1990;

        yearSelect.empty().append('<option value="">Any Year</option>');

        for (let year = currentYear + 1; year >= startYear; year--) {
            yearSelect.append(`<option value="${year}">${year}</option>`);
        }
    }

    // Fallback data if API fails
    function loadFallbackData() {
        const fallbackBrands = [
            {name: 'Toyota', count: 25},
            {name: 'Nissan', count: 18},
            {name: 'Hyundai', count: 15},
            {name: 'Kia', count: 12},
            {name: 'BYD', count: 8},
            {name: 'Mitsubishi', count: 10},
            {name: 'Suzuki', count: 9},
            {name: 'Volkswagen', count: 7},
            {name: 'Mercedes-Benz', count: 6}
        ];

        const fallbackCategories = [
            {name: 'Engine Parts', count: 45},
            {name: 'Brake Systems', count: 32},
            {name: 'Body Parts', count: 28},
            {name: 'Suspension', count: 22},
            {name: 'Electrical', count: 19},
            {name: 'Filters', count: 35},
            {name: 'Tools & Equipment', count: 15},
            {name: 'Tires & Wheels', count: 20},
            {name: 'Hybrid/EV Components', count: 8}
        ];

        populateBrands(fallbackBrands);
        populateCategories(fallbackCategories);
    }

    // Handle search form submission
    $('#quickSearchForm').submit(function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const searchParams = new URLSearchParams();

        // Build search parameters
        for (let [key, value] of formData.entries()) {
            if (value && value.trim() !== '') {
                searchParams.append(key, value.trim());
            }
        }

        // Show loading state
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Searching...');

        // Perform search
        performSearch(searchParams, function() {
            // Reset button
            submitBtn.prop('disabled', false).html(originalText);
        });
    });

    // Perform search function
    function performSearch(params, callback) {
        // Add loading class to form
        $('#quickSearchForm').addClass('loading');

        // Simulate API call (replace with actual API endpoint)
        setTimeout(function() {
            // For now, redirect to shop page with parameters
            const shopUrl = 'pages/shop.php?' + params.toString();
            window.location.href = shopUrl;

            $('#quickSearchForm').removeClass('loading');
            if (callback) callback();
        }, 1000);
    }

    // Search parts function (for backward compatibility)
    window.searchParts = function() {
        $('#quickSearchForm').trigger('submit');
    };

    // Advanced filters toggle animation
    $('[data-bs-toggle="collapse"]').click(function() {
        const icon = $(this).find('.collapse-icon');
        if ($(this).hasClass('collapsed')) {
            icon.css('transform', 'rotate(180deg)');
        } else {
            icon.css('transform', 'rotate(0deg)');
        }
    });

})(jQuery);

