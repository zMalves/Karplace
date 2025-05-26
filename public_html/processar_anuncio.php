
<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';
require_once '../classes/Vehicle.php';
require_once '../classes/Listing.php';

// Verifica se o usuário está logado
if (!isLoggedIn()) {
    $_SESSION['error'] = 'Você precisa estar logado para anunciar um veículo.';
    redirect('login.php');
}

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('anunciar.php');
}

// Inicializa a classe
$listingObj = new Listing();

// Valida os dados obrigatórios
$required_fields = ['brand_id', 'model_id', 'year_manufacture', 'year_model', 'mileage', 'color', 'transmission', 'fuel_type', 'doors', 'title', 'price', 'location'];
$errors = [];

foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        $errors[] = "O campo " . ucfirst(str_replace('_', ' ', $field)) . " é obrigatório.";
    }
}

// Validações específicas
if (!empty($_POST['price']) && !is_numeric($_POST['price'])) {
    $errors[] = "O preço deve ser um valor numérico.";
}

if (!empty($_POST['mileage']) && (!is_numeric($_POST['mileage']) || $_POST['mileage'] < 0)) {
    $errors[] = "A quilometragem deve ser um número válido.";
}

if (!empty($errors)) {
    $_SESSION['error'] = implode('<br>', $errors);
    redirect('anunciar.php');
}

// Prepara os dados para inserção
$data = [
    'user_id' => $_SESSION['user_id'],
    'brand_id' => (int)$_POST['brand_id'],
    'model_id' => (int)$_POST['model_id'],
    'year_manufacture' => (int)$_POST['year_manufacture'],
    'year_model' => (int)$_POST['year_model'],
    'mileage' => (int)$_POST['mileage'],
    'transmission' => $_POST['transmission'],
    'fuel_type' => $_POST['fuel_type'],
    'color' => trim($_POST['color']),
    'doors' => (int)$_POST['doors'],
    'engine' => !empty($_POST['engine']) ? trim($_POST['engine']) : null,
    'power' => !empty($_POST['power']) ? trim($_POST['power']) : null,
    'features' => !empty($_POST['features']) ? trim($_POST['features']) : null,
    'title' => trim($_POST['title']),
    'description' => !empty($_POST['description']) ? trim($_POST['description']) : null,
    'price' => (float)$_POST['price'],
    'location' => trim($_POST['location']),
    'status' => 'pending', // Anúncios começam como pendentes
    'featured' => false,
    'verified' => false
];

// Cria o anúncio
$listing_id = $listingObj->create($data);

if ($listing_id) {
    // Processa as fotos se foram enviadas
    if (!empty($_FILES['photos']['name'][0])) {
        $upload_errors = [];
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
        $max_file_size = 5 * 1024 * 1024; // 5MB
        $upload_dir = UPLOAD_PATH;
        
        // Cria o diretório se não existir
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        foreach ($_FILES['photos']['name'] as $key => $filename) {
            if ($_FILES['photos']['error'][$key] === UPLOAD_ERR_OK) {
                $tmp_name = $_FILES['photos']['tmp_name'][$key];
                $file_size = $_FILES['photos']['size'][$key];
                $file_type = $_FILES['photos']['type'][$key];
                
                // Validações
                if (!in_array($file_type, $allowed_types)) {
                    $upload_errors[] = "Arquivo {$filename}: Tipo não permitido.";
                    continue;
                }
                
                if ($file_size > $max_file_size) {
                    $upload_errors[] = "Arquivo {$filename}: Tamanho muito grande.";
                    continue;
                }
                
                // Gera nome único para o arquivo
                $file_extension = pathinfo($filename, PATHINFO_EXTENSION);
                $new_filename = uniqid() . '_' . $listing_id . '.' . $file_extension;
                $file_path = $upload_dir . $new_filename;
                $web_path = '/uploads/vehicles/' . $new_filename;
                
                // Move o arquivo
                if (move_uploaded_file($tmp_name, $file_path)) {
                    // Adiciona a foto ao banco de dados
                    $is_primary = ($key === 0); // Primeira foto é a principal
                    $listingObj->addPhoto($listing_id, $new_filename, $web_path, $key + 1, $is_primary);
                } else {
                    $upload_errors[] = "Erro ao fazer upload do arquivo {$filename}.";
                }
            }
        }
        
        if (!empty($upload_errors)) {
            $_SESSION['error'] = implode('<br>', $upload_errors);
        }
    }
    
    $_SESSION['success'] = 'Anúncio criado com sucesso! Ele será analisado e publicado em breve.';
    redirect('index.php');
} else {
    $_SESSION['error'] = 'Erro ao criar o anúncio. Tente novamente.';
    redirect('anunciar.php');
}
?>
