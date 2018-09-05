<?php
/**
 * Created by Josh.
 * Date: 8/29/2018
 * Time: 11:06 AM
 */

require_once "CategoryNews.php";

/**
 * Class News
 */
class News extends ObjectModel
{
    /**
     * @see ObjectModel::$definition
     */
    public static $definition = array(
        'table' => 'news',
        'primary' => 'id_news',
        'multilang' => true,
        'fields' => array(
            'image' => array('type' => self::TYPE_STRING),
            'date_from' => array('type' => self::TYPE_DATE, 'required' => true, 'validate' => 'isDate'),
            'date_to' => array('type' => self::TYPE_DATE, 'required' => true, 'validate' => 'isDate'),
            'active' => array('type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isBool'),

            //lang fields
            'description' => array('type' => self::TYPE_STRING, 'lang' => true, 'required' => true),
            'title' => array(
                'type' => self::TYPE_STRING,
                'lang' => true,
                'validate' => 'isGenericName',
                'required' => true
            ),
            'deleted' => array('type' => self::TYPE_INT),
        )
    );
    public $id_news;
    public $image;
    public $description;
    public $title;
    public $deleted;
    public $date_from;
    public $date_to;
    /**
     * @var bool  News status
     */
    public $active = true;

    /**
     * return all news from db who are actives and date_to is greater than current date
     * @param int $limit represents the limit of the query
     * @param string $filter
     * @return array|false|mysqli_result|null|PDOStatement|resource
     * @throws PrestaShopDatabaseException
     */
    public static function getAll($limit = 0,$filter = '')
    {
        $currentDate = date("Y-m-d");

        $query = new DbQuery();
        $query->select('n.`id_news`, n.`image`,n.`date_from`,n.`date_to`, nl.`title`, nl.`description`');
        $query->from('news', 'n');
        $query->leftJoin('news_lang', 'nl', 'nl.`id_news` = n.`id_news`');
        $query->where('nl.`id_lang` = '
            . (int)Context::getContext()->language->id
            . ' AND n.`active` = true'
            . ' AND n.`date_to` > ' . "'" . $currentDate . "'"
            . ' AND nl.`title` LIKE ' . "'%{$filter}%'"
        );
        $query->orderBy('n.date_to DESC');
        if ($limit != 0) {
            $query->limit($limit);
        };

        return Db::getInstance()->executeS($query);
    }

    /**
     * get all id_categories for this news
     * @return array
     * @throws PrestaShopDatabaseException
     */
    public function getCategories()
    {
        $query = new DbQuery();
        $query->select('id_categorynews');
        $query->from('news_categorynews');
        $query->where('id_news=' . $this->id_news);

        $resultQuery = Db::getInstance()->executeS($query);
        $result = array();

        foreach ($resultQuery as $key => $value) {
            $result[] = $value['id_categorynews'];
        }

        return $result;
    }

    public static function getNewsLetterAfterId($idNews){
        $query = new DbQuery();
        $query->select('n.`id_news`, n.`image`,n.`date_from`,n.`date_to`, nl.`title`, nl.`description`');
        $query->from('news', 'n');
        $query->leftJoin('news_lang', 'nl', 'nl.`id_news` = n.`id_news`');
        $query->where('nl.`id_lang` = ' . (int)Context::getContext()->language->id . ' AND n.`active` = true AND n.id_news = ' . $idNews);

        return Db::getInstance()->getRow($query);
    }

    public static function uploadImg($id, $targetDir, $imageName)
    {
        if ($_FILES[$imageName]['name'] !== '') {

            $fileName = "{$id}." . substr($_FILES[$imageName]['name'],
                    strrpos($_FILES[$imageName]['name'], '.') + 1); //get extension of the file
            $targetFile = $targetDir . "/{$fileName}";

            $check = getimagesize($_FILES[$imageName]["tmp_name"]);
            if ($check !== false) {
                move_uploaded_file($_FILES[$imageName]["tmp_name"], $targetFile);
                return $fileName;
            } else {
                return '';
            }
        }
        return '';
    }
}