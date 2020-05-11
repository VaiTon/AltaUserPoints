<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view' );
jimport( 'joomla.html.pagination' );

class altauserpointsViewLatestactivity extends JViewLegacy
{
	/*function _display($tpl = null) {		
		
		$app	=  JFactory::getApplication();
		$doc	=  JFactory::getDocument();
		$doc->addStyleSheet(JURI::base(true).'/components/com_altauserpoints/assets/css/altauserpoints.css');
	
		$app_params = $app->getParams();
		$this->params = $app_params->toArray();
		
		$this->assignRef( 'params', $this->params );
		$this->assignRef( 'allowGuestUserViewProfil', $this->allowGuestUserViewProfil );		
		$this->assignRef( 'latestactivity', $this->latestactivity );
		$this->assignRef( 'total', $this->total );
		$this->assignRef( 'limit', $this->limit );
		$this->assignRef( 'limitstart', $this->limitstart );
		$this->assignRef( 'useAvatarFrom', $this->useAvatarFrom );
		$this->assignRef( 'linkToProfile', $this->linkToProfile );
		
		$pagination = new JPagination( $this->total, $this->limitstart, $this->limit );		
		$this->assignRef ('pagination', $pagination );
		
		// insert the page counter in the title of the window page
		$titlesuite =  ( $this->limitstart ) ? ' - ' . $pagination->getPagesCounter() : '';
		$doc->setTitle( $doc->getTitle() . $titlesuite );		
		
		parent::display($tpl);
	}*/
	
	public function display($tpl = null) 
	{	
	
		$app	=  JFactory::getApplication();
		$doc	=  JFactory::getDocument();
		$doc->addStyleSheet(JURI::base(true).'/components/com_altauserpoints/assets/css/altauserpoints.css');
		
		$com_params = JComponentHelper::getParams( 'com_altauserpoints' );
		$_useAvatarFrom = $com_params->get('useAvatarFrom');	
		$_profilelink	= $com_params->get('linkToProfile');		
		$_allowGuestUserViewProfil = $com_params->get('allowGuestUserViewProfil', 1);	

		$model      = $this->getModel ( 'latestactivity' );
		//$view       = $this->getView  ( 'latestactivity','html' );
	
		$app 	= JFactory::getApplication();
		$menus 	= $app->getMenu();
		$menu   = $menus->getActive();
		$menuid = $menu->id;
		$params = $menus->getParams($menuid);		
		
		$_latestactivity = $this->get('LatestActivity');
		$total 		= $_latestactivity[1];
		$limit 		= $_latestactivity[2];
		$limitstart = $_latestactivity[3];
		
		$this->assignRef('params', $params );
		$this->assignRef('allowGuestUserViewProfil', $_allowGuestUserViewProfil );
		$this->assignRef('latestactivity', $_latestactivity[0] );
		$this->assignRef('total', $total );
		$this->assignRef('limit', $limit );
		$this->assignRef('limitstart', $limitstart );
		$this->assignRef('useAvatarFrom', $_useAvatarFrom );
		$this->assignRef('linkToProfile', $_profilelink );
		
		$pagination = new JPagination( $total, $limitstart, $limit );		
		$this->assignRef ('pagination', $pagination );
		
		// insert the page counter in the title of the window page
		$titlesuite =  ( $this->limitstart ) ? ' - ' . $pagination->getPagesCounter() : '';
		$doc->setTitle( $doc->getTitle() . $titlesuite );
		
		parent::display($tpl);
	}
	
	
}
?>