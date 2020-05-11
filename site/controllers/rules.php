<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * extension menu created by Mike Gusev (migus)
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/**
 * @package AltaUserPoints
 */
class AltauserpointsControllerRules extends AltauserpointsController
{
	/**
	 * Custom Constructor
	 */
 	public function __construct()	{
		parent::__construct( );
	}
	
	public function display($cachable = false, $urlparams = false) 
	{

		$model      = $this->getModel ( 'rules' );
		$view       = $this->getView  ( 'rules','html' );		
		
    	$lang = JFactory::getLanguage();
    	$lang->load( 'com_altauserpoints', JPATH_ADMINISTRATOR);		

		$app = JFactory::getApplication();
		$menus = $app->getMenu();
		$menu       = $menus->getActive();
		$menuid     = $menu->id;

		$params     = $menus->getParams($menuid);		
		
		$_rules = $model->_getRulesList();
		
		$view->assign('params', $params );
		$view->assign('rules', $_rules[0] );
		$view->assign('total', $_rules[1] );
		$view->assign('limit', $_rules[2] );
		$view->assign('limitstart', $_rules[3] );
		
		$view->_displaylist();
	}

}
?>