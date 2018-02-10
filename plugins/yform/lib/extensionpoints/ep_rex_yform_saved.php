<?php

/**
 * cryptor/yform execute extension point REX_YFORM_SAVED
 *
 * @author marcel@scherkamp.de
 * @author <a href="https://scherkamp.de">scherkamp.de</a>
 */

class cryptor_yform_ep_rex_yform_saved extends cryptor_yform_ep_abstract {
    
    public static function execute($ep, $fieldnames = []) {
        // Only encrypt on insert actions
        if ($ep->getParam('action') !== 'insert') {
            return;
        }
        
        // Get field configs
        $fields = parent::getConfigFieldnames($fieldnames);
        if ($fields === false) {
            return;
        }
        
        // Fetch the entry
        $entry = parent::getTableEntry($ep->getParam('table'), $ep->getParam('id'), $fields);
        if ($entry === false) {
            return;
        }
        
        // Prepare the entry (remove empty, trim, encrypt).
        $preparedEntry = parent::prepareEntry($entry, ['trim', 'encrypt']);
        
        // Update the table entry
        return parent::updateTableEntry($ep->getParam('table'), $ep->getParam('id'), $preparedEntry);
    }
}
