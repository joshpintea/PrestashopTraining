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

    const NEWS_TABLE_NAME = 'news';
    const NEWS_LANG_TABLE_NAME = 'news_lang';
    const CATEGORY_TABLE_NAME = 'categorynews';
    const CATEGORY_LANG_TABLE_NAME = 'categorynews_lang';
    const NEWS_TO_CATEGORY_TABLE_NAME = 'news_categorynews';


    const IMG_DIR_NEWS = _PS_IMG_DIR_ . "dashnews";

    public $tabs;

    /**
     * DashNews constructor.
     */
    public function __construct()
    {
        $this->name = "dashnews";
        $this->tab = "dashboard";
        $this->version = '0.2';
        $this->author = "Josh Pintea";

        $this->initTabs();

        parent::__construct();

        $this->displayName = $this->trans('Dashboard News', array(), 'Modules.Dashnews.Admin');
        $this->description = $this->trans('See news', array(), 'Modules.Dashnews.Admin');
    }

    private function initTabs()
    {
        $this->tabs = array(
            0 => array(
                'name' => 'News',
                'parent' => true,
                'idParent' => 2,
                'className' => 'AdminParentNews'
            ),
            1 => array(
                'name' => 'News',
                'parent' => false,
                'idParent' => 0, // get tab id from the array at the
                'className' => 'AdminNews'
            ),
            2 => array(
                'name' => 'Categories',
                'parent' => false,
                'idParent' => 0,
                'className' => 'AdminCategoryNews'
            )
        );
    }

    public function install()
    {
        Configuration::updateValue('NUMBER_OF_NEWS_DISPLAYED', 4);
        $result = true;
        if (!parent::install()) {
            $result = false;
            $this->context->controller->errors[] = "can't installed the module";
        }

        if ($result && !$this->createTables()) {
            $result = false;
            $this->context->controller->errors[] = "can't create tables";
        }

        if ($result && !$this->createNavTabs()) {
            $result = false;
            $this->context->controller->errors[] = "can't create nav tabs";
        }

        if ($result && !$this->registerHook('displayHome')) {
            $result = false;
            $this->context->controller->errors[] = "can't register displayHome hook";
        }

        if ($result && !$this->registerHook('displayNav2')) {
            $result = false;
            $this->context->controller->errors[] = "can't register displayNav2 hook";
        }

        if ($result && !$this->registerHook('moduleRoutes')) {
            $result = false;
            $this->context->controller->errors[] = "can't register moduleRoutes hook";
        }

        if ($result && !$this->registerHook('header')) {
            $result = false;
            $this->context->controller->errors[] = "can't register header hook";
        }

        if ($result && !$this->createImgNewsDir()) {
            $result = false;
            $this->context->controller->errors[] = "can't create images dir";
        }

        return $result;
    }

    /**
     * after creating tables data from migration.csv will be imported in db
     * @return bool
     */
    private function createTables()
    {
        $queries = array(
            0 => "CREATE TABLE `shop`.`{$this->getNewsTableName()}` ( 
                                                `id_news` INT NOT NULL AUTO_INCREMENT , 
                                                `image` VARCHAR(100) NOT NULL , 
                                                `deleted` INT NOT null , 
                                                `date_from` DATE NOT null, 
                                                `date_to` DATE NOT null, 
                                                `active` BOOLEAN DEFAULT true,
                                                 PRIMARY KEY (`id_news`)) ENGINE = InnoDB",
            1 => "CREATE TABLE `shop`.`{$this->getNewsLangTableName()}` (
                                                `id_news` INT NOT NULL , `id_lang` INT NOT NULL , 
                                                `title` VARCHAR(200) NOT NULL , 
                                                `description` VARCHAR(200) NOT NULL ) ENGINE = InnoDB;",
            2 => "CREATE TABLE `shop`.`{$this->getCategoryTableName()}`(
                                                `id_categorynews` INT NOT NULL AUTO_INCREMENT,
                                                `active` BOOLEAN DEFAULT true,
                                                PRIMARY KEY (`id_categorynews`))  ENGINE = InnoDB;",
            3 => "CREATE TABLE `shop`.`{$this->getCategoryLangTableName()}`(
                                                `id_categorynews` INT NOT NULL,
                                                `id_lang` INT NOT NULL,
                                                `description` VARCHAR(200) NOT NULL,
                                                `title` VARCHAR(200) NOT NULL) ENGINE = InnoDB;",
            4 => "CREATE TABLE `shop`.`{$this->getNewsToCategoryTableName()}`(
                                                `id_categorynews` INT NOT NULL,
                                                `id_news` INT NOT NULL) ENGINE = InnoDB;",
        );

        $return = $this->executeQueries($queries);
        $return &= $this->importData();

        return $return;
    }

    private function getNewsTableName()
    {
        return _DB_PREFIX_ . SELF::NEWS_TABLE_NAME;
    }

    private function getNewsLangTableName()
    {
        return _DB_PREFIX_ . self::NEWS_LANG_TABLE_NAME;
    }

    private function getCategoryTableName()
    {
        return _DB_PREFIX_ . SELF::CATEGORY_TABLE_NAME;
    }

    private function getCategoryLangTableName()
    {
        return _DB_PREFIX_ . self::CATEGORY_LANG_TABLE_NAME;
    }

    private function getNewsToCategoryTableName()
    {
        return _DB_PREFIX_ . SELF::NEWS_TO_CATEGORY_TABLE_NAME;
    }

    /**
     * @param $queries array
     * @return bool
     */
    private function executeQueries($queries)
    {
        $db = Db::getInstance();
        $return = true;
        foreach ($queries as $query) {
            $return &= $db->execute($query);
        }
        return $return;
    }

    /**
     * read backup file and insert data into tables
     * @return bool
     * @throws PrestaShopDatabaseException
     */
    private function importData()
    {
        $return = true;
        $fileData = array_map('str_getcsv', file(_PS_MODULE_DIR_ . 'dashnews/backup/backup.csv'));

        $count = (int)$fileData[0][0];
        $currentLine = 1;

        $db = Db::getInstance();
        for ($counter = 0; $counter < $count; $counter++) {
            $tableName = $fileData[$currentLine][0];
            $currentLine++;
            $numberOfRows = $fileData[$currentLine][0];

            $currentLine++;
            $fields = $fileData[$currentLine];
            $currentLine++;

            for ($j = 0; $j < $numberOfRows; $j++) {
                $data = array();
                for ($r = 0; $r < count($fields); $r++) {
                    $data[$fields[$r]] = $fileData[$currentLine][$r];
                }
                $currentLine++;
                $return &= $db->insert($tableName, $data);
            }
        }

        return $return;
    }

    /**
     * create admin tabs after tabs field of this class
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    private function createNavTabs()
    {
        $return = true;
        foreach ($this->tabs as $pos => $tab) {
            if ($tab['parent']) {
                $this->tabs[$pos]['tab'] = $this->createTab($tab['name'], $tab['idParent'], $tab['className']);
                if (!$this->tabs[$pos]['tab']) {
                    $return = false;
                }
            } else {
                $this->tabs[$pos]['tab'] = $this->createTab($tab['name'], $this->tabs[$tab['idParent']]['tab']->id,
                    $tab['className']);
            }
        }

        return $return;
    }

    /**
     * create an admin after
     * return object is save operation is success, else return false
     * @param $name
     * @param $idParent
     * @param $className
     * @return bool|Tab
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
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
        return ($tab->save()) ? $tab : false;
    }

    /**
     * create news image directory
     * @return bool
     */
    private function createImgNewsDir()
    {
        return mkdir(self::IMG_DIR_NEWS);
    }

    public function uninstall()
    {
        $result = true;
        if (!parent::uninstall()) {
            $result = false;
            $this->context->controller->errors[] = "can't uninstall the module";
        }

        if ($result && !$this->deleteTables()) {
            $result = false;
            $this->context->controller->errors[] = "can't delete tables";
        }

        if ($result && !$this->deleteNavTabs()) {
            $result = false;
            $this->context->controller->errors[] = "can't delete nav tabs";
        }

        if ($result && !$this->deleteImgNewsDir()) {
            $result = false;
            $this->context->controller->errors[] = "can't delete img news dir";
        }

        return $result;
    }

    /**
     * before deleting tables,data from db will be saved into migration.csv file
     * @return bool
     */
    private function deleteTables()
    {
        $return = $this->exportData();

        $queries = array(
            0 => "DROP TABLE {$this->getNewsTableName()}",
            1 => "DROP TABLE {$this->getNewsLangTableName()}",
            2 => "DROP TABLE {$this->getCategoryTableName()}",
            3 => "DROP TABLE {$this->getCategoryLangTableName()}",
            4 => "DROP TABLE {$this->getNewsToCategoryTableName()}",
        );

        $return &= $this->executeQueries($queries);
        return $return;
    }

    /**
     * create a backup file
     * @return bool
     */
    private function exportData()
    {
        $file = fopen(_PS_MODULE_DIR_ . 'dashnews/backup/backup.csv', 'w');

        //number of tables
        fwrite($file, "5" . "\n");

        $return = true;

        $return &= $this->exportDataFromTable('news', $file);
        $return &= $this->exportDataFromTable('news_lang', $file);
        $return &= $this->exportDataFromTable('categorynews', $file);
        $return &= $this->exportDataFromTable('categorynews_lang', $file);
        $return &= $this->exportDataFromTable('news_categorynews', $file);

        fclose($file);
        return $return;
    }

    /**
     * write data from $tableName into the $file
     * @param $tableName
     * @param $file
     * @return bool
     */
    private function exportDataFromTable($tableName, $file)
    {
        fwrite($file, $tableName . "\n");
        $result = true;
        try {
            $query = new DbQuery();
            $query->select("*");
            $query->from($tableName);

            $data = Db::getInstance()->executeS($query);

            fwrite($file, count($data) . "\n");

            $fields = $this->getTableFields($tableName);
            fputcsv($file, $fields);

            foreach ($data as $row => $current) {
                fputcsv($file, $current);
            }
        } catch (PrestaShopException $e) {
            $result = false;
        }

        return $result;

    }

    /**
     * @param $tableName
     * @return array
     * @throws PrestaShopDatabaseException
     */
    private function getTableFields($tableName)
    {
        $query = "SELECT `COLUMN_NAME`
                      FROM `INFORMATION_SCHEMA`.`COLUMNS`
                      WHERE `TABLE_SCHEMA`='shop' 
                          AND `TABLE_NAME`='ps_{$tableName}';
                      ";
        $dbResult = Db::getInstance()->executeS($query);
        $result = array();
        foreach ($dbResult as $row => $value) {
            $result[] = $value['COLUMN_NAME'];
        }
        return $result;
    }

    private function deleteNavTabs()
    {
        $idParent = Tab::getIdFromClassName('AdminParentNews');
        $idNewsTab = Tab::getIdFromClassName('AdminNews');
        $idCategoryTab = Tab::getIdFromClassName('AdminCategoryNews');
        if ($idNewsTab && $idCategoryTab && $idParent) {
            try {
                $parent = new Tab($idParent);
                $newsTab = new Tab($idNewsTab);
                $categoryTab = new Tab($idCategoryTab);

                $newsTab->delete();
                $categoryTab->delete();
                $parent->delete();
            } catch (PrestaShopException $e) {
                return false;
            }

        }
        return true;
    }

    private function deleteImgNewsDir()
    {
        if (!is_dir(self::IMG_DIR_NEWS)) {
            return false;
        }

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

    public function hookDisplayHome($parameters)
    {
        $news = News::getAll(4);
        $this->context->smarty->assign(array(
            'title' => 'News',
            'news' => $news
        ));

        $this->context->controller->addCSS("modules/dashnews/views/css/news_page.css");

        return $this->display(__FILE__, 'display_news.tpl');
    }

    public function hookDisplayNav2($parameters)
    {
        $this->context->controller->addCSS("modules/dashnews/views/css/news_tab.css");
        return $this->display(__FILE__, 'news_tab.tpl');
    }

    public function hookHeader($params)
    {
        $this->context->controller->addCSS("modules/dashnews/views/css/news_tab.css");
    }

    public function hookModuleRoutes($params)
    {
        return array(
            'module-dashnews-createnews' => [
                'controller' => 'createnews',
                'rule' => 'news-create/',
                'params' => [
                    'fc' => 'module',
                    'module' => 'dashnews',
                    'controller' => 'createnews'
                ],
            ],
            'module-dashnews-newsletterpage' => [
                'controller' => 'newsletterpage',
                'rule' => 'display-newsletter/{id_news}',
                'keywords' => [
                    'id_news' => ['regexp' => '[0-9]+', 'param' => 'id_news']
                ],
                'params' => [
                    'fc' => 'module',
                    'module' => 'dashnews',
                    'controller' => 'newsletterpage'
                ],
            ],
            'module-dashnews-news' => [
                'controller' => 'news',
                'rule' => 'news/',
                'params' => [
                    'fc' => 'module',
                    'module' => 'dashnews',
                    'controller' => 'news'
                ]
            ]
        );
    }

    public function getContent()
    {
        $output = null;

        if (Tools::isSubmit('submit' . $this->name)) {
            $newsPageTitle = strval(Tools::getValue('title'));
            $numberOfNews = strval(Tools::getValue('numberOfNews'));

            if (!is_numeric($numberOfNews) || !$newsPageTitle || empty($newsPageTitle) || !Validate::isGenericName($newsPageTitle) ) {
                $output .= $this->displayError($this->l('Invalid Configuration value'));
            } else {
                Configuration::updateValue('NEWS_PAGE_TITLE', $newsPageTitle);
                Configuration::updateValue('NUMBER_OF_NEWS_DISPLAYED', $numberOfNews);

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
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l("Enter number of news displayed of news page"),
                    'name' => 'numberOfNews',
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

        $helper->submit_action = 'submit' . $this->name;

        $helper->fields_value['title'] = Configuration::get('title');
        $helper->fields_value['numberOfNews'] = Configuration::get('numberOfNews');

        return $helper->generateForm($fieldsForm);
    }
}
