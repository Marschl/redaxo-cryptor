<?php

/**
 * Autoencrypt yform fields on insert
 *
 * @author Marshall
 */
class cryptor_yform_autoencrypt extends cryptor_yform {
    
    const UPDATE_CHUNK_SIZE = 100;
    
    protected static $extensionPoints = ['REX_YFORM_SAVED'];
    protected static $fieldTypes = ['text', 'textarea', 'email'];
    
    /**
     * Returns all supported extension points
     * @return <array> $autoencryptExtensionPoints
     */
    public static function getExtensionPoints() {
        return static::$extensionPoints;
    }

    /**
     * Returns all supported encryption field types
     * @return <array> $autoencryptFieldTypes
     */
    public static function getFieldTypes() {
        return static::$fieldTypes;
    }
    
    /**
     * Returns encrypted fields and their attributes
     * @param <array> $config
     * @param <object> $yFormTable
     * @return <array> $fields
     */
    public static function getFieldLabels($config, $yFormTable) {
        $fields = [];
        if (is_array($config)) {
            foreach ($config as $extensionPoint) {
                if (!is_array($extensionPoint)) {
                    continue;
                }
                foreach ($extensionPoint as $fieldname => $status) {
                    if ($status === parent::CHECKBOX_STATUS_ENABLED) {
                        $fields[] = [
                            'name' => $fieldname,
                            'label' => $yFormTable->getValueField($fieldname)->getLabel(),
                        ];
                    }
                }
            }
        }
        return $fields;
    }
    
    /**
     * Returns the fieldnames which are encrypted 
     * @param <array> $config
     * @param <object> $yFormTable
     * @return <array> $fieldnames
     */
    public static function getFieldNames($config, $yFormTable) {
        $fields = self::getFieldLabels($config, $yFormTable);
        $fieldNames = [];
        foreach($fields as $field) {
            $fieldNames[] = $field['name'];
        }
        return $fieldNames;
    }
    
    /**
     * Returns the differences between old config and new config
     * @param <array> $oldConfig
     * @param <array> $newConfig
     * @return <array> $difference
     */
    public static function getConfigDifference($oldConfig = [], $newConfig = []) {
        $difference = [
            'fields_decrypt' => [],
            'fields_encrypt' => []
        ];
        foreach(self::getExtensionPoints() as $epName) {
            $oldFields = (isset($oldConfig[$epName]) && is_array($oldConfig[$epName])) ? $oldConfig[$epName] : [];
            $newFields = (isset($newConfig[$epName]) && is_array($newConfig[$epName])) ? $newConfig[$epName] : [];

            // Get encrypted fields which are to decrypt now
            foreach($oldFields as $fieldName => $status) {
                if (!isset($newFields[$fieldName])) {
                    $difference['fields_decrypt'][] = $fieldName;
                }
            }

            // Get encrypted fields which are to decrypt now
            foreach($newFields as $fieldName => $status) {
                if (!isset($oldFields[$fieldName])) {
                    $difference['fields_encrypt'][] = $fieldName;
                }
            }
        }
        return $difference;
    }
    
    /**
     * De- or encrypt hole table 
     * @param <string> $method encrypt or decrypt
     * @param <string> $tableName
     * @param <array> $tableFields
     * @return <int> $updatedRows
     */
    public static function updateTableFields($method, $tableName, $tableFields = []) {
        if (!count($tableFields)) {
            return 0;
        }
        
        // Collection of fields and update values
        $fields = $tableFields;
        array_unshift($fields, 'id');
        $updates = [];
        
        // Fetch data and create update collection
        $sql = rex_sql::factory();
        $sql->setTable($tableName);
        $sql->select(implode(',', $fields));
        $sql->execute();
        for ($i = 0; $i < $sql->getRows(); $i++) {
            $values = [$sql->getValue('id')];
            foreach($tableFields as $tableField) {
               $value = cryptor::$method($sql->getValue($tableField));
               $values[] = $sql->escape($value);
            }
            $updates[] = '(' . implode(',', $values) . ')';
            $sql->next();
        }
        if (!count($updates)) {
            return 0;
        }
        
        // Build the replace queries
        $keys = [];
        foreach($tableFields as $tableField) {
            $keys[] = $sql->escapeIdentifier($tableField) . '=VALUES(' . $sql->escapeIdentifier($tableField) . ')';
        }
        $insertQuery  = 'INSERT INTO ' . $sql->escapeIdentifier($tableName) . ' (' . implode(',', $fields) . ') VALUES ';
        $duplicateQuery = ' ON DUPLICATE KEY UPDATE ' . implode(',', $keys);
        
        // Split the updates into chunks
        $count = 0;
        foreach(array_chunk($updates, self::UPDATE_CHUNK_SIZE) as $updateChunk) {
            $query = $insertQuery .  implode(',', $updateChunk) . $duplicateQuery;
            $sql->setTable($tableName);
            $sql->setQuery($query);
            $count += count($updateChunk);
        }
        return $count;
    }
}
