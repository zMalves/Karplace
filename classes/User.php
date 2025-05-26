<?php
/**
 * Classe User para gerenciamento de usuários
 */
class User {
    private $db;
    
    /**
     * Construtor - inicializa a conexão com o banco de dados
     */
    public function __construct() {
        $this->db = new Database();
    }
    
    /**
     * Registra um novo usuário
     * @param array $data Dados do usuário
     * @return boolean
     */
    public function register($data) {
        // Prepara a query
        $this->db->query('INSERT INTO users (name, email, password, phone, user_type) VALUES (:name, :email, :password, :phone, :user_type)');
        
        // Hash da senha
        $password_hash = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => HASH_COST]);
        
        // Vincula os parâmetros
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':email', $data['email']);
        $this->db->bind(':password', $password_hash);
        $this->db->bind(':phone', $data['phone'] ?? null);
        $this->db->bind(':user_type', $data['user_type'] ?? 'buyer');
        
        // Executa a query
        if ($this->db->execute()) {
            return $this->db->lastInsertId();
        } else {
            return false;
        }
    }
    
    /**
     * Verifica se um email já está em uso
     * @param string $email Email a ser verificado
     * @return boolean
     */
    public function emailExists($email) {
        $this->db->query('SELECT id FROM users WHERE email = :email');
        $this->db->bind(':email', $email);
        
        $row = $this->db->single();
        
        return $row ? true : false;
    }
    
    /**
     * Autentica um usuário
     * @param string $email Email do usuário
     * @param string $password Senha do usuário
     * @return mixed Dados do usuário ou false
     */
    public function login($email, $password) {
        $this->db->query('SELECT id, name, email, password, user_type, status FROM users WHERE email = :email');
        $this->db->bind(':email', $email);
        
        $row = $this->db->single();
        
        if (!$row) {
            return false;
        }
        
        // Verifica se a conta está ativa
        if ($row['status'] !== 'active') {
            return false;
        }
        
        // Verifica a senha
        $hashed_password = $row['password'];
        if (password_verify($password, $hashed_password)) {
            unset($row['password']); // Remove a senha do array
            return $row;
        } else {
            return false;
        }
    }
    
    /**
     * Obtém um usuário pelo ID
     * @param int $id ID do usuário
     * @return mixed Dados do usuário ou false
     */
    public function getUserById($id) {
        $this->db->query('SELECT id, name, email, phone, user_type, address, city, state, profile_image, created_at FROM users WHERE id = :id');
        $this->db->bind(':id', $id);
        
        $row = $this->db->single();
        
        return $row ? $row : false;
    }
    
    /**
     * Atualiza os dados de um usuário
     * @param array $data Dados do usuário
     * @return boolean
     */
    public function update($data) {
        // Prepara a query
        $this->db->query('UPDATE users SET name = :name, phone = :phone, address = :address, city = :city, state = :state WHERE id = :id');
        
        // Vincula os parâmetros
        $this->db->bind(':id', $data['id']);
        $this->db->bind(':name', $data['name']);
        $this->db->bind(':phone', $data['phone'] ?? null);
        $this->db->bind(':address', $data['address'] ?? null);
        $this->db->bind(':city', $data['city'] ?? null);
        $this->db->bind(':state', $data['state'] ?? null);
        
        // Executa a query
        return $this->db->execute();
    }
    
    /**
     * Atualiza a senha de um usuário
     * @param int $id ID do usuário
     * @param string $password Nova senha
     * @return boolean
     */
    public function updatePassword($id, $password) {
        // Hash da senha
        $password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => HASH_COST]);
        
        // Prepara a query
        $this->db->query('UPDATE users SET password = :password WHERE id = :id');
        
        // Vincula os parâmetros
        $this->db->bind(':id', $id);
        $this->db->bind(':password', $password_hash);
        
        // Executa a query
        return $this->db->execute();
    }
    
    /**
     * Atualiza a imagem de perfil de um usuário
     * @param int $id ID do usuário
     * @param string $image_path Caminho da imagem
     * @return boolean
     */
    public function updateProfileImage($id, $image_path) {
        // Prepara a query
        $this->db->query('UPDATE users SET profile_image = :profile_image WHERE id = :id');
        
        // Vincula os parâmetros
        $this->db->bind(':id', $id);
        $this->db->bind(':profile_image', $image_path);
        
        // Executa a query
        return $this->db->execute();
    }
}
