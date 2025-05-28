<?php
// Incluir arquivos necessários
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';
require_once '../classes/Vehicle.php';
require_once '../classes/Listing.php';
require_once '../includes/notifications.php';

// Inicializa as classes
$vehicleObj = new Vehicle();
$listingObj = new Listing();

// Obtém o ID do anúncio da URL
$listing_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Verifica se o ID é válido
if ($listing_id <= 0) {
    $_SESSION['error'] = 'Anúncio não encontrado.';
    redirect('index.php');
}

// Obtém os dados do anúncio
$listing = $listingObj->getListingById($listing_id);

// Verifica se o anúncio existe
if (!$listing) {
    $_SESSION['error'] = 'Anúncio não encontrado.';
    redirect('index.php');
}

// Verifica se o anúncio está ativo
if ($listing['status'] !== 'active' && (!isLoggedIn() || $_SESSION['user_id'] != $listing['user_id'])) {
    $_SESSION['error'] = 'Este anúncio não está disponível.';
    redirect('index.php');
}

// Formata os dados para exibição
$vehicle_info = [
    'Marca' => $listing['brand_name'],
    'Modelo' => $listing['model_name'],
    'Ano' => $listing['year_manufacture'] . '/' . $listing['year_model'],
    'Quilometragem' => number_format($listing['mileage'], 0, ',', '.') . ' km',
    'Câmbio' => $vehicleObj->getAvailableTransmissions()[$listing['transmission']],
    'Combustível' => $vehicleObj->getAvailableFuelTypes()[$listing['fuel_type']],
    'Cor' => $listing['color'],
    'Portas' => $listing['doors'],
    'Motor' => $listing['engine'],
    'Potência' => $listing['power']
];

// Obtém anúncios similares
$similar_filters = [
    'brand_id' => $listing['brand_id'],
    'model_id' => $listing['model_id'],
    'price_min' => $listing['price'] * 0.8,
    'price_max' => $listing['price'] * 1.2
];
$similar_results = $listingObj->searchListings($similar_filters, 1, 4);
$similar_listings = $similar_results['listings'];

// Remove o anúncio atual da lista de similares
foreach ($similar_listings as $key => $similar) {
    if ($similar['id'] == $listing_id) {
        unset($similar_listings[$key]);
        break;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $listing['title']; ?> - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include_once '../includes/header.php'; ?>

    <main>
        <div class="container">
            <!-- Navegação de migalhas de pão -->
            <div class="breadcrumb">
                <a href="index.php">Home</a> &gt;
                <a href="anuncios.php">Anúncios</a> &gt;
                <span><?php echo $listing['title']; ?></span>
            </div>

            <!-- Mensagens de alerta -->
            <?php echo flashMessage('success'); ?>
            <?php echo flashMessage('error'); ?>

            <!-- Detalhes do anúncio -->
            <div class="listing-detail">
                <div class="listing-header">
                    <div class="listing-title">
                        <h1><?php echo $listing['title']; ?></h1>
                        <div class="listing-subtitle">
                            <span><?php echo $listing['brand_name']; ?> <?php echo $listing['model_name']; ?></span>
                            <span><?php echo $listing['year_manufacture']; ?>/<?php echo $listing['year_model']; ?></span>
                        </div>
                    </div>
                    <div class="listing-price">
                        <h2><?php echo formatMoney($listing['price']); ?></h2>
                    </div>
                </div>

                <div class="listing-content">
                    <div class="listing-gallery">
                        <?php if (empty($listing['photos'])): ?>
                            <div class="no-photos">
                                <i class="fas fa-image"></i>
                                <p>Sem fotos disponíveis</p>
                            </div>
                        <?php else: ?>
                            <div class="main-photo">
                                <img id="main-photo" src="<?php echo $listing['photos'][0]['file_path']; ?>" alt="<?php echo $listing['title']; ?>">
                            </div>
                            <div class="thumbnails">
                                <?php foreach ($listing['photos'] as $index => $photo): ?>
                                    <div class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>" data-src="<?php echo $photo['file_path']; ?>">
                                        <img src="<?php echo $photo['file_path']; ?>" alt="<?php echo $listing['title']; ?> - Foto <?php echo $index + 1; ?>">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="listing-info-container">
                        <div class="listing-tabs">
                            <div class="tab-buttons">
                                <button class="tab-button active" data-tab="details">Detalhes</button>
                                <button class="tab-button" data-tab="description">Descrição</button>
                                <button class="tab-button" data-tab="features">Opcionais</button>
                            </div>

                            <div class="tab-content">
                                <div class="tab-pane active" id="details">
                                    <div class="vehicle-details">
                                        <?php foreach ($vehicle_info as $label => $value): ?>
                                            <?php if (!empty($value)): ?>
                                                <div class="detail-item">
                                                    <span class="detail-label"><?php echo $label; ?></span>
                                                    <span class="detail-value"><?php echo $value; ?></span>
                                                </div>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <div class="tab-pane" id="description">
                                    <div class="vehicle-description">
                                        <?php if (!empty($listing['description'])): ?>
                                            <p><?php echo nl2br($listing['description']); ?></p>
                                        <?php else: ?>
                                            <p>Sem descrição disponível.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="tab-pane" id="features">
                                    <div class="vehicle-features">
                                        <?php if (!empty($listing['features'])): ?>
                                            <?php $features = explode(',', $listing['features']); ?>
                                            <ul class="features-list">
                                                <?php foreach ($features as $feature): ?>
                                                    <li><i class="fas fa-check"></i> <?php echo trim($feature); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            <p>Sem opcionais listados.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="seller-info">
                            <h3>Informações do Vendedor</h3>
                            <div class="seller-details">
                                <p><strong>Nome:</strong> <?php echo $listing['seller_name']; ?></p>
                                <p><strong>Localização:</strong> <?php echo $listing['seller_city']; ?>/<?php echo $listing['seller_state']; ?></p>
                                <p><strong>Anúncio publicado:</strong> <?php echo timeAgo($listing['created_at']); ?></p>
                                <p><strong>Visualizações:</strong> <?php echo $listing['views']; ?></p>
                            </div>
                            <div class="seller-actions">
                                <a href="tel:<?php echo $listing['seller_phone']; ?>" class="btn btn-primary"><i class="fas fa-phone"></i> Ligar</a>
                                <a href="https://wa.me/<?php echo preg_replace('/\D/', '', $listing['seller_phone']); ?>?text=Olá, vi seu anúncio no <?php echo SITE_NAME; ?> e tenho interesse no veículo <?php echo $listing['title']; ?>" class="btn btn-success" target="_blank"><i class="fab fa-whatsapp"></i> WhatsApp</a>
                                <?php if (isLoggedIn()): ?>
                                    <a href="mensagem.php?listing_id=<?php echo $listing_id; ?>" class="btn btn-outline"><i class="fas fa-envelope"></i> Mensagem</a>
                                <?php else: ?>
                                    <a href="login.php?redirect=anuncio.php?id=<?php echo $listing_id; ?>" class="btn btn-outline"><i class="fas fa-envelope"></i> Mensagem</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Simulador de financiamento -->
                <div class="financing-calculator">
                    <h3>Simule o Financiamento</h3>
                    <div class="calculator-form">
                        <div class="form-group">
                            <label for="down_payment">Entrada (R$)</label>
                            <input type="number" id="down_payment" min="0" max="<?php echo $listing['price']; ?>" value="<?php echo round($listing['price'] * 0.2); ?>">
                        </div>
                        <div class="form-group">
                            <label for="installments">Parcelas</label>
                            <select id="installments">
                                <option value="12">12x</option>
                                <option value="24">24x</option>
                                <option value="36">36x</option>
                                <option value="48" selected>48x</option>
                                <option value="60">60x</option>
                                <option value="72">72x</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="interest_rate">Taxa de Juros (% a.m.)</label>
                            <input type="number" id="interest_rate" min="0.1" max="3.0" step="0.1" value="1.2">
                        </div>
                        <button type="button" id="calculate-btn" class="btn btn-primary">Calcular</button>
                    </div>
                    <div class="calculator-result">
                        <div class="result-item">
                            <span class="result-label">Valor Financiado:</span>
                            <span class="result-value" id="financed_amount">R$ 0,00</span>
                        </div>
                        <div class="result-item">
                            <span class="result-label">Valor da Parcela:</span>
                            <span class="result-value" id="installment_value">R$ 0,00</span>
                        </div>
                        <div class="result-item">
                            <span class="result-label">Valor Total:</span>
                            <span class="result-value" id="total_amount">R$ 0,00</span>
                        </div>
                        <p class="disclaimer">Esta simulação é apenas uma estimativa. Consulte um banco ou financeira para valores exatos.</p>
                    </div>
                </div>

                <!-- Anúncios similares -->
                <?php if (!empty($similar_listings)): ?>
                    <div class="similar-listings">
                        <h3>Veículos Similares</h3>
                        <div class="listings-grid">
                            <?php foreach ($similar_listings as $similar): ?>
                                <div class="listing-card">
                                    <div class="listing-image">
                                        <?php if (!empty($similar['primary_photo'])): ?>
                                            <img src="<?php echo $similar['primary_photo']; ?>" alt="<?php echo $similar['title']; ?>">
                                        <?php else: ?>
                                            <div class="no-image">Sem imagem</div>
                                        <?php endif; ?>
                                        
                                        <?php if ($similar['verified']): ?>
                                            <span class="badge verified">Vistoriado</span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="listing-details">
                                        <h3><a href="anuncio.php?id=<?php echo $similar['id']; ?>"><?php echo $similar['title']; ?></a></h3>
                                        
                                        <div class="listing-info">
                                            <span><?php echo $similar['year_manufacture']; ?></span>
                                            <span><?php echo number_format($similar['mileage'], 0, ',', '.'); ?> km</span>
                                            <span><?php echo $vehicleObj->getAvailableFuelTypes()[$similar['fuel_type']]; ?></span>
                                        </div>
                                        
                                        <div class="listing-price">
                                            <strong><?php echo formatMoney($similar['price']); ?></strong>
                                            <span class="location"><?php echo $similar['location']; ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include_once '../includes/footer.php'; ?>

    <script src="assets/js/main.js"></script>
    <script>
        // Galeria de fotos
        document.addEventListener('DOMContentLoaded', function() {
            const thumbnails = document.querySelectorAll('.thumbnail');
            const mainPhoto = document.getElementById('main-photo');
            
            thumbnails.forEach(thumbnail => {
                thumbnail.addEventListener('click', function() {
                    // Remove a classe active de todas as thumbnails
                    thumbnails.forEach(t => t.classList.remove('active'));
                    
                    // Adiciona a classe active à thumbnail clicada
                    this.classList.add('active');
                    
                    // Atualiza a foto principal
                    mainPhoto.src = this.dataset.src;
                });
            });
            
            // Tabs
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabPanes = document.querySelectorAll('.tab-pane');
            
            tabButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Remove a classe active de todos os botões e painéis
                    tabButtons.forEach(b => b.classList.remove('active'));
                    tabPanes.forEach(p => p.classList.remove('active'));
                    
                    // Adiciona a classe active ao botão clicado
                    this.classList.add('active');
                    
                    // Ativa o painel correspondente
                    const tabId = this.dataset.tab;
                    document.getElementById(tabId).classList.add('active');
                });
            });
            
            // Simulador de financiamento
            const calculateBtn = document.getElementById('calculate-btn');
            const downPaymentInput = document.getElementById('down_payment');
            const installmentsSelect = document.getElementById('installments');
            const interestRateInput = document.getElementById('interest_rate');
            const financedAmountOutput = document.getElementById('financed_amount');
            const installmentValueOutput = document.getElementById('installment_value');
            const totalAmountOutput = document.getElementById('total_amount');
            
            calculateBtn.addEventListener('click', function() {
                const price = <?php echo $listing['price']; ?>;
                const downPayment = parseFloat(downPaymentInput.value) || 0;
                const installments = parseInt(installmentsSelect.value) || 48;
                const interestRate = parseFloat(interestRateInput.value) || 1.2;
                
                // Valor financiado
                const financedAmount = price - downPayment;
                
                // Cálculo da parcela (Sistema Francês de Amortização - Tabela Price)
                const monthlyRate = interestRate / 100;
                const installmentValue = financedAmount * (monthlyRate * Math.pow(1 + monthlyRate, installments)) / (Math.pow(1 + monthlyRate, installments) - 1);
                
                // Valor total
                const totalAmount = installmentValue * installments + downPayment;
                
                // Atualiza os resultados
                financedAmountOutput.textContent = formatCurrency(financedAmount);
                installmentValueOutput.textContent = formatCurrency(installmentValue);
                totalAmountOutput.textContent = formatCurrency(totalAmount);
            });
            
            // Formata um valor como moeda
            function formatCurrency(value) {
                return 'R$ ' + value.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
            }
        });
    </script>
</body>
</html>
