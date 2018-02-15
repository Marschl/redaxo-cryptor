<?php

/** @var rex_addon $this */

if (!$this->hasConfig()) {
    $this->setConfig([
        'path' => cryptor_logs::getDefaultLogPath(),
        'filename' => 'access_log_',
        'dateformat' => 'Y-m-d',
        'minage' => 30,
        'extension' => 'gz',
        'suffix' => 'cryptor',
        'pattern' => '/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})/',
        'replacement' => '${1}.${2}.${3}.***',
        'delete' => 0
    ]);
}