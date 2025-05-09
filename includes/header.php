<?php
session_start();
require_once __DIR__ . '/functions.php';
?>
<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Webshoppen</title>
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">
    <link rel="stylesheet" href="/webshoppen/public/css/style.css">
</head>
<body>
    <div class="top-banner" role="banner">
        LEVERANS 1-3 VARDAGAR - FRI FRAKT ÖVER 799 KR
    </div>
    
    <header>
        <nav>
            <ul>
                <li class="logo-container">
                    <button class="menu-toggle d-mobile-only" aria-label="Öppna meny">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
                    <a href="/webshoppen/public/" class="logo">WEBSHOPPEN</a>
                </li>
                
                <li class="search-container">
                    <form action="/webshoppen/public/search.php" method="GET" class="search-form" role="search">
                        <input type="search" name="q" placeholder="Sök" aria-label="Sök produkter" required>
                        <button type="submit" aria-label="Sök">
                            <i class="ri-search-2-line"></i>
                        </button>
                    </form>
                </li>
                
                <li class="nav-links">
                    <a href="/webshoppen/public/profile.php" aria-label="Mitt konto">
                        <i class="ri-user-3-line"></i>
                    </a>
                    <a href="/webshoppen/public/favorites.php" aria-label="Mina favoriter">
                        <i class="ri-heart-3-line"></i>
                    </a>
                    <a href="/webshoppen/public/cart.php" aria-label="Kundvagn">
                        <i class="ri-shopping-bag-3-line"></i>
                    </a>
                </li>
            </ul>
        </nav>

        <div class="category-nav" role="navigation">
            <ul>
                <li><a href="/webshoppen/public/category.php?cat=nyheter">NYHETER</a></li>
                <li><a href="/webshoppen/public/category.php?cat=kategorier">KATEGORIER</a></li>
                <li><a href="/webshoppen/public/category.php?cat=marken">MÄRKEN</a></li>
                <li><a href="/webshoppen/public/category.php?cat=brollop">BRÖLLOP</a></li>
                <li><a href="/webshoppen/public/category.php?cat=student">STUDENT & BAL</a></li>
                <li><a href="/webshoppen/public/category.php?cat=bestsellers">BÄSTSÄLJARE</a></li>
                <li><a href="/webshoppen/public/category.php?cat=curve">CURVE</a></li>
                <li><a href="/webshoppen/public/category.php?cat=outlet">OUTLET</a></li>
                <li><a href="/webshoppen/public/category.php?cat=hallbarhet">HÅLLBARHET</a></li>
            </ul>
        </div>

        <!-- Mobil meny -->
        <div class="mobile-nav-container">
            <div class="mobile-nav-header">
                <h2>Meny</h2>
                <button class="mobile-nav-close" aria-label="Stäng meny">
                    <i class="ri-close-line"></i>
                </button>
            </div>
            <div class="mobile-nav-links">
                <a href="/webshoppen/public/category.php?cat=nyheter">NYHETER</a>
                <a href="/webshoppen/public/category.php?cat=kategorier">KATEGORIER</a>
                <a href="/webshoppen/public/category.php?cat=marken">MÄRKEN</a>
                <a href="/webshoppen/public/category.php?cat=brollop">BRÖLLOP</a>
                <a href="/webshoppen/public/category.php?cat=student">STUDENT & BAL</a>
                <a href="/webshoppen/public/category.php?cat=bestsellers">BÄSTSÄLJARE</a>
                <a href="/webshoppen/public/category.php?cat=curve">CURVE</a>
                <a href="/webshoppen/public/category.php?cat=outlet">OUTLET</a>
                <a href="/webshoppen/public/category.php?cat=hallbarhet">HÅLLBARHET</a>
            </div>
        </div>
    </header>

    <main>
        <div class="main-content">
            <?php echo displayFlashMessage(); ?>

            <script>
                // Hantera mobilmenyn
                document.addEventListener('DOMContentLoaded', function() {
                    const menuToggle = document.querySelector('.menu-toggle');
                    const mobileNav = document.querySelector('.mobile-nav-container');
                    const closeButton = document.querySelector('.mobile-nav-close');

                    menuToggle.addEventListener('click', function() {
                        mobileNav.classList.add('active');
                        document.body.style.overflow = 'hidden';
                    });

                    closeButton.addEventListener('click', function() {
                        mobileNav.classList.remove('active');
                        document.body.style.overflow = '';
                    });
                });
            </script> 
        </div>
    </main>
</body>
</html> 