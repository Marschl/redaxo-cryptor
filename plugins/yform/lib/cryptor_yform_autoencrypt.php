<?php

/**
 * Autoencrypt yform fields on insert
 *
 * @author Marshall
 */
class cryptor_yform_autoencrypt extends cryptor_yform {
    
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
    
}
