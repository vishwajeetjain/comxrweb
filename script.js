
// Mobile Menu Toggle
const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
const mobileMenu = document.querySelector('.mobile-menu');

mobileMenuBtn.addEventListener('click', () => {
    mobileMenuBtn.classList.toggle('active');
    mobileMenu.classList.toggle('active');
});

// Close mobile menu when clicking on links
const mobileNavLinks = document.querySelectorAll('.mobile-nav-link');
mobileNavLinks.forEach(link => {
    link.addEventListener('click', () => {
        mobileMenuBtn.classList.remove('active');
        mobileMenu.classList.remove('active');
    });
});

// Header Background on Scroll
const header = document.querySelector('.header');
window.addEventListener('scroll', () => {
    if (window.scrollY > 50) {
        header.classList.add('scrolled');
    } else {
        header.classList.remove('scrolled');
    }
});

// Smooth Scrolling for Navigation Links
const navLinks = document.querySelectorAll('a[href^="#"]');
navLinks.forEach(link => {
    link.addEventListener('click', (e) => {
        e.preventDefault();
        const targetId = link.getAttribute('href');
        const targetSection = document.querySelector(targetId);
        
        if (targetSection) {
            const headerHeight = header.offsetHeight;
            const targetPosition = targetSection.offsetTop - headerHeight;
            
            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });
        }
    });
});

// Button Ripple Effect
const buttons = document.querySelectorAll('.cta-button');
buttons.forEach(button => {
    button.addEventListener('click', function(e) {
        const ripple = document.createElement('span');
        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        
        ripple.style.cssText = `
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: scale(0);
            animation: ripple 0.6s ease-out;
            left: ${x}px;
            top: ${y}px;
            width: ${size}px;
            height: ${size}px;
        `;
        
        this.appendChild(ripple);
        
        setTimeout(() => {
            ripple.remove();
        }, 600);
    });
});

// Add ripple animation keyframes
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple {
        to {
            transform: scale(2);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Scroll Animation for Elements
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('fade-in');
        }
    });
}, observerOptions);

// Observe all sections and cards
const elementsToAnimate = document.querySelectorAll('section, .glass-card, .brand-card, .why-card, .client-card');
elementsToAnimate.forEach(el => {
    observer.observe(el);
});

// Card Hover Effects
const cards = document.querySelectorAll('.glass-card, .brand-card, .why-card, .client-card');
cards.forEach(card => {
    card.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-8px) scale(1.02)';
    });
    
    card.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0) scale(1)';
    });
});

// Parallax Effect for Hero Section
window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    const hero = document.querySelector('.hero');
    const heroContent = document.querySelector('.hero-content');
    
    if (hero && heroContent) {
        heroContent.style.transform = `translateY(${scrolled * 0.3}px)`;
    }
});

// Loading Animation
window.addEventListener('load', () => {
    document.body.style.opacity = '0';
    document.body.style.transition = 'opacity 0.3s ease';
    
    setTimeout(() => {
        document.body.style.opacity = '1';
    }, 100);
});

// Dropdown Menu Functionality (add this to your existing script.js)
document.addEventListener('DOMContentLoaded', function() {
    // Desktop dropdown - close on click outside
    const dropdowns = document.querySelectorAll('.nav-dropdown');
    
    document.addEventListener('click', function(event) {
        dropdowns.forEach(dropdown => {
            if (!dropdown.contains(event.target)) {
                dropdown.classList.remove('active');
            }
        });
    });

    // Mobile dropdown toggle
    const mobileDropdownTriggers = document.querySelectorAll('.mobile-dropdown-trigger');
    
    mobileDropdownTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            const dropdown = this.closest('.mobile-dropdown');
            const isActive = dropdown.classList.contains('active');
            
            // Close all other dropdowns
            document.querySelectorAll('.mobile-dropdown').forEach(d => {
                d.classList.remove('active');
            });
            
            // Toggle current dropdown
            if (!isActive) {
                dropdown.classList.add('active');
            }
        });
    });

    // Close mobile dropdown when clicking on items
    const mobileDropdownItems = document.querySelectorAll('.mobile-dropdown-item');
    mobileDropdownItems.forEach(item => {
        item.addEventListener('click', function() {
            // Close mobile menu
            const mobileMenu = document.querySelector('.mobile-menu');
            const mobileMenuBtn = document.querySelector('.mobile-menu-btn');
            
            if (mobileMenu && mobileMenuBtn) {
                mobileMenu.classList.remove('active');
                mobileMenuBtn.classList.remove('active');
            }
            
            // Close dropdown
            document.querySelectorAll('.mobile-dropdown').forEach(d => {
                d.classList.remove('active');
            });
        });
    });

    // Prevent dropdown from closing when clicking inside it
    const dropdownMenus = document.querySelectorAll('.dropdown-menu');
    dropdownMenus.forEach(menu => {
        menu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });
});

// Contact Form Handler
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contactForm');
    
    if (contactForm) {
        contactForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = contactForm.querySelector('.submit-btn');
            const originalText = submitBtn.innerHTML;
            
            // Show loading state
            submitBtn.innerHTML = '<span>Sending...</span>';
            submitBtn.disabled = true;
            
            try {
                const formData = new FormData(contactForm);
                
                const response = await fetch('process_contact.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Show success message
                    showMessage(result.message, 'success');
                    contactForm.reset();
                } else {
                    showMessage(result.message, 'error');
                }
            } catch (error) {
                showMessage('Network error. Please check your connection and try again.', 'error');
                console.error('Form submission error:', error);
            } finally {
                // Reset button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });
    }
    
    // Show message function
    function showMessage(message, type) {
        // Remove existing messages
        const existingMessage = document.querySelector('.form-message');
        if (existingMessage) {
            existingMessage.remove();
        }
        
        // Create message element
        const messageEl = document.createElement('div');
        messageEl.className = `form-message form-message-${type}`;
        messageEl.textContent = message;
        
        // Insert message
        const form = document.getElementById('contactForm');
        form.insertBefore(messageEl, form.firstChild);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (messageEl.parentNode) {
                messageEl.remove();
            }
        }, 5000);
        
        // Scroll to message
        messageEl.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});


