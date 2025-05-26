<?php
// API para obter modelos de uma marca
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../classes/Database.php';
require_once '../classes/Vehicle.php';

// Verifica se o ID da marca foi fornecido
if (!isset($_GET['brand_id']) || empty($_GET['brand_id'])) {
    echo json_encode([]);
    exit;
}

$brand_id = (int)$_GET['brand_id'];

// Obtém os modelos da marca
$vehicleObj = new Vehicle();
$models = $vehicleObj->getModelsByBrand($brand_id);

// Retorna os modelos em formato JSON
header('Content-Type: application/json');
echo json_encode($models);
