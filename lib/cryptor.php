<?php

/**
 * Cryptor class
 *
 * @author Marshall
 */
class cryptor {
    
    const ADDON_NAME = 'cryptor';
    const IV_DELIMITER = '|~~|';
    const LOG_ENABLED = true;
    
    private static $cipher = null;
    private static $key = null;
    private static $hashAlgorithm = null;

    /**
     * Initialize the class
     */
    public static function init() {
        if (is_null(static::$cipher)) {
            static::$hashAlgorithm = rex_addon::get('cryptor')->getConfig('hashAlgorithm');
            static::$key = openssl_digest(rex_addon::get('cryptor')->getConfig('key'), static::$hashAlgorithm);
            static::$cipher = rex_addon::get('cryptor')->getConfig('cipher');
        }
    }

    /**
     * Encrypt given data
     * @param <mixed> $data
     * @return <mixed> $encryptredData
     */
    public static function encrypt($data = '', $key = null) {
        return self::_crypt('encrypt', $data, $key);
    }
    
    /**
     * Decrypt given data
     * @param <mixed> $data
     * @return <mixed> $decryptredData
     */
    public static function decrypt($data = '', $key = null) {
        return self::_crypt('decrypt', $data, $key);
    }
    
    private static function _crypt($method, $data, $key) {
        self::init();
        
        // Loop over arrays
        if (is_array($data)) {
            $array = [];
            foreach ($data as $key => $value) {
                $array[$key] = self::_crypt($method, $value, $key);
            }
            return $array;
        }
        
        // Loop over objects
        else if (is_object($data)) {
            $object = new $data;
            foreach ($data as $key => $value) {
                $object->$key = self::_crypt($method, $value, $key);
            }
            return $object;
        }
        
        if (empty($key)) {
            $key = static::$key;
        }
        
        if ($method === 'decrypt') {
            return self::_decrypt($data, $key);
        }
        return self::_encrypt($data, $key);
    }
    
    /**
     * Returns true if cipher exists
     * @param <string> $cipher
     * @return <boolean>
     */
    public static function hasCipher($cipher) {
        return in_array($cipher, openssl_get_cipher_methods());
    }
    
    /**
     * Returns the min length of an encrypted string
     * @return <int>
     */
    public static function getCryptedStringMinLength() {
        return self::$cryptedStringMinLength;
    }

    /**
     * Private encryptor
     * @param <string> $string
     * @return <string>
     */
    private static function _encrypt($string, $key) {
        $iv = self::_iv();
        $encryptedString = openssl_encrypt($string, static::$cipher, static::$key, 0, $iv);
        return base64_encode($encryptedString . self::IV_DELIMITER . $iv);
    }

    /**
     * Private decryptor
     * @param <string> $string
     * @return <string>
     */
    private static function _decrypt($string, $key) {
        $stringParts = explode(self::IV_DELIMITER, base64_decode($string));
        if (count($stringParts) === 2) {
            list($encryptedString, $iv) = $stringParts;
            return openssl_decrypt($encryptedString, static::$cipher, static::$key, 0, $iv);
        } else {
            self::_log('initializeVector missing in: ' . implode('', $stringParts));
        }
        return $string;
    }

    /**
     * Private: returns an initialization vector
     * @return <string>
     */
    private static function _iv() {
        return openssl_random_pseudo_bytes(self::_ivLength());
    }
    
    /**
     * Returns length of initialization vector
     * @return <int>
     */
    private static function _ivLength() {
        return openssl_cipher_iv_length(static::$cipher);
    }
    
    /**
     * Log helper
     * @param <string> $message
     */
    private static function _log($message) {
        if (self::LOG_ENABLED === true) {
            $logFile = date('Y-m-d') . '.log';
            $logPath = rex_path::addonData(self::ADDON_NAME, $logFile);
            $log = rex_file::get($logPath);
            rex_file::put($logPath, $log . date('Y-m-d H:i:s') . ' ' . $message . PHP_EOL);
        }
    }

}
