<?php
/**
 * Plugin JL Social Interlock
 *
 * @version 1.4
 * @author Artem Zhukov, Vadim Kunicin, Arkadiy Sedelnikov (sale@joomline.ru)
 * @copyright (C) 2013 by Artem Zhukov, Vadim Kunicin, Arkadiy Sedelnikov(http://www.joomline.ru)
 * @license GNU/GPL: http://www.gnu.org/copyleft/gpl.html
 **/

// no direct access
defined('_JEXEC') or die;

require_once dirname(__FILE__) . '/helper.php';

class JLLikeLock_com_zoo_helper extends plgJLLikeLockHelper
{
    var $params = null;

    function __construct($pluginParams)
    {
        $this->params = $pluginParams;
    }

    function go($context, &$article, &$params, $page)
    {
        $addindextxt = $this->params->get('addindextxt', '');
        $textlimits = $this->params->get('zootextlimits', 5);
        $autoAdd = $this->params->get('zooautoAdd', 1);
//        $excludeCats = $this->params->get('zoocategories', array());

        $input = new JInput();
        $view = $input->getString('view', '');
        $layout = $input->getString('layout', '');
        $task = $input->getString('task', '');
        $item_id = $input->getInt('item_id', 0);


//        if(in_array($article->catid, $excludeCats))
//        {//если категория исключена из обработки плагином - чистим и закругляемся
//            $article->text = self::cleanText($article->text);
//            return true;
//        }

        if (strpos($article->text, '{jllikelock-off}') !== false)
        {//если категория исключена из обработки плагином - чистим и закругляемся
            $article->text = self::cleanText($article->text);
            return true;
        }


        $entity = '';
        if($view == 'category' && $layout == 'category')
        {
            $entity = 'category';
        }
        else if($task == 'item' || ($view == 'item' && $layout == 'item'))
        {
            $entity = 'item';
        }

        switch ($entity)
        {
            case 'category':
                if(strpos($article->text, '{jllikelock') !== false)
                {
                    $article->text = self::cuttext($article->text, $item_id, '0', $this->params, 'cindex', $addindextxt);
                }
                else if ($autoAdd == 1)
                {
                    $article->text = self::limittext($textlimits, $article->text, '', $item_id, '0', 'cindex', $addindextxt);
                }
                else
                {
                    $article->text = self::cleanText($article->text);
                }
                break;

            case 'item':
                if(strpos($article->text, '{jllikelock') !== false)
                {
                    $article->text = self::cuttext($article->text, $item_id, '1', $this->params, 'cindex', $addindextxt);
                }
                else if ($autoAdd == 1)
                {
                    $ShowContent = self::ShowIN($article->text, $article->id, $this->params);
                    $article->text = '<div id="jllikekeys">' . self::limittext($textlimits, $article->text, '', $article->id, '0', 'cindex', $addindextxt) . '<br clear="both" />' . $ShowContent . '</div><div class="cls"></div>';
                }
                else
                {
                    $article->text = self::cleanText($article->text);
                }
                break;

            default:
                $article->text = self::cleanText($article->text);
                break;
        }

    }
}