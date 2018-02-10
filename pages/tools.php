<?php

/**
 * Cryptor Addon
 *
 * @author marcel@scherkamp.de
 * @package redaxo5
 * @var rex_addon $this
 */
$warnings = [];
$messages = [];

// Text vars
$encryptText = '';
$encryptResult = '';
$decryptText = '';
$decryptResult = '';

// Fetch post vars and en/decrypt it
if (rex_post('tools-submit', 'string') !== '') {
    $encryptText = rex_post('encrypttext', 'string', null);
    if (strlen($encryptText)) {
        $encryptResult = cryptor::encrypt($encryptText);
    }
    $decryptText = rex_post('decrypttext', 'string', null);
    if (!empty($decryptText)) {
        $decryptResult = cryptor::decrypt($decryptText);
    }
}

// Content collection
$content = '';
$content .= '<fieldset class="col-sm-6"><legend>' . $this->i18n('tools_encrypt') . '</legend>';

// Form element collection
$formElements = [];

// String to encrypt
$n = [];
$n['label'] = '<label for="cryptor-tools-plaintext">' . $this->i18n('tools_encrypt_plaintext') . '</label>';
$n['field'] = '<input class="form-control" id="cryptor-tools-plaintext" type="text" name="encrypttext" value="' . $encryptText . '">';
$formElements[] = $n;

// Encrypt result
$n = [];
$n['label'] = '<label for="cryptor-tools-encrypt-result">' . $this->i18n('tools_encrypt_result') . '</label>';
$n['field'] = '<input class="form-control" id="cryptor-tools-encrypt-result" type="text" value="' . $encryptResult . '" readonly>';
$formElements[] = $n;

// Add fragment
$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/form.php');

// Second fieldset
$content .= '</fieldset><fieldset class="col-sm-6"><legend>' . $this->i18n('tools_decrypt') . '</legend>';

// Form element collection
$formElements = [];

// String to decrypt
$n = [];
$n['label'] = '<label for="cryptor-tools-encryptedtext">' . $this->i18n('tools_encrypt_encryptedtext') . '</label>';
$n['field'] = '<input class="form-control" id="cryptor-tools-encryptedtext" type="text" name="decrypttext" value="' . $decryptText . '">';
$formElements[] = $n;

// Decrypt result
$n = [];
$n['label'] = '<label for="cryptor-tools-decrypt-result">' . $this->i18n('tools_decrypt_result') . '</label>';
$n['field'] = '<input class="form-control" id="cryptor-tools-decrypt-result" type="text" value="' . $decryptResult . '" readonly>';
$formElements[] = $n;

// Add fragment
$fragment = new rex_fragment();
$fragment->setVar('elements', $formElements, false);
$content .= $fragment->parse('core/form/form.php');

// Submit button
$formElements = [];
$n = [];
$n['field'] = '<button class="btn btn-save rex-form-aligned" type="submit" name="tools-submit" value="1" ' . rex::getAccesskey($this->i18n('button_save'), 'save') . '>' . $this->i18n('button_generate') . '</button>';
$formElements[] = $n;

// Submit button fragment
$fragment = new rex_fragment();
$fragment->setVar('flush', true);
$fragment->setVar('elements', $formElements, false);
$buttons = $fragment->parse('core/form/submit.php');

// Build page
$fragment = new rex_fragment();
$fragment->setVar('class', 'edit');
$fragment->setVar('title', $this->i18n('tools_title'));
$fragment->setVar('body', $content, false);
$fragment->setVar('buttons', $buttons, false);
$content = $fragment->parse('core/page/section.php');
$content .= '</fieldset>';  

echo '
<form action="' . rex_url::currentBackendPage() . '" method="post">' . $content . '</form>';