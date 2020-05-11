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

class altauserpointsViewUsers extends JViewLegacy
{

	function _display($tpl = null) {		
		
		$document	=  JFactory::getDocument();
		$uri 		= JFactory::getURI();				
		$uri2string = $uri->toString();
		
		$document->addScript(JURI::base(true).'/media/system/js/core.js');
		
		$pagination = new JPagination( $this->total, $this->limitstart, $this->limit );
		
		// insert the page counter in the title of the window page
		$titlesuite =  ( $this->limitstart ) ? ' - ' . $pagination->getPagesCounter() : '';
		$document->setTitle( $document->getTitle() . $titlesuite );
		
		$this->assignRef( 'params', $this->params );
		$this->assignRef( 'allowGuestUserViewProfil', $this->allowGuestUserViewProfil );	
		$this->assignRef( 'rows', $this->rows );
		$this->assignRef( 'lists', $this->lists);	
		$this->assignRef( 'limit', $this->limit);
		$this->assignRef( 'pagination', $pagination );
		$this->assignRef( 'action',	$uri2string );		
		$this->assignRef( 'useAvatarFrom',  $this->useAvatarFrom );
		$this->assignRef( 'linkToProfile', $this->linkToProfile );
		
		parent::display($tpl);
	}
	
}
?>