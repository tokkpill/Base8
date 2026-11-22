/**
 * Base8 — Marketplace Frontend
 * Quick preview, purchase confirmations, and card animations
 */
document.addEventListener('DOMContentLoaded', () => {
    // Animate cards on scroll into view
    const cards = document.querySelectorAll('.asset-card, .advisor-card, .stat-card');
    
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        cards.forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            observer.observe(card);
        });
    }

    // Purchase confirmation with SweetAlert2
    const purchaseForms = document.querySelectorAll('form[action="/marketplace/comprar"]');
    purchaseForms.forEach(form => {
        form.addEventListener('submit', (e) => {
            e.preventDefault();
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '¿Confirmar compra?',
                    text: 'Se procesará la adquisición de este recurso',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#10B981',
                    cancelButtonColor: '#64748B',
                    confirmButtonText: 'Sí, comprar',
                    cancelButtonText: 'Cancelar',
                    background: '#1E1E2E',
                    color: '#F1F5F9',
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            } else {
                if (confirm('¿Confirmar compra de este recurso?')) {
                    form.submit();
                }
            }
        });
    });

    // Prestige badge hover animation
    const badges = document.querySelectorAll('.prestige-badge');
    badges.forEach(badge => {
        badge.addEventListener('mouseenter', () => {
            badge.style.transform = 'scale(1.1)';
            badge.style.transition = 'transform 200ms ease';
        });
        badge.addEventListener('mouseleave', () => {
            badge.style.transform = 'scale(1)';
        });
    });

    // Progress bar animation
    const progressBars = document.querySelectorAll('.rep-profile__progress-bar-fill');
    progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0%';
        setTimeout(() => {
            bar.style.width = width;
        }, 500);
    });
});
