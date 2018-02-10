<?php

/**
 * Cryptor/yform_autodelete class
 *
 * @author Marshall
 */
class cryptor_yform_autodelete extends cryptor_yform {
    
    const CONFIG_KEY_FIELDS = 'autodelete_fields';
    const CONFIG_KEY_UPLOADS = 'autodelete_uploads';
    const DEFAULT_DATESTAMP_PATTERN = 'Y-m-d H:i:s';

    private static $debug = false;
    private static $intervals = ['days' => 'DAY', 'weeks' => 'WEEK', 'months' => 'MONTH', 'years' => 'YEAR'];
    private static $dateFields = [];
    private static $uploadFields = [];
    private static $tableName = null;
    private static $tableConfig = null;
    private static $yFormTable = null;

    /**
     * Executes the autodelete on a table
     * @param <string> $tableName
     * @return <boolean>
     */
    public static function execute($tableName) {
        
        // Set the autodelete table informations
        self::setTable($tableName);
        if (!self::isValidTable()) {
            return;
        }
        
        // Get an array of where statements
        $whereStatements = self::_getWhereStatements();
        if (!count($whereStatements)) {
            return;
        }
        
        // If upload fields exist, delete the uploads first
        if (self::_hasUploadFields()) {
            $entries = self::_getEntriesWhere(implode(' OR ', $whereStatements));
            foreach($entries as $entry) {
                self::_deleteEntryUploads($entry);
            }
        }
        
        // Delete the entries
        self::_deleteEntriesWhere(implode(' OR ', $whereStatements));
    }

    /**
     * Sets the tableConfig, yFormTable and autodeleteFields by table name
     * @param <string> $tableName
     */
    public static function setTable($tableName) {
        self::$tableName = $tableName;
        self::$tableConfig = parent::getConfig($tableName);
        self::$yFormTable = rex_yform_manager_table::get($tableName);
        
        // Set the date fields
        if (isset(self::$tableConfig[self::CONFIG_KEY_FIELDS]) && is_array(self::$tableConfig[self::CONFIG_KEY_FIELDS])) {
            self::$dateFields = self::$tableConfig[self::CONFIG_KEY_FIELDS];
        }
        
        // Set the upload fields
        if (self::_hasDateFields() && isset(self::$tableConfig[self::CONFIG_KEY_UPLOADS]) && is_array(self::$tableConfig[self::CONFIG_KEY_UPLOADS])) {
            foreach(self::$tableConfig[self::CONFIG_KEY_UPLOADS] as $fieldName => $fieldValue) {
                if ($fieldValue !== parent::CHECKBOX_STATUS_ENABLED) {
                    continue;
                }
                self::$uploadFields[] = $fieldName;
            }
        }
    }

    /**
     * Returns true if given $format is a valid datetime format
     * @param <string> $format
     * @return <boolean>
     */
    public static function isValidDateFormat($format) {
        if ($format === '' || $format === 'mysql') {
            $format = self::DEFAULT_DATESTAMP_PATTERN;
        }
        return (\DateTime::createFromFormat($format, date($format)) !== false);
    }

    /**
     * Returns true if given $interval is a valid interval
     * @param <string> $interval
     * @return <boolean>
     */
    public static function isValidInterval($interval) {
        return in_array($interval, self::$intervals);
    }

    /**
     * Returns true if the table configuration seems to be valid
     * @return <boolean>
     */
    public static function isValidTable() {
        return (is_array(self::$tableConfig) && self::$yFormTable instanceof rex_yform_manager_table && is_array(self::$dateFields));
    }

    /**
     * Returns all supported auto delete intervals
     * @return <array> $autodeleteIntervals
     */
    public static function getIntervals() {
        return self::$intervals;
    }

    /**
     * Returns the format attribute of a yFormField
     * @param <object> $yFormField
     * @return <string> $format
     */
    public static function getDateFormat($yFormField) {
        $format = trim($yFormField->getElement('format'));
        if ($format === '' || $format === 'mysql') {
            return self::DEFAULT_DATESTAMP_PATTERN;
        }
        return $format;
    }

    /**
     * Returns the autodelete date, depending on the field settings or false if settings/value are invalid
     * Note: only used by the backend view
     * @param <string> $tableName
     * @param <string> $fieldName
     * @param <string> $dateString
     * @return <mixed> $dateTimeObject | false
     */
    public static function getDeleteDate($tableName, $fieldName, $dateString = null) {
        
        // Get config for table and field
        $config = self::getConfig($tableName);
        $yFormTable = rex_yform_manager_table::get($tableName);
        if (!$yFormTable || !$config || !isset($config[self::CONFIG_KEY_FIELDS]) || !isset($config[self::CONFIG_KEY_FIELDS][$fieldName])) {
            return false;
        }

        // Check if the field exists, has a number and an valid interval
        $field = $yFormTable->getValueField($fieldName);
        $number = (int) $config[self::CONFIG_KEY_FIELDS][$fieldName]['number'];
        $interval = (string) $config[self::CONFIG_KEY_FIELDS][$fieldName]['interval'];
        if (!$field || !$number || !self::isValidInterval($interval)) {
            return false;
        }

        // Get the formated value of this field
        $date = self::_getDateTime($field, $dateString);
        if ($date === false) {
            return false;
        }

        // Finally calculate the autodelete date
        $date->modify('+' . $number . ' ' . strtolower($interval));
        return $date;
    }
    
    /**
     * Returns true, if any date fields exists
     * @return <bool>
     */
    private static function _hasDateFields() {
        return count(self::$dateFields) > 0;
    }
    
    /**
     * Returns true, if an autodelete table has upload fields
     * @return <bool>
     */
    private static function _hasUploadFields() {
        return count(self::$uploadFields) > 0;
    }
    
    /**
     * Delete the uploads for an entry
     * @param <array> $entry
     */
    private static function _deleteEntryUploads($entry) {
        foreach(self::$uploadFields as $uploadFieldName) {
            $uploadPath = self::_getUploadPath($uploadFieldName);
            foreach (glob($uploadPath . $entry['id'] . '_*') as $filePath) {
                rex_file::delete($filePath);
            }
        }
    }

    /**
     * Returns the upload path for a field (by field name)
     * @param <string> $fieldName
     * @return <string> $uploadPath
     */
    private static function _getUploadPath($fieldName) {
        return rex_plugin::get('yform', 'manager')->getDataPath('upload/' . self::$tableName . '/' . $fieldName . '/');
    }

    /**
     * Returns datetime object formated by the field attribute 'format'
     * @param <object> $yFormField
     * @param <string> $dateString
     * @return <mixed> $dateObject || false
     */
    private static function _getDateTime($yFormField, $dateString) {
        $format = self::getDateFormat($yFormField);
        return \DateTime::createFromFormat($format, $dateString);
    }

    /**
     * Returns an array of where statements depending on configuration
     * @return <array>
     */
    private static function _getWhereStatements() {
        $statements = [];
        foreach (self::$dateFields as $fieldName => $attributes) {

            // Attributes number and interval must be set
            $number = (int) $attributes['number'];
            $interval = (string) $attributes['interval'];
            if (!$number || !self::isValidInterval($interval)) {
                continue;
            }

            // yForm field must be exist and type of datestamp
            $yFormField = self::$yFormTable->getValueField($fieldName);
            if (!$yFormField || $yFormField->getTypeName() !== 'datestamp') {
                continue;
            }

            // yForm format must be a valid dateTime format
            $format = self::getDateFormat($yFormField);
            if (!$format || !self::isValidDateFormat($format)) {
                continue;
            }

            // Add the delete statement for this field
            $statements[] = '`' . $fieldName . '` < NOW() - INTERVAL ' . $number . ' ' . $interval;
        }
        return $statements;
    }

    /**
     * Delete table entries by where statement
     * @param <string> $tableName
     * @param <string> $where
     */
    private static function _deleteEntriesWhere($where) {
        $sql = rex_sql::factory();
        $sql->setDebug(self::$debug);
        $query = 'DELETE FROM ' . $sql->escapeIdentifier(self::$tableName) . ' WHERE ( ' . $where . ')';
        $sql->setQuery($query);
    }
    
    /**
     * Get table entries by where statement
     * @param <string> $tableName
     * @param <string> $where
     * @return <array>
     */
    private static function _getEntriesWhere($where) {
        $sql = rex_sql::factory();
        $sql->setDebug(self::$debug);
        $query = 'SELECT * FROM ' . $sql->escapeIdentifier(self::$tableName) . ' WHERE ( ' . $where . ')';
        return $sql->getArray($query);
    }
}
