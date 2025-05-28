<?php
// Incluir arquivos necessários
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';
require_once '../includes/notifications.php';


// Verifica se o usuário já está logado
if (isLoggedIn()) {
    redirect('index.php');
}

// Inicializa variáveis
$email = '';
$password = '';
$errors = [];

// Processa o formulário de login
if (isPostRequest()) {
    // Verifica o token CSRF
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Erro de segurança. Por favor, tente novamente.';
    } else {
        // Obtém os dados do formulário
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        // Valida os dados
        if (empty($email)) {
            $errors[] = 'O e-mail é obrigatório.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'O e-mail informado é inválido.';
        }

        if (empty($password)) {
            $errors[] = 'A senha é obrigatória.';
        }

        // Se não houver erros, tenta fazer login
        if (empty($errors)) {
            $userObj = new User();
            $user = $userObj->login($email, $password);
            
            if ($user) {
                // Login bem-sucedido, cria a sessão
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_type'] = $user['user_type'];

                // Redireciona para a página solicitada ou para a home
                $redirect = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php';
                $_SESSION['success'] = 'Login realizado com sucesso!';
                redirect($redirect);
            } else {
                $errors[] = 'E-mail ou senha inválidos.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include_once '../includes/header.php'; ?>

    <main>
        <div class="container">
            <div class="auth-container">
                <div class="auth-box">
                    <h1>Login</h1>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-error">
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
            
                    
                    <form action="login.php<?php echo isset($_GET['redirect']) ? '?redirect=' . urlencode($_GET['redirect']) : ''; ?>" method="POST" class="auth-form">
                        <?php echo csrfField(); ?>
                        
                        <div class="form-group">
                            <label for="email">E-mail</label>
                            <input type="email" id="email" name="email" value="<?php echo sanitize($email); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Senha</label>
                            <input type="password" id="password" name="password" required>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block">Entrar</button>
                        </div>
                        
                        <div class="auth-links">
                            <a href="recuperar-senha.php">Esqueceu sua senha?</a>
                            <span>|</span>
                            <a href="registro.php">Criar uma conta</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php include_once '../includes/footer.php'; ?>
</body>
</html>