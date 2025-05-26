
<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';
require_once '../classes/Listing.php';

// Verifica se o usuário está logado
if (!isLoggedIn()) {
    $_SESSION['error'] = 'Você precisa estar logado para enviar mensagens.';
    redirect('login.php');
}

$listingObj = new Listing();
$listing_id = isset($_GET['listing_id']) ? (int)$_GET['listing_id'] : 0;

if ($listing_id <= 0) {
    $_SESSION['error'] = 'Anúncio não encontrado.';
    redirect('index.php');
}

$listing = $listingObj->getListingById($listing_id);

if (!$listing) {
    $_SESSION['error'] = 'Anúncio não encontrado.';
    redirect('index.php');
}

// Não pode enviar mensagem para si mesmo
if ($listing['user_id'] == $_SESSION['user_id']) {
    $_SESSION['error'] = 'Você não pode enviar mensagem para seu próprio anúncio.';
    redirect('anuncio.php?id=' . $listing_id);
}

// Processa o envio da mensagem
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['message'])) {
    $db = new Database();
    
    $message_text = trim($_POST['message']);
    
    if (strlen($message_text) > 0) {
        $db->query('INSERT INTO messages (listing_id, sender_id, receiver_id, message) VALUES (:listing_id, :sender_id, :receiver_id, :message)');
        $db->bind(':listing_id', $listing_id);
        $db->bind(':sender_id', $_SESSION['user_id']);
        $db->bind(':receiver_id', $listing['user_id']);
        $db->bind(':message', $message_text);
        
        if ($db->execute()) {
            $_SESSION['success'] = 'Mensagem enviada com sucesso!';
        } else {
            $_SESSION['error'] = 'Erro ao enviar mensagem.';
        }
    } else {
        $_SESSION['error'] = 'Digite uma mensagem válida.';
    }
    
    redirect('mensagem.php?listing_id=' . $listing_id);
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enviar Mensagem - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include_once '../includes/header.php'; ?>

    <main>
        <div class="container">
            <div class="message-container">
                <div class="message-header">
                    <h1>Enviar Mensagem</h1>
                    <p>Entre em contato sobre o anúncio: <strong><?php echo $listing['title']; ?></strong></p>
                </div>

                <?php echo flashMessage('success'); ?>
                <?php echo flashMessage('error'); ?>

                <div class="message-content">
                    <div class="listing-summary">
                        <div class="listing-image">
                            <?php if (!empty($listing['photos'][0]['file_path'])): ?>
                                <img src="<?php echo $listing['photos'][0]['file_path']; ?>" alt="<?php echo $listing['title']; ?>">
                            <?php else: ?>
                                <div class="no-image">Sem imagem</div>
                            <?php endif; ?>
                        </div>
                        <div class="listing-details">
                            <h3><?php echo $listing['title']; ?></h3>
                            <p class="price"><?php echo formatMoney($listing['price']); ?></p>
                            <p class="location"><?php echo $listing['location']; ?></p>
                            <p class="seller">Vendedor: <?php echo $listing['seller_name']; ?></p>
                        </div>
                    </div>

                    <form method="POST" class="message-form">
                        <div class="form-group">
                            <label for="message">Sua mensagem</label>
                            <textarea name="message" id="message" rows="6" required placeholder="Digite sua mensagem aqui..."></textarea>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Enviar Mensagem
                            </button>
                            <a href="anuncio.php?id=<?php echo $listing_id; ?>" class="btn btn-outline">Voltar ao Anúncio</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>

    <?php include_once '../includes/footer.php'; ?>
</body>
</html>
