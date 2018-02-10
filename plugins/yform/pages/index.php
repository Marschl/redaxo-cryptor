<?php

/**
 * cryptor/yform.
 *
 * @author marcel@scherkamp.de
 * @package cryptor
 */

// Fetch yfrom tables
try {
    $yFormTables = rex_yform_manager_table::getAll();
} catch (Exception $e) {
    $yFormTables = [];
}

// Create a list of yform tables
$sql = 'SELECT id, name, table_name FROM `' . rex_yform_manager_table::table() . '` ORDER BY prio, table_name';
$list = rex_list::factory($sql);

// Set labels
$list->removeColumn('id');
$list->setColumnLabel('prio', rex_i18n::msg('yform_manager_table_prio_short'));
$list->setColumnLabel('name', rex_i18n::msg('yform_manager_name'));

// Tablename
$list->setColumnLabel('table_name', rex_i18n::msg('yform_manager_table_name'));
$list->setColumnFormat('table_name', 'custom', function ($params) {
    return '<a href="index.php?page=cryptor/yform/fields&table_name=' . $params['value'] . '">' . $params['value'] . '</a>';
});

// Fragment: List of tables
$fragment = new rex_fragment();
$fragment->setVar('title', rex_i18n::msg('yform_table_overview'));
$fragment->setVar('content', $list->get(), false);
echo $fragment->parse('core/page/section.php');

// Fragment: Description
$file = rex_file::get(rex_path::plugin('cryptor', 'yform', 'README.md'));
$readme = rex_markdown::factory()->parse($file);
$fragment = new rex_fragment();
$fragment->setVar('title', $this->i18n('description'), false);
$fragment->setVar('body', $readme, false);
echo $fragment->parse('core/page/section.php');
