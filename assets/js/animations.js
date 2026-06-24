/* ============================================
   WATCHSHOP - ANIMATIONS INTELLIGENTES
   ============================================ */

class WatchShopAnimations {
    constructor() {
        this.init();
    }

    init() {
        // Écouter les événements de formulaire
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', (e) => {
                this.showFormLoading(e.target);
            });
        });

        // Écouter les clics sur les boutons d'action
        document.querySelectorAll('.btn-add-cart, .btn-wishlist, .btn-acheter').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.buttonFeedback(e.target);
            });
        });

        // Observer les animations au scroll
        this.initScrollAnimations();
    }

    // ===== 1. ANIMATION DE CHARGEMENT DE FORMULAIRE =====
    showFormLoading(form) {
        const btn = form.querySelector('button[type="submit"]');
        if (!btn) return;

        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> En cours...';
        btn.disabled = true;

        // Rétablir après 2 secondes (ou si erreur)
        setTimeout(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }, 2000);
    }

    // ===== 2. FEEDBACK SUR LES BOUTONS =====
    buttonFeedback(btn) {
        const originalText = btn.innerHTML;
        const icon = btn.querySelector('i')?.className || '';

        // Effet de pulsation
        btn.style.transform = 'scale(0.95)';
        setTimeout(() => {
            btn.style.transform = 'scale(1)';
        }, 200);

        // Changer temporairement le texte
        if (btn.classList.contains('btn-add-cart')) {
            btn.innerHTML = '<i class="fas fa-check"></i> Ajouté !';
            setTimeout(() => {
                btn.innerHTML = originalText;
            }, 1500);
        }

        if (btn.classList.contains('btn-wishlist')) {
            const heart = btn.querySelector('.fa-heart');
            if (heart) {
                heart.style.color = '#e94560';
                heart.style.transform = 'scale(1.3)';
                setTimeout(() => {
                    heart.style.transform = 'scale(1)';
                }, 300);
            }
        }
    }

    // ===== 3. ANIMATIONS AU SCROLL =====
    initScrollAnimations() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    
                    // Animations différentes selon la classe
                    if (el.classList.contains('fade-in-left')) {
                        el.style.animation = 'slideInLeft 0.6s ease forwards';
                    } else if (el.classList.contains('fade-in-right')) {
                        el.style.animation = 'slideInRight 0.6s ease forwards';
                    } else if (el.classList.contains('fade-in-up')) {
                        el.style.animation = 'fadeInUp 0.6s ease forwards';
                    } else if (el.classList.contains('scale-in')) {
                        el.style.animation = 'scaleIn 0.5s ease forwards';
                    } else {
                        el.style.animation = 'fadeInUp 0.5s ease forwards';
                    }
                    
                    observer.unobserve(el);
                }
            });
        }, { threshold: 0.1 });

        document.querySelectorAll('.animate-on-scroll').forEach(el => {
            el.style.opacity = '0';
            observer.observe(el);
        });
    }

    // ===== 4. ANIMATION DE SUPPRESSION =====
    deleteAnimation(element, callback) {
        element.style.transition = 'all 0.3s ease';
        element.style.transform = 'scale(0.8)';
        element.style.opacity = '0';
        setTimeout(() => {
            if (callback) callback();
        }, 300);
    }

    // ===== 5. ANIMATION D'AJOUT =====
    addAnimation(element) {
        element.style.transition = 'all 0.3s ease';
        element.style.transform = 'scale(0.8)';
        element.style.opacity = '0';
        setTimeout(() => {
            element.style.transform = 'scale(1)';
            element.style.opacity = '1';
        }, 100);
    }

    // ===== 6. TOAST AVEC ANIMATION =====
    showToast(message, type = 'success') {
        const colors = {
            success: '#28a745',
            error: '#dc3545',
            warning: '#ffc107',
            info: '#17a2b8'
        };

        const toast = document.createElement('div');
        toast.style.cssText = `
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: ${colors[type] || '#333'};
            color: white;
            padding: 16px 24px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            z-index: 9999;
            transform: translateX(120%);
            transition: transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            max-width: 400px;
            font-weight: 500;
        `;
        toast.innerHTML = `
            <div style="display: flex; align-items: center; gap: 12px;">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle'}"></i>
                <span>${message}</span>
            </div>
        `;
        document.body.appendChild(toast);

        // Entrée
        setTimeout(() => {
            toast.style.transform = 'translateX(0)';
        }, 50);

        // Sortie après 4 secondes
        setTimeout(() => {
            toast.style.transform = 'translateX(120%)';
            setTimeout(() => {
                toast.remove();
            }, 500);
        }, 4000);
    }
}

// Initialiser
document.addEventListener('DOMContentLoaded', function() {
    window.watchAnimations = new WatchShopAnimations();
    console.log('✨ Animations WatchShop chargées !');
});
