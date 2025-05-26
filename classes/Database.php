<?php
/**
 * Classe Database para conexão e operações com o banco de dados
 */
class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;
    
    private $conn;
    private $error;
    private $stmt;
    
    /**
     * Construtor - estabelece conexão com o banco de dados
     */
    public function __construct() {
        // Set DSN (Data Source Name)
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';charset=utf8mb4';
        
        // Set options
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        );
        
        // Create PDO instance
        try {
            $this->conn = new PDO($dsn, $this->user, $this->pass, $options);
        } catch(PDOException $e) {
            $this->error = $e->getMessage();
            if (DEBUG_MODE) {
                echo 'Database Connection Error: ' . $this->error;
            } else {
                echo 'Database Connection Error. Please try again later.';
            }
            exit;
        }
    }
    
    /**
     * Prepara uma query SQL
     * @param string $sql Query SQL a ser preparada
     */
    public function query($sql) {
        $this->stmt = $this->conn->prepare($sql);
    }
    
    /**
     * Vincula um valor a um parâmetro
     * @param string $param Nome do parâmetro
     * @param mixed $value Valor a ser vinculado
     * @param mixed $type Tipo do parâmetro (opcional)
     */
    public function bind($param, $value, $type = null) {
        if (is_null($type)) {
            switch (true) {
                case is_int($value):
                    $type = PDO::PARAM_INT;
                    break;
                case is_bool($value):
                    $type = PDO::PARAM_BOOL;
                    break;
                case is_null($value):
                    $type = PDO::PARAM_NULL;
                    break;
                default:
                    $type = PDO::PARAM_STR;
            }
        }
        
        $this->stmt->bindValue($param, $value, $type);
    }
    
    /**
     * Executa a query preparada
     * @return boolean
     */
    public function execute() {
        try {
            return $this->stmt->execute();
        } catch(PDOException $e) {
            $this->error = $e->getMessage();
            if (DEBUG_MODE) {
                echo 'Query Error: ' . $this->error;
            }
            return false;
        }
    }
    
    /**
     * Retorna um único registro
     * @return object
     */
    public function single() {
        $this->execute();
        return $this->stmt->fetch();
    }
    
    /**
     * Retorna todos os registros
     * @return array
     */
    public function resultSet() {
        $this->execute();
        return $this->stmt->fetchAll();
    }
    
    /**
     * Retorna o número de linhas afetadas
     * @return int
     */
    public function rowCount() {
        return $this->stmt->rowCount();
    }
    
    /**
     * Retorna o último ID inserido
     * @return int
     */
    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }
    
    /**
     * Inicia uma transação
     */
    public function beginTransaction() {
        return $this->conn->beginTransaction();
    }
    
    /**
     * Confirma uma transação
     */
    public function commit() {
        return $this->conn->commit();
    }
    
    /**
     * Reverte uma transação
     */
    public function rollBack() {
        return $this->conn->rollBack();
    }
    
    /**
     * Retorna o erro da última operação
     * @return string
     */
    public function getError() {
        return $this->error;
    }
}
