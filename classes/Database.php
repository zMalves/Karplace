<?php
/**
 * Classe Database para gerenciamento de conexão e operações com o banco de dados
 */
class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $dbname = DB_NAME;
    
    private $dbh;
    private $stmt;
    private $error;
    
    /**
     * Construtor - estabelece conexão com o banco de dados
     */
    public function __construct() {
        // DSN
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->dbname . ';charset=utf8mb4';
        
        // Opções PDO
        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
        );
        
        // Criar instância PDO
        try {
            $this->dbh = new PDO($dsn, $this->user, $this->pass, $options);
        } catch(PDOException $e) {
            $this->error = $e->getMessage();
            if (DEBUG_MODE) {
                echo 'Database Error: ' . $this->error;
            }
        }
    }
    
    /**
     * Prepara uma query SQL
     * @param string $sql Query SQL
     */
    public function query($sql) {
        $this->stmt = $this->dbh->prepare($sql);
    }
    
    /**
     * Vincula valores aos parâmetros da query
     * @param string $param Parâmetro
     * @param mixed $value Valor
     * @param mixed $type Tipo do parâmetro
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
        return $this->stmt->execute();
    }
    
    /**
     * Retorna múltiplos resultados como array
     * @return array
     */
    public function resultSet() {
        $this->execute();
        return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    /**
     * Retorna um único resultado
     * @return mixed
     */
    public function single() {
        $this->execute();
        return $this->stmt->fetch(PDO::FETCH_ASSOC);
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
        return $this->dbh->lastInsertId();
    }
    
    /**
     * Inicia uma transação
     * @return boolean
     */
    public function beginTransaction() {
        return $this->dbh->beginTransaction();
    }
    
    /**
     * Confirma uma transação
     * @return boolean
     */
    public function commit() {
        return $this->dbh->commit();
    }
    
    /**
     * Reverte uma transação
     * @return boolean
     */
    public function rollBack() {
        return $this->dbh->rollBack();
    }
    
    /**
     * Debug da última query
     */
    public function debugDumpParams() {
        return $this->stmt->debugDumpParams();
    }
}
?>
