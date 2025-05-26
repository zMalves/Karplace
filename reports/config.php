<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Função para carregar variáveis do .env
function loadEnv($file = '.env') {
    if (!file_exists($file)) {
        return;
    }
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        list($name, $value) = explode('=', $line, 2);
        $_ENV[$name] = trim($value);
        putenv("$name=$value");
    }
}

// Carregar variáveis de ambiente
loadEnv();
?>
