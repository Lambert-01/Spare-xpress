/**
 * SPARE XPRESS - Scroll Animations & Interactive Elements
 * Handles scroll-triggered animations and micro-interactions
 */

// Intersection Observer for Scroll Animations
class ScrollAnimations {
    constructor() {
        this.animatedElements = document.querySelectorAll('.fade-in, .slide-in-left, .slide-in-right, .scale-in');
        this.init();
    }

    init() {
        const options = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, options);

        this.animatedElements.forEach(element => {
            observer.observe(element);
        });
    }
}

// Smooth Scroll for Anchor Links
class SmoothScroll {
    constructor() {
        this.init();
    }

    init() {
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', (e) => {
                const href = anchor.getAttribute('href');
                if (href === '#' || href === '#!') return;

                const target = document.querySelector(href);
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }
}

// Navbar Scroll Effect
class NavbarScroll {
    constructor() {
        this.navbar = document.querySelector('.navbar, header');
        this.init();
    }

    init() {
        if (!this.navbar) return;

        let lastScroll = 0;

        window.addEventListener('scroll', () => {
            const currentScroll = window.pageYOffset;

            if (currentScroll > 100) {
                this.navbar.classList.add('navbar-scrolled');
            } else {
                this.navbar.classList.remove('navbar-scrolled');
            }

            lastScroll = currentScroll;
        });
    }
}

// Add to Cart Animation
function animateAddToCart(productElement, cartButton) {
    const productImg = productElement.querySelector('img');
    if (!productImg || !cartButton) return;

    // Create flying image
    const flyingImg = productImg.cloneNode(true);
    const imgRect = productImg.getBoundingClientRect();
    const cartRect = cartButton.getBoundingClientRect();

    flyingImg.style.cssText = `
    position: fixed;
    top: ${imgRect.top}px;
    left: ${imgRect.left}px;
    width: ${imgRect.width}px;
    height: ${imgRect.height}px;
    z-index: 9999;
    transition: all 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    pointer-events: none;
  `;

    document.body.appendChild(flyingImg);

    // Animate to cart
    requestAnimationFrame(() => {
        flyingImg.style.top = `${cartRect.top + cartRect.height / 2}px`;
        flyingImg.style.left = `${cartRect.left + cartRect.width / 2}px`;
        flyingImg.style.width = '0px';
        flyingImg.style.height = '0px';
        flyingImg.style.opacity = '0';
    });

    // Remove element after animation
    setTimeout(() => {
        flyingImg.remove();

        // Shake cart button
        cartButton.style.animation = 'button-pop 0.3s ease';
        setTimeout(() => {
            cartButton.style.animation = '';
        }, 300);
    }, 800);
}

// Parallax Effect for Hero Section
class ParallaxEffect {
    constructor() {
        this.parallaxElements = document.querySelectorAll('[data-parallax]');
        this.init();
    }

    init() {
        if (this.parallaxElements.length === 0) return;

        window.addEventListener('scroll', () => {
            const scrolled = window.pageYOffset;

            this.parallaxElements.forEach(element => {
                const speed = element.dataset.parallax || 0.5;
                const yPos = -(scrolled * speed);
                element.style.transform = `translateY(${yPos}px)`;
            });
        });
    }
}

// Image Lazy Loading with Blur Effect
class LazyLoadImages {
    constructor() {
        this.images = document.querySelectorAll('img[data-src]');
        this.init();
    }

    init() {
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    const src = img.dataset.src;

                    img.src = src;
                    img.classList.add('loaded');
                    imageObserver.unobserve(img);

                    img.addEventListener('load', () => {
                        img.style.filter = 'blur(0)';
                    });
                }
            });
        });

        this.images.forEach(img => {
            img.style.filter = 'blur(10px)';
            img.style.transition = 'filter 0.3s ease';
            imageObserver.observe(img);
        });
    }
}

// Skeleton Loading Helper
function createSkeleton(type = 'card') {
    const templates = {
        card: `
      <div class="skeleton-card p-4">
        <div class="skeleton skeleton-image"></div>
        <div class="skeleton skeleton-title"></div>
        <div class="skeleton skeleton-text"></div>
        <div class="skeleton skeleton-text" style="width: 80%;"></div>
      </div>
    `,
        text: `
      <div class="skeleton skeleton-text mb-2"></div>
      <div class="skeleton skeleton-text mb-2"></div>
      <div class="skeleton skeleton-text" style="width: 60%;"></div>
    `,
        product: `
      <div class="card-product">
        <div class="skeleton skeleton-image" style="height: 240px;"></div>
        <div class="p-4">
          <div class="skeleton skeleton-text mb-3"></div>
          <div class="skeleton skeleton-text mb-3" style="width: 70%;"></div>
          <div class="skeleton skeleton-title mb-3" style="width: 40%; height: 2.5rem;"></div>
          <div class="d-flex gap-2">
            <div class="skeleton flex-fill" style="height: 44px;"></div>
            <div class="skeleton flex-fill" style="height: 44px;"></div>
          </div>
        </div>
      </div>
    `
    };

    return templates[type] || templates.card;
}

// Show skeleton loading
function showSkeletonLoading(container, count = 3, type = 'card') {
    if (!container) return;

    const skeletons = Array(count).fill(createSkeleton(type)).join('');
    container.innerHTML = `<div class="row g-4">${skeletons}</div>`;
}

// Debounce Helper
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Initialize all animations on page load
document.addEventListener('DOMContentLoaded', () => {
    // Initialize scroll animations
    new ScrollAnimations();
    new SmoothScroll();
    new NavbarScroll();
    new ParallaxEffect();
    new LazyLoadImages();

    // Add hover effects to product cards
    const productCards = document.querySelectorAll('.card-product, .product-card');
    productCards.forEach(card => {
        card.addEventListener('mouseenter', function () {
            this.style.transform = 'translateY(-12px) scale(1.02)';
        });

        card.addEventListener('mouseleave', function () {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Add ripple effect to buttons
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.classList.add('ripple');
    });

    // Initialize animations for elements already in viewport
    setTimeout(() => {
        const elementsInView = document.querySelectorAll('.fade-in, .slide-in-left, .slide-in-right, .scale-in');
        elementsInView.forEach(element => {
            const rect = element.getBoundingClientRect();
            if (rect.top < window.innerHeight) {
                element.classList.add('visible');
            }
        });
    }, 100);
});

// Export functions for use in other files
window.animateAddToCart = animateAddToCart;
window.showSkeletonLoading = showSkeletonLoading;
window.debounce = debounce;
window.createSkeleton = createSkeleton;
