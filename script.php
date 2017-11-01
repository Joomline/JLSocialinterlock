<?php
/**
 * Plugin JL Social Interlock
 *
 * @version 1.8.0
 * @author Artem Zhukov, Vadim Kunicin, Arkadiy Sedelnikov (sale@joomline.ru)
 * @copyright (C) 2013 by Artem Zhukov, Vadim Kunicin, Arkadiy Sedelnikov(http://www.joomline.ru)
 * @license GNU/GPL: http://www.gnu.org/copyleft/gpl.html
 **/

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.installer.installer');

$db = &JFactory::getDBO();
$query = "UPDATE `#__extensions` SET `ordering`='0' WHERE `folder`='content' and `element`='jllikelock'";
$db->setQuery($query);
$db->query();

?>
