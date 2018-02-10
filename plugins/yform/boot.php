<?php
$config = $this->getConfig();

// Load assets
if (rex::isBackend() && rex::getUser() && rex::getUser()->hasPerm('cryptor[yform]')) {
    rex_view::addJsFile($this->getAssetsUrl('cryptor_yform.min.js'));
    rex_view::addCssFile($this->getAssetsUrl('cryptor_yform.css'));
}

// Execute ajax requests
if (rex::isBackend() && rex::getUser() && rex::getUser()->hasPerm('cryptor[yform]')) {
    $type = rex_post('cryptor_yform', 'string', null);
    $value = rex_post('cryptor_yform_value');
    if ($type && $value) {
        $response = cryptor_yform::executeAjaxRequest($type, $value);
        rex_response::sendContent($response, 'application/json');
        die;
    }
}

// Loop over each table config
$registeredExtensionPoints = [];
foreach($config as $tableName => $table) {
    // Register extension points
    foreach(cryptor_yform_autoencrypt::getExtensionPoints() as $extensionPoint) {
        if (isset($table[$extensionPoint]) && !in_array($extensionPoint, $registeredExtensionPoints)) {
            $registeredExtensionPoints[] = $extensionPoint;
            rex_extension::register($extensionPoint, function (rex_extension_point $ep) {   
                cryptor_yform::executeExtensionpoint($ep);
            });
        }
    }
}
