
<?php
header('Content-Type: application/json');

require_once '../../includes/config.php';
require_once '../../classes/Database.php';
require_once '../../classes/Vehicle.php';

// Verifica se o parâmetro brand_id foi fornecido
if (!isset($_GET['brand_id']) || empty($_GET['brand_id'])) {
    echo json_encode([]);
    exit;
}

$brand_id = (int)$_GET['brand_id'];

try {
    $vehicleObj = new Vehicle();
    $models = $vehicleObj->getModelsByBrand($brand_id);
    
    echo json_encode($models);
} catch (Exception $e) {
    if (DEBUG_MODE) {
        echo json_encode(['error' => $e->getMessage()]);
    } else {
        echo json_encode([]);
    }
}
?>
