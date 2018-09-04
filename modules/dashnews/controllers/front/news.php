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

        $news = News::getAll(4);

        $categories = CategoryNews::getAll();
        $this->context->smarty->assign(array(
            'title' => 'News',
            'news' => $news,
            'categories' => $categories,
            'newsUrl' => 'news',
            'displayNewsUrl' => 'display-newsletter'
        ));
        $this->setTemplate('module:dashnews/views/templates/front/news_page.tpl');
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->context->controller->addCSS($this->module->getLocalPath() . "views/css/news_page.css");
        $this->context->controller->addJS($this->module->getLocalPath() . "views/js/news.js");
    }
}