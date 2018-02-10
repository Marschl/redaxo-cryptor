<?php

/**
 * Cryptor Addon
 *
 * @author marcel@scherkamp.de
 * @package redaxo5
 * @var rex_addon $this
 */
$file = rex_file::get(rex_path::addon('cryptor', 'README.md'));
$body = rex_markdown::factory()->parse($file);
$fragment = new rex_fragment();
$fragment->setVar('body', $body, false);
$content = $fragment->parse('core/page/section.php');
echo $content;