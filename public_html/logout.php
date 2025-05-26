<?php
// Página de logout
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Destrói a sessão
session_destroy();

// Redireciona para a página inicial
$_SESSION['success'] = 'Você saiu com sucesso.';
redirect('index.php');
