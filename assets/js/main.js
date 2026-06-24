/* ============================================
   WATCHSHOP - MAIN.JS
   ============================================ */

document.addEventListener('DOMContentLoaded', function() {
    
    // ===== HIDE LOADING SCREEN (disparition naturelle) =====
    // Le loading disparaît quand la page est complètement chargée
    window.addEventListener('load', function() {
        const loading = document.getElementById('loading-screen');
        if (loading) {
            loading.classList.add('hidden');
        }
    });

    // ===== SEARCH AUTOCOMPLETE =====
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
                    .then(response => response.json())
                    .then(data => {
                        if (data.results && data.results.length > 0) {
                            searchResults.innerHTML = data.results.map(item => `
                                <div class="result-item" onclick="window.location.href='${item.url}'">
                                    <span class="result-name">${item.nom}</span>
                                    <span class="result-price">${parseFloat(item.prix).toFixed(2)} €</span>
                                    <span class="result-stock ${item.stock > 0 ? 'bg-success' : 'bg-danger'}" style="color:white;">${item.stock > 0 ? '✅ En stock' : '❌ Rupture'}</span>
                                </div>
                            `).join('');
                            searchResults.style.display = 'block';
                        } else {
                            searchResults.innerHTML = '<div class="no-result">Aucun résultat trouvé</div>';
                            searchResults.style.display = 'block';
                        }
                    })
                    .catch(() => { searchResults.style.display = 'none'; });
            }, 300);
        });

        document.addEventListener('click', function(e) {
            if (!e.target.closest('.search-container')) {
                searchResults.style.display = 'none';
            }
        });

        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const query = this.value.trim();
                if (query.length > 0) {
                    window.location.href = '/produits?search=' + encodeURIComponent(query);
                }
            }
        });

        document.getElementById('searchButton').addEventListener('click', function() {
            const query = searchInput.value.trim();
            if (query.length > 0) {
                window.location.href = '/produits?search=' + encodeURIComponent(query);
            }
        });
    }

    // ===== UPDATE COUNTS =====
    function updateCounts() {
        fetch('/panier/count')
            .then(r => r.json())
            .then(data => {
                const el = document.getElementById('cartCount');
                if (el) el.textContent = data.count || 0;
            })
            .catch(() => {});

        fetch('/wishlist/count')
            .then(r => r.json())
            .then(data => {
                const el = document.getElementById('wishlistCount');
                if (el) el.textContent = data.count || 0;
            })
            .catch(() => {});

        fetch('/notifications/count')
            .then(r => r.json())
            .then(data => {
                const el = document.getElementById('notifCount');
                if (el) el.textContent = data.count || 0;
            })
            .catch(() => {});
    }
    updateCounts();
    setInterval(updateCounts, 30000);

    console.log('🕐 WatchShop loaded successfully!');
});
