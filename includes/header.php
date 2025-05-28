<?php
// Se ainda não estiver incluído
if (!defined('HEADER_INCLUDED')) {
    define('HEADER_INCLUDED', true);
    // Inclui dependências necessárias
    require_once __DIR__ . '/../classes/Database.php';
    require_once __DIR__ . '/../classes/Vehicle.php';  // Mude aqui para require_once
    require_once __DIR__ . '/../classes/User.php';

    // Se não estiver logado e tentar acessar área restrita
    $restricted_pages = ['anunciar.php', 'perfil.php', 'meus-anuncios.php'];
    $current_page = basename($_SERVER['PHP_SELF']);

    if (in_array($current_page, $restricted_pages) && !isLoggedIn()) {
        header('Location: login.php');
        exit;
    }
}
?>
<header class="site-header">
    <div class="container">
        <div class="header-content">
            <div class="logo">
                <a href="index.php">
                    <!--  <h1><?php echo SITE_NAME; ?></h1> -->
                    <img src="logo.png" alt="Logo Karplace">
                </a>
            </div>

            <nav class="main-nav">
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="anuncios.php">Veículos</a></li>
                    <li><a href="anunciar.php">Vender</a></li>
                    <li><a href="#?off">Financiamento</a></li>
                    <li><a href="#?off">Sobre</a></li>
                </ul>
            </nav>

            <div class="user-actions">
                <?php if (isLoggedIn()): ?>
                    <div class="user-menu">
                        <button class="user-menu-button">
                            <i class="fas fa-user-circle"></i>
                            <span><?php echo $_SESSION['user_name']; ?></span>
                            <i class="fas fa-chevron-down"></i>
                        </button>
                        <div class="user-dropdown">
                            <ul>
                                <li><a href="perfil.php"><i class="fas fa-user"></i> Meu Perfil</a></li>
                                <li><a href="meus-anuncios.php"><i class="fas fa-list"></i> Meus Anúncios</a></li>
                                <li><a href="favoritos.php"><i class="fas fa-heart"></i> Favoritos</a></li>
                                <li><a href="mensagens.php"><i class="fas fa-envelope"></i> Mensagens</a></li>
                                <?php if (isAdmin()): ?>
                                    <li><a href="admin/index.php"><i class="fas fa-cog"></i> Administração</a></li>
                                <?php endif; ?>
                                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Sair</a></li>
                            </ul>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline">Entrar</a>
                    <a href="registro.php" class="btn btn-primary">Cadastrar</a>
                <?php endif; ?>
            </div>

            <button class="mobile-menu-toggle">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>
</header>