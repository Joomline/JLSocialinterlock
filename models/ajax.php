<?php

/**
 * Plugin JL Social Interlock
 *
 * @version 1.4
 * @author Artem Zhukov, Vadim Kunicin, Arkadiy Sedelnikov (sale@joomline.ru)
 * @copyright (C) 2013 by Artem Zhukov, Vadim Kunicin, Arkadiy Sedelnikov(http://www.joomline.ru)
 * @license GNU/GPL: http://www.gnu.org/copyleft/gpl.html
 **/

$encoded = (isset($_REQUEST['encoded'])) ? $_REQUEST['encoded'] : 0;

if ($encoded == 1) {
    $encoded_text = $_REQUEST['encoded_text'];
    echo JLLikeLockAjax::gethiddentext($encoded_text);
    die;
}

class JLLikeLockAjax
{
    static function gethiddentext($arttext)
    {
        $text = base64_decode($arttext);
        return $text;
    }
}
?>

