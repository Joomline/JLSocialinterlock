<?php
/**
 * Plugin JL Social Interlock
 *
 * @version 2.1.0
 * @author Artem Zhukov, Vadim Kunicin, Arkadiy Sedelnikov (sale@joomline.ru)
 * @copyright (C) 2013 by Artem Zhukov, Vadim Kunicin, Arkadiy Sedelnikov(http://www.joomline.ru)
 * @license GNU/GPL: http://www.gnu.org/copyleft/gpl.html
 **/

// no direct access
defined('_JEXEC') or die;

class plgJLLikeLockHelper
{
    /** Функция для вызова замка в любом месте компонентов joomla
     * @param $hiddenHtml - скрытый замком html
     * @param $href - ссылка на расшариваемую страницу
     * @param bool $hideLikes - скрывать лайки после открытия замка (срабатывает после перезагрузки страницы)
     * @param int $itemId - не обязательно.
     * @return string
     * Пример использования функции:
     require_once JPATH_ROOT.'/plugins/system/jllikelock/helpers/helper.php';
     $link = 'http://poligon25.argens.ru/index.php?option=com_content&view=article&id=5&catid=25&Itemid=318';
     $hiddenHtml = '<p>Привет!</p>';
     echo plgJLLikeLockHelper::loadLikeLock($hiddenHtml, $link);
     */
    static function loadLikeLock($hiddenHtml, $href, $hideLikes=true, $itemId=0)
    {
        $plugin = JPluginHelper::getPlugin('system', 'jllikelock');
        $pluginParams = new JRegistry($plugin->params);
        $ShowContent = self::ShowIN('', $itemId, $pluginParams, $href);
        $cookieName = 'jltindex_' . 'cindex' . $itemId;
        $lockIsOpen = true;
        if (!(!empty($_COOKIE['jllikelockon'])
            && isset($_COOKIE[$cookieName])
            && ($_COOKIE[$cookieName] == 'cindex' . $itemId)
        ))
        {
            $lockIsOpen = false;
        }

        if(!$lockIsOpen)
        {
            $hiddenHtml = ($pluginParams->get('addindextxt', 0) == 1) ? $hiddenHtml : base64_encode($hiddenHtml);
            $hiddenHtml = '<div class="jlLikeLockAlltxt" style="display:none;float:left;">' . $hiddenHtml . '</div>';
            $lockIsOpen = false;
        }

        if($hideLikes && $lockIsOpen)
        {
            $text = $hiddenHtml;
        }
        else
        {
            $text = '
            <div class="jllikekeys">
                ' . $hiddenHtml . '
                <br clear="both" />
                ' . $ShowContent . '
            </div>
            <div class="cls"></div>
            ';
        }

        return $text;
    }


    static function ShowIn($text, $itemId, $params, $href='#')
    {
        $doc = JFactory::getDocument();
        JFactory::getLanguage()->load('plg_system_jllikelock', JPATH_ADMINISTRATOR);
        $titlefc = JText::_('PLG_JLLIKELOCK_TITLE_FC');
        $titlevk = JText::_('PLG_JLLIKELOCK_TITLE_VK');
        $titletw = JText::_('PLG_JLLIKELOCK_TITLE_TW');
    //    $titlegg = JText::_('PLG_JLLIKELOCK_TITLE_GG');
    //    $titleok = JText::_('PLG_JLLIKELOCK_TITLE_OD');
    //    $titlemm = JText::_('PLG_JLLIKELOCK_TITLE_MM');
        $txtindex = $params->get('addindextxt', '');

        $protokol = (JFactory::getConfig()->get('force_ssl') == 2) ? 'https://' : 'http://';
        $script = '
        var pathbs = "' . $protokol . $params->get('pathbase', ''). str_replace('www.', '', $_SERVER['HTTP_HOST']) . '";
        var typeGet="' . $params->get('typesget') . '";
        var jllock="1";
        var jltid="cindex' . $itemId . '";
        var txtindex="' . $txtindex . '";
        ';
        $doc->addScriptDeclaration($script);

        if ($params->get('load_libs') == 0)
        {
            if ($params->get('jqload') == 1)
            {
                $doc->addScript("https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js");
            }
            $doc->addScript(JURI::base() . "plugins/system/jllikelock/js/buttons.js?5");
        }
        else
        {
            if ($params->get('jqload') == 1)
            {
                $doc->addCustomTag('<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>');
            }
            $doc->addCustomTag('<script src="' . JURI::base() . 'plugins/system/jllikelock/js/buttons.js?5"></script>');
        }

        $doc->addCustomTag('<script src="' . JURI::base() . 'plugins/system/jllikelock/js/jquery.cookie.js"></script>');
        $doc->addStyleSheet(JURI::base() . "plugins/system/jllikelock/js/buttons.css");

        $firsttxt = JText::_($params->get('firsttxt'));
        $colortxt = $params->get('colortxt');
        $bgcolor = $params->get('bgcolor');

        $cookieName = 'jltindex_' . 'cindex' . $itemId;
        if (!empty($_COOKIE['jllikelockon'])
            && isset($_COOKIE[$cookieName])
            && ($_COOKIE[$cookieName] == 'cindex' . $itemId)
        )
        {
            $ftxt = '';
        }
        else
        {
            $ftxt = '<div id="ftxt" style="float:left;width:100%;color:' . $colortxt . ';">' . $firsttxt . '</div>';
        }

        $scriptPage = <<<HTML
				
				<div class="event-container" >
				<div class="likes-lock" style="background: none repeat scroll 0 0 $bgcolor;">
				$ftxt<img src='/plugins/system/jllikelock/ajax-loader.gif' id='waitimg' style='display:none;margin-right:15px;'>
HTML;
        if ($params->get('addfacebook', 1)) {

            $langTag = str_replace('-', '_', JFactory::getLanguage()->getTag());
            $doc->addScript('//connect.facebook.net/'.$langTag.'/all.js');
            $doc->addScript('//connect.facebook.net/'.$langTag.'/sdk.js');
            $script = '
                window.fbAsyncInit = function() {
                    FB.init({
                        appId      : "1866833980259274",
                        xfbml      : true,
                        version    : "v2.8"
                    });
                };
            ';
            $doc->addScriptDeclaration($script);

            $scriptPage .= <<<HTML
					<a title="$titlefc" class="likelc lc-fblc" data-href="$href" id="lc-fblc-$itemId">
					<i class="lc-icolc"></i>
					<span class="lc-countlc"></span>
					</a>
HTML;
        }
        if ($params->get('addvk', 1)) {
            $scriptPage .= <<<HTML
					<a title="$titlevk" class="likelc lc-vklc" data-href="$href" id="lc-vklc-$itemId">
					<i class="lc-icolc"></i>
					<span class="lc-countlc"></span>
					</a>
HTML;
        }
        if ($params->get('addtw', 1)) {
            $scriptPage .= <<<HTML
					<a title="$titletw" class="likelc lc-twlc" data-href="$href" id="lc-twlc-$itemId">
					<i class="lc-icolc"></i>
					<span class="lc-countlc"></span>
					</a>
HTML;
        }
//        if ($params->get('addgp', 1)) {
//            $scriptPage .= <<<HTML
//					<a title="$titlegg" class="likelc lc-gplc" data-href="$href" id="lc-gplc-$itemId">
//					<i class="lc-icolc"></i>
//					<span class="lc-countlc"></span>
//					</a>
//HTML;
//        }
//        if ($params->get('addok', 1)) {
//            $scriptPage .= <<<HTML
//					<a title="$titleok" class="likelc lc-oklc" data-href="$href" id="lc-oklc-$itemId">
//					<i class="lc-icolc"></i>
//					<span class="lc-countlc"></span>
//					</a>
//HTML;
//        }
//        if ($params->get('addmm', 1)) {
//            $scriptPage .= <<<HTML
//					<a title="$titlemm" class="likelc lc-mllc" data-href="$href" id="lc-mllc-$itemId">
//					<i class="lc-icolc"></i>
//					<span class="lc-countlc"></span>
//					</a>
//HTML;
//        }

        $scriptPage .= <<<HTML
			</div>		
            <div style="text-align: right;">
			    <a style="text-decoration:none; color: #c0c0c0; font-family: arial,helvetica,sans-serif; font-size: 5pt;" target="_blank" href="https://joomline.ru/rasshirenija/plugin/jlsocialinterlock.html">JL Social Interlock</a>
			
            </div>
            </div>
HTML;

        return $scriptPage;

    }

    static function limittext($maxchar, $introtext, $fulltext, $itemId, $show, $tind, $txtindex)
    {
        $visibleText = $hiddentext = '';
        $wordtext = $introtext . ' ' . $fulltext;
        $wordtext = self::cleanText($wordtext);
        $words = explode(' ', $wordtext);

        $trigger = 0;
        foreach ($words as $word)
        {
            if($trigger == 0)
            {
                //если длина превысила допустимую, то переворачиваем триггер
                $trigger = (JString::strlen($visibleText . ' ' . $word) > $maxchar - 1) ? 1 : 0;
                //если трир=ггер = 0 то прибавляем слово к видимому, если нет, то к скрытому
                ($trigger == 0) ? $visibleText .= ' ' . $word : $hiddentext .= ' ' . $word;
            }
            else
            {
                $hiddentext .= ' ' . $word;
            }
        }

        if ($show == 0) {
            $cookieName = 'jltindex_' . $tind . $itemId;
            if (!empty($_COOKIE['jllikelockon'])
                && isset($_COOKIE[$cookieName])
                && ($_COOKIE[$cookieName] == $tind . $itemId)
            )
            {
                return $wordtext;
            }
            else
            {
                $hiddentext = ($txtindex == 1) ? $hiddentext : base64_encode($hiddentext);
                return $visibleText . '<div class="jlLikeLockAlltxt" style="display:none;float:left;">' . $hiddentext . '</div>';
            }
        }
        else
        {
            return $visibleText;
        }
    }

    static function cuttext($text, $itemId, $showShares, $params, $tind, $txtindex)
    {

        preg_match_all('/(.*){jllikelock(.*)}(.+?){\/jllikelock}(.*)/is', $text, $tmp_text);

        //параметры из тега плагина
        $tagParams = trim($tmp_text[2][0]);
        if(!empty($tagParams))
        {
            $tp = array();
            $tagParams = explode(';', $tagParams);
            foreach($tagParams as $tagParam)
            {
                $a = explode('=', trim($tagParam));
                if(count($a) != 2)
                    continue;
                $tp[$a[0]] = $a[1];
            }
            $tagParams = $tp;
            unset($tp, $a);
        }

        $href = (!empty($tagParams['url'])) ? $tagParams['url'] : '#';
        $href = ($href != '#' && strpos($href, 'http://') === false && strpos($href, 'https://') === false) ? 'http://'.$href : $href;

        $shares = ($showShares == 1) ? '<div class="cls"></div>' . self::ShowIN($text, $itemId, $params, $href) : '';

        $cookieName = 'jltindex_' . $tind . $itemId;
        if (!empty($_COOKIE['jllikelockon'])
            && isset($_COOKIE[$cookieName])
            && ($_COOKIE[$cookieName] == $tind . $itemId)
        )
        {
            return $tmp_text[1][0]  . $tmp_text[3][0] . $tmp_text[4][0] . '<br clear="all" />' . $shares;
        }
        else
        {
            $hiddenText = ($txtindex == 1) ? $tmp_text[3][0] : base64_encode($tmp_text[3][0]);
            return $tmp_text[1][0] . '<div class="jlLikeLockAlltxt" style="display:none;float:left;">' . $hiddenText .'</div>' . $shares . $tmp_text[4][0];
        }
    }



    private static function gethk($input, $decrypt = false)
    {
        $o = $s1 = $s2 = array(); // Arrays for: Output, Square1, Square2
        // формируем базовый массив с набором символов
        $basea = array('?', '(', '@', ';', '$', '#', "]", "&", '*'); // base symbol set
        $basea = array_merge($basea, range('a', 'z'), range('A', 'Z'), range(0, 9));
        $basea = array_merge($basea, array('!', ')', '_', '+', '|', '%', '/', '[', '.', ' '));
        $dimension = 9; // of squares
        for ($i = 0; $i < $dimension; $i++) { // create Squares
            for ($j = 0; $j < $dimension; $j++) {
                $s1[$i][$j] = $basea[$i * $dimension + $j];
                $s2[$i][$j] = str_rot13($basea[($dimension * $dimension - 1) - ($i * $dimension + $j)]);
            }
        }
        unset($basea);
        $m = floor(strlen($input) / 2) * 2; // !strlen%2
        $symbl = $m == strlen($input) ? '' : $input[strlen($input) - 1]; // last symbol (unpaired)
        $al = array();
        // crypt/uncrypt pairs of symbols
        for ($ii = 0; $ii < $m; $ii += 2) {
            $symb1 = $symbn1 = strval($input[$ii]);
            $symb2 = $symbn2 = strval($input[$ii + 1]);
            $a1 = $a2 = array();
            for ($i = 0; $i < $dimension; $i++) { // search symbols in Squares
                for ($j = 0; $j < $dimension; $j++) {
                    if ($decrypt) {
                        if ($symb1 === strval($s2[$i][$j])) $a1 = array($i, $j);
                        if ($symb2 === strval($s1[$i][$j])) $a2 = array($i, $j);
                        if (!empty($symbl) && $symbl === strval($s2[$i][$j])) $al = array($i, $j);
                    } else {
                        if ($symb1 === strval($s1[$i][$j])) $a1 = array($i, $j);
                        if ($symb2 === strval($s2[$i][$j])) $a2 = array($i, $j);
                        if (!empty($symbl) && $symbl === strval($s1[$i][$j])) $al = array($i, $j);
                    }
                }
            }
            if (sizeof($a1) && sizeof($a2)) {
                $symbn1 = $decrypt ? $s1[$a1[0]][$a2[1]] : $s2[$a1[0]][$a2[1]];
                $symbn2 = $decrypt ? $s2[$a2[0]][$a1[1]] : $s1[$a2[0]][$a1[1]];
            }
            $o[] = $symbn1 . $symbn2;
        }
        if (!empty($symbl) && sizeof($al)) // last symbol
        $o[] = $decrypt ? $s1[$al[1]][$al[0]] : $s2[$al[1]][$al[0]];
        return implode('', $o);
    }

    static function getalw($params)
    {
        $allowedHost = $params->get('klht', '');
        $allowedHost = (empty($allowedHost)) ? 'localhost' : $allowedHost;

        $allowedHost = explode('::', $allowedHost);
        $allow = false;

        foreach ($allowedHost as $allowed) {
            $allowed = self::gethk($allowed, true);

            if (!empty($allowed)) {
                $allowed = explode('|', $allowed);
                $site = (!empty($allowed[0])) ? $allowed[0] : 'localhost';
                $extension = (!empty($allowed[1])) ? $allowed[1] : '';
                $expireDate = (!empty($allowed[2])) ? $allowed[2] : '';

                if (strpos($_SERVER['HTTP_HOST'], $site) !== false) {
                    $allow = true;
                    break;
                }
            }
        }
        if (!$allow) {
            return 'x0001';
        } else {
            return 'x0000';
        }
    }

    public static function cleanText($text)
    {
        $text = preg_replace("#{jllikelock[^}]*}#", '', $text);
        $text = JString::str_ireplace(array('{/jllikelock}', '{jllikelock-off}'), '', $text);
        return $text;
    }
}