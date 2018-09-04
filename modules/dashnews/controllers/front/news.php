<?php
/**
 * Created by Josh.
 * Date: 8/30/2018
 * Time: 1:54 PM
 */

require_once "modules/dashnews/classes/News.php";
require_once "modules/dashnews/classes/CategoryNews.php";

class dashnewsnewsModuleFrontController extends ModuleFrontController
{

    public function init(){
        parent::init();
    }

    public function initContent()
    {
        parent::initContent();
        $idCategory = -1;

        if(Tools::getValue('id_categorynews')){
            $idCategory = Tools::getValue('id_categorynews');

            $news = CategoryNews::getNewsAfterCategoryId($idCategory);
        }else{
            $news = News::getAll();
        }

        $categories = CategoryNews::getAll();
        $this->context->smarty->assign(array(
            'title' => 'News',
            'news' => $news,
            'categories' => $categories,
            'idCategory' => $idCategory,
            'newsUrl' => 'news',
            'displayNewsUrl' => 'display-newsletter'
        ));
        $this->setTemplate('module:dashnews/views/templates/front/news_page.tpl');
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->context->controller->addCSS($this->module->getLocalPath() . "views/css/news_page.css");
    }
}