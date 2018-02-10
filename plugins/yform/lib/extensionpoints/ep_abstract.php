<?php

/**
 * cryptor/yform abstract for extension points
 *
 * @author marcel@scherkamp.de
 * @author <a href="https://scherkamp.de">scherkamp.de</a>
 */
abstract class cryptor_yform_ep_abstract {
    
    /**
     * Returns a table entry
     * @param <string> $tablename
     * @param <int> $id
     * @param <array> $fields
     * @return <mixed> $tableEntryArray || false
     */
    protected static function getTableEntry($tablename, $id, $fields) {
        $sql = rex_sql::factory();
        $sql->setTable($tablename);
        $sql->setWhere(['id' => $id]);
        $sql->select(implode(',', $fields));
        try {
            $result = $sql->getArray();
            if (count($result)) {
                return $result[0];
            }
        } catch (rex_exception $ex) {
        }
        return false;
    }
    
    /**
     * Update a table entry
     * @param <string> $tablename
     * @param <int> $id
     * @param <array> $entry
     * @return <bool>
     */
    protected static function updateTableEntry($tablename, $id, $entry) {
        $sql = rex_sql::factory();
        $sql->setTable($tablename);
        $sql->setWhere(['id' => $id]);
        $sql->setValues(cryptor::encrypt($entry));
        try {
            return $sql->update();
        } catch (rex_exception $ex) {
        }
        return false;
    }
    
    /**
     * Returns fieldnames of a config checkbox array
     * @param <array> $fieldnames
     * @return <mixed> $fieldsArray || false
     */
    protected static function getConfigFieldnames($fieldnames = []) {
        $fields = [];
        if (is_array($fieldnames)) {
            foreach($fieldnames as $fieldname => $value) {
                if ($value === cryptor_yform::CHECKBOX_STATUS_ENABLED) {
                    $fields[] = $fieldname;
                }
            }
        }
        if (count($fields)) {
            return $fields;
        }
        return false;
    }
    
    /**
     * Applies filter on entry
     * @param <array> $entry
     * @param <array> $methods
     * @return <array> $filteredEntry
     */
    protected static function prepareEntry($entry, $methods = []) {
        foreach($methods as $method) {
            switch($method) {
                case 'trim': 
                    $entry = array_map('trim', $entry);
                    break;
                case 'empty':
                    $entry = array_filter($entry, 
                        function($value) { 
                            return !empty($value); 
                        }
                    );
                    break;
                case 'encrypt': 
                    $entry = array_filter($entry, 
                        function($value) {
                            if (empty($value)) {
                                return '';
                            }
                            return cryptor::encrypt($value); 
                        }
                    );
                default:
                    break;
            }
        }
        return $entry;
    }
}
