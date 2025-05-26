<?php
// Evita qualquer saída antes da inicialização da sessão
ob_start();

require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';
require_once '../classes/Vehicle.php';
require_once '../classes/Listing.php';

// Inicializa as classes
$vehicleObj = new Vehicle();
$listingObj = new Listing();

// Obtém anúncios em destaque
$featured_listings = $listingObj->getFeaturedListings(6);

// Obtém anúncios recentes
$recent_listings = $listingObj->getRecentListings(6);

// Obtém marcas para o filtro de busca
$brands = $vehicleObj->getAllBrands();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Marketplace Automotivo</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include_once '../includes/header.php'; ?>

    <main>
        <!-- Banner Principal -->
        <section class="hero">
            <div class="container">
                <div class="hero-content">
                    <h1>Encontre o veículo ideal para você</h1>
                    <p>Conectamos compradores e vendedores de forma segura e eficiente</p>
                    
                    <div class="search-box">
                        <form action="anuncios.php" method="GET">
                            <div class="search-input">
                                <input type="text" name="search" placeholder="Busque por marca, modelo ou versão...">
                                <button type="submit" class="btn btn-primary">Buscar</button>
                            </div>
                            
                            <div class="search-filters">
                                <div class="filter-group">
                                    <label for="brand_id">Marca</label>
                                    <select name="brand_id" id="brand_id">
                                        <option value="">Todas</option>
                                        <?php foreach ($brands as $brand): ?>
                                            <option value="<?php echo $brand['id']; ?>"><?php echo $brand['name']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="filter-group">
                                    <label for="model_id">Modelo</label>
                                    <select name="model_id" id="model_id" disabled>
                                        <option value="">Selecione uma marca</option>
                                    </select>
                                </div>
                                
                                <div class="filter-group">
                                    <label for="price_max">Preço até</label>
                                    <select name="price_max" id="price_max">
                                        <option value="">Qualquer</option>
                                        <option value="50000">R$ 50.000</option>
                                        <option value="100000">R$ 100.000</option>
                                        <option value="150000">R$ 150.000</option>
                                        <option value="200000">R$ 200.000</option>
                                        <option value="300000">R$ 300.000</option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    <div class="quick-links">
                        <a href="anuncios.php?type=car">Carros</a>
                        <a href="anuncios.php?type=motorcycle">Motos</a>
                        <a href="vender.php">Vender</a>
                        <a href="financiamento.php">Financiamento</a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Anúncios em Destaque -->
        <section class="featured-listings">
            <div class="container">
                <h2>Veículos em Destaque</h2>
                
                <div class="listings-grid">
                    <?php if (empty($featured_listings)): ?>
                        <p class="no-results">Nenhum veículo em destaque no momento.</p>
                    <?php else: ?>
                        <?php foreach ($featured_listings as $listing): ?>
                            <div class="listing-card">
                                <div class="listing-image">
                                    <?php if (!empty($listing['primary_photo'])): ?>
                                        <img src="<?php echo $listing['primary_photo']; ?>" alt="<?php echo $listing['title']; ?>">
                                    <?php else: ?>
                                        <div class="no-image">Sem imagem</div>
                                    <?php endif; ?>

                                    <?php if (isset($listing['verified']) && $listing['verified']): ?>
                                        <span class="badge verified">Vistoriado</span>
                                    <?php else: ?>
                                        <!-- Caso não esteja verificado, você pode deixar vazio ou adicionar uma mensagem -->
                                    <?php endif; ?>
                                </div>
                                
                                <div class="listing-details">
                                    <h3><a href="anuncio.php?id=<?php echo $listing['id']; ?>"><?php echo $listing['title']; ?></a></h3>
                                    
                                    <div class="listing-info">
                                        <span><?php echo $listing['year_manufacture']; ?></span>
                                        <span><?php echo number_format($listing['mileage'], 0, ',', '.'); ?> km</span>
                                        <span><?php echo $vehicleObj->getAvailableFuelTypes()[$listing['fuel_type']]; ?></span>
                                        <span><?php echo $vehicleObj->getAvailableTransmissions()[$listing['transmission']]; ?></span>
                                    </div>
                                    
                                    <div class="listing-price">
                                        <strong><?php echo formatMoney($listing['price']); ?></strong>
                                        <span class="location"><?php echo $listing['location']; ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <div class="view-more">
                    <a href="anuncios.php" class="btn btn-outline">Ver mais veículos</a>
                </div>
            </div>
        </section>

        <!-- Por que escolher nossa plataforma -->
        <section class="features">
            <div class="container">
                <h2>Por que escolher nossa plataforma?</h2>
                
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3>Compra Segura</h3>
                        <p>Transações protegidas e veículos verificados para sua tranquilidade.</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <h3>Melhores Preços</h3>
                        <p>Comparação de preços e histórico para decisões de compra inteligentes.</p>
                    </div>
                    
                    <div class="feature-card">
                        <div class="feature-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <h3>Suporte Completo</h3>
                        <p>Assistência durante todo o processo de compra e venda de veículos.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Anúncios Recentes -->
        <section class="recent-listings">
            <div class="container">
                <h2>Adicionados Recentemente</h2>
                
                <div class="listings-grid">
                    <?php if (empty($recent_listings)): ?>
                        <p class="no-results">Nenhum veículo recente no momento.</p>
                    <?php else: ?>
                        <?php foreach ($recent_listings as $listing): ?>
                            <div class="listing-card">
                                <div class="listing-image">
                                    <?php if (!empty($listing['primary_photo'])): ?>
                                        <img src="<?php echo $listing['primary_photo']; ?>" alt="<?php echo $listing['title']; ?>">
                                    <?php else: ?>
                                        <div class="no-image">Sem imagem</div>
                                    <?php endif; ?>
                                    
                                    <?php if ($listing['verified']): ?>
                                        <span class="badge verified">Vistoriado</span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="listing-details">
                                    <h3><a href="anuncio.php?id=<?php echo $listing['id']; ?>"><?php echo $listing['title']; ?></a></h3>
                                    
                                    <div class="listing-info">
                                        <span><?php echo $listing['year_manufacture']; ?></span>
                                        <span><?php echo number_format($listing['mileage'], 0, ',', '.'); ?> km</span>
                                        <span><?php echo $vehicleObj->getAvailableFuelTypes()[$listing['fuel_type']]; ?></span>
                                        <span><?php echo $vehicleObj->getAvailableTransmissions()[$listing['transmission']]; ?></span>
                                    </div>
                                    
                                    <div class="listing-price">
                                        <strong><?php echo formatMoney($listing['price']); ?></strong>
                                        <span class="location"><?php echo $listing['location']; ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                
                <div class="view-more">
                    <a href="anuncios.php" class="btn btn-outline">Ver mais veículos</a>
                </div>
            </div>
        </section>

        <!-- Como funciona -->
        <section class="how-it-works">
            <div class="container">
                <h2>Como funciona</h2>
                
                <div class="steps-grid">
                    <div class="step-card">
                        <div class="step-number">1</div>
                        <h3>Busque</h3>
                        <p>Encontre o veículo ideal com nossos filtros avançados.</p>
                    </div>
                    
                    <div class="step-card">
                        <div class="step-number">2</div>
                        <h3>Compare</h3>
                        <p>Analise preços, especificações e histórico dos veículos.</p>
                    </div>
                    
                    <div class="step-card">
                        <div class="step-number">3</div>
                        <h3>Contate</h3>
                        <p>Entre em contato diretamente com o vendedor.</p>
                    </div>
                    
                    <div class="step-card">
                        <div class="step-number">4</div>
                        <h3>Negocie</h3>
                        <p>Feche o negócio com segurança e tranquilidade.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <?php include_once '../includes/footer.php'; ?>

    <script src="assets/js/main.js"></script>
    <script>
        // Script para carregar modelos quando uma marca é selecionada
        document.getElementById('brand_id').addEventListener('change', function() {
            const brandId = this.value;
            const modelSelect = document.getElementById('model_id');
            
            // Limpa o select de modelos
            modelSelect.innerHTML = '<option value="">Selecione um modelo</option>';
            
            if (brandId) {
                // Habilita o select de modelos
                modelSelect.disabled = false;
                
                // Faz uma requisição AJAX para buscar os modelos
                fetch(`../api/get_models.php?brand_id=${brandId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.length > 0) {
                            data.forEach(model => {
                                const option = document.createElement('option');
                                option.value = model.id;
                                option.textContent = model.name;
                                modelSelect.appendChild(option);
                            });
                        }
                    })
                    .catch(error => console.error('Erro ao carregar modelos:', error));
            } else {
                // Desabilita o select de modelos
                modelSelect.disabled = true;
            }
        });
    </script>
</body>
</html>
