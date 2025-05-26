<?php
// Incluir arquivos necessários
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';

// Verifica se o usuário já está logado
if (isLoggedIn()) {
    redirect('index.php');
}

// Inicializa variáveis
$name = '';
$email = '';
$phone = '';
$user_type = 'buyer';
$errors = [];

// Processa o formulário de registro
if (isPostRequest()) {
    // Verifica o token CSRF
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Erro de segurança. Por favor, tente novamente.';
    } else {
        // Obtém os dados do formulário
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $user_type = $_POST['user_type'] ?? 'buyer';
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';
        
        // Valida os dados
        if (empty($name)) {
            $errors[] = 'O nome é obrigatório.';
        }
        
        if (empty($email)) {
            $errors[] = 'O e-mail é obrigatório.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'O e-mail informado é inválido.';
        }
        
        if (empty($password)) {
            $errors[] = 'A senha é obrigatória.';
        } elseif (strlen($password) < 6) {
            $errors[] = 'A senha deve ter pelo menos 6 caracteres.';
        }
        
        if ($password !== $password_confirm) {
            $errors[] = 'As senhas não conferem.';
        }
        
        if (!in_array($user_type, ['buyer', 'seller'])) {
            $errors[] = 'Tipo de usuário inválido.';
        }
        
        // Verifica se o e-mail já está em uso
        if (empty($errors)) {
            $userObj = new User();
            if ($userObj->emailExists($email)) {
                $errors[] = 'Este e-mail já está em uso.';
            }
        }
        
        // Se não houver erros, registra o usuário
        if (empty($errors)) {
            $userData = [
                'name' => $name,
                'email' => $email,
                'password' => $password,
                'phone' => $phone,
                'user_type' => $user_type
            ];
            
            $userObj = new User();
            $user_id = $userObj->register($userData);
            
            if ($user_id) {
                // Registro bem-sucedido, cria a sessão
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_type'] = $user_type;
                
                // Redireciona para a página inicial
                $_SESSION['success'] = 'Registro realizado com sucesso! Bem-vindo ao ' . SITE_NAME . '.';
                redirect('index.php');
            } else {
                $errors[] = 'Erro ao registrar usuário. Por favor, tente novamente.';
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
    <title>Registro - <?php echo SITE_NAME; ?></title>
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
                    <h1>Criar Conta</h1>
                    
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-error">
                            <ul>
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    
                    <?php echo flashMessage('success'); ?>
                    <?php echo flashMessage('error'); ?>
                    
                    <form action="registro.php" method="POST" class="auth-form">
                        <?php echo csrfField(); ?>
                        
                        <div class="form-group">
                            <label for="name">Nome completo</label>
                            <input type="text" id="name" name="name" value="<?php echo sanitizeHTML($name); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">E-mail</label>
                            <input type="email" id="email" name="email" value="<?php echo sanitizeHTML($email); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Telefone</label>
                            <input type="tel" id="phone" name="phone" value="<?php echo sanitizeHTML($phone); ?>" placeholder="(00) 00000-0000">
                        </div>
                        
                        <div class="form-group">
                            <label>Tipo de conta</label>
                            <div class="radio-group">
                                <label>
                                    <input type="radio" name="user_type" value="buyer" <?php echo $user_type === 'buyer' ? 'checked' : ''; ?>>
                                    Comprador
                                </label>
                                <label>
                                    <input type="radio" name="user_type" value="seller" <?php echo $user_type === 'seller' ? 'checked' : ''; ?>>
                                    Vendedor
                                </label>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Senha</label>
                            <input type="password" id="password" name="password" required>
                            <small>A senha deve ter pelo menos 6 caracteres.</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="password_confirm">Confirmar senha</label>
                            <input type="password" id="password_confirm" name="password_confirm" required>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-block">Criar Conta</button>
                        </div>
                        
                        <div class="auth-links">
                            <span>Já tem uma conta?</span>
                            <a href="login.php">Faça login</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php include_once '../includes/footer.php'; ?>

    <script src="assets/js/main.js"></script>
</body>
</html>
