<?php
/**
 * Created by Josh.
 * Date: 9/4/2018
 * Time: 12:20 PM
 */


class dashnewsnewsletterpageModuleFrontController extends ModuleFrontController
{
    public function init(){
        parent::init();
    }

    public function initContent()
    {
        parent::initContent();

        if(Tools::getValue('id_news')){
            $newsLetter = News::getNewsLetterAfterId(Tools::getValue('id_news'));
        }

        $this->context->smarty->assign(array(
            'title' => 'News',
            'newsLetter' => $newsLetter
        ));

        $this->setTemplate('module:dashnews/views/templates/front/display-newsletter.tpl');
    }

    public function setMedia()
    {
        parent::setMedia();
        $this->context->controller->addCSS($this->module->getLocalPath() . "views/css/display-news-letter.css");
    }
}