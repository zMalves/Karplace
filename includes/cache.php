
<?php
/**
 * Sistema de cache simples para melhorar performance
 */
class Cache {
    private static $cache_dir = 'cache/';
    private static $default_ttl = 3600; // 1 hora
    
    /**
     * Inicializa o diretório de cache
     */
    public static function init() {
        if (!file_exists(self::$cache_dir)) {
            mkdir(self::$cache_dir, 0755, true);
        }
    }
    
    /**
     * Obtém um item do cache
     */
    public static function get($key) {
        self::init();
        
        $file = self::$cache_dir . md5($key) . '.cache';
        
        if (!file_exists($file)) {
            return false;
        }
        
        $data = unserialize(file_get_contents($file));
        
        // Verifica se o cache expirou
        if ($data['expires'] < time()) {
            unlink($file);
            return false;
        }
        
        return $data['value'];
    }
    
    /**
     * Define um item no cache
     */
    public static function set($key, $value, $ttl = null) {
        self::init();
        
        if ($ttl === null) {
            $ttl = self::$default_ttl;
        }
        
        $file = self::$cache_dir . md5($key) . '.cache';
        
        $data = [
            'value' => $value,
            'expires' => time() + $ttl
        ];
        
        file_put_contents($file, serialize($data), LOCK_EX);
    }
    
    /**
     * Remove um item do cache
     */
    public static function delete($key) {
        $file = self::$cache_dir . md5($key) . '.cache';
        
        if (file_exists($file)) {
            unlink($file);
        }
    }
    
    /**
     * Limpa todo o cache
     */
    public static function clear() {
        $files = glob(self::$cache_dir . '*.cache');
        foreach ($files as $file) {
            unlink($file);
        }
    }
}
