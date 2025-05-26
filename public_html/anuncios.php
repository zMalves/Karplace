<?php
// Incluir arquivos necessários
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../classes/Database.php';
require_once '../classes/User.php';
require_once '../classes/Vehicle.php';
require_once '../classes/Listing.php';

// Inicializa as classes
$vehicleObj = new Vehicle();
$listingObj = new Listing();

// Obtém os parâmetros de busca
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$brand_id = isset($_GET['brand_id']) ? (int)$_GET['brand_id'] : 0;
$model_id = isset($_GET['model_id']) ? (int)$_GET['model_id'] : 0;
$price_min = isset($_GET['price_min']) ? (int)$_GET['price_min'] : 0;
$price_max = isset($_GET['price_max']) ? (int)$_GET['price_max'] : 0;
$year_min = isset($_GET['year_min']) ? (int)$_GET['year_min'] : 0;
$year_max = isset($_GET['year_max']) ? (int)$_GET['year_max'] : 0;
$mileage_max = isset($_GET['mileage_max']) ? (int)$_GET['mileage_max'] : 0;
$fuel_type = isset($_GET['fuel_type']) ? $_GET['fuel_type'] : '';
$transmission = isset($_GET['transmission']) ? $_GET['transmission'] : '';
$order_by = isset($_GET['order_by']) ? $_GET['order_by'] : 'created_at_desc';

// Monta os filtros
$filters = [
    'search' => $search,
    'brand_id' => $brand_id,
    'model_id' => $model_id,
    'price_min' => $price_min,
    'price_max' => $price_max,
    'year_min' => $year_min,
    'year_max' => $year_max,
    'mileage_max' => $mileage_max,
    'fuel_type' => $fuel_type,
    'transmission' => $transmission,
    'order_by' => $order_by
];

// Busca os anúncios
$results = $listingObj->searchListings($filters, $page);
$listings = $results['listings'];
$total = $results['total'];
$total_pages = $results['total_pages'];

// Obtém marcas para o filtro
$brands = $vehicleObj->getAllBrands();

// Obtém modelos se uma marca foi selecionada
$models = [];
if ($brand_id > 0) {
    $models = $vehicleObj->getModelsByBrand($brand_id);
}

// Obtém anos disponíveis para filtro
$years = $vehicleObj->getAvailableYears();
$min_year = !empty($years) ? min($years) : date('Y') - 20;
$max_year = !empty($years) ? max($years) : date('Y');

// Obtém tipos de combustível e transmissão
$fuel_types = $vehicleObj->getAvailableFuelTypes();
$transmissions = $vehicleObj->getAvailableTransmissions();

// Opções de ordenação
$order_options = [
    'created_at_desc' => 'Mais recentes',
    'price_asc' => 'Menor preço',
    'price_desc' => 'Maior preço',
    'year_desc' => 'Ano mais recente',
    'mileage_asc' => 'Menor quilometragem'
];

// Constrói a URL base para paginação
$pagination_url = 'anuncios.php?';
foreach ($filters as $key => $value) {
    if (!empty($value) && $key != 'page') {
        $pagination_url .= $key . '=' . urlencode($value) . '&';
    }
}
$pagination_url .= 'page=%d';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anúncios - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include_once '../includes/header.php'; ?>

    <main>
        <div class="container">
            <div class="listings-page">
                <div class="listings-header">
                    <h1>Veículos à Venda</h1>
                    <p><?php echo $total; ?> veículos encontrados</p>
                </div>

                <!-- Mensagens de alerta -->
                <?php echo flashMessage('success'); ?>
                <?php echo flashMessage('error'); ?>

                <div class="listings-container">
                    <div class="filters-sidebar">
                        <div class="filters-header">
                            <h3>Filtros</h3>
                            <a href="anuncios.php" class="clear-filters">Limpar filtros</a>
                        </div>

                        <form action="anuncios.php" method="GET" class="filters-form">
                            <!-- Busca -->
                            <div class="filter-group">
                                <label for="search">Busca</label>
                                <input type="text" name="search" id="search" placeholder="Marca, modelo ou versão" value="<?php echo $search; ?>">
                            </div>

                            <!-- Marca -->
                            <div class="filter-group">
                                <label for="brand_id">Marca</label>
                                <select name="brand_id" id="brand_id">
                                    <option value="">Todas</option>
                                    <?php foreach ($brands as $brand): ?>
                                        <option value="<?php echo $brand['id']; ?>" <?php echo $brand_id == $brand['id'] ? 'selected' : ''; ?>>
                                            <?php echo $brand['name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Modelo -->
                            <div class="filter-group">
                                <label for="model_id">Modelo</label>
                                <select name="model_id" id="model_id" <?php echo empty($models) ? 'disabled' : ''; ?>>
                                    <option value="">Todos</option>
                                    <?php foreach ($models as $model): ?>
                                        <option value="<?php echo $model['id']; ?>" <?php echo $model_id == $model['id'] ? 'selected' : ''; ?>>
                                            <?php echo $model['name']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Preço -->
                            <div class="filter-group">
                                <label>Preço</label>
                                <div class="range-inputs">
                                    <input type="number" name="price_min" placeholder="Mínimo" value="<?php echo $price_min > 0 ? $price_min : ''; ?>">
                                    <span>até</span>
                                    <input type="number" name="price_max" placeholder="Máximo" value="<?php echo $price_max > 0 ? $price_max : ''; ?>">
                                </div>
                            </div>

                            <!-- Ano -->
                            <div class="filter-group">
                                <label>Ano</label>
                                <div class="range-inputs">
                                    <select name="year_min">
                                        <option value="">Mínimo</option>
                                        <?php for ($y = $min_year; $y <= $max_year; $y++): ?>
                                            <option value="<?php echo $y; ?>" <?php echo $year_min == $y ? 'selected' : ''; ?>>
                                                <?php echo $y; ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                    <span>até</span>
                                    <select name="year_max">
                                        <option value="">Máximo</option>
                                        <?php for ($y = $max_year; $y >= $min_year; $y--): ?>
                                            <option value="<?php echo $y; ?>" <?php echo $year_max == $y ? 'selected' : ''; ?>>
                                                <?php echo $y; ?>
                                            </option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Quilometragem -->
                            <div class="filter-group">
                                <label for="mileage_max">Quilometragem até</label>
                                <select name="mileage_max" id="mileage_max">
                                    <option value="">Qualquer</option>
                                    <option value="10000" <?php echo $mileage_max == 10000 ? 'selected' : ''; ?>>10.000 km</option>
                                    <option value="30000" <?php echo $mileage_max == 30000 ? 'selected' : ''; ?>>30.000 km</option>
                                    <option value="50000" <?php echo $mileage_max == 50000 ? 'selected' : ''; ?>>50.000 km</option>
                                    <option value="100000" <?php echo $mileage_max == 100000 ? 'selected' : ''; ?>>100.000 km</option>
                                    <option value="150000" <?php echo $mileage_max == 150000 ? 'selected' : ''; ?>>150.000 km</option>
                                </select>
                            </div>

                            <!-- Combustível -->
                            <div class="filter-group">
                                <label for="fuel_type">Combustível</label>
                                <select name="fuel_type" id="fuel_type">
                                    <option value="">Qualquer</option>
                                    <?php foreach ($fuel_types as $key => $value): ?>
                                        <option value="<?php echo $key; ?>" <?php echo $fuel_type == $key ? 'selected' : ''; ?>>
                                            <?php echo $value; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Câmbio -->
                            <div class="filter-group">
                                <label for="transmission">Câmbio</label>
                                <select name="transmission" id="transmission">
                                    <option value="">Qualquer</option>
                                    <?php foreach ($transmissions as $key => $value): ?>
                                        <option value="<?php echo $key; ?>" <?php echo $transmission == $key ? 'selected' : ''; ?>>
                                            <?php echo $value; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary btn-block">Aplicar Filtros</button>
                        </form>
                    </div>

                    <div class="listings-results">
                        <div class="results-header">
                            <div class="results-count">
                                <p>Exibindo <?php echo count($listings); ?> de <?php echo $total; ?> veículos</p>
                            </div>
                            <div class="results-sort">
                                <label for="order_by">Ordenar por:</label>
                                <select name="order_by" id="order_by" onchange="updateOrder(this.value)">
                                    <?php foreach ($order_options as $key => $value): ?>
                                        <option value="<?php echo $key; ?>" <?php echo $order_by == $key ? 'selected' : ''; ?>>
                                            <?php echo $value; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <?php if (empty($listings)): ?>
                            <div class="no-results">
                                <i class="fas fa-search"></i>
                                <h3>Nenhum veículo encontrado</h3>
                                <p>Tente ajustar seus filtros ou realizar uma nova busca.</p>
                            </div>
                        <?php else: ?>
                            <div class="listings-grid">
                                <?php foreach ($listings as $listing): ?>
                                    <div class="listing-card">
                                        <div class="listing-image">
                                            <?php if (!empty($listing['primary_photo'])): ?>
                                                <a href="anuncio.php?id=<?php echo $listing['id']; ?>">
                                                    <img src="<?php echo $listing['primary_photo']; ?>" alt="<?php echo $listing['title']; ?>">
                                                </a>
                                            <?php else: ?>
                                                <a href="anuncio.php?id=<?php echo $listing['id']; ?>">
                                                    <div class="no-image">Sem imagem</div>
                                                </a>
                                            <?php endif; ?>
                                            
                                            <?php if ($listing['verified']): ?>
                                                <span class="badge verified">Vistoriado</span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="listing-details">
                                            <h3><a href="anuncio.php?id=<?php echo $listing['id']; ?>"><?php echo $listing['title']; ?></a></h3>
                                            
                                            <div class="listing-info">
                                                <span><?php echo $listing['year_manufacture']; ?>/<?php echo $listing['year_model']; ?></span>
                                                <span><?php echo number_format($listing['mileage'], 0, ',', '.'); ?> km</span>
                                                <span><?php echo $fuel_types[$listing['fuel_type']]; ?></span>
                                                <span><?php echo $transmissions[$listing['transmission']]; ?></span>
                                            </div>
                                            
                                            <div class="listing-price">
                                                <strong><?php echo formatMoney($listing['price']); ?></strong>
                                                <span class="location"><?php echo $listing['location']; ?></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Paginação -->
                            <?php if ($total_pages > 1): ?>
                                <div class="pagination-container">
                                    <?php echo pagination($page, $total_pages, $pagination_url); ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include_once '../includes/footer.php'; ?>

    <script src="assets/js/main.js"></script>
    <script>
        // Atualiza a ordenação
        function updateOrder(value) {
            const url = new URL(window.location.href);
            url.searchParams.set('order_by', value);
            window.location.href = url.toString();
        }

        // Carrega modelos quando uma marca é selecionada
        document.getElementById('brand_id').addEventListener('change', function() {
            const brandId = this.value;
            const modelSelect = document.getElementById('model_id');
            
            // Limpa o select de modelos
            modelSelect.innerHTML = '<option value="">Todos</option>';
            
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
