<?php
/**
 * Classe Vehicle para gerenciamento de veículos, marcas e modelos
 */
class Vehicle {
    private $db;
    
    /**
     * Construtor - inicializa a conexão com o banco de dados
     */
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Obtém todas as marcas ativas
     * @return array Marcas disponíveis
     */
    public function getAllBrands() {
        $this->db->query('SELECT * FROM brands WHERE status = "active" ORDER BY name ASC');
        return $this->db->resultSet();
    }
    
    /**
     * Obtém modelos de uma marca específica
     * @param int $brand_id ID da marca
     * @return array Modelos da marca
     */
    public function getModelsByBrand($brand_id) {
        $this->db->query('SELECT * FROM models WHERE brand_id = :brand_id AND status = "active" ORDER BY name ASC');
        $this->db->bind(':brand_id', $brand_id);
        return $this->db->resultSet();
    }
    
    /**
     * Obtém os tipos de combustível disponíveis
     * @return array Tipos de combustível
     */
    public function getAvailableFuelTypes() {
        return [
            'gasoline' => 'Gasolina',
            'ethanol' => 'Etanol',
            'flex' => 'Flex',
            'diesel' => 'Diesel',
            'electric' => 'Elétrico',
            'hybrid' => 'Híbrido'
        ];
    }
    
    /**
     * Obtém os tipos de transmissão disponíveis
     * @return array Tipos de transmissão
     */
    public function getAvailableTransmissions() {
        return [
            'manual' => 'Manual',
            'automatic' => 'Automático',
            'cvt' => 'CVT',
            'semi-automatic' => 'Semi-automático'
        ];
    }
    
    /**
     * Cria uma nova marca
     * @param string $name Nome da marca
     * @param string $logo Logo da marca (opcional)
     * @return boolean|int ID da marca ou false
     */
    public function createBrand($name, $logo = null) {
        $this->db->query('INSERT INTO brands (name, logo) VALUES (:name, :logo)');
        $this->db->bind(':name', $name);
        $this->db->bind(':logo', $logo);
        
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    
    /**
     * Cria um novo modelo
     * @param int $brand_id ID da marca
     * @param string $name Nome do modelo
     * @return boolean|int ID do modelo ou false
     */
    public function createModel($brand_id, $name) {
        $this->db->query('INSERT INTO models (brand_id, name) VALUES (:brand_id, :name)');
        $this->db->bind(':brand_id', $brand_id);
        $this->db->bind(':name', $name);
        
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    
    /**
     * Obtém uma marca pelo ID
     * @param int $id ID da marca
     * @return mixed Dados da marca ou false
     */
    public function getBrandById($id) {
        $this->db->query('SELECT * FROM brands WHERE id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    /**
     * Obtém um modelo pelo ID
     * @param int $id ID do modelo
     * @return mixed Dados do modelo ou false
     */
    public function getModelById($id) {
        $this->db->query('SELECT m.*, b.name as brand_name FROM models m JOIN brands b ON m.brand_id = b.id WHERE m.id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    /**
     * Obtém um veículo pelo ID
     * @param int $id ID do veículo
     * @return mixed Dados do veículo ou false
     */
    public function getVehicleById($id) {
        $this->db->query('SELECT v.*, b.name as brand_name, m.name as model_name 
                         FROM vehicles v 
                         JOIN brands b ON v.brand_id = b.id 
                         JOIN models m ON v.model_id = m.id 
                         WHERE v.id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
    
    /**
     * Obtém anos de fabricação disponíveis (para filtros)
     * @return array Anos disponíveis
     */
    public function getAvailableYears() {
        $this->db->query('SELECT DISTINCT year_manufacture FROM vehicles ORDER BY year_manufacture DESC');
        $years = $this->db->resultSet();
        $result = [];
        foreach ($years as $year) {
            $result[] = $year['year_manufacture'];
        }
        return $result;
    }
    
    /**
     * Obtém tipos de combustível disponíveis (para filtros)
     * @return array Tipos de combustível
     */
    public function getAvailableFuelTypes() {
        return [
            'gasoline' => 'Gasolina',
            'ethanol' => 'Etanol',
            'flex' => 'Flex',
            'diesel' => 'Diesel',
            'electric' => 'Elétrico',
            'hybrid' => 'Híbrido'
        ];
    }
    
    /**
     * Obtém tipos de transmissão disponíveis (para filtros)
     * @return array Tipos de transmissão
     */
    public function getAvailableTransmissions() {
        return [
            'manual' => 'Manual',
            'automatic' => 'Automático',
            'cvt' => 'CVT',
            'semi-automatic' => 'Semi-automático'
        ];
    }
    
    /**
     * Obtém cores disponíveis (para filtros)
     * @return array Cores disponíveis
     */
    public function getAvailableColors() {
        $this->db->query('SELECT DISTINCT color FROM vehicles ORDER BY color ASC');
        $colors = $this->db->resultSet();
        $result = [];
        foreach ($colors as $color) {
            $result[] = $color['color'];
        }
        return $result;
    }
}
?>