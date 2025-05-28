<?php
ob_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';
require_once '../classes/Vehicle.php';
require_once '../classes/Listing.php';
require_once '../includes/notifications.php';

// Verifica se o usuário está logado
if (!isLoggedIn()) {
//    flash('Você precisa estar logado para anunciar um veículo.', FLASH_ERROR);
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
    <style>
        /* Estilos adicionais para melhorar o formulário */
        .form-section {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 30px;
        }
        .form-section h2 {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #4D4D4D;
        }
        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            font-family: inherit;
            transition: border-color 0.3s ease;
        }
        .form-group input[type="text"]:focus,
        .form-group input[type="number"]:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #E60000;
            outline: none;
            box-shadow: 0 0 0 2px rgba(230, 0, 0, 0.2);
        }
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }
        .form-group select[disabled] {
            background-color: #f8f9fa;
            cursor: not-allowed;
        }
        .form-help {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        .file-input-wrapper {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.3s ease;
        }
        .file-input-wrapper:hover {
            border-color: #E60000;
        }
        .file-input-wrapper i {
            font-size: 30px;
            color: #E60000;
            margin-bottom: 10px;
        }
        .file-input-wrapper p {
            margin-bottom: 5px;
            font-weight: 500;
        }
        .file-input {
            display: none; /* Esconde o input original */
        }
        .page-header {
            margin-bottom: 30px;
            text-align: center;
        }
        .page-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
            color: #333;
        }
        .page-header p {
            font-size: 16px;
            color: #666;
        }
        .submit-button-container {
            text-align: right;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <?php include_once '../includes/header.php';?>

    <main>
        <div class="container">
            <div class="page-header">
                <h1>Anunciar Veículo</h1>
                <p>Preencha os dados do seu veículo para criar um anúncio</p>
            </div>

            <form action="processar_anuncio.php" method="POST" enctype="multipart/form-data" class="vehicle-form">
                
                <div class="form-section">
                    <h2><i class="fas fa-car"></i> Informações do Veículo</h2>

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
                            <input type="number" name="mileage" id="mileage" min="0" required placeholder="Ex: 55000">
                        </div>

                        <div class="form-group">
                            <label for="color">Cor *</label>
                            <input type="text" name="color" id="color" required placeholder="Ex: Prata">
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
                        <div class="form-group"> <!-- Empty group to balance the row if needed --> </div>
                    </div>

                    <div class="form-group">
                        <label for="features">Opcionais</label>
                        <textarea name="features" id="features" rows="4" placeholder="Liste os opcionais do veículo, separados por vírgula (ex: Ar condicionado, Direção hidráulica, Vidros elétricos)"></textarea>
                        <div class="form-help">Separe os itens por vírgula.</div>
                    </div>
                </div>

                <div class="form-section">
                    <h2><i class="fas fa-bullhorn"></i> Informações do Anúncio</h2>

                    <div class="form-group">
                        <label for="title">Título do Anúncio *</label>
                        <input type="text" name="title" id="title" required maxlength="255" placeholder="Ex: Honda Civic EXL 2.0 2020 - Único Dono">
                        <div class="form-help">Seja claro e descritivo. Máximo 255 caracteres.</div>
                    </div>

                    <div class="form-group">
                        <label for="description">Descrição</label>
                        <textarea name="description" id="description" rows="6" placeholder="Descreva detalhes importantes do veículo, histórico, estado de conservação, motivo da venda, etc."></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="price">Preço (R$) *</label>
                            <input type="number" name="price" id="price" min="1" step="0.01" required placeholder="Ex: 85000.00">
                        </div>

                        <div class="form-group">
                            <label for="location">Localização *</label>
                            <input type="text" name="location" id="location" required placeholder="Ex: São Paulo - SP">
                            <div class="form-help">Cidade e Estado onde o veículo se encontra.</div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h2><i class="fas fa-camera"></i> Fotos do Veículo</h2>
                    <p class="form-help">Adicione até 10 fotos do seu veículo. A primeira foto será a principal. Boa qualidade de imagem aumenta suas chances!</p>

                    <div class="form-group">
                        <label for="photos" class="file-input-wrapper">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Clique aqui ou arraste as fotos</p>
                            <span class="form-help">Formatos aceitos: JPG, PNG, WEBP. Tamanho máximo: 5MB por foto.</span>
                        </label>
                        <input type="file" name="photos[]" id="photos" multiple accept="image/*" class="file-input">
                        <!-- Adicionar preview de imagens aqui via JS seria ideal -->
                    </div>
                </div>

                <div class="submit-button-container">
                     <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-check"></i> Criar Anúncio</button>
                </div>
            </form>
        </div>
    </main>

    <?php include_once '../includes/footer.php'; ?>

    <script>
        // Script para carregar modelos quando uma marca é selecionada
        document.getElementById('brand_id').addEventListener('change', function() {
            const brandId = this.value;
            const modelSelect = document.getElementById('model_id');
            
            // Limpa o select de modelos
            modelSelect.innerHTML = '<option value="">Carregando...</option>';
            modelSelect.disabled = true;
            
            if (brandId) {
                // Faz uma requisição AJAX para buscar os modelos
                fetch(`api/get_models.php?brand_id=${brandId}`)
                    .then(response => response.json())
                    .then(data => {
                        modelSelect.innerHTML = '<option value="">Selecione o modelo</option>'; // Reset option
                        if (data.length > 0) {
                            data.forEach(model => {
                                const option = document.createElement('option');
                                option.value = model.id;
                                option.textContent = model.name;
                                modelSelect.appendChild(option);
                            });
                            modelSelect.disabled = false;
                        } else {
                             modelSelect.innerHTML = '<option value="">Nenhum modelo encontrado</option>';
                        }
                    })
                    .catch(error => {
                        console.error('Erro ao carregar modelos:', error);
                        modelSelect.innerHTML = '<option value="">Erro ao carregar</option>';
                    });
            } else {
                // Reseta se nenhuma marca for selecionada
                modelSelect.innerHTML = '<option value="">Selecione uma marca primeiro</option>';
                modelSelect.disabled = true;
            }
        });

        // Adiciona interatividade ao input de arquivo (opcional, mas melhora UX)
        const fileInput = document.getElementById('photos');
        const fileInputWrapper = document.querySelector('.file-input-wrapper');
        
        fileInputWrapper.addEventListener('click', () => {
            fileInput.click();
        });

        fileInput.addEventListener('change', (event) => {
            const files = event.target.files;
            const feedbackP = fileInputWrapper.querySelector('p');
            if (files.length > 0) {
                feedbackP.textContent = `${files.length} foto(s) selecionada(s)`;
            } else {
                feedbackP.textContent = 'Clique aqui ou arraste as fotos';
            }
            // Aqui poderia ser implementado um preview das imagens
        });

        // Previne o comportamento padrão de arrastar e soltar para evitar abrir o arquivo no navegador
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            fileInputWrapper.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false); // Previne no body também
        });

        function preventDefaults (e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // Adiciona feedback visual ao arrastar sobre a área
        ['dragenter', 'dragover'].forEach(eventName => {
            fileInputWrapper.addEventListener(eventName, () => fileInputWrapper.style.borderColor = '#E60000', false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            fileInputWrapper.addEventListener(eventName, () => fileInputWrapper.style.borderColor = '#ddd', false);
        });

        // Lida com o drop de arquivos
        fileInputWrapper.addEventListener('drop', (e) => {
            fileInput.files = e.dataTransfer.files; // Associa os arquivos soltos ao input
            fileInput.dispatchEvent(new Event('change')); // Dispara o evento change para atualizar o feedback
        }, false);

    </script>
</body>
</html>
