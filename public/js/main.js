document.addEventListener('DOMContentLoaded', function() {

    // ===== HIDE LOADING =====
    const loading = document.getElementById('loading-screen');
    if (loading) {
        setTimeout(() => loading.classList.add('hidden'), 800);
    }

    // ===== SCROLL REVEAL =====
    const revealElements = document.querySelectorAll('.scroll-reveal, .scroll-reveal-left, .scroll-reveal-right, .scroll-reveal-scale, .stagger-children');

    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                revealObserver.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    revealElements.forEach(el => revealObserver.observe(el));

    // ===== NAVBAR SCROLL =====
    const navbar = document.querySelector('.navbar');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    });

    // ===== SEARCH =====
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');
    let searchTimeout;

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const query = this.value.trim();
            if (query.length < 2) {
                searchResults.style.display = 'none';
                return;
            }
            searchTimeout = setTimeout(() => {
                fetch('/api/search?q=' + encodeURIComponent(query))
                    .then(r => r.json())
                    .then(data => {
                        if (data.results && data.results.length > 0) {
                            searchResults.innerHTML = data.results.map(item => `
                                <div class="result-item" onclick="window.location.href='${item.url}'">
                                    <span class="result-name">${item.nom}</span>
                                    <span class="result-price">${parseFloat(item.prix).toFixed(2)} €</span>
                                </div>
                            `).join('');
                            searchResults.style.display = 'block';
                        } else {
                            searchResults.innerHTML = '<div class="no-result">Aucun résultat</div>';
                            searchResults.style.display = 'block';
                        }
                    })
                    .catch(() => searchResults.style.display = 'none');
            }, 300);
        });

        document.addEventListener('click', e => {
            if (!e.target.closest('.search-container')) {
                searchResults.style.display = 'none';
            }
        });

        searchInput.addEventListener('keypress', e => {
            if (e.key === 'Enter') {
                const query = e.target.value.trim();
                if (query) window.location.href = '/produits?search=' + encodeURIComponent(query);
            }
        });

        document.getElementById('searchButton').addEventListener('click', () => {
            const query = searchInput.value.trim();
            if (query) window.location.href = '/produits?search=' + encodeURIComponent(query);
        });
    }

    // ===== COUNTERS =====
    function updateCounts() {
        fetch('/panier/count').then(r => r.json()).then(d => {
            document.getElementById('cartCount').textContent = d.count || 0;
        }).catch(() => {});
        fetch('/wishlist/count').then(r => r.json()).then(d => {
            document.getElementById('wishlistCount').textContent = d.count || 0;
        }).catch(() => {});
        fetch('/notifications/count').then(r => r.json()).then(d => {
            document.getElementById('notifCount').textContent = d.count || 0;
        }).catch(() => {});
    }
    updateCounts();
    setInterval(updateCounts, 30000);

    console.log('⌚ WatchShop Premium loaded');
});
