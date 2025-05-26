<?php
/**
 * Classe Listing para gerenciamento de anúncios
 */
class Listing {
    private $db;
    
    /**
     * Construtor - inicializa a conexão com o banco de dados
     */
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Cria um novo anúncio
     * @param array $data Dados do anúncio
     * @return boolean|int ID do anúncio ou false
     */
    public function create($data) {
        // Inicia uma transação
        $this->db->beginTransaction();
        
        try {
            // Insere o veículo
            $this->db->query('INSERT INTO vehicles (brand_id, model_id, year_manufacture, year_model, mileage, transmission, fuel_type, color, doors, engine, power, features) 
                             VALUES (:brand_id, :model_id, :year_manufacture, :year_model, :mileage, :transmission, :fuel_type, :color, :doors, :engine, :power, :features)');
            
            $this->db->bind(':brand_id', $data['brand_id']);
            $this->db->bind(':model_id', $data['model_id']);
            $this->db->bind(':year_manufacture', $data['year_manufacture']);
            $this->db->bind(':year_model', $data['year_model']);
            $this->db->bind(':mileage', $data['mileage']);
            $this->db->bind(':transmission', $data['transmission']);
            $this->db->bind(':fuel_type', $data['fuel_type']);
            $this->db->bind(':color', $data['color']);
            $this->db->bind(':doors', $data['doors']);
            $this->db->bind(':engine', $data['engine'] ?? null);
            $this->db->bind(':power', $data['power'] ?? null);
            $this->db->bind(':features', $data['features'] ?? null);
            
            $this->db->execute();
            $vehicle_id = $this->db->lastInsertId();
            
            // Insere o anúncio
            $this->db->query('INSERT INTO listings (user_id, vehicle_id, title, description, price, location, status, featured, verified) 
                             VALUES (:user_id, :vehicle_id, :title, :description, :price, :location, :status, :featured, :verified)');
            
            $this->db->bind(':user_id', $data['user_id']);
            $this->db->bind(':vehicle_id', $vehicle_id);
            $this->db->bind(':title', $data['title']);
            $this->db->bind(':description', $data['description']);
            $this->db->bind(':price', $data['price']);
            $this->db->bind(':location', $data['location']);
            $this->db->bind(':status', $data['status'] ?? 'pending');
            $this->db->bind(':featured', $data['featured'] ?? false);
            $this->db->bind(':verified', $data['verified'] ?? false);
            
            $this->db->execute();
            $listing_id = $this->db->lastInsertId();
            
            // Confirma a transação
            $this->db->commit();
            
            return $listing_id;
        } catch (Exception $e) {
            // Reverte a transação em caso de erro
            $this->db->rollBack();
            if (DEBUG_MODE) {
                echo 'Error: ' . $e->getMessage();
            }
            return false;
        }
    }
    
    /**
     * Atualiza um anúncio existente
     * @param array $data Dados do anúncio
     * @return boolean
     */
    public function update($data) {
        // Inicia uma transação
        $this->db->beginTransaction();
        
        try {
            // Atualiza o veículo
            $this->db->query('UPDATE vehicles SET 
                             brand_id = :brand_id, 
                             model_id = :model_id, 
                             year_manufacture = :year_manufacture, 
                             year_model = :year_model, 
                             mileage = :mileage, 
                             transmission = :transmission, 
                             fuel_type = :fuel_type, 
                             color = :color, 
                             doors = :doors, 
                             engine = :engine, 
                             power = :power, 
                             features = :features 
                             WHERE id = :vehicle_id');
            
            $this->db->bind(':vehicle_id', $data['vehicle_id']);
            $this->db->bind(':brand_id', $data['brand_id']);
            $this->db->bind(':model_id', $data['model_id']);
            $this->db->bind(':year_manufacture', $data['year_manufacture']);
            $this->db->bind(':year_model', $data['year_model']);
            $this->db->bind(':mileage', $data['mileage']);
            $this->db->bind(':transmission', $data['transmission']);
            $this->db->bind(':fuel_type', $data['fuel_type']);
            $this->db->bind(':color', $data['color']);
            $this->db->bind(':doors', $data['doors']);
            $this->db->bind(':engine', $data['engine'] ?? null);
            $this->db->bind(':power', $data['power'] ?? null);
            $this->db->bind(':features', $data['features'] ?? null);
            
            $this->db->execute();
            
            // Atualiza o anúncio
            $this->db->query('UPDATE listings SET 
                             title = :title, 
                             description = :description, 
                             price = :price, 
                             location = :location, 
                             status = :status 
                             WHERE id = :id');
            
            $this->db->bind(':id', $data['id']);
            $this->db->bind(':title', $data['title']);
            $this->db->bind(':description', $data['description']);
            $this->db->bind(':price', $data['price']);
            $this->db->bind(':location', $data['location']);
            $this->db->bind(':status', $data['status'] ?? 'pending');
            
            $this->db->execute();
            
            // Confirma a transação
            $this->db->commit();
            
            return true;
        } catch (Exception $e) {
            // Reverte a transação em caso de erro
            $this->db->rollBack();
            if (DEBUG_MODE) {
                echo 'Error: ' . $e->getMessage();
            }
            return false;
        }
    }
    
    /**
     * Obtém um anúncio pelo ID
     * @param int $id ID do anúncio
     * @return mixed Dados do anúncio ou false
     */
    public function getListingById($id) {
        $this->db->query('SELECT l.*, v.*, b.name as brand_name, m.name as model_name, u.name as seller_name, u.phone as seller_phone, u.email as seller_email, u.city as seller_city, u.state as seller_state
                         FROM listings l
                         JOIN vehicles v ON l.vehicle_id = v.id
                         JOIN brands b ON v.brand_id = b.id
                         JOIN models m ON v.model_id = m.id
                         JOIN users u ON l.user_id = u.id
                         WHERE l.id = :id');
        
        $this->db->bind(':id', $id);
        
        $listing = $this->db->single();
        
        if ($listing) {
            // Incrementa o contador de visualizações
            $this->incrementViews($id);
            
            // Obtém as fotos do anúncio
            $photos = $this->getListingPhotos($id);
            $listing['photos'] = $photos;
            
            return $listing;
        } else {
            return false;
        }
    }
    
    /**
     * Obtém as fotos de um anúncio
     * @param int $listing_id ID do anúncio
     * @return array Fotos do anúncio
     */
    public function getListingPhotos($listing_id) {
        $this->db->query('SELECT * FROM photos WHERE listing_id = :listing_id ORDER BY is_primary DESC, order_position ASC');
        $this->db->bind(':listing_id', $listing_id);
        
        return $this->db->resultSet();
    }
    
    /**
     * Incrementa o contador de visualizações de um anúncio
     * @param int $id ID do anúncio
     */
    private function incrementViews($id) {
        $this->db->query('UPDATE listings SET views = views + 1 WHERE id = :id');
        $this->db->bind(':id', $id);
        $this->db->execute();
    }
    
    /**
     * Obtém anúncios em destaque
     * @param int $limit Limite de anúncios
     * @return array Anúncios em destaque
     */
    public function getFeaturedListings($limit = 6) {
        $this->db->query('SELECT l.id, l.title, l.price, l.location, l.views, l.created_at, 
                         v.year_manufacture, v.year_model, v.mileage, v.transmission, v.fuel_type, v.color,
                         b.name as brand_name, m.name as model_name,
                         (SELECT file_path FROM photos WHERE listing_id = l.id AND is_primary = 1 LIMIT 1) as primary_photo
                         FROM listings l
                         JOIN vehicles v ON l.vehicle_id = v.id
                         JOIN brands b ON v.brand_id = b.id
                         JOIN models m ON v.model_id = m.id
                         WHERE l.status = "active" AND l.featured = 1
                         ORDER BY l.created_at DESC
                         LIMIT :limit');
        
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        
        return $this->db->resultSet();
    }
    
    /**
     * Obtém anúncios recentes
     * @param int $limit Limite de anúncios
     * @return array Anúncios recentes
     */
    public function getRecentListings($limit = 12) {
        $this->db->query('SELECT l.id, l.title, l.price, l.location, l.views, l.created_at, l.verified,
                         v.year_manufacture, v.year_model, v.mileage, v.transmission, v.fuel_type, v.color,
                         b.name as brand_name, m.name as model_name,
                         (SELECT file_path FROM photos WHERE listing_id = l.id AND is_primary = 1 LIMIT 1) as primary_photo
                         FROM listings l
                         JOIN vehicles v ON l.vehicle_id = v.id
                         JOIN brands b ON v.brand_id = b.id
                         JOIN models m ON v.model_id = m.id
                         WHERE l.status = "active"
                         ORDER BY l.created_at DESC
                         LIMIT :limit');
        
        $this->db->bind(':limit', $limit, PDO::PARAM_INT);
        
        return $this->db->resultSet();
    }
    
    /**
     * Busca anúncios com filtros
     * @param array $filters Filtros de busca
     * @param int $page Página atual
     * @param int $per_page Itens por página
     * @return array Anúncios encontrados e total
     */
    public function searchListings($filters = [], $page = 1, $per_page = ITEMS_PER_PAGE) {
        $conditions = ['l.status = "active"'];
        $params = [];
        
        // Filtro por marca
        if (!empty($filters['brand_id'])) {
            $conditions[] = 'v.brand_id = :brand_id';
            $params[':brand_id'] = $filters['brand_id'];
        }
        
        // Filtro por modelo
        if (!empty($filters['model_id'])) {
            $conditions[] = 'v.model_id = :model_id';
            $params[':model_id'] = $filters['model_id'];
        }
        
        // Filtro por ano
        if (!empty($filters['year_min'])) {
            $conditions[] = 'v.year_manufacture >= :year_min';
            $params[':year_min'] = $filters['year_min'];
        }
        if (!empty($filters['year_max'])) {
            $conditions[] = 'v.year_manufacture <= :year_max';
            $params[':year_max'] = $filters['year_max'];
        }
        
        // Filtro por preço
        if (!empty($filters['price_min'])) {
            $conditions[] = 'l.price >= :price_min';
            $params[':price_min'] = $filters['price_min'];
        }
        if (!empty($filters['price_max'])) {
            $conditions[] = 'l.price <= :price_max';
            $params[':price_max'] = $filters['price_max'];
        }
        
        // Filtro por quilometragem
        if (!empty($filters['mileage_max'])) {
            $conditions[] = 'v.mileage <= :mileage_max';
            $params[':mileage_max'] = $filters['mileage_max'];
        }
        
        // Filtro por combustível
        if (!empty($filters['fuel_type'])) {
            $conditions[] = 'v.fuel_type = :fuel_type';
            $params[':fuel_type'] = $filters['fuel_type'];
        }
        
        // Filtro por câmbio
        if (!empty($filters['transmission'])) {
            $conditions[] = 'v.transmission = :transmission';
            $params[':transmission'] = $filters['transmission'];
        }
        
        // Filtro por termo de busca
        if (!empty($filters['search'])) {
            $conditions[] = '(l.title LIKE :search OR l.description LIKE :search OR b.name LIKE :search OR m.name LIKE :search)';
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        // Monta a cláusula WHERE
        $where_clause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
        
        // Ordenação
        $order_by = 'l.created_at DESC';
        if (!empty($filters['order_by'])) {
            switch ($filters['order_by']) {
                case 'price_asc':
                    $order_by = 'l.price ASC';
                    break;
                case 'price_desc':
                    $order_by = 'l.price DESC';
                    break;
                case 'year_desc':
                    $order_by = 'v.year_manufacture DESC';
                    break;
                case 'mileage_asc':
                    $order_by = 'v.mileage ASC';
                    break;
            }
        }
        
        // Calcula o offset para paginação
        $offset = ($page - 1) * $per_page;
        
        // Query para contar o total de resultados
        $count_query = "SELECT COUNT(*) as total
                       FROM listings l
                       JOIN vehicles v ON l.vehicle_id = v.id
                       JOIN brands b ON v.brand_id = b.id
                       JOIN models m ON v.model_id = m.id
                       $where_clause";
        
        $this->db->query($count_query);
        
        // Vincula os parâmetros
        foreach ($params as $param => $value) {
            $this->db->bind($param, $value);
        }
        
        $count_result = $this->db->single();
        $total = $count_result['total'];
        
        // Query para buscar os anúncios
        $query = "SELECT l.id, l.title, l.price, l.location, l.views, l.created_at, l.verified,
                 v.year_manufacture, v.year_model, v.mileage, v.transmission, v.fuel_type, v.color,
                 b.name as brand_name, m.name as model_name,
                 (SELECT file_path FROM photos WHERE listing_id = l.id AND is_primary = 1 LIMIT 1) as primary_photo
                 FROM listings l
                 JOIN vehicles v ON l.vehicle_id = v.id
                 JOIN brands b ON v.brand_id = b.id
                 JOIN models m ON v.model_id = m.id
                 $where_clause
                 ORDER BY $order_by
                 LIMIT :offset, :per_page";
        
        $this->db->query($query);
        
        // Vincula os parâmetros
        foreach ($params as $param => $value) {
            $this->db->bind($param, $value);
        }
        $this->db->bind(':offset', $offset, PDO::PARAM_INT);
        $this->db->bind(':per_page', $per_page, PDO::PARAM_INT);
        
        $listings = $this->db->resultSet();
        
        return [
            'listings' => $listings,
            'total' => $total,
            'page' => $page,
            'per_page' => $per_page,
            'total_pages' => ceil($total / $per_page)
        ];
    }
    
    /**
     * Adiciona uma foto a um anúncio
     * @param int $listing_id ID do anúncio
     * @param string $file_name Nome do arquivo
     * @param string $file_path Caminho do arquivo
     * @param int $order_position Posição da ordem
     * @param bool $is_primary Se é a foto principal
     * @return boolean|int ID da foto ou false
     */
    public function addPhoto($listing_id, $file_name, $file_path, $order_position = 0, $is_primary = false) {
        $this->db->query('INSERT INTO photos (listing_id, file_name, file_path, order_position, is_primary) 
                         VALUES (:listing_id, :file_name, :file_path, :order_position, :is_primary)');
        
        $this->db->bind(':listing_id', $listing_id);
        $this->db->bind(':file_name', $file_name);
        $this->db->bind(':file_path', $file_path);
        $this->db->bind(':order_position', $order_position);
        $this->db->bind(':is_primary', $is_primary);
        
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }
    
    /**
     * Define uma foto como principal
     * @param int $photo_id ID da foto
     * @param int $listing_id ID do anúncio
     * @return boolean
     */
    public function setPrimaryPhoto($photo_id, $listing_id) {
        // Inicia uma transação
        $this->db->beginTransaction();
        
        try {
            // Remove a marcação de foto principal de todas as fotos do anúncio
            $this->db->query('UPDATE photos SET is_primary = 0 WHERE listing_id = :listing_id');
            $this->db->bind(':listing_id', $listing_id);
            $this->db->execute();
            
            // Define a foto selecionada como principal
            $this->db->query('UPDATE photos SET is_primary = 1 WHERE id = :photo_id AND listing_id = :listing_id');
            $this->db->bind(':photo_id', $photo_id);
            $this->db->bind(':listing_id', $listing_id);
            $this->db->execute();
            
            // Confirma a transação
            $this->db->commit();
            
            return true;
        } catch (Exception $e) {
            // Reverte a transação em caso de erro
            $this->db->rollBack();
            if (DEBUG_MODE) {
                echo 'Error: ' . $e->getMessage();
            }
            return false;
        }
    }
    
    /**
     * Remove uma foto
     * @param int $photo_id ID da foto
     * @param int $listing_id ID do anúncio (para verificação)
     * @return boolean
     */
    public function removePhoto($photo_id, $listing_id) {
        // Verifica se a foto pertence ao anúncio
        $this->db->query('SELECT file_path, is_primary FROM photos WHERE id = :photo_id AND listing_id = :listing_id');
        $this->db->bind(':photo_id', $photo_id);
        $this->db->bind(':listing_id', $listing_id);
        
        $photo = $this->db->single();
        
        if (!$photo) {
            return false;
        }
        
        // Remove o arquivo físico
        $file_path = $_SERVER['DOCUMENT_ROOT'] . $photo['file_path'];
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        
        // Remove o registro do banco
        $this->db->query('DELETE FROM photos WHERE id = :photo_id');
        $this->db->bind(':photo_id', $photo_id);
        
        $result = $this->db->execute();
        
        // Se a foto removida era a principal, define outra como principal
        if ($result && $photo['is_primary']) {
            $this->db->query('UPDATE photos SET is_primary = 1 WHERE listing_id = :listing_id ORDER BY order_position ASC LIMIT 1');
            $this->db->bind(':listing_id', $listing_id);
            $this->db->execute();
        }
        
        return $result;
    }
    
    /**
     * Obtém anúncios de um usuário
     * @param int $user_id ID do usuário
     * @return array Anúncios do usuário
     */
    public function getUserListings($user_id) {
        $this->db->query('SELECT l.id, l.title, l.price, l.status, l.views, l.created_at,
                         v.year_manufacture, v.year_model,
                         b.name as brand_name, m.name as model_name,
                         (SELECT file_path FROM photos WHERE listing_id = l.id AND is_primary = 1 LIMIT 1) as primary_photo
                         FROM listings l
                         JOIN vehicles v ON l.vehicle_id = v.id
                         JOIN brands b ON v.brand_id = b.id
                         JOIN models m ON v.model_id = m.id
                         WHERE l.user_id = :user_id
                         ORDER BY l.created_at DESC');
        
        $this->db->bind(':user_id', $user_id);
        
        return $this->db->resultSet();
    }
    
    /**
     * Marca um anúncio como vendido
     * @param int $id ID do anúncio
     * @param int $user_id ID do usuário (para verificação)
     * @return boolean
     */
    public function markAsSold($id, $user_id) {
        $this->db->query('UPDATE listings SET status = "sold" WHERE id = :id AND user_id = :user_id');
        $this->db->bind(':id', $id);
        $this->db->bind(':user_id', $user_id);
        
        return $this->db->execute();
    }
    
    /**
     * Desativa um anúncio
     * @param int $id ID do anúncio
     * @param int $user_id ID do usuário (para verificação)
     * @return boolean
     */
    public function deactivateListing($id, $user_id) {
        $this->db->query('UPDATE listings SET status = "inactive" WHERE id = :id AND user_id = :user_id');
        $this->db->bind(':id', $id);
        $this->db->bind(':user_id', $user_id);
        
        return $this->db->execute();
    }
    
    /**
     * Reativa um anúncio
     * @param int $id ID do anúncio
     * @param int $user_id ID do usuário (para verificação)
     * @return boolean
     */
    public function reactivateListing($id, $user_id) {
        $this->db->query('UPDATE listings SET status = "active" WHERE id = :id AND user_id = :user_id');
        $this->db->bind(':id', $id);
        $this->db->bind(':user_id', $user_id);
        
        return $this->db->execute();
    }
    
    /**
     * Exclui um anúncio
     * @param int $id ID do anúncio
     * @param int $user_id ID do usuário (para verificação)
     * @return boolean
     */
    public function deleteListing($id, $user_id) {
        // Verifica se o anúncio pertence ao usuário
        $this->db->query('SELECT vehicle_id FROM listings WHERE id = :id AND user_id = :user_id');
        $this->db->bind(':id', $id);
        $this->db->bind(':user_id', $user_id);
        
        $listing = $this->db->single();
        
        if (!$listing) {
            return false;
        }
        
        $vehicle_id = $listing['vehicle_id'];
        
        // Inicia uma transação
        $this->db->beginTransaction();
        
        try {
            // Remove as fotos
            $this->db->query('SELECT file_path FROM photos WHERE listing_id = :listing_id');
            $this->db->bind(':listing_id', $id);
            $photos = $this->db->resultSet();
            
            foreach ($photos as $photo) {
                $file_path = $_SERVER['DOCUMENT_ROOT'] . $photo['file_path'];
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
            
            // Remove os registros de fotos
            $this->db->query('DELETE FROM photos WHERE listing_id = :listing_id');
            $this->db->bind(':listing_id', $id);
            $this->db->execute();
            
            // Remove o histórico de preços
            $this->db->query('DELETE FROM price_history WHERE listing_id = :listing_id');
            $this->db->bind(':listing_id', $id);
            $this->db->execute();
            
            // Remove os favoritos
            $this->db->query('DELETE FROM favorites WHERE listing_id = :listing_id');
            $this->db->bind(':listing_id', $id);
            $this->db->execute();
            
            // Remove as mensagens
            $this->db->query('DELETE FROM messages WHERE listing_id = :listing_id');
            $this->db->bind(':listing_id', $id);
            $this->db->execute();
            
            // Remove o anúncio
            $this->db->query('DELETE FROM listings WHERE id = :id');
            $this->db->bind(':id', $id);
            $this->db->execute();
            
            // Remove o veículo
            $this->db->query('DELETE FROM vehicles WHERE id = :id');
            $this->db->bind(':id', $vehicle_id);
            $this->db->execute();
            
            // Confirma a transação
            $this->db->commit();
            
            return true;
        } catch (Exception $e) {
            // Reverte a transação em caso de erro
            $this->db->rollBack();
            if (DEBUG_MODE) {
                echo 'Error: ' . $e->getMessage();
            }
            return false;
        }
    }
}
