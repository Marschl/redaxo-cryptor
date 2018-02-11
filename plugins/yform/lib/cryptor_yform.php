<?php

/**
 * Cryptor/yform class
 *
 * @author Marshall
 */
class cryptor_yform {

    const PLUGIN_NAME = 'yform';
    const CHECKBOX_STATUS_ENABLED = 'on';

    /**
     * Execute a extension point
     * @param <obj> $ep
     */
    public static function executeExtensionpoint(rex_extension_point $ep) {
        if ($ep->getSubject() instanceof Exception) {
            return;
        }

        // Get the config for this table name
        $config = self::getConfig($ep->getParam('table'));
        if (!is_array($config)) {
            return;
        }

        // Loop over each config of this table and callback the cryptor extension point
        foreach ($config as $eventType => $fieldNames) {
            $className = 'cryptor_yform_ep_' . strtolower($ep->getName());
            if ($ep->getName() === $eventType && class_exists($className)) {
                call_user_func([$className, 'execute'], $ep, $fieldNames);
            }
        }
    }

    /**
     * Execute the autodelete
     * @param <mixed> $tableName | null
     */
    public static function executeAutodelete($tableName = null) {
        // Execute on given table
        if (is_string($tableName)) {
            cryptor_yform_autodelete::execute($tableName);
        }

        // Execute on all defined tables
        else {
            $config = self::getConfig();
            foreach ($config as $tableName => $tableConfig) {
                cryptor_yform_autodelete::execute($tableName);
            }
        }
    }

    /**
     * Executes ajax request
     * @param <string> $type
     * @param <array> $params
     * @return <string> $json
     */
    public static function executeAjaxRequest($type, $params) {
        // Decrypt a value within backend
        if ($type === 'backend_decrypt') {
            return json_encode(cryptor::decrypt($params));
        } 
        // Encrypt a value within backend
        else if ($type === 'backend_encrypt') {
            return json_encode(cryptor::encrypt($params));
        } 
        // Return a array of field names and their labels
        else if ($type === 'field_names') {
            $yFormTable = rex_yform_manager_table::get($params);
            $config = self::getConfig($params);
            $fields = cryptor_yform_autoencrypt::getFieldLabels($config, $yFormTable);
            return json_encode($fields);
        }
    }
    
    /**
     * Execute data export
     * @param rex_extension_point $ep
     */
    public static function executeDatasetExport(rex_extension_point $ep) {
        if ($ep->getSubject() instanceof Exception || !$ep->hasParam('table')) {
            return;
        }
 
        // Fetch yform table, config and fields
        $yFormTable = $ep->getParam('table');
        $config = self::getConfig($yFormTable->getTableName());
        $fields = cryptor_yform_autoencrypt::getFieldNames($config, $yFormTable);
        if (!count($fields)) {
            return;
        }
        
        // Decrypt dataset
        $dataset = $ep->getSubject();
        foreach($dataset as $key => $data) {
            foreach($fields as $fieldName) {
                $dataset[$key][$fieldName] = cryptor::decrypt($data[$fieldName]);
            }
        }
        $ep->setSubject($dataset);
    }

    /**
     * Returns the plugin config
     * @return <mixed> $configArray || false
     */
    protected static function getConfig($tableName = null) {
        return rex_plugin::get(cryptor::ADDON_NAME, self::PLUGIN_NAME)->getConfig($tableName);
    }
}
