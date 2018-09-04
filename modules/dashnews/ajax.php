<?php
/**
 * Created by Josh.
 * Date: 9/4/2018
 * Time: 1:30 PM
 */

require_once('../../config/config.inc.php');
require_once('../../init.php');

require_once('classes/CategoryNews.php');
require_once('classes/News.php');

switch (Tools::getValue('method')) {
    case 'getAfterIdCategory' :
        if(Tools::getValue('idCategory')){
            $news = CategoryNews::getNewsAfterCategoryId(Tools::getValue('idCategory'),Tools::getValue('filter'));
            die( Tools::jsonEncode( array('news' => $news)));
        }else{
            die(Tools::jsonEncode(array('error' => 'Bad submit')));
        }
        break;
    case 'filterNews' :
        $news = News::getAll(0,Tools::getValue('filter'));
        die( Tools::jsonEncode( array('news' => $news)));
    default:
        exit;
}
exit;