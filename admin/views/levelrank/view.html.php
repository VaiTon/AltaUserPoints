<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */


// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.view' );
jimport( 'joomla.html.pagination' );

class altauserpointsViewLevelrank extends JViewLegacy {

	function _displaylist($tpl = null) {
	
		$logo = '<img src="'. JURI::root() . 'administrator/components/com_altauserpoints/assets/images/icon-48-altauserpoints.png" />&nbsp;&nbsp;';
		
		JToolBarHelper::title( $logo . 'AltaUserPoints :: ' .  JText::_( 'AUP_LEVEL_RANK_MEDALS' ), 'levels' );
		getCpanelToolbar();
		if (JFactory::getUser()->authorise('core.edit', 'com_altauserpoints')) {
			JToolBarHelper::editList( 'editlevelrank' );
		}
		if (JFactory::getUser()->authorise('core.create', 'com_altauserpoints')) {
			JToolBarHelper::addNew( 'editlevelrank' );
		}
		if (JFactory::getUser()->authorise('core.delete', 'com_altauserpoints')) {
			JToolBarHelper::custom( 'deletelevelrank', 'delete.png', 'delete.png', JText::_('AUP_DELETE') );		
		}
		getPrefHelpToolbar();
		
		$pagination = new JPagination( $this->total, $this->limitstart, $this->limit );		
		
		$this->assignRef( 'pagination', $pagination );
		$this->assignRef( 'levelrank', $this->levelrank );
		$this->assignRef( 'lists',  $this->lists );

		parent::display( $tpl) ;
	}
	
	function _edit_levelrank($tpl = null) {
		
		JFactory::getApplication()->input->set( 'hidemainmenu', 1 );
		
		$logo = '<img src="'. JURI::root() . 'administrator/components/com_altauserpoints/assets/images/icon-48-altauserpoints.png" />&nbsp;&nbsp;';
		
		JToolBarHelper::title(  $logo . 'AltaUserPoints :: ' . JText::_( 'AUP_LEVEL_RANK_MEDALS' ), 'levels-add' );
		
		getCpanelToolbar();
		if (JFactory::getUser()->authorise('core.edit.state', 'com_altauserpoints')) {
			JToolBarHelper::save( 'savelevelrank' );
		}
		JToolBarHelper::cancel( 'cancellevelrank' );
		getPrefHelpToolbar();
		
		
		$this->lists['notification'] = JHTML::_('select.booleanlist', 'notification', '', $this->row->notification);
		$options = array();		
		$options[] = JHTML::_('select.option', '0', JText::_( 'AUP_PLAIN-TEXT' ) );
		$options[] = JHTML::_('select.option', '1', JText::_( 'AUP_HTML' ) );
		$this->lists['emailformat'] = JHTML::_('select.genericlist', $options, 'emailformat', 'class="inputbox" size="1"' ,'value', 'text', $this->row->emailformat );
		
		$this->lists['bcc2admin'] = JHTML::_('select.booleanlist', 'bcc2admin', '', $this->row->bcc2admin);
		
		
		$this->assignRef( 'row', $this->row );
		$this->assignRef( 'lists', $this->lists );
		
		parent::display( "form" ) ;
	}
	
	
	function  _displaydetailrank($tpl = null) {
	
		$logo = '<img src="'. JURI::root() . 'administrator/components/com_altauserpoints/assets/images/icon-48-altauserpoints.png" />&nbsp;&nbsp;';

		JToolBarHelper::title( $logo . 'AltaUserPoints :: ' .  JText::_( 'AUP_AWARDED' ), 'addedit' );
		getCpanelToolbar();
		JToolBarHelper::back();
		getPrefHelpToolbar();
		
		$this->assignRef( 'detailrank', $this->detailrank );
	
		$pagination = new JPagination( $this->total, $this->limitstart, $this->limit );		
		$this->assignRef( 'pagination', $pagination );
		
		parent::display( "listing" );
	}
}
?>
