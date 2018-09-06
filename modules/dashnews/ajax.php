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

$news = array();
switch (Tools::getValue('method')) {
    case 'getAfterIdCategory' :
        if (Tools::getValue('idCategory')) {
            $news = CategoryNews::getNewsAfterCategoryId(Tools::getValue('idCategory'), Tools::getValue('filter'));
        } else {
            die(Tools::jsonEncode(array('error' => 'Bad submit')));
        }
        break;
    case 'filterNews' :
        $news = News::getAll(Configuration::get('NUMBER_OF_NEWS_DISPLAYED'), Tools::getValue('filter'));
        break;
    case 'getAllNews' :
        $news = News::getAll(Configuration::get('NUMBER_OF_NEWS_DISPLAYED'));
        break;
    default:
        exit;
}
die(renderNews($news));
exit;

/**
 * @param $news array
 * @return string
 */
function renderNews($news)
{
    $translator = Context::getContext()->getTranslator();
    $link = Context::getContext()->link;
    $html = "";

    $newsBaseLink = $link->getModuleLink('dashnews','newsletterpage',array('id_news' =>'1'));
    $newsBaseLink = substr($newsBaseLink, 0,strlen($newsBaseLink)-1);

    foreach ($news as $key => $newsLetter) {
        $divTitle = createDOMElement(array(
                'tagName' => 'div',
                'attr' => array(
                    'class' => array('news-title')
                ),
                'content' => "<h1>" . $translator->trans("Title") . ":</h1>" . $newsLetter['title'],
                'print' => false
            )
        );
        $divDescription = createDOMElement(array(
                'tagName' => 'div',
                'attr' => array(
                    'class' => array('news-descrition')
                ),
                'content' => "<b>" . $translator->trans("Description") . ":</b>" . $newsLetter['description'],
                'print' => false
            )
        );

        $divImage = createDOMElement(array(
                'tagName' => 'div',
                'attr' => array(
                    'class' => array('news-image'),
                ),
                'style' => array(
                    'background-image' => "url('../../../img/dashnews/" . $newsLetter['image'] . "')"
                ),
                'print' => false
            )
        );

        $divNews = createDOMElement(array(
            'tagName' => 'div',
            'attr' => array('class' => 'news'),
            'content' => $divTitle . $divDescription . $divImage,
            'print' => false
        ));

        $link = createDOMElement(array(
                'tagName' => 'a',
                'attr' => array('href' => $newsBaseLink . $newsLetter['id_news']),
                'content' => $divNews,
                'print' => false
            )
        );

        $html .= $link;
    }
    return $html;
}

function createDOMElement($argument)
{
    $attr = "";

    if (isset($argument['attr'])) {
        foreach ($argument['attr'] as $key => $value) {
            if (is_array($value)) {
                $attr .= "{$key}=\"";

                $attr .= implode(' ', $value);
                $attr .= "\" ";
            } else {
                $attr .= "{$key}=\"{$value}\" ";
            }
        }
    }

    $style = '';
    if(isset($argument['style'])){
        $style .= 'style="';
        foreach ($argument['style'] as $key => $value){
            $style .= ($key . ':' . $value . ';');
        }
        $style .= '"';
    }

    $arg = "<{$argument['tagName']} " . $style . " ";
    $arg .= $attr . ">";

    if (isset($argument['content'])) {
        $arg .= $argument['content'];
    }

    $arg .= "</{$argument['tagName']}>";
    if (isset($argument['print']) && !$argument['print']) {
        return $arg;
    }
    echo $arg;
    return $arg;
}

