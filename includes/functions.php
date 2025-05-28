<?php
/**
 * Funções auxiliares do sistema
 */
/**
 * Verifica se o usuário está logado
 * @return boolean
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}
/**
 * Obtém o ID do usuário logado
 * @return int|null
 */
function getUserId() {
    return isLoggedIn() ? $_SESSION['user_id'] : null;
}
/**
 * Verifica se o usuário é administrador
 * @return boolean
 */
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'admin';
}
/**
 * Verifica se a requisição é do tipo POST
 * @return boolean
 */
function isPostRequest() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}
/**
 * Gera um campo de formulário com um token CSRF
 * @return string Campo de entrada HTML
 */
function csrfField() {
    return '<input type="hidden" name="csrf_token" value="' . generateCSRFToken() . '">';
}
/**
 * Redireciona para uma página
 * @param string $url URL de destino
 */
function redirect(string $url): void
{
    // descarta TODO o conteúdo já armazenado no(s) buffer(es)
    while (ob_get_level() > 0) {
        ob_end_clean();
    }

    header("Location: {$url}");
    exit;
}

/**
 * Formata um valor monetário
 * @param float $value Valor
 * @return string Valor formatado
 */
function formatMoney($value) {
    return 'R$ ' . number_format($value, 2, ',', '.');
}

/**
 * Formata uma data para exibição
 * @param string $date Data
 * @return string Data formatada
 */
function formatDate($date) {
    return date('d/m/Y', strtotime($date));
}

/**
 * Formata uma data/hora para exibição
 * @param string $datetime Data/hora
 * @return string Data/hora formatada
 */
function formatDateTime($datetime) {
    return date('d/m/Y H:i', strtotime($datetime));
}

/**
 * Calcula o tempo decorrido desde uma data
 * @param string $datetime Data/hora
 * @return string Tempo decorrido
 */
function timeAgo($datetime) {
    $time = time() - strtotime($datetime);

    if ($time < 60) {
        return 'Agora há pouco';
    } elseif ($time < 3600) {
        $minutes = floor($time / 60);
        return $minutes . ' minuto' . ($minutes > 1 ? 's' : '') . ' atrás';
    } elseif ($time < 86400) {
        $hours = floor($time / 3600);
        return $hours . ' hora' . ($hours > 1 ? 's' : '') . ' atrás';
    } elseif ($time < 2592000) {
        $days = floor($time / 86400);
        return $days . ' dia' . ($days > 1 ? 's' : '') . ' atrás';
    } elseif ($time < 31536000) {
        $months = floor($time / 2592000);
        return $months . ' mês' . ($months > 1 ? 'es' : '') . ' atrás';
    } else {
        $years = floor($time / 31536000);
        return $years . ' ano' . ($years > 1 ? 's' : '') . ' atrás';
    }
}

/**
 * Sanitiza uma string para exibição segura
 * @param string $string String para sanitizar
 * @return string String sanitizada
 */
function sanitize($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Valida um email
 * @param string $email Email para validar
 * @return boolean
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Gera um hash seguro para senha
 * @param string $password Senha
 * @return string Hash da senha
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT, ['cost' => HASH_COST]);
}

/**
 * Verifica se uma senha corresponde ao hash
 * @param string $password Senha
 * @param string $hash Hash
 * @return boolean
 */
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Exibe mensagens flash
 * @param string $type Tipo da mensagem (success, error, warning, info)
 * @return string HTML da mensagem
 */
function flashMessage($type) {
    if (isset($_SESSION[$type])) {
        $message = $_SESSION[$type];
        unset($_SESSION[$type]);
        return "<div class=\"alert alert-{$type}\">{$message}</div>";
    }
    return '';
}

/**
 * Gera um token CSRF
 * @return string Token
 */
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Verifica um token CSRF
 * @param string $token Token para verificar
 * @return boolean
 */
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Limita o tamanho de uma string
 * @param string $string String
 * @param int $length Tamanho máximo
 * @param string $suffix Sufixo para strings cortadas
 * @return string String limitada
 */
function limitString($string, $length = 100, $suffix = '...') {
    if (strlen($string) > $length) {
        return substr($string, 0, $length) . $suffix;
    }
    return $string;
}

/**
 * Converte uma string para URL amigável (slug)
 * @param string $string String para converter
 * @return string Slug
 */
function createSlug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    $string = preg_replace('/[\s-]+/', '-', $string);
    return trim($string, '-');
}

/**
 * Obtém a extensão de um arquivo
 * @param string $filename Nome do arquivo
 * @return string Extensão
 */
function getFileExtension($filename) {
    return pathinfo($filename, PATHINFO_EXTENSION);
}

/**
 * Verifica se um arquivo é uma imagem válida
 * @param string $filename Nome do arquivo
 * @return boolean
 */
function isValidImage($filename) {
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
    $extension = strtolower(getFileExtension($filename));
    return in_array($extension, $allowed_extensions);
}

/**
 * Formata o tamanho de um arquivo
 * @param int $bytes Tamanho em bytes
 * @return string Tamanho formatado
 */
function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];

    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }

    return round($bytes, 2) . ' ' . $units[$i];
}

// ---- Notificações  -----//
define('FLASH_SUCCESS', 'success');
define('FLASH_ERROR',   'error');
define('FLASH_WARNING', 'warning');
define('FLASH_INFO',    'info');

function flash(string $message, string $type = FLASH_SUCCESS): void
{
    $_SESSION['flash'] = [
        'message' => $message,
        'type'    => $type,
    ];
}