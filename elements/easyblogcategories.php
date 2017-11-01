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

class JFormFieldEasyblogCategories extends JFormField
{

    public $type = 'easyblogcategories';

    protected function getInput()
    {
        if(!is_file(JPATH_ADMINISTRATOR.'/components/com_easyblog/admin.easyblog.php'))
        {
            return '';
        }

        $name = $this->name;
        $value = empty($this->value) ? '' : $this->value;

        $db = JFactory::getDBO();
        $query = 'SELECT * FROM #__easyblog_category ORDER BY parent_id, ordering, title';
        $db->setQuery($query);
        $mitems = $db->loadObjectList();
        $children = array();
        if ($mitems)
        {
            foreach ($mitems as $v)
            {
                //$v->title = $v->title;
                //$v->parent_id = $v->parent_id;
                $pt = $v->parent_id;
                $list = @$children[$pt] ? $children[$pt] : array();
                array_push($list, $v);
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