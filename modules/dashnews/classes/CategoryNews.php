<?php
/**
 * Created by Josh.
 * Date: 9/3/2018
 * Time: 10:57 AM
 */


class CategoryNews extends ObjectModel
{
    public static $definition = array(
        'table' => 'categorynews',
        'primary' => 'id_categorynews',
        'multilang' => true,
        'fields' => array(
            'active' => array('type' => self::TYPE_BOOL, 'shop' => true, 'validate' => 'isBool'),

            //lang fields
            'title' => array('type' => self::TYPE_STRING, 'lang' => true, 'required' => true),
            'description' => array('type' => self::TYPE_STRING, 'lang' => true, 'required' => true)
        )
    );
    /**
     * @var bool  Category status
     */
    public $active = true;
    public $description;
    public $title;

    public static function getAll()
    {

        $query = new DbQuery();
        $query->select('cn.`id_categorynews`, cnl.`title`, cnl.`description`');
        $query->from('categorynews', 'cn');
        $query->leftJoin('categorynews_lang', 'cnl', 'cnl.`id_categorynews` = cn.`id_categorynews`');
        $query->where('cnl.`id_lang` = ' . (int)Context::getContext()->language->id . ' AND cn.`active` = true ');

        return Db::getInstance()->executeS($query);
    }

    public static function getNewsAfterCategoryId($idCategoryNews){
        $query = new DbQuery();
        $query->select('n.`id_news`,n.`image`,n.`date_from`,n.`date_to`,nl.`title`,nl.`description`');
        $query->from('news','n');
        $query->leftJoin('news_lang','nl','n.`id_news`=nl.`id_news`');
        $query->leftJoin('news_categorynews','ncn','n.`id_news` = ncn.`id_news`');
        $query->where('nl.`id_lang` = ' . (int)Context::getContext()->language->id . ' AND n.`active` = true AND ncn.`id_categorynews` = ' . $idCategoryNews);

        return Db::getInstance()->executeS($query);

        
    }
}
