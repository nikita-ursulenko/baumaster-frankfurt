// Baumaster Frankfurt - Main JavaScript

// ===== iOS SAFARI VIEWPORT FIX =====
// Fix for iOS Safari viewport height issues
// IMPORTANT: Run immediately, before DOMContentLoaded
(function () {
    function setViewportHeight() {
        // Set CSS custom property for viewport height
        const vh = window.innerHeight * 0.01;
        document.documentElement.style.setProperty('--vh', `${vh}px`);

        // Set hero section height for iOS Safari
        const hero = document.getElementById('hero');
        if (hero) {
            hero.style.minHeight = `${window.innerHeight}px`;
            hero.style.height = `${window.innerHeight}px`;
        }
    }

    // Run immediately
    setViewportHeight();

    // Run on load
    window.addEventListener('load', setViewportHeight);

    // Run on resize with debounce
    let resizeTimer;
    window.addEventListener('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            setViewportHeight();
        }, 100); // Faster response
    });

    // Run on orientation change with proper timing
    window.addEventListener('orientationchange', function () {
        // Multiple attempts to ensure it works after orientation change
        setViewportHeight();
        setTimeout(setViewportHeight, 100);
        setTimeout(setViewportHeight, 300);
        setTimeout(setViewportHeight, 500);
    });

    // iOS-specific: run on scroll end (for address bar hide/show)
    let scrollTimer;
    window.addEventListener('scroll', function () {
        clearTimeout(scrollTimer);
        scrollTimer = setTimeout(function () {
            setViewportHeight();
        }, 200);
    }, { passive: true });

    // iOS-specific: run when page becomes visible again
    document.addEventListener('visibilitychange', function () {
        if (!document.hidden) {
            setTimeout(setViewportHeight, 100);
        }
    });
})();

document.addEventListener('DOMContentLoaded', function () {
    // Initialize hero animations
    initHeroAnimations();

    // Mobile menu toggle
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');

    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function () {
            mobileMenu.classList.toggle('hidden');
        });
    }

    // Form validation
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(form => {
        form.addEventListener('submit', function (e) {
            if (!validateForm(this)) {
                e.preventDefault();
            }
        });
    });

    // Image lazy loading
    const images = document.querySelectorAll('img[data-src]');
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.classList.remove('lazy');
                    imageObserver.unobserve(img);
                }
            });
        });

        images.forEach(img => imageObserver.observe(img));
    }

    // Smooth scrolling for anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Contact form handling
    const contactForm = document.getElementById('contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', function (e) {
            e.preventDefault();
            handleContactForm(this);
        });
    }
});

// Form validation function
function validateForm(form) {
    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            showFieldError(field, 'Это поле обязательно для заполнения');
            isValid = false;
        } else if (field.type === 'email' && !isValidEmail(field.value)) {
            showFieldError(field, 'Введите корректный email адрес');
            isValid = false;
        } else {
            clearFieldError(field);
        }
    });

    return isValid;
}

// Email validation
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Show field error
function showFieldError(field, message) {
    clearFieldError(field);

    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error text-error text-sm mt-1';
    errorDiv.textContent = message;

    field.classList.add('border-error');
    field.parentNode.appendChild(errorDiv);
}

// Clear field error
function clearFieldError(field) {
    field.classList.remove('border-error');
    const existingError = field.parentNode.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }
}

// Handle contact form submission
function handleContactForm(form) {
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    const originalText = submitButton.textContent;

    // Show loading state
    submitButton.textContent = 'Отправка...';
    submitButton.disabled = true;

    // Simulate form submission (replace with actual AJAX call)
    fetch('/contact_form.php', {
        method: 'POST',
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Сообщение успешно отправлено!', 'success');
                form.reset();
            } else {
                showNotification('Ошибка при отправке сообщения: ' + data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Произошла ошибка при отправке сообщения', 'error');
            console.error('Error:', error);
        })
        .finally(() => {
            submitButton.textContent = originalText;
            submitButton.disabled = false;
        });
}

// Show notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type} fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50`;
    notification.textContent = message;

    // Add styles based on type
    switch (type) {
        case 'success':
            notification.classList.add('bg-success', 'text-white');
            break;
        case 'error':
            notification.classList.add('bg-error', 'text-white');
            break;
        case 'warning':
            notification.classList.add('bg-warning', 'text-white');
            break;
        default:
            notification.classList.add('bg-gray-800', 'text-white');
    }

    document.body.appendChild(notification);

    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.remove();
    }, 5000);
}

// Utility functions
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

function throttle(func, limit) {
    let inThrottle;
    return function () {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

// ===== HERO ANIMATIONS =====

// Initialize hero animations
function initHeroAnimations() {
    // Check if we're on the home page
    if (!document.getElementById('hero-title')) {
        return;
    }

    // Start animations after a short delay to ensure page is loaded
    setTimeout(() => {
        startHeroAnimations();
    }, 200);
}

// Start hero animations with proper timing
function startHeroAnimations() {
    const title = document.getElementById('hero-title');
    const subtitle = document.getElementById('hero-subtitle');
    const button1 = document.getElementById('hero-button-1');
    const button2 = document.getElementById('hero-button-2');

    if (!title || !subtitle || !button1 || !button2) {
        return;
    }

    // Animate title with word-by-word effect
    animateTitleWords(title);

    // Animate subtitle with word-by-word effect after title
    setTimeout(() => {
        animateSubtitleWords(subtitle);
    }, 1200);

    // Animate first button
    setTimeout(() => {
        button1.classList.add('animate-button-slide-up', 'hero-button-1');
    }, 2200);

    // Animate second button
    setTimeout(() => {
        button2.classList.add('animate-button-slide-up', 'hero-button-2');
    }, 2600);
}

// Animate title words one by one
function animateTitleWords(element) {
    const text = element.textContent;
    const words = text.split(' ');

    element.innerHTML = '';

    words.forEach((word, index) => {
        const span = document.createElement('span');
        span.textContent = word + ' ';
        span.classList.add('hero-title-words', 'animate-word-slide-up');
        span.style.animationDelay = `${0.4 + (index * 0.1)}s`;
        element.appendChild(span);
    });
}

// Animate subtitle words one by one
function animateSubtitleWords(element) {
    const text = element.textContent;
    const words = text.split(' ');

    element.innerHTML = '';

    words.forEach((word, index) => {
        const span = document.createElement('span');
        span.textContent = word + ' ';
        span.classList.add('hero-subtitle-words', 'animate-fade-in-up');
        span.style.animationDelay = `${1.3 + (index * 0.1)}s`;
        element.appendChild(span);
    });
}

// Add typing effect to title (optional)
function addTypingEffect(element, text, speed = 100) {
    element.textContent = '';
    let i = 0;

    function typeWriter() {
        if (i < text.length) {
            element.textContent += text.charAt(i);
            i++;
            setTimeout(typeWriter, speed);
        }
    }

    typeWriter();
}

// Add word-by-word animation to text
function animateWords(element) {
    const text = element.textContent;
    const words = text.split(' ');

    element.innerHTML = '';

    words.forEach((word, index) => {
        const span = document.createElement('span');
        span.textContent = word + ' ';
        span.classList.add('animate-words');
        span.style.animationDelay = `${index * 0.1}s`;
        element.appendChild(span);
    });
}

// Add ripple effect to buttons
function addRippleEffect(button) {
    button.addEventListener('click', function (e) {
        const ripple = document.createElement('span');
        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;

        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        ripple.classList.add('ripple');

        this.appendChild(ripple);

        setTimeout(() => {
            ripple.remove();
        }, 600);
    });
}

// Initialize ripple effects for all buttons
function initRippleEffects() {
    const buttons = document.querySelectorAll('.btn-ripple');
    buttons.forEach(button => {
        addRippleEffect(button);
    });
}

// Call ripple initialization
document.addEventListener('DOMContentLoaded', function () {
    initRippleEffects();
});

