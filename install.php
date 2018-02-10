<?php

/** @var rex_addon $this */

if (!$this->hasConfig()) {
    $this->setConfig([
        'cipher' => 'AES-256-CTR',
        'key' => bin2hex(openssl_random_pseudo_bytes(16)),
        'hashAlgorithm' => 'sha256'
    ]);
}