<?php

/**
 * Addon Framework Classes.
 *
 * @author marcel@scherkamp.de
 * @package redaxo5
 * @var rex_addon $this
 */
$warnings = [];
$messages = [];

// Save config
if (rex_post('config-submit', 'string') !== '' && rex::getUser()->hasPerm('cryptor[config]')) {
    $this->setConfig(rex_post('settings', [
        ['cipher', 'string'],
        ['key', 'string'],
        ['hashAlgorithm', 'string', 'sha256']
    ]));
    $messages[] = $this->i18n('message_config_saved_successful');
}

// Check if cipher is sete
if ($this->getConfig('cipher') && !in_array($this->getConfig('cipher'), openssl_get_cipher_methods())) {
    $warnings[] = $this->i18n('message_cipher_does_not_exists');
}

// Check if key is set
if ($this->getConfig('key') === '') {
    $warnings[] = $this->i18n('message_encryption_key_not_set');
}

// Show warnings
if (count($warnings)) {
    echo rex_view::warning(implode('<br>', $warnings));
}

// Show messages
if (count($messages)) {
    echo rex_view::success(implode('<br>', $messages));
}

// Select cipher methods
$selectedCipherMethod = (empty($this->getConfig('cipher')) ? 'AES-256-CTR' : $this->getConfig('cipher'));
$selectCipherMethod = new rex_select();
$selectCipherMethod->setId('cryptor-cipher-method');
$selectCipherMethod->setName('settings[cipher]');
$selectCipherMethod->setSize(1);
$selectCipherMethod->setAttribute('class', 'form-control selectpicker');
$selectCipherMethod->setSelected($selectedCipherMethod);
foreach (openssl_get_cipher_methods() as $cipher) {
    $selectCipherMethod->addOption($cipher, $cipher);
}

// Select cipher methods
$selectedHashAlgorithm = (empty($this->getConfig('hashAlgorithm')) ? 'sha256' : $this->getConfig('hashAlgorithm'));
$selectHashAlgorithm = new rex_select();
$selectHashAlgorithm->setId('cryptor-hash-algorithm');
$selectHashAlgorithm->setName('settings[hashAlgorithm]');
$selectHashAlgorithm->setSize(1);
$selectHashAlgorithm->setAttribute('class', 'form-control selectpicker');
$selectHashAlgorithm->setSelected($selectedHashAlgorithm);
foreach (hash_algos() as $algorithm) {
    $selectHashAlgorithm->addOption($algorithm, $algorithm);
}

// Form element collection
$formElements = [];

// Content collection
$content = '';
$content .= '<fieldset class="col-sm-12"><legend>' . $this->i18n('settings_openssl_title') . '</legend>';

// Cipher method
$n = [];
$n['label'] = '<label for="cryptor-cipher-method">' . $this->i18n('settings_cipher_method') . '</label>';
$n['field'] = $selectCipherMethod->get();
$n['note'] = $this->i18n('settings_cipher_method_note');
$formElements[] = $n;

// Encryption key
$n = [];
$n['label'] = '<label for="cryptor-encryption-key">' . $this->i18n('settings_encryption_key') . '</label>';
$n['field'] = '<input class="form-control" id="cryptor-encryption-key" type="text" name="settings[key]" placeholder="' . $this->i18n('settings_encryption_key_placeholder') . '" value="' . $this->getConfig('key') . '">';
$n['note'] = $this->i18n('settings_encryption_key_note');
$formElements[] = $n;

// Hash algorithm method
$n = [];
$n['label'] = '<label for="cryptor-hash-algorithm">' . $this->i18n('settings_hash_algorithm') . '</label>';
$n['field'] = $selectHashAlgorithm->get();
$n['note'] = $this->i18n('settings_hash_algorithm_note');
$formElements[] = $n;

// Create fragment
$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/form.php');

// Submit button
$formElements = [];
$field  = '<button class="btn btn-save rex-form-aligned" type="submit" name="config-submit" value="1" ';
$field .= ' onclick="return confirm(\'' . $this->i18n('settings_confirm_change') . '\')" ';
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
$fragment->setVar('title', $this->i18n('settings_title'));
$fragment->setVar('body', $content, false);
$fragment->setVar('buttons', $buttons, false);
$content = $fragment->parse('core/page/section.php');

$content .= '</fieldset>';

echo '<form action="' . rex_url::currentBackendPage() . '" method="post">' . $content . '</form>';