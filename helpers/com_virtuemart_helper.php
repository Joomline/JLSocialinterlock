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

class JLLikeLock_com_virtuemart_helper extends plgJLLikeLockHelper
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

        $exceptcat = is_array($this->params->get('virt_categories')) ? $this->params->get('virt_categories') : array($this->params->get('virt_categories'));

        if (in_array($article->virtuemart_category_id, $exceptcat))
        {
            $article->text = self::cleanText($article->text);
            return true;
        }

        if ($context == 'com_virtuemart.productdetails')
        {
            If (!$print)
            {
                if (strpos($article->text, '{jllikelock') !== false)
                {
                    $article->text = self::cuttext(
                            $article->text,
                            $article->virtuemart_product_id,
                            '1',
                            $this->params,
                            'cindex',
                            $this->params->get('addindextxt')
                        )
                        . '<div class="cls"></div>';
                }
                else if ($this->params->get('virt_autoAdd') == 1)
                {
                    $ShowContent = self::ShowIN($article->text, $article->virtuemart_product_id, $this->params);
                    $article->text = '<div id="jllikekeys">'
                        . self::limittext(
                            $this->params->get('virt_textlimits'),
                            '',
                            $article->text,
                            $article->virtuemart_product_id,
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