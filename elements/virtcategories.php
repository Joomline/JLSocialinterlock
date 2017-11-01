<?php
/**
 * @version		$Id: categoriesmultiple.php 1812 2013-01-14 18:45:06Z lefteris.kavadas $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die ;

jimport('joomla.form.formfield');

class JFormFieldVirtCategories extends JFormField
{

    public $type = 'virtcategories';

    protected function getInput()
    {
        if(!is_file(JPATH_ADMINISTRATOR.'/components/com_virtuemart/admin.virtuemart.php'))
        {
            return '';
        }

        $name = $this->name;
        $value = empty($this->value) ? '' : $this->value;
        $lang = str_replace('-', '_', strtolower(JFactory::getLanguage()->getTag()));
        $db = JFactory::getDBO();
        $query = 'SELECT cc.category_parent_id, cc.category_child_id, vc.category_name
            FROM #__virtuemart_categories_'.$lang.' AS vc,
            #__virtuemart_category_categories AS cc
            WHERE cc.category_child_id = vc.virtuemart_category_id
            ORDER BY cc.category_parent_id, cc.ordering, vc.category_name';
        $db->setQuery($query);
        $mitems = $db->loadObjectList();
        $children = array();
        if ($mitems)
        {
            foreach ($mitems as $v)
            {
                $val = new stdClass();
                $val->id = $v->category_child_id;
                $val->title = $v->category_name;
                $pt = $v->category_parent_id;
                $val->parent_id = $pt;

                $list = @$children[$pt] ? $children[$pt] : array();
                array_push($list, $val);
                $children[$pt] = $list;
            }
        }
        $list = JHTML::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);
        $mitems = array();

        foreach ($list as $item)
        {
            $item->treename = JString::str_ireplace('&#160;', '- ', $item->treename);
            $mitems[] = JHTML::_('select.option', $item->id, '   '.$item->treename);
        }

        $fieldName = $name;

        $output = JHTML::_('select.genericlist', $mitems, $fieldName, 'class="inputbox" multiple="multiple" size="10"', 'value', 'text', $value);
        return $output;
    }
}