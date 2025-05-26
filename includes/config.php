<?php
/**
 * Configuração do banco de dados e constantes do sistema
 */

// Configurações do banco de dados
define('DB_HOST', 'srv1845.hstgr.io');
define('DB_USER', 'u150266992_admin_karzone');
define('DB_PASS', 'M@ychel123');
define('DB_NAME', 'u150266992_karzone');

// Configurações do site
define('SITE_NAME', 'Karplace');
define('SITE_URL', 'http://0.0.0.0:5000'); // Use 0.0.0.0 para acessibilidade externa
define('UPLOAD_PATH', $_SERVER['DOCUMENT_ROOT'] . '/uploads/vehicles/');
define('UPLOAD_URL', SITE_URL . '/uploads/vehicles/');

// Configurações de email
define('EMAIL_FROM', 'contato@automarket.com.br');
define('EMAIL_NAME', 'AutoMarket');

// Configurações de sessão
define('SESSION_NAME', 'karplace_session');
define('SESSION_LIFETIME', 86400); // 24 horas

// Configurações de segurança
define('HASH_COST', 10); // Custo do bcrypt

// Configurações de páginação
define('ITEMS_PER_PAGE', 12);

// Configurações de debug
define('DEBUG_MODE', true);

// Função para exibir erros em modo de debug
function debug($data) {
    if (DEBUG_MODE) {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
}

// Inicialização da sessão ANTES de qualquer inclusão que possa gerar saída
session_name(SESSION_NAME);
session_start();

// Incluir classes auxiliares APÓS a inicialização da sessão
require_once __DIR__ . '/validation.php';
require_once __DIR__ . '/logger.php';
require_once __DIR__ . '/cache.php';

// Inicializa o logger sem gerar saída
Logger::init();