
<?php
/**
 * Classe para logging de erros e eventos
 */
class Logger {
    private static $log_file = 'logs/app.log';
    
    /**
     * Inicializa o sistema de log
     */
    public static function init() {
        $log_dir = dirname(self::$log_file);
        if (!file_exists($log_dir)) {
            mkdir($log_dir, 0755, true);
        }
    }
    
    /**
     * Log de erro
     */
    public static function error($message, $context = []) {
        self::log('ERROR', $message, $context);
    }
    
    /**
     * Log de warning
     */
    public static function warning($message, $context = []) {
        self::log('WARNING', $message, $context);
    }
    
    /**
     * Log de info
     */
    public static function info($message, $context = []) {
        self::log('INFO', $message, $context);
    }
    
    /**
     * Log de debug
     */
    public static function debug($message, $context = []) {
        if (DEBUG_MODE) {
            self::log('DEBUG', $message, $context);
        }
    }
    
    /**
     * Método principal de log
     */
    private static function log($level, $message, $context = []) {
        self::init();
        
        $timestamp = date('Y-m-d H:i:s');
        $context_str = empty($context) ? '' : ' | Context: ' . json_encode($context);
        $log_entry = "[{$timestamp}] {$level}: {$message}{$context_str}" . PHP_EOL;
        
        file_put_contents(self::$log_file, $log_entry, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Log de exceção
     */
    public static function exception($exception) {
        $message = "Exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine();
        self::error($message, ['trace' => $exception->getTraceAsString()]);
    }
}
