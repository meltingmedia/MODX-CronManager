<?php

$action= $modx->newObject('modAction');
$action->fromArray(array(
    'id' => 1,
    'namespace' => 'cronmanager',
    'parent' => 0,
    'controller' => 'index',
    'haslayout' => true,
    'lang_topics' => 'cronmanager:default',
), '', true, true);

$menu= $modx->newObject('modMenu');
$menu->fromArray(array(
    'text' => 'cronmanager',
    'parent' => 'components',
    'description' => 'cronmanager.desc',
    'icon' => 'images/icons/plugin.gif',
), '', true, true);
$menu->addOne($action);
unset($menus);

return $menu;
