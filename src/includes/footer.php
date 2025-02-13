<?php
/**
 * Footer component for Bito Webshop
 * Appears on all pages except checkout
 */
?>
<footer class="site-footer">
    <div class="footer-content">
        <div class="footer-section">
            <h4>Über uns</h4>
            <p>Bito Webshop - Ihr verlässlicher Partner für hochwertige Produkte.</p>
        </div>
        
        <div class="footer-section">
            <h4>Rechtliches</h4>
            <ul>
                <li><a href="/impressum.php">Impressum</a></li>
                <li><a href="/agb.php">AGB</a></li>
                <li><a href="/datenschutz.php">Datenschutz</a></li>
            </ul>
        </div>
        
        <div class="footer-section">
            <h4>Kontakt</h4>
            <ul>
                <li>Email: info@bito-webshop.de</li>
                <li>Tel: +49 123 456789</li>
            </ul>
        </div>
    </div>
    
    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> Bito Webshop. Alle Rechte vorbehalten.</p>
    </div>
</footer>

<style>
.site-footer {
    background-color: #f8f9fa;
    padding: 2rem 0;
    margin-top: 3rem;
    border-top: 1px solid #dee2e6;
}

.footer-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
}

.footer-section h4 {
    color: #333;
    margin-bottom: 1rem;
}

.footer-section ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-section ul li {
    margin-bottom: 0.5rem;
}

.footer-section a {
    color: #666;
    text-decoration: none;
    transition: color 0.3s ease;
}

.footer-section a:hover {
    color: #333;
}

.footer-bottom {
    text-align: center;
    margin-top: 2rem;
    padding-top: 1rem;
    border-top: 1px solid #dee2e6;
}

.footer-bottom p {
    color: #666;
    margin: 0;
}

@media (max-width: 768px) {
    .footer-content {
        grid-template-columns: 1fr;
        text-align: center;
    }
    
    .footer-section {
        margin-bottom: 1.5rem;
    }
}
</style>