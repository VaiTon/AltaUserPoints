<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();


jimport( 'joomla.application.component.view');

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
jimport( 'joomla.html.pagination' );

class altauserpointsViewstatistics extends JViewLegacy {

	function _displaylist($tpl = null) {	
		
		$document	=  JFactory::getDocument();
		
		$document->addStyleDeclaration( ".icon-32-user-reset {background-image: url(components/com_altauserpoints/assets/images/icon-32-user-reset.png);}", "text/css" );
		
		$logo = '<img src="'. JURI::root() . 'administrator/components/com_altauserpoints/assets/images/icon-48-altauserpoints.png" />&nbsp;&nbsp;';
	
		JToolBarHelper::title(  $logo . 'AltaUserPoints :: ' . JText::_('AUP_USERS'), 'user' );
		getCpanelToolbar();
		if (JFactory::getUser()->authorise('core.edit', 'com_altauserpoints')) {
			JToolBarHelper::editList( 'edituser' );
		}
		JToolBarHelper::custom( 'applybonus', 'new.png', 'new.png', JText::_('AUP_BONUS'), true );
		JToolBarHelper::custom( 'applycustomrule', 'apply.png', 'new.png', JText::_('AUP_CUSTOM_POINTS'), true );
		JToolBarHelper::divider();	
		if (JFactory::getUser()->authorise('core.create', 'com_altauserpoints')) {
			JToolBarHelper::custom( 'exportallactivitiesallusers', 'upload.png', 'upload.png', JText::_('AUP_EXPORT_ACTIVITIES'), false );
		}
		JToolBarHelper::custom( 'resetuser', 'cancel.png', 'cancel.png', JText::_('AUP_RESET_USER'), true );
		getPrefHelpToolbar();
		
		$this->assignRef( 'usersStats', $this->usersStats );
	
		$pagination = new JPagination( $this->total, $this->limitstart, $this->limit );		
		$this->assignRef('pagination', $pagination );
		$this->assignRef('lists', $this->lists);
		$this->assignRef('ranksexist', $this->ranksexist);
		$this->assignRef('medalsexist', $this->medalsexist);
		$this->assignRef('medalslistuser', $this->medalslistuser);
		$this->assignRef('listmedals', $this->listmedals);
		
		parent::display( $tpl) ;	
		
	}
	
	function _edit_user($tpl = null) {
		
		$document	=  JFactory::getDocument();
		
		JFactory::getApplication()->input->set( 'hidemainmenu', 1 );
		
		$logo = '<img src="'. JURI::root() . 'administrator/components/com_altauserpoints/assets/images/icon-48-altauserpoints.png" />&nbsp;&nbsp;';

		JToolBarHelper::title(  $logo . 'AltaUserPoints :: ' . JText::_('AUP_USERS_POINTS') . ': ' . $this->row->name, 'user' );
		getCpanelToolbar();
		if (JFactory::getUser()->authorise('core.edit.state', 'com_altauserpoints')) {
			JToolbarHelper::apply('applyuser');
			JToolBarHelper::save( 'saveuser' );
		}
		JToolBarHelper::cancel( 'canceluser' );
		getPrefHelpToolbar();
			
		JHTML::_('behavior.calendar');
		//$document->addScriptDeclaration("window.addEvent('domready', function(){ var JTooltips = new Tips($$('.hasTip'), { maxTitleChars: 50, fixed: false}); });");

		$this->assignRef( 'row', $this->row );		
		$this->assignRef( 'listrank', $this->listrank );
		$this->assignRef( 'medalsexist', $this->medalsexist );
		
		
		$pagination = new JPagination( $this->total, $this->limitstart, $this->limit );		
		$this->assignRef('pagination', $pagination );
		$this->assignRef('userDetails', $this->userDetails );
		$this->assignRef('total', $this->total );
		$this->assignRef('limit', $this->limit);
		$this->assignRef('limitstart', $this->limitstart );
		$this->assignRef('lists', $this->lists );
		$this->assignRef('name', $this->name );
		$this->assignRef('cid', $this->cid );
	
		parent::display( "form" ) ;
	}
	
}
?>
