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

class JLLikeLock_com_content_helper extends plgJLLikeLockHelper
{
    var $params = null;

    function __construct($pluginParams){
        $this->params = $pluginParams;
    }

    function go($context, &$article, &$params, $page){
        $print = JRequest::getCmd('print');

        if (strpos($article->text, '{jllikelock-off}') !== false) {
            $article->text = self::cleanText($article->text);
            return true;
        }

        $exceptcat = is_array($this->params->get('categories')) ? $this->params->get('categories') : array($this->params->get('categories'));

        if (in_array($article->catid, $exceptcat))
        {
            $article->text = self::cleanText($article->text);
            return true;
        }

        if ($context == 'com_content.category')
        {
            if(strpos($article->text, '{jllikelock') !== false)
            {
                $article->text = self::cuttext($article->text, $article->id, '0', $this->params, 'cindex', $this->params->get('addindextxt'));

            }
            else if ($this->params->get('autoAdd') == 1)
            {
                $article->text = self::limittext($this->params->get('textlimits'), $article->introtext, $article->fulltext, $article->id, '1', 'cindex', $this->params->get('addindextxt'));
            }
            else
            {
                $article->text = self::cleanText($article->text);
            }
        }
        else if ($context == 'com_content.article')
        {
            If (!$print)
            {
                if (strpos($article->text, '{jllikelock') !== false)
                {
                    $article->text = self::cuttext($article->text, $article->id, '1', $this->params, 'cindex', $this->params->get('addindextxt')) . '<div class="cls"></div>';
                }
                else if ($this->params->get('autoAdd') == 1)
                {
                    $ShowContent = self::ShowIN($article->introtext, $article->id, $this->params);
                    $article->text = '<div id="jllikekeys">' . self::limittext($this->params->get('textlimits'), $article->introtext, $article->fulltext, $article->id, '0', 'cindex', $this->params->get('addindextxt')) . '<br clear="both" />' . $ShowContent . '</div><div class="cls"></div>';
                }
                else
                {
                    $article->text = self::cleanText($article->text);
                }
            }
            else
            {
                $article->text = self::cleanText($article->text);
            }
        }
        else
        {
            $article->text = self::cleanText($article->text);
        }
    }
}