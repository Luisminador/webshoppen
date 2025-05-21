<?php
session_start();
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ljus & Harmoni - Din doftljusbutik</title>
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">
    <link rel="stylesheet" href="/webshoppen/public/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="top-banner" role="banner">
        FRI FRAKT VID KÖP ÖVER 899 KR
    </div>
    
    <header>
        <nav class="main-nav">
            <div class="nav-content">
                <li class="logo-container">
                    <button class="menu-toggle d-mobile-only" aria-label="Öppna meny">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                    <a href="/webshoppen/public/" class="logo">CANDELAS</a>
                </li>
                
                <form action="/webshoppen/public/search.php" method="GET" class="search-form">
                    <div class="search-container">
                        <input type="search" 
                               name="q" 
                               placeholder="Sök efter produkter..." 
                               required
                               minlength="2"
                               autocomplete="off">
                        <button type="submit">
                            <i class="fas fa-search"></i>
                        </button>
                        <div class="live-search-results"></div>
                    </div>
                </form>

                <div class="nav-links">
                    <a href="<?= isLoggedIn() ? '/webshoppen/public/profile.php' : '/webshoppen/public/login.php' ?>" aria-label="<?= isLoggedIn() ? 'Mitt konto' : 'Logga in' ?>">
                        <i class="ri-user-3-line"></i>
                    </a>
                    <a href="/webshoppen/public/favorites.php" aria-label="Mina favoriter">
                        <i class="ri-heart-3-line"></i>
                    </a>
                    <a href="/webshoppen/public/cart.php" aria-label="Kundvagn">
                        <i class="ri-shopping-bag-3-line"></i>
                    </a>
                </div>
            </div>
        </nav>

        <div class="category-nav" role="navigation">
            <ul>
                <li><a href="/webshoppen/public/category.php?cat=ljus">DOFTLJUS</a></li>
                <li><a href="/webshoppen/public/category.php?cat=tillbehor">TILLBEHÖR</a></li>
                <li><a href="/webshoppen/public/category.php?cat=rea">REA</a></li>
            </ul>
        </div>

        <div class="mobile-nav-container">
            <div class="mobile-nav-header">
                <h2>Meny</h2>
                <button class="mobile-nav-close" aria-label="Stäng meny">
                    <i class="ri-close-line"></i>
                </button>
            </div>
            <div class="mobile-nav-links">
                <a href="/webshoppen/public/category.php?cat=ljus">DOFTLJUS</a>
                <a href="/webshoppen/public/category.php?cat=tillbehor">TILLBEHÖR</a>
                <a href="/webshoppen/public/category.php?cat=rea">REA</a>
            </div>
        </div>
    </header>

    <main>
        <div class="main-content">
            <?php echo displayFlashMessage(); ?>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const menuToggle = document.querySelector('.menu-toggle');
                    const mobileNav = document.querySelector('.mobile-nav-container');
                    const closeButton = document.querySelector('.mobile-nav-close');
                    const searchInput = document.querySelector('.search-form input[type="search"]');
                    const searchResults = document.querySelector('.live-search-results');
                    let searchTimeout;

                    menuToggle.addEventListener('click', function() {
                        mobileNav.classList.add('active');
                        document.body.style.overflow = 'hidden';
                    });

                    closeButton.addEventListener('click', function() {
                        mobileNav.classList.remove('active');
                        document.body.style.overflow = '';
                    });

                    searchInput.addEventListener('input', function() {
                        const query = this.value.trim();
                        
                        clearTimeout(searchTimeout);
                        
                        if (query.length < 2) {
                            searchResults.style.display = 'none';
                            return;
                        }
                        
                        searchTimeout = setTimeout(() => {
                            fetch(`/webshoppen/public/ajax/live-search.php?q=${encodeURIComponent(query)}`)
                                .then(response => response.json())
                                .then(data => {
                                    if (data.length > 0) {
                                        const html = data.map(product => `
                                            <a href="${product.url}" class="live-search-item">
                                                <img src="${product.image_url || 'placeholder.jpg'}" 
                                                     alt="${product.title}"
                                                     class="live-search-image">
                                                <div class="live-search-info">
                                                    <div class="live-search-title">${product.title}</div>
                                                    <div class="live-search-category">${product.category}</div>
                                                    <div class="live-search-price-container">
                                                        ${product.deal_price 
                                                            ? `<span class="live-search-price sale">${product.deal_price} kr</span>
                                                               <span class="live-search-price original">${product.price} kr</span>`
                                                            : `<span class="live-search-price">${product.price} kr</span>`
                                                        }
                                                    </div>
                                                </div>
                                            </a>
                                        `).join('');
                                        
                                        searchResults.innerHTML = html;
                                        searchResults.style.display = 'block';
                                    } else {
                                        searchResults.innerHTML = '<div class="no-results-message">Inga produkter hittades</div>';
                                        searchResults.style.display = 'block';
                                    }
                                });
                        }, 300);
                    });

                    document.addEventListener('click', function(e) {
                        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                            searchResults.style.display = 'none';
                        }
                    });
                });
            </script> 
        </div>
    </main>
</body>
</html> 