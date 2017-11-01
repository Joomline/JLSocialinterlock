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

class JLLikeLock_com_k2_helper extends plgJLLikeLockHelper
{
    var $params = null;

    function __construct($pluginParams)
    {
        $this->params = $pluginParams;
    }

    function go($context, &$article, &$params, $page)
    {
        $addindextxt = $this->params->get('addindextxt');
        $textlimits = $this->params->get('k2textlimits');
        $autoAdd = $this->params->get('k2autoAdd');
        $excludeCats = $this->params->get('k2categories', array());

        if(in_array($article->catid, $excludeCats))
        {//если категория исключена из обработки плагином - чистим и закругляемся
            $article->introtext = self::cleanText($article->introtext);
            $article->fulltext = self::cleanText($article->fulltext);
            return true;
        }

        if (strpos($article->introtext, '{jllikelock-off}') !== false
            || strpos($article->fulltext, '{jllikelock-off}') !== false)
        {//если категория исключена из обработки плагином - чистим и закругляемся
            $article->introtext = self::cleanText($article->introtext);
            $article->fulltext = self::cleanText($article->fulltext);
            return true;
        }

        switch ($context)
        {
            case 'com_k2.itemlist':
                if(strpos($article->introtext, '{jllikelock') !== false)
                {
                    $article->text = self::cuttext($article->text, $article->id, '0', $this->params, 'cindex', $addindextxt);
                    $article->fulltext = '';
                }
                else if ($autoAdd == 1)
                {
                    $article->text = self::limittext($textlimits, $article->text, '', $article->id, '1', 'cindex', $addindextxt);
                    $article->fulltext = '';
                }
                else
                {
                    $article->introtext = self::cleanText($article->introtext);
                    $article->fulltext = self::cleanText($article->fulltext);
                }
                break;

            case 'com_k2.item':

				if(!$article->id){
					return;
				}

                $k2attach = $this->params->get('k2attach', 'fulltext');

                if (strpos($article->$k2attach, '{jllikelock') !== false)
                {//скрытие текста в теге
                    $article->$k2attach  = self::cuttext($article->$k2attach, $article->id, '1', $this->params, 'cindex', $addindextxt) . '<div class="cls"></div>';
                }
                else if ($autoAdd == 1)
                {//скрытие по счетчику символов
                    $ShowContent = self::ShowIN($article->$k2attach, $article->id, $this->params);
                    $article->$k2attach = '<div id="jllikekeys">' . self::limittext($textlimits, $article->$k2attach, '', $article->id, '0', 'cindex', $addindextxt) . '<br clear="both" />' . $ShowContent . '</div><div class="cls"></div>';
                }
                else
                {
                    $article->introtext = self::cleanText($article->introtext);
                    $article->fulltext = self::cleanText($article->fulltext);
                }
                break;

            default:
                $article->introtext = self::cleanText($article->introtext);
                $article->fulltext = self::cleanText($article->fulltext);
                break;
        }

    }
}