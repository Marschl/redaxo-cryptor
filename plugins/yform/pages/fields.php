<?php

/**
 * cryptor/yform
 *
 * @author marcel@scherkamp.de
 * @package cryptor
 */

// Collection of warnings and messages
$warnings = [];
$messages = [];

// Default configs
$autoencryptExtensionPoints = cryptor_yform_autoencrypt::getExtensionPoints();
$tableName = rex_get('table_name', 'string', null);

cryptor_yform_autodelete::setTable($tableName);

// Save config
if (rex_post('fields-submit', 'string') !== '' && rex::getUser()->hasPerm('cryptor[yform]')) {
    $this->setConfig($tableName, rex_post('cryptor', [
        $autoencryptExtensionPoints, 
        ['autodelete_fields'],
        ['autodelete_uploads']
    ]));
    $messages[] = $this->i18n('message_config_saved_successful');
}

// Show warnings
if (count($warnings)) {
    echo rex_view::warning(implode('<br>', $warnings));
}

// Show messages
if (count($messages)) {
    echo rex_view::success(implode('<br>', $messages));
}

// Get config and yform table
$config = $this->getConfig($tableName);
$table = rex_yform_manager_table::get($tableName);

// ---- Table header
$content = '<table class="table"><thead>';
$content .= '<tr><th>' . $this->i18n('yform_label_fieldname') . '</th>';
$content .= '<th>' . $this->i18n('yform_label_fieldlabel') . '</th>';
$content .= '<th>' . $this->i18n('yform_label_fieldtype') . '</th>';
$content .= '<th>' . $this->i18n('yform_label_encrypt_on_extension_point') . '</th>';
$content .= '<th>' . $this->i18n('yform_label_autodelete_field') . '</th>';
$content .= '</tr></thead>';

// Table body
if ($table) {
    $fields = $table->getValueFields();
    
    // Some flags for this table fields
    $hasDatestampFields = false;
    $hasUploadFields = false;
    foreach($fields as $field) {
        if ($field->getTypeName() === 'datestamp') {
            $hasDatestampFields = true;
        }
        if ($field->getTypeName() === 'upload') {
            $hasUploadFields = true;
        }
    }
    
    // Loop over each field and show settings
    foreach($fields as $field) {
        
        // Create table row
        $content .= '<tr><td>' . $field->getName() . '</td>';
        $content .= '<td>' . $field->getLabel() . '</td>';
        $content .= '<td>' . $field->getTypeName() . '</td>';
        
        // Autoencrypt fields
        if (in_array($field->getTypeName(), cryptor_yform_autoencrypt::getFieldTypes()) === true) {
            foreach(cryptor_yform_autoencrypt::getExtensionPoints() as $epName) {
                $checked = (isset($config[$epName][$field->getName()]) && $config[$epName][$field->getName()] === cryptor_yform::CHECKBOX_STATUS_ENABLED);
                $content .= '<td><label class="cryptor-yform-label-checkbox"><input type="checkbox" name="cryptor[' . $epName . '][' . $field->getName() . ']"';
                $content .= (($checked === true) ? 'checked' : '') . '> ' . $epName. '</label></td>';
            }
        } 
        else {
            $content .= '<td></td>';
        }
        
        // Autodelete fields
        if ($field->getTypeName() === 'datestamp') {
            
            $format = cryptor_yform_autodelete::getDateFormat($field);
            $isValidFormat = cryptor_yform_autodelete::isValidDateFormat($format);
            
            // Invalid datestamp format
            if (!$isValidFormat) {
                $content .= '<td>';
                $content .= '<code>' . $this->i18n('yform_option_interval_date_invalid') . ' (' . $field->getElement('format') . ')</code>';
                $content .= '</td>';
            } 
            
            // Field has valid datestamp format
            else {
                // Autodelete interval number
                $value = 0;
                if (isset($config['autodelete_fields'][$field->getName()]['number'])) {
                    $value = (int) $config['autodelete_fields'][$field->getName()]['number'];
                }
                $content .= '<td>';
                $content .= '<input type="number" min="0" step="1" name="cryptor[autodelete_fields][' . $field->getName() . '][number]" ';
                $content .= 'value="' . $value . '" class="form-control cryptor-yform-control-50 cryptor-yform-control-inline">';

                // Autodelete interval select
                $content .= '<select name="cryptor[autodelete_fields][' . $field->getName() . '][interval]" ';
                $content .= 'class="form-control cryptor-yform-control-100 cryptor-yform-control-inline">';
                foreach(cryptor_yform_autodelete::getIntervals() as $option) {
                    $selected = (isset($config['autodelete_fields'][$field->getName()]['interval']) && $config['autodelete_fields'][$field->getName()]['interval'] === $option);
                    $content .= '<option value="' . $option . '"' . ($selected ? 'selected' : '') . '>';
                    $content .= $this->i18n('yform_option_interval_' . strtolower($option)) . '</option>';
                }
                $content .= '</select>';
                
                // Validate date format
                if ($isValidFormat && $value > 0) {
                    $date = cryptor_yform_autodelete::getDeleteDate($tableName, $field->getName(), date($format));
                    if ($date) {
                        $content .= '<small>' . $date->format($format) . '</small>';
                    }
                }
                $content .= '</td>';
            }
        }
        
        // Autodelete Upload field
        else if ($field->getTypeName() === 'upload' && $hasDatestampFields) {
            $checked = (isset($config['autodelete_uploads'][$field->getName()]) && $config['autodelete_uploads'][$field->getName()] === cryptor_yform::CHECKBOX_STATUS_ENABLED);
            $content .= '<td><label class="cryptor-yform-label-checkbox">';
            $content .= '<input type="checkbox" name="cryptor[autodelete_uploads][' . $field->getName() . ']"';
            $content .= (($checked === true) ? 'checked' : '') . '> ';
            $content .= rex_i18n::msg('cryptor_yform_label_autodelete_upload') . '</label></td>';
        }
        
        else {
            $content .= '<td></td>';
        }
        $content .= '</tr>';
    }
}
$content .= '</table>';

// Notes
$content .= '<p class="help-block rex-note"><strong>' . rex_i18n::msg('cryptor_yform_note_ep_legend') . ':</strong><br>';
foreach(cryptor_yform_autoencrypt::getExtensionPoints() as $epName) {
    $content .= $this->i18n('yform_note_ep_' . strtolower($epName), $epName)  . '<br>';
}
$content .= $this->i18n('yform_note_ep_change') . '</p>';

$content .= '<p class="help-block rex-note"><strong>' . rex_i18n::msg('cryptor_yform_note_autodelete_legend') . ':</strong><br>';
$content .= $this->i18n('yform_note_autodelete_interval') .'<br>';
$content .= $this->i18n('yform_note_autodelete_format') . '<br>';
$content .= $this->i18n('yform_note_autodelete_change') . '<br>';
$content .= $this->i18n('yform_note_autodelete_multible') . '<br>';
$content .= $this->i18n('yform_note_autodelete_upload') . '</p>';


// Submit button
$formElements = [];
$field  = '<button class="btn btn-save rex-form-aligned" type="submit" name="fields-submit" value="1" ';
$field .= ' onclick="return confirm(\'' . $this->i18n('yform_fields_confirm_change') . '\')" ';
$field .= rex::getAccesskey($this->i18n('button_save'), 'save') . '>';
$field .= $this->i18n('button_save') . '</button>';
$formElements[] = ['field' => $field];

// Submit button fragment
$fragment = new rex_fragment();
$fragment->setVar('flush', true);
$fragment->setVar('elements', $formElements, false);
$buttons = $fragment->parse('core/form/submit.php');

// Build page
$fragment = new rex_fragment();
$fragment->setVar('class', 'edit');
$fragment->setVar('title', $this->i18n('yform_fields_title', $table->getName()) . '');
$fragment->setVar('body', $content, false);
$fragment->setVar('buttons', $buttons, false);
$out = $fragment->parse('core/page/section.php');

echo '<form action="' . rex_url::currentBackendPage(['table_name' => $tableName]) . '" method="post">' . $out . '</form>';