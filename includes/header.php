<?php
require_once __DIR__ . '/functions.php';
initSession();
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
        LEVERANS 1-3 VARDAGAR - FRI FRAKT ÖVER 799 KR
    </div>
    <header>
        <nav class="main-nav">
            <div class="nav-content">
                <div class="logo-container">
                    <a href="/webshoppen/public/" class="logo">LJUS & HARMONI</a>
                </div>
                <button class="menu-toggle d-mobile-only" aria-label="Öppna meny">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
                <form class="search-form" action="/webshoppen/public/search.php" method="GET">
                    <div class="search-container">
                        <input type="search" name="q" placeholder="Sök produkter..." aria-label="Sök produkter">
                        <button type="submit" aria-label="Sök">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    <div class="live-search-results"></div>
                </form>
                <div class="nav-links">
                    <a href="/webshoppen/public/favorites.php" aria-label="Favoriter">
                        <i class="far fa-heart"></i>
                    </a>
                    <a href="/webshoppen/public/cart.php" aria-label="Varukorg">
                        <i class="fas fa-shopping-cart"></i>
                    </a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="/webshoppen/public/profile.php" aria-label="Min profil">
                            <i class="far fa-user"></i>
                        </a>
                    <?php else: ?>
                        <a href="/webshoppen/public/login.php" aria-label="Logga in">
                            <i class="far fa-user"></i>
                        </a>
                    <?php endif; ?>
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
    </header>

    <div class="mobile-menu">
        <div class="mobile-menu-header">
            <button class="close-menu" aria-label="Stäng meny">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <nav class="mobile-menu-content">
            <a href="/webshoppen/public/category.php?cat=ljus">DOFTLJUS</a>
            <a href="/webshoppen/public/category.php?cat=tillbehor">TILLBEHÖR</a>
            <a href="/webshoppen/public/category.php?cat=rea">REA</a>
        </nav>
    </div>

    <nav class="mobile-bottom-nav">
        <a href="/webshoppen/public/" aria-label="Hem">
            <i class="ri-home-5-line"></i>
            <span class="menu-text">Hem</span>
        </a>
        <a href="/webshoppen/public/search.php" aria-label="Sök">
            <i class="ri-search-line"></i>
            <span class="menu-text">Sök</span>
        </a>
        <a href="/webshoppen/public/favorites.php" aria-label="Favoriter">
            <i class="ri-heart-3-line"></i>
            <span class="menu-text">Favoriter</span>
        </a>
        <a href="<?php echo isLoggedIn() ? '/webshoppen/public/profile.php' : '/webshoppen/public/login.php'; ?>" 
           aria-label="<?php echo isLoggedIn() ? 'Profil' : 'Logga in'; ?>">
            <i class="ri-user-3-line"></i>
            <span class="menu-text"><?php echo isLoggedIn() ? 'Profil' : 'Logga in'; ?></span>
        </a>
    </nav>

    <main>
        <div class="main-content">
            <?php echo displayFlashMessage(); ?>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.querySelector('.menu-toggle');
            const mobileMenu = document.querySelector('.mobile-menu');
            const closeMenu = document.querySelector('.close-menu');
            const searchInput = document.querySelector('.search-form input[type="search"]');
            const searchResults = document.querySelector('.live-search-results');
            let searchTimeout;

            if (menuToggle && mobileMenu && closeMenu) {
                menuToggle.addEventListener('click', function() {
                    mobileMenu.classList.add('active');
                    document.body.style.overflow = 'hidden';
                });

                closeMenu.addEventListener('click', function() {
                    mobileMenu.classList.remove('active');
                    document.body.style.overflow = '';
                });

                document.addEventListener('click', function(e) {
                    if (mobileMenu.classList.contains('active') && 
                        !mobileMenu.contains(e.target) && 
                        !menuToggle.contains(e.target)) {
                        mobileMenu.classList.remove('active');
                        document.body.style.overflow = '';
                    }
                });
            }

            if (searchInput && searchResults) {
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
            }
        });
    </script>
</body>
</html> 