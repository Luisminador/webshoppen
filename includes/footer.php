<?php
function getCurrentYear() {
    return date('Y');
}
?>

<footer>
    <div class="footer-content">
        <div class="footer-section">
            <h3>Kundservice</h3>
            <ul>
                <li><a href="/webshoppen/public/contact.php">Kontakta oss</a></li>
                <li><a href="/webshoppen/public/faq.php">Vanliga frågor</a></li>
                <li><a href="/webshoppen/public/shipping.php">Leverans & Retur</a></li>
                <li><a href="/webshoppen/public/size-guide.php">Storleksguide</a></li>
                <li><a href="/webshoppen/public/terms.php">Köpvillkor</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h3>Om Webshoppen</h3>
            <ul>
                <li><a href="/webshoppen/public/about.php">Om oss</a></li>
                <li><a href="/webshoppen/public/sustainability.php">Hållbarhet</a></li>
                <li><a href="/webshoppen/public/career.php">Jobba hos oss</a></li>
                <li><a href="/webshoppen/public/press.php">Press</a></li>
                <li><a href="/webshoppen/public/privacy.php">Integritetspolicy</a></li>
            </ul>
        </div>

        <div class="footer-section">
            <h3>Följ oss</h3>
            <ul>
                <li><a href="#">Instagram</a></li>
                <li><a href="#">Facebook</a></li>
                <li><a href="#">TikTok</a></li>
                <li><a href="#">Pinterest</a></li>
            </ul>
            <div class="social-links">
                <a href="#" aria-label="Instagram"><i class="ri-instagram-line"></i></a>
                <a href="#" aria-label="Facebook"><i class="ri-facebook-line"></i></a>
                <a href="#" aria-label="TikTok"><i class="ri-tiktok-line"></i></a>
                <a href="#" aria-label="Pinterest"><i class="ri-pinterest-line"></i></a>
            </div>
        </div>

        <div class="footer-section">
            <h3>Nyhetsbrev</h3>
            <p style="color: #666; font-size: 0.875rem; margin-bottom: 1rem;">Prenumerera på vårt nyhetsbrev för att få inspiration och exklusiva erbjudanden.</p>
            <form class="newsletter-form" action="/webshoppen/public/newsletter-signup.php" method="POST">
                <input type="email" name="email" placeholder="Din e-postadress" required>
                <button type="submit" aria-label="Prenumerera på nyhetsbrev">
                    <i class="ri-arrow-right-line"></i>
                </button>
            </form>
        </div>
    </div>

    <div class="copyright">
        © <?php echo getCurrentYear(); ?> Webshoppen. Alla rättigheter förbehållna.
    </div>
</footer>
</body>
</html> 