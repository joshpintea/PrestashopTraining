<?php
/**
 * Created by Josh.
 * Date: 8/28/2018
 * Time: 1:43 PM
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

require_once(_PS_MODULE_DIR_ . "dashnews/classes/News.php");

class DashNews extends Module
{

    const TABLE_NAME = 'news';
    const CATEGORY_TABLE_NAME = 'categorynews';
    const IMG_DIR_NEWS = _PS_IMG_DIR_ . "dashnews";
    const NEWS_TO_CATEGORY = 'news_categorynews';

    /**
     * DashNews constructor.
     */
    public function __construct()
    {
        $this->name = "dashnews";
        $this->tab = "dashboard";
        $this->version = '0.2';
        $this->author = "Josh Pintea";

        parent::__construct();

        $this->displayName = $this->trans('Dashboard News', array(), 'Modules.Dashnews.Admin');
        $this->description = $this->trans('See news', array(), 'Modules.Dashnews.Admin');
    }

    public function install()
    {
        return parent::install() && $this->createNavTabs()
            && $this->createTables()
            && $this->registerHook('displayHome')
            && $this->registerHook('displayNav2')
            && $this->registerHook('moduleRoutes')
            && $this->registerHook('header')
            && $this->createImgNewsDir()
            && $this->insertNewsPage();
    }

    private function createNavTabs()
    {
        $parent = $this->createTab('News', 2, "AdminParentNews");
        $this->createTab('News', $parent->id, "AdminNews");
        $this->createTab('Categories', $parent->id, "AdminCategoryNews");

        return true;
    }

    private function createTab($name, $idParent, $className)
    {
        $tab = new Tab();
        $languages = Language::getLanguages(true);
        foreach ($languages as $language) {
            $tab->name[$language['id_lang']] = $name;
        }
        $tab->module = $this->name;
        $tab->active = 1;
        $tab->id_parent = $idParent;
        $tab->class_name = $className;

        $tab->save();

        return $tab;
    }

    private function createTables()
    {
        $queries = array(
            0 => "CREATE TABLE `shop`.`{$this->getNewsTableTable()}` ( `id_news` INT NOT NULL AUTO_INCREMENT , 
                                                `image` VARCHAR(100) NOT NULL , 
                                                `deleted` INT NOT null , 
                                                `date_from` DATE NOT null, 
                                                `date_to` DATE NOT null, 
                                                `active` BOOLEAN DEFAULT true,
                                                PRIMARY KEY (`id_news`)) ENGINE = InnoDB",
            1 => "CREATE TABLE `shop`.`{$this->getNewsTableTable()}_lang` ( `id_news` INT NOT NULL , `id_lang` INT NOT NULL , 
                                                                                `title` VARCHAR(200) NOT NULL , 
                                                                                `description` VARCHAR(200) NOT NULL ) ENGINE = InnoDB;",
            2 => "CREATE TABLE `shop`.`{$this->getCategoryTableName()}`
                                (`id_categorynews` INT NOT NULL AUTO_INCREMENT,
                                `active` BOOLEAN DEFAULT true,
                                PRIMARY KEY (`id_categorynews`))  ENGINE = InnoDB;",
            3 => "CREATE TABLE `shop`.`{$this->getCategoryTableName()}_lang`
                                (`id_categorynews` INT NOT NULL,
                                  `id_lang` INT NOT NULL,
                                  `description` VARCHAR(200) NOT NULL,
                                  `title` VARCHAR(200) NOT NULL
                                ) ENGINE = InnoDB;",
            4 => "CREATE TABLE `shop`.`{$this->getNewsToCategoryTableName()}`
                                (`id_categorynews` INT NOT NULL,
                                  `id_news` INT NOT NULL
                                ) ENGINE = InnoDB;",
        );

        $db = Db::getInstance();

        foreach ($queries as $query) {
            $ok = $db->execute($query);
            if ($ok == false) {
                return false;
            }
        }
        return true;
    }

    private function getNewsTableTable()
    {
        return _DB_PREFIX_ . SELF::TABLE_NAME;
    }

    private function getCategoryTableName()
    {
        return _DB_PREFIX_ . SELF::CATEGORY_TABLE_NAME;
    }

    private function getNewsToCategoryTableName()
    {
        return _DB_PREFIX_ . SELF::NEWS_TO_CATEGORY;
    }

    private function createImgNewsDir()
    {
        return mkdir(self::IMG_DIR_NEWS);
    }

    private function insertNewsPage()
    {
        $db = Db::getInstance();

        $db->insert('meta', array('page' => 'module-dashnews-news'));

        $query = new DbQuery();
        $query->select('id_meta');
        $query->from('meta');
        $query->where("page='module-dashnews-news'");

        $obj = $db->executeS($query);

        $db->insert('meta_lang', array(
            'id_meta' => $obj[0]["id_meta"],
            'id_shop' => 1,
            'id_lang' => 1,
            'title' => 'news',
            'url_rewrite' => 'news'
        ));

        $db->insert('meta_lang', array(
            'id_meta' => $obj[0]["id_meta"],
            'id_shop' => 1,
            'id_lang' => 2,
            'title' => 'noutati',
            'url_rewrite' => 'noutati'
        ));

        return true;
    }

    public function uninstall()
    {
        return parent::uninstall()
            && Configuration::deleteByName($this->name)
            && $this->deleteTable()
            && $this->deleteImgNewsDir()
            && $this->deleteNavTab()
            && $this->deleteNewsPage();
    }

    private function deleteTable()
    {
        $queries = array(
            0 => "DROP TABLE {$this->getNewsTableTable()}",
            1 => "DROP TABLE {$this->getNewsTableTable()}_lang",
            2 => "DROP TABLE {$this->getCategoryTableName()}",
            3 => "DROP TABLE {$this->getCategoryTableName()}_lang",
            4 => "DROP TABLE {$this->getNewsToCategoryTableName()}",
        );

        $db = Db::getInstance();
        foreach ($queries as $query) {
            $ok = $db->execute($query);
            if ($ok == false) {
                return false;
            }
        }

        return true;
    }

    private function deleteImgNewsDir()
    {
        $this->readDirAndDeleteAllFiles(self::IMG_DIR_NEWS);
        return true;
    }

    private function readDirAndDeleteAllFiles($path)
    {
        $dirContent = scandir($path);

        foreach ($dirContent as $key => $name) {
            if ($name !== ".." && $name != ".")// parent directory and current directory
            {
                $fileName = $path . "/{$name}";
                if (is_dir($fileName)) {
                    readDirAndDeleteAllFiles($fileName);
                } else {
                    unlink($fileName);
                }
            }
        }
        rmdir($path);
    }

    private function deleteNavTab()
    {
        $idParent = Tab::getIdFromClassName('AdminParentNews');
        $idNewsTab = Tab::getIdFromClassName('AdminNews');
        $idCategoryTab = Tab::getIdFromClassName('AdminNewsCategory');
        if ($idNewsTab && $idCategoryTab && $idParent) {
            $parent = new Tab($idParent);
            $newsTab = new Tab($idNewsTab);
            $categoryTab = new Tab($idCategoryTab);

            $newsTab->delete();
            $categoryTab->delete();
            $parent->delete();
        }
        return true;
    }

    private function deleteNewsPage()
    {
        $db = Db::getInstance();

        $query = new DbQuery();
        $query->select('id_meta');
        $query->from('meta');
        $query->where("page='module-dashnews-news'");

        $obj = $db->executeS($query);

        $db->delete('meta_lang', 'id_meta=' . $obj[0]["id_meta"]);

        $db->delete('meta', 'id_meta=' . $obj[0]["id_meta"]);

        return true;
    }

    public function hookDisplayHome($parameters)
    {
        $news = News::getAll(4);
        $this->context->smarty->assign(array(
            'title' => 'News',
            'news' => $news
        ));

        $this->context->controller->addCSS("modules/dashnews/views/css/news_page.css");

        return "<h1>News<h1>" . $this->display(__FILE__, 'display_news.tpl');
    }

    public function hookDisplayNav2($parameters)
    {

        $this->context->controller->addCSS("modules/dashnews/views/css/news_tab.css");
        return $this->display(__FILE__, 'news_tab.tpl');
    }

    public function hookHeader($params){
        $this->context->controller->addCSS("modules/dashnews/views/css/news_tab.css");
    }

    public function hookModuleRoutes($params){
        return  [
            'module-dashnews-newsletterpage' => [
                'controller' => 'newsletterpage',
                'rule' => 'display-newsletter/{id_news}',
                'keywords' => [
                    'id_news' => ['regexp' => '[0-9]+', 'param' => 'id_news']
                ],
                'params' => [
                    'fc' => 'module',
                    'module' => 'dashnews',
                    'controller' => 'news-letterpage'
                ],
            ],
        ];
    }

    public function getContent()
    {
        $output = null;

        if (Tools::isSubmit('submit' . $this->name)) {
            $newsPageTitle = strval(Tools::getValue('title'));
            if (!$newsPageTitle || empty($newsPageTitle) || !Validate::isGenericName($newsPageTitle)) {
                $output .= $this->displayError($this->l('Invalid Configuration value'));
            } else {
                $db = Db::getInstance();

                $query = new DbQuery();
                $query->select('id_meta');
                $query->from('meta');
                $query->where("page='module-dashnews-news'");

                $obj = $db->executeS($query);

                Db::getInstance()->update('meta_lang', array('title'=>$newsPageTitle),'id_meta=' . $obj[0]['id_meta'] . ' AND id_lang=1');

                $output .= $this->displayConfirmation($this->l('Settings updated'));
            }
        }
        return $output . $this->displayForm();
    }

    public function displayForm()
    {
        $fieldsForm[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Settings'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Enter news page title'),
                    'name' => 'title',
                    'size' => 20,
                    'required' => true
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            )
        );

        $helper = new HelperForm();

        $helper->toolbar_btn = array(
            'save' =>
                array(
                    'desc' => $this->l('Save'),
                    'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name .
                        '&token=' . Tools::getAdminTokenLite('AdminModules'),
                ),
            'back' => array(
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );

        $helper->submit_action = 'submit'.$this->name;


        $helper->fields_value['title'] = Configuration::get('title');


        return $helper->generateForm($fieldsForm);
    }
}
