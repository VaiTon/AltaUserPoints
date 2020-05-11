<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

/**
 * @package AltaUserPoints
 */
class AltauserpointsControllerUsers extends AltauserpointsController
{	
	/**
	 * Custom Constructor
	 */
 	public function __construct()	{
		parent::__construct( );
	}


	public function display($cachable = false, $urlparams = false) 
	{
		$com_params = JComponentHelper::getParams( 'com_altauserpoints' );
		$_useAvatarFrom = $com_params->get('useAvatarFrom');
		$_profilelink	= $com_params->get('linkToProfile');	
		$_allowGuestUserViewProfil = $com_params->get('allowGuestUserViewProfil', 1);
		
		$model        = $this->getModel ( 'altauserpoints' );
		$view         = $this->getView  ( 'users','html' );
		
		$result = $model->_getUsersList();
		
		// Get the parameters of the active menu item
		$params = $model->_getParamsAUP();
				
		$view->assign('params', $params );
		$view->assign('allowGuestUserViewProfil', $_allowGuestUserViewProfil );
		$view->assign('rows', $result[0] );
		$view->assign('total', $result[1] );
		$view->assign('limit', $result[2] );
		$view->assign('limitstart', $result[3] );
		$view->assign('lists', $result[4] );
		$view->assign('useAvatarFrom', $_useAvatarFrom );		
		$view->assign('linkToProfile', $_profilelink );
		
		// Display
		$view->_display();

	}
}
?>