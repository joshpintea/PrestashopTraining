<?php
/**
 * Created by Josh.
 * Date: 9/4/2018
 * Time: 3:50 PM
 */

require_once(_PS_MODULE_DIR_ . "dashnews/classes/CategoryNews.php");
require_once(_PS_MODULE_DIR_ . "dashnews/classes/News.php");

class dashnewscreatenewsModuleFrontController extends ModuleFrontController
{

    public function setMedia()
    {
        parent::setMedia();
        $this->context->controller->addCSS($this->module->getLocalPath() . "views/css/create_news.css");
    }

    public function postProcess()
    {
        if (Tools::isSubmit('create-news')) {
            $title = pSQL(Tools::getValue('title'));
            $description = pSQL(Tools::getValue('description'));
            $dateFrom = Tools::getValue('date-from');
            $dateTo = Tools::getValue('date-to');
            $categories = Tools::getValue('categories');

            //validate input
            $error = $this->validate($title, $description, $dateFrom, $dateTo);

            if ($error !== '') {
                $this->context->smarty->assign(array(
                    'error' => $error
                ));
                $this->initContent();
            } else {
                $this->insertNewsLetter($dateFrom, $dateTo, $title, $description, $categories);
            }
        }
    }

    /**
     * @param $title
     * @param $description
     * @param $dateFrom
     * @param $dateTo
     * @return string
     */
    private function validate($title, $description, $dateFrom, $dateTo)
    {
        $error = '';
        if ($title === '') {
            $error = 'Title is required';
        } else {
            if ($description === '') {
                $error = 'Description is required';
            } else {
                if ($dateFrom > $dateTo) {
                    $error = 'Date-to need to be greater than date-from';
                }
            }
        }
        return $error;
    }

    public function initContent()
    {
        parent::initContent();

        $customerEmail = $this->context->customer->email;

        $query = new DbQuery();
        $query->select("*");
        $query->from('employee');
        $query->where('email=' . "'{$customerEmail}'");

        $employee = Db::getInstance()->getRow($query);

        if ($customerEmail === null || !$employee) {
            return $this->setTemplate('module:dashnews/views/templates/front/401_unauthorized.tpl');
        }

        $categories = CategoryNews::getAll();

        $this->context->smarty->assign(array(
            'categories' => $categories,
        ));

        return $this->setTemplate('module:dashnews/views/templates/front/create_news.tpl');
    }

    /**
     * @param $dateFrom
     * @param $dateTo
     * @param $title
     * @param $description
     * @param $categories
     * @throws PrestaShopDatabaseException
     */
    private function insertNewsLetter($dateFrom, $dateTo, $title, $description, $categories)
    {
        $db = Db::getInstance();
        $db->insert('news',
            array('deleted' => 0, 'date_from' => $dateFrom, 'date_to' => $dateTo, 'active' => 1));
        $id = $db->Insert_ID();

        $db->insert('news_lang', array(
            'id_news' => $id,
            'id_lang' => $this->context->language->id,
            'title' => $title,
            'description' => $description
        ));

        $fileName = News::uploadImg($id, _PS_IMG_DIR_ . "dashnews", 'image');
        $db->update('news', array('image' => $fileName), 'id_news=' . $id);

        if ($categories) {
            foreach ($categories as $key => $idCategory) {
                $db->insert('news_categorynews', array('id_categorynews' => $idCategory, 'id_news' => $id));
            }
        }
    }
}