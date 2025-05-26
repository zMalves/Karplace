<?php
/**
 * Funções utilitárias para o sistema
 */

/**
 * Redireciona para uma URL
 * @param string $url URL para redirecionamento
 */
function redirect($url) {
    header('Location: ' . $url);
    exit;
}

/**
 * Verifica se o usuário está logado
 * @return boolean
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Verifica se o usuário é um vendedor
 * @return boolean
 */
function isSeller() {
    return isset($_SESSION['user_type']) && ($_SESSION['user_type'] == 'seller' || $_SESSION['user_type'] == 'admin');
}

/**
 * Verifica se o usuário é um administrador
 * @return boolean
 */
function isAdmin() {
    return isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'admin';
}

/**
 * Requer login para acessar a página
 * @param string $redirect URL para redirecionamento caso não esteja logado
 */
function requireLogin($redirect = '/login.php') {
    if (!isLoggedIn()) {
        $_SESSION['error'] = 'Você precisa estar logado para acessar esta página.';
        redirect($redirect);
    }
}

/**
 * Requer permissão de vendedor para acessar a página
 * @param string $redirect URL para redirecionamento caso não tenha permissão
 */
function requireSeller($redirect = '/index.php') {
    requireLogin('/login.php');
    if (!isSeller()) {
        $_SESSION['error'] = 'Você precisa ser um vendedor para acessar esta página.';
        redirect($redirect);
    }
}

/**
 * Requer permissão de administrador para acessar a página
 * @param string $redirect URL para redirecionamento caso não tenha permissão
 */
function requireAdmin($redirect = '/index.php') {
    requireLogin('/login.php');
    if (!isAdmin()) {
        $_SESSION['error'] = 'Você precisa ser um administrador para acessar esta página.';
        redirect($redirect);
    }
}

/**
 * Formata um valor monetário
 * @param float $value Valor a ser formatado
 * @return string Valor formatado
 */
function formatMoney($value) {
    return 'R$ ' . number_format($value, 2, ',', '.');
}

/**
 * Formata uma data
 * @param string $date Data a ser formatada
 * @param string $format Formato desejado
 * @return string Data formatada
 */
function formatDate($date, $format = 'd/m/Y') {
    $datetime = new DateTime($date);
    return $datetime->format($format);
}

/**
 * Formata uma data com hora
 * @param string $date Data a ser formatada
 * @return string Data formatada
 */
function formatDateTime($date) {
    return formatDate($date, 'd/m/Y H:i');
}

/**
 * Calcula o tempo decorrido desde uma data
 * @param string $date Data de referência
 * @return string Tempo decorrido
 */
function timeAgo($date) {
    $datetime = new DateTime($date);
    $now = new DateTime();
    $interval = $now->diff($datetime);
    
    if ($interval->y > 0) {
        return $interval->y . ' ano' . ($interval->y > 1 ? 's' : '') . ' atrás';
    } elseif ($interval->m > 0) {
        return $interval->m . ' mês' . ($interval->m > 1 ? 'es' : '') . ' atrás';
    } elseif ($interval->d > 0) {
        return $interval->d . ' dia' . ($interval->d > 1 ? 's' : '') . ' atrás';
    } elseif ($interval->h > 0) {
        return $interval->h . ' hora' . ($interval->h > 1 ? 's' : '') . ' atrás';
    } elseif ($interval->i > 0) {
        return $interval->i . ' minuto' . ($interval->i > 1 ? 's' : '') . ' atrás';
    } else {
        return 'agora mesmo';
    }
}

/**
 * Limita o tamanho de um texto
 * @param string $text Texto a ser limitado
 * @param int $length Tamanho máximo
 * @param string $append Texto a ser adicionado ao final
 * @return string Texto limitado
 */
function limitText($text, $length = 100, $append = '...') {
    if (strlen($text) <= $length) {
        return $text;
    }
    
    $text = substr($text, 0, $length);
    $text = substr($text, 0, strrpos($text, ' '));
    
    return $text . $append;
}

/**
 * Gera um slug a partir de um texto
 * @param string $text Texto a ser convertido
 * @return string Slug gerado
 */
function generateSlug($text) {
    // Remove acentos
    $text = iconv('UTF-8', 'ASCII//TRANSLIT', $text);
    
    // Converte para minúsculas
    $text = strtolower($text);
    
    // Remove caracteres especiais
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    
    // Substitui espaços por hífens
    $text = preg_replace('/[\s-]+/', '-', $text);
    
    // Remove hífens do início e do fim
    $text = trim($text, '-');
    
    return $text;
}

/**
 * Sanitiza uma string para uso em HTML
 * @param string $string String a ser sanitizada
 * @return string String sanitizada
 */
function sanitizeHTML($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Exibe uma mensagem flash
 * @param string $type Tipo da mensagem (success, error, warning, info)
 * @return string HTML da mensagem
 */
function flashMessage($type) {
    if (isset($_SESSION[$type])) {
        $message = $_SESSION[$type];
        unset($_SESSION[$type]);
        return '<div class="alert alert-' . $type . '">' . $message . '</div>';
    }
    return '';
}

/**
 * Gera um token CSRF
 * @return string Token gerado
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifica um token CSRF
 * @param string $token Token a ser verificado
 * @return boolean
 */
function verifyCSRFToken($token) {
    if (!isset($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        return false;
    }
    return true;
}

/**
 * Gera um campo de token CSRF para formulários
 * @return string HTML do campo
 */
function csrfField() {
    $token = generateCSRFToken();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Verifica se uma requisição é POST
 * @return boolean
 */
function isPostRequest() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Obtém o valor de um parâmetro GET
 * @param string $key Nome do parâmetro
 * @param mixed $default Valor padrão
 * @return mixed Valor do parâmetro
 */
function getParam($key, $default = null) {
    return isset($_GET[$key]) ? $_GET[$key] : $default;
}

/**
 * Obtém o valor de um campo POST
 * @param string $key Nome do campo
 * @param mixed $default Valor padrão
 * @return mixed Valor do campo
 */
function postParam($key, $default = null) {
    return isset($_POST[$key]) ? $_POST[$key] : $default;
}

/**
 * Gera links de paginação
 * @param int $page Página atual
 * @param int $total_pages Total de páginas
 * @param string $url_pattern Padrão de URL para links
 * @return string HTML da paginação
 */
function pagination($page, $total_pages, $url_pattern = '?page=%d') {
    if ($total_pages <= 1) {
        return '';
    }
    
    $html = '<div class="pagination">';
    
    // Link para página anterior
    if ($page > 1) {
        $html .= '<a href="' . sprintf($url_pattern, $page - 1) . '" class="page-link">&laquo; Anterior</a>';
    } else {
        $html .= '<span class="page-link disabled">&laquo; Anterior</span>';
    }
    
    // Links para páginas
    $start = max(1, $page - 2);
    $end = min($total_pages, $page + 2);
    
    if ($start > 1) {
        $html .= '<a href="' . sprintf($url_pattern, 1) . '" class="page-link">1</a>';
        if ($start > 2) {
            $html .= '<span class="page-link dots">...</span>';
        }
    }
    
    for ($i = $start; $i <= $end; $i++) {
        if ($i == $page) {
            $html .= '<span class="page-link active">' . $i . '</span>';
        } else {
            $html .= '<a href="' . sprintf($url_pattern, $i) . '" class="page-link">' . $i . '</a>';
        }
    }
    
    if ($end < $total_pages) {
        if ($end < $total_pages - 1) {
            $html .= '<span class="page-link dots">...</span>';
        }
        $html .= '<a href="' . sprintf($url_pattern, $total_pages) . '" class="page-link">' . $total_pages . '</a>';
    }
    
    // Link para próxima página
    if ($page < $total_pages) {
        $html .= '<a href="' . sprintf($url_pattern, $page + 1) . '" class="page-link">Próxima &raquo;</a>';
    } else {
        $html .= '<span class="page-link disabled">Próxima &raquo;</span>';
    }
    
    $html .= '</div>';
    
    return $html;
}

/**
 * Gera um nome de arquivo único para upload
 * @param string $original_name Nome original do arquivo
 * @return string Nome único gerado
 */
function generateUniqueFilename($original_name) {
    $extension = pathinfo($original_name, PATHINFO_EXTENSION);
    return uniqid() . '_' . time() . '.' . $extension;
}

/**
 * Verifica se um arquivo é uma imagem válida
 * @param array $file Dados do arquivo ($_FILES)
 * @return boolean
 */
function isValidImage($file) {
    // Verifica se houve erro no upload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    // Verifica o tipo MIME
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowed_types)) {
        return false;
    }
    
    // Verifica o tamanho (máximo 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        return false;
    }
    
    // Verifica se é uma imagem válida
    $image_info = getimagesize($file['tmp_name']);
    if (!$image_info) {
        return false;
    }
    
    return true;
}

/**
 * Faz upload de uma imagem
 * @param array $file Dados do arquivo ($_FILES)
 * @param string $destination Diretório de destino
 * @return mixed Nome do arquivo ou false
 */
function uploadImage($file, $destination) {
    // Verifica se é uma imagem válida
    if (!isValidImage($file)) {
        return false;
    }
    
    // Cria o diretório se não existir
    if (!file_exists($destination)) {
        mkdir($destination, 0755, true);
    }
    
    // Gera um nome único
    $filename = generateUniqueFilename($file['name']);
    $filepath = $destination . '/' . $filename;
    
    // Move o arquivo
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return $filename;
    }
    
    return false;
}
