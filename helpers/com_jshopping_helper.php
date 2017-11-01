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

class JLLikeLock_com_jshopping_helper extends plgJLLikeLockHelper
{
    var $params = null;

    function __construct($pluginParams){
        $this->params = $pluginParams;
    }

    function go($context, &$article, &$params, $page){
        $print = JRequest::getCmd('print');
        $category_id = JRequest::getInt('category_id', 0);

        if (empty($article->product_id)){
            return true;
        }

        if (strpos($article->text, '{jllikelock-off}') !== false) {
            $article->text = self::cleanText($article->text);
            return true;
        }

        $exceptcat = is_array($this->params->get('js_categories')) ? $this->params->get('js_categories') : array($this->params->get('js_categories'));

        if (in_array($category_id, $exceptcat))
        {
            $article->text = self::cleanText($article->text);
            return true;
        }

        if ($context == 'com_content.article')
        {
            If (!$print)
            {
                if (strpos($article->text, '{jllikelock') !== false)
                {
                    $article->text = self::cuttext(
                            $article->text,
                            $article->product_id,
                            '1',
                            $this->params,
                            'cindex',
                            $this->params->get('addindextxt')
                        )
                        . '<div class="cls"></div>';
                }
                else if ($this->params->get('js_autoAdd') == 1)
                {
                    $ShowContent = self::ShowIN($article->text, $article->product_id, $this->params);
                    $article->text = '<div id="jllikekeys">'
                        . self::limittext(
                            $this->params->get('js_textlimits'),
                            '',
                            $article->text,
                            $article->product_id,
                            '0',
                            'cindex',
                            $this->params->get('addindextxt')
                        )
                        . '<br clear="both" />' . $ShowContent . '</div><div class="cls"></div>';
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