<?php
// Rodapé comum para todas as páginas
?>
<footer class="site-footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-logo">
                <h2><?php echo SITE_NAME; ?></h2>
                <p>Conectando compradores e vendedores de veículos desde 2025</p>
            </div>
            
            <div class="footer-links">
                <div class="footer-column">
                    <h3>Navegação</h3>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <li><a href="anuncios.php">Veículos</a></li>
                        <li><a href="vender.php">Vender</a></li>
                        <li><a href="financiamento.php">Financiamento</a></li>
                        <li><a href="sobre.php">Sobre</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h3>Informações</h3>
                    <ul>
                        <li><a href="termos.php">Termos de Uso</a></li>
                        <li><a href="privacidade.php">Política de Privacidade</a></li>
                        <li><a href="cookies.php">Política de Cookies</a></li>
                        <li><a href="ajuda.php">Ajuda e FAQ</a></li>
                        <li><a href="contato.php">Contato</a></li>
                    </ul>
                </div>
                
                <div class="footer-column">
                    <h3>Contato</h3>
                    <ul class="contact-info">
                        <li><i class="fas fa-phone"></i> (11) 1234-5678</li>
                        <li><i class="fas fa-envelope"></i> contato@automarket.com.br</li>
                        <li><i class="fas fa-map-marker-alt"></i> Av. Paulista, 1000 - São Paulo/SP</li>
                    </ul>
                    
                    <div class="social-links">
                        <a href="#" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" target="_blank"><i class="fab fa-instagram"></i></a>
                        <a href="#" target="_blank"><i class="fab fa-twitter"></i></a>
                        <a href="#" target="_blank"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. Todos os direitos reservados.</p>
        </div>
    </div>
</footer>
