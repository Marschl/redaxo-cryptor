<?php

/**
 * cryptor/logs
 *
 * @author marcel@scherkamp.de
 * @package cryptor
 */
$warnings = [];
$messages = [];

// Save config
$submitSave = rex_post('config-submit', 'string');
$submitExecute = rex_post('config-execute', 'string');
if (($submitSave || $submitExecute) && rex::getUser()->hasPerm('cryptor[logs]')) {
    $this->setConfig(rex_post('settings', [
        ['path', 'string'],
        ['filename', 'string'],
        ['dateformat', 'string'],
        ['minage', 'int'],
        ['extension', 'string'],
        ['suffix', 'string'],
        ['pattern', 'string'],
        ['replacement', 'string'],
        ['delete', 'int'],
    ]));
    $messages[] = $this->i18n('logs_message_config_saved_successful');
    
    if ($submitExecute) {
        $filesUpdated = cryptor_logs::executeIpReplacement();
        if ($filesUpdated > 0) {
            $messages[] = $this->i18n('logs_message_config_executed_successful', $filesUpdated); 
        }
    }
}

// Validate config
$warnings = array_merge($warnings, cryptor_logs::validateConfig());

// Show warnings
if (count($warnings)) {
    echo rex_view::warning(implode('<br>', $warnings));
}

// Show messages
if (count($messages)) {
    echo rex_view::success(implode('<br>', $messages));
}

// Fragment: Description
$file = rex_file::get(rex_path::plugin('cryptor', 'logs', 'README.md'));
$readme = rex_markdown::factory()->parse($file);
$fragment = new rex_fragment();
$fragment->setVar('title', $this->i18n('description'), false);
$fragment->setVar('body', $readme, false);
echo $fragment->parse('core/page/section.php');

// Form element collection
$formElements = [];

// Content collection
$content  = '<fieldset class="col-sm-12"><legend>' . $this->i18n('logs_config_settings') . '</legend>';

// Server log path
$defaultLogPath = cryptor_logs::getDefaultLogPath();
$n = [];
$n['label'] = '<label for="cryptor-logs-config-path">' . $this->i18n('logs_config_path') . '</label>';
$n['field'] = '<input class="form-control rex-code" id="cryptor-logs-config-path" type="text" name="settings[path]" value="' . $this->getConfig('path') . '" placeholder="' . $defaultLogPath . '">';
$n['note'] = $this->i18n('logs_config_path_note', $defaultLogPath);
$formElements[] = $n;

// Log file name
$n = [];
$n['label'] = '<label for="cryptor-logs-config-filename">' . $this->i18n('logs_config_filename') . '</label>';
$n['field'] = '<input class="form-control rex-code" id="cryptor-logs-config-filename" type="text" name="settings[filename]" value="' . $this->getConfig('filename') . '" placeholder="' . $this->i18n('logs_config_default_filename') . '">';
$n['note'] = $this->i18n('logs_config_filename_note');
$formElements[] = $n;

// Log file date format
$n = [];
$n['label'] = '<label for="cryptor-logs-config-dateformat">' . $this->i18n('logs_config_dateformat') . '</label>';
$n['field'] = '<input class="form-control rex-code" id="cryptor-logs-config-dateformat" type="text" name="settings[dateformat]" value="' . $this->getConfig('dateformat') . '" placeholder="' . $this->i18n('logs_config_default_dateformat') . '">';
$n['note'] = $this->i18n('logs_config_dateformat_note');
$formElements[] = $n;

// Log file min age
$n = [];
$n['label'] = '<label for="cryptor-logs-config-minage">' . $this->i18n('logs_config_minage') . '</label>';
$n['field'] = '<input class="form-control rex-code" id="cryptor-logs-config-minage" type="number" min="0" step="1" name="settings[minage]" value="' . $this->getConfig('minage') . '" placeholder="' . $this->i18n('logs_config_default_minage') . '">';
$n['note'] = $this->i18n('logs_config_minage_note');
$formElements[] = $n;

// Log file type
$n = [];
$n['label'] = '<label for="cryptor-logs-config-extension">' . $this->i18n('logs_config_extension') . '</label>';
$n['field'] = '<input class="form-control rex-code" id="cryptor-logs-config-extension" type="text" name="settings[extension]" value="' . $this->getConfig('extension') . '" placeholder="' . $this->i18n('logs_config_default_extension') . '">';
$n['note'] = $this->i18n('logs_config_extension_note');
$formElements[] = $n;

// Log cryptor suffix
$n = [];
$n['label'] = '<label for="cryptor-logs-config-suffix">' . $this->i18n('logs_config_suffix') . '</label>';
$n['field'] = '<input class="form-control rex-code" id="cryptor-logs-config-suffix" type="text" name="settings[suffix]" value="' . $this->getConfig('suffix') . '" placeholder="' . $this->i18n('logs_config_default_suffix') . '">';
$n['note'] = $this->i18n('logs_config_suffix_note');
$formElements[] = $n;

// Log ip address pattern
$n = [];
$n['label'] = '<label for="cryptor-logs-config-pattern">' . $this->i18n('logs_config_pattern') . '</label>';
$n['field'] = '<input class="form-control rex-code" id="cryptor-logs-config-pattern" type="text" name="settings[pattern]" value="' . $this->getConfig('pattern') . '" placeholder="' . $this->i18n('logs_config_default_pattern') . '">';
$n['note'] = $this->i18n('logs_config_pattern_note');
$formElements[] = $n;

// Log ip replace pattern
$n = [];
$n['label'] = '<label for="cryptor-logs-config-replacement">' . $this->i18n('logs_config_replacement') . '</label>';
$n['field'] = '<input class="form-control rex-code" id="cryptor-logs-config-replacement" type="text" name="settings[replacement]" value="' . $this->getConfig('replacement') . '" placeholder="' . $this->i18n('logs_config_default_replacement') . '">';
$n['note'] = $this->i18n('logs_config_replacement_note');
$formElements[] = $n;

// Delete log file after mask
$checked = (intval($this->getConfig('delete')) === 1) ? ' checked="checked"' : '';
$n = [];
$n['label'] = '<label for="cryptor-logs-config-delete">' . $this->i18n('logs_config_delete') . '</label>';
$n['field'] = '<input class="form-checkbox" id="cryptor-logs-config-delete" type="checkbox" name="settings[delete]" value="1" "' . $checked . '"> ' . $this->i18n('logs_config_delete_note');
$formElements[] = $n;

$path = cryptor_logs::getLogFilePath();
if (!empty($path)) {
    
    // File list
    $fileList = cryptor_logs::getFileList($path);
    $n = [];
    $n['label'] = '<label for="cryptor-logs-config-filelist">' . $this->i18n('logs_config_filelist') . '</label>';
    $n['field'] = '<textarea name="" class="form-control" rows="8" readonly>' . implode(PHP_EOL, $fileList) . '</textarea>';
    $n['note'] = $this->i18n('logs_config_path') . ': <code>' . $path . '</code>';
    $formElements[] = $n;
    
    // Log File list
    $logFileList = cryptor_logs::getLogFileList(true);
    $n = [];
    $n['label'] = '<label for="cryptor-logs-config-logfilelist">' . $this->i18n('logs_config_logfilelist') . '</label>';
    $n['field'] = '<textarea name="" class="form-control" rows="8" readonly>' . implode(PHP_EOL, $logFileList) . '</textarea>';
    $n['note'] = $this->i18n('logs_config_path') . ': <code>' . $path . '</code>';
    $formElements[] = $n;
    
    // Log file list limited to max age
    $minAge = cryptor_logs::getLogFileMinAge();
    $logFileList = cryptor_logs::getLogFileList(true, $minAge);
    $n = [];
    $n['label'] = '<label for="cryptor-logs-config-logfilelist">' . $this->i18n('cryptor_logs_config_logfilelist_limited', count($logFileList),  $minAge) . '</label>';
    $n['field'] = '<textarea name="" class="form-control" rows="8" readonly>' . implode(PHP_EOL, $logFileList) . '</textarea>';
    $n['note'] = $this->i18n('logs_config_path') . ': <code>' . $path . '</code>';
    $formElements[] = $n;
    
}

// Create fragment
$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/form.php');

// Submit button
$formElements = [];
$field  = '<button class="btn btn-save rex-form-aligned" type="submit" name="config-submit" value="1" ';
$field .= rex::getAccesskey($this->i18n('button_save'), 'save') . '>';
$field .= $this->i18n('button_save') . '</button>';
$formElements[] = ['field' => $field];

// Execute
$field  = '<button class="btn btn-warning rex-form-aligned" type="submit" name="config-execute" value="1">';
$field .= $this->i18n('logs_config_button_execute') . '</button>';
$formElements[] = ['field' => $field];

// Submit button fragment
$fragment = new rex_fragment();
$fragment->setVar('flush', true);
$fragment->setVar('elements', $formElements, false);
$buttons = $fragment->parse('core/form/submit.php');

// Build page
$fragment = new rex_fragment();
$fragment->setVar('class', 'edit');
$fragment->setVar('title', $this->i18n('logs_config_title'));
$fragment->setVar('body', $content, false);
$fragment->setVar('buttons', $buttons, false);
$content = $fragment->parse('core/page/section.php');

$content .= '</fieldset>';

echo '<form action="' . rex_url::currentBackendPage() . '" method="post">' . $content . '</form>';