<?php
/**
 * Plugin JL Social Interlock
 *
 * @version 2.1.0
 * @author Artem Zhukov, Vadim Kunicin, Arkadiy Sedelnikov (sale@joomline.ru)
 * @copyright (C) 2013-2017 by Artem Zhukov, Vadim Kunicin, Arkadiy Sedelnikov(http://www.joomline.ru)
 * @license GNU/GPL: http://www.gnu.org/copyleft/gpl.html
 **/

// no direct access
defined('_JEXEC') or die;
error_reporting(E_ERROR);
jimport('joomla.plugin.plugin');

require_once dirname(__FILE__) . '/helpers/helper.php';


class plgSystemJllikelock extends JPlugin
{
    public function __construct(&$subject, $config = array())
    {
        parent::__construct($subject, $config);
        $this->loadLanguage('plg_system_jllikelock', JPATH_ROOT.'/plugins/system/jllikelock');
    }

    public function onContentPrepare($context, &$article, &$params, $page = 0)
    {
        $allowContext = array(
            'com_content.featured',
            'com_content.article',
            'com_content.category',
            'com_k2.itemlist',
            'com_k2.item',
            'com_zoo.element.textarea',
            'easyblog.blog',//для статьи и блога не меняется
            'com_virtuemart.productdetails',
            //'com_virtuemart.category'
        );

        if(!in_array($context, $allowContext)){
            return true;
        }

        if (JFactory::getUser()->id > 0 && $this->params->get('hide_only_guest', 0) == 1)
        {
            $article->text = plgJLLikeLockHelper::cleanText($article->text);
            return true;
        }

        $option = JRequest::getCmd('option');

        if(is_file(dirname(__FILE__) . '/helpers/' . $option . '_helper.php')){
            require_once dirname(__FILE__) . '/helpers/' . $option . '_helper.php';
            $class = 'JLLikeLock_' . $option . '_helper';
            if(class_exists($class)){
                $helper = new $class($this->params);
                if(method_exists($helper, 'go')){
                    $helper->go($context, $article, $params, $page);
                }
            }
        }
    }

    public function onAfterInitialise()
    {
        $app = JFactory::getApplication();

        //перехватываем событие
        if ( $app->input->getCmd( 'plg_system_jllikelock', '' ) == 1 )
        {
            require_once JPATH_ROOT.'/plugins/system/jllikelock/helpers/twitter.php';
            switch ($app->input->getCmd( 'method', '' )){
                case 'get_twitter_followers':
                    $helper = new plgJLLikeLockTwitterHelper($this->params);
                    echo $helper->getTwirrerFollowers();
                    break;
                case 'get_twirrer_url':
                    $helper = new plgJLLikeLockTwitterHelper($this->params);
                    $url = $helper->getTwitterAuthUrl();
                    $app->redirect($url);
                    break;
                default:
                    break;
            }
            $app->close(); //выходим из приложения
        }
        else if($app->input->getCmd( 'plg_system_jllikelock_twitter_auth', '' ) == 1 ){
            require_once JPATH_ROOT.'/plugins/system/jllikelock/helpers/twitter.php';
            $helper = new plgJLLikeLockTwitterHelper($this->params);
            $helper->loadTwitterId();
            $app->close(); //выходим из приложения
        }
    }


}