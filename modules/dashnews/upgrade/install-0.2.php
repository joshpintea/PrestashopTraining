<?php
/**
 * Created by Josh.
 * Date: 8/31/2018
 * Time: 3:53 PM
 */


if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_0_2($object)
{
    $query = 'ALTER TABLE `' . _DB_PREFIX_ . 'news` ADD `date_from` DATE NOT null,ADD `date_to` DATE NOT null';

    return Db::getInstance()->execute($query);
}