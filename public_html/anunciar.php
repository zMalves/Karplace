
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

// Inicializa as classes
$vehicleObj = new Vehicle();

// Obtém as marcas para o select
$brands = $vehicleObj->getAllBrands();
$fuel_types = $vehicleObj->getAvailableFuelTypes();
$transmissions = $vehicleObj->getAvailableTransmissions();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anunciar Veículo - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include_once '../includes/header.php'; ?>

    <main>
        <div class="container">
            <div class="page-header">
                <h1>Anunciar Veículo</h1>
                <p>Preencha os dados do seu veículo para criar um anúncio</p>
            </div>

            <?php echo flashMessage('success'); ?>
            <?php echo flashMessage('error'); ?>

            <form action="processar_anuncio.php" method="POST" enctype="multipart/form-data" class="vehicle-form">
                <div class="form-section">
                    <h2>Informações do Veículo</h2>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="brand_id">Marca *</label>
                            <select name="brand_id" id="brand_id" required>
                                <option value="">Selecione a marca</option>
                                <?php foreach ($brands as $brand): ?>
                                    <option value="<?php echo $brand['id']; ?>"><?php echo $brand['name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="model_id">Modelo *</label>
                            <select name="model_id" id="model_id" required disabled>
                                <option value="">Selecione uma marca primeiro</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="year_manufacture">Ano de Fabricação *</label>
                            <select name="year_manufacture" id="year_manufacture" required>
                                <option value="">Selecione</option>
                                <?php for ($year = date('Y') + 1; $year >= 1970; $year--): ?>
                                    <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="year_model">Ano do Modelo *</label>
                            <select name="year_model" id="year_model" required>
                                <option value="">Selecione</option>
                                <?php for ($year = date('Y') + 1; $year >= 1970; $year--): ?>
                                    <option value="<?php echo $year; ?>"><?php echo $year; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="mileage">Quilometragem (km) *</label>
                            <input type="number" name="mileage" id="mileage" min="0" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="color">Cor *</label>
                            <input type="text" name="color" id="color" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="transmission">Câmbio *</label>
                            <select name="transmission" id="transmission" required>
                                <option value="">Selecione</option>
                                <?php foreach ($transmissions as $key => $value): ?>
                                    <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="fuel_type">Combustível *</label>
                            <select name="fuel_type" id="fuel_type" required>
                                <option value="">Selecione</option>
                                <?php foreach ($fuel_types as $key => $value): ?>
                                    <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="doors">Portas *</label>
                            <select name="doors" id="doors" required>
                                <option value="">Selecione</option>
                                <option value="2">2 portas</option>
                                <option value="4">4 portas</option>
                                <option value="5">5 portas</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="engine">Motor</label>
                            <input type="text" name="engine" id="engine" placeholder="Ex: 1.0, 2.0, 1.4 TSI">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="power">Potência</label>
                            <input type="text" name="power" id="power" placeholder="Ex: 120 cv, 150 cv">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="features">Opcionais</label>
                        <textarea name="features" id="features" rows="3" placeholder="Liste os opcionais do veículo, separados por vírgula (ex: Ar condicionado, Direção hidráulica, Vidros elétricos)"></textarea>
                    </div>
                </div>

                <div class="form-section">
                    <h2>Informações do Anúncio</h2>
                    
                    <div class="form-group">
                        <label for="title">Título do Anúncio *</label>
                        <input type="text" name="title" id="title" required maxlength="255" placeholder="Ex: Honda Civic EXL 2.0 2020">
                    </div>

                    <div class="form-group">
                        <label for="description">Descrição</label>
                        <textarea name="description" id="description" rows="5" placeholder="Descreva detalhes importantes do veículo, histórico, estado de conservação, etc."></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="price">Preço (R$) *</label>
                            <input type="number" name="price" id="price" min="1" step="0.01" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="location">Localização *</label>
                            <input type="text" name="location" id="location" required placeholder="Ex: São Paulo - SP">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h2>Fotos do Veículo</h2>
                    <p class="form-help">Adicione até 10 fotos do seu veículo. A primeira foto será a principal.</p>
                    
                    <div class="form-group">
                        <label for="photos">Fotos</label>
                        <input type="file" name="photos[]" id="photos" multiple accept="image/*" class="file-input">
                        <div class="file-help">Formatos aceitos: JPG, PNG, WEBP. Tamanho máximo: 5MB por foto.</div>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Criar Anúncio</button>
                    <a href="index.php" class="btn btn-outline">Cancelar</a>
                </div>
            </form>
        </div>
    </main>

    <?php include_once '../includes/footer.php'; ?>

    <script src="assets/js/main.js"></script>
    <script>
        // Carrega modelos quando uma marca é selecionada
        document.getElementById('brand_id').addEventListener('change', function() {
            const brandId = this.value;
            const modelSelect = document.getElementById('model_id');
            
            modelSelect.innerHTML = '<option value="">Carregando...</option>';
            
            if (brandId) {
                fetch(`api/get_models.php?brand_id=${brandId}`)
                    .then(response => response.json())
                    .then(data => {
                        modelSelect.innerHTML = '<option value="">Selecione o modelo</option>';
                        modelSelect.disabled = false;
                        
                        data.forEach(model => {
                            const option = document.createElement('option');
                            option.value = model.id;
                            option.textContent = model.name;
                            modelSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Erro ao carregar modelos:', error);
                        modelSelect.innerHTML = '<option value="">Erro ao carregar modelos</option>';
                    });
            } else {
                modelSelect.innerHTML = '<option value="">Selecione uma marca primeiro</option>';
                modelSelect.disabled = true;
            }
        });
    </script>
</body>
</html>
