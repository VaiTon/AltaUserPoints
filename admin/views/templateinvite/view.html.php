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

class altauserpointsViewTemplateinvite extends JViewLegacy {

	function _displaylist($tpl = null) {

		$document	=  JFactory::getDocument();
		
		$logo = '<img src="'. JURI::root() . 'administrator/components/com_altauserpoints/assets/images/icon-48-altauserpoints.png" />&nbsp;&nbsp;';
		
		JToolBarHelper::title(  $logo . 'AltaUserPoints :: ' . JText::_( 'AUP_TEMPLATES' ), 'thememanager' );
		
		getCpanelToolbar();
		if (JFactory::getUser()->authorise('core.edit', 'com_altauserpoints')) {
			JToolBarHelper::editList( 'edittemplateinvite' );
		}
		if (JFactory::getUser()->authorise('core.create', 'com_altauserpoints')) {
			JToolBarHelper::addNew( 'edittemplateinvite' );
		}
		if (JFactory::getUser()->authorise('core.delete', 'com_altauserpoints')) {
			JToolBarHelper::custom( 'deletetemplateinvite', 'delete.png', 'delete.png', JText::_('AUP_DELETE') );
		}
		getPrefHelpToolbar();
			
		$this->assignRef( 'templateinvite', $this->templateinvite );
	
		$pagination = new JPagination( $this->total, $this->limitstart, $this->limit );		
		$this->assignRef( 'pagination', $pagination );
		
		parent::display( $tpl) ;
	}
	
	function _edit_templateinvite($tpl = null) {
		
		$document	=  JFactory::getDocument();
		
		JFactory::getApplication()->input->set( 'hidemainmenu', 1 );
		
		JToolBarHelper::title( 'AltaUserPoints :: ' .  JText::_( 'AUP_TEMPLATE' ), 'thememanager' );
		getCpanelToolbar();		
		if (JFactory::getUser()->authorise('core.edit.state', 'com_altauserpoints')) {
			JToolbarHelper::apply('applytemplateinvite');
			JToolBarHelper::save( 'savetemplateinvite' );
		}
		JToolBarHelper::cancel( 'canceltemplateinvite' );
		getPrefHelpToolbar();				
		$document->addScriptDeclaration("window.addEvent('domready', function(){ var JTooltips = new Tips($$('.hasTip'), { maxTitleChars: 50, fixed: false}); });");
		
		$lists = array();
		$lists['published'] = JHTML::_('select.booleanlist', 'published', '', $this->row->published);
		$options = array();		
		$options[] = JHTML::_('select.option', '0', JText::_( 'AUP_PLAIN-TEXT' ) );
		$options[] = JHTML::_('select.option', '1', JText::_( 'AUP_HTML' ) );
		$lists['emailformat'] = JHTML::_('select.genericlist', $options, 'emailformat', 'class="inputbox" size="1"' ,'value', 'text', $this->row->emailformat );
		$lists['bcc2admin'] = JHTML::_('select.booleanlist', 'bcc2admin', '', $this->row->bcc2admin);
		
		$this->assignRef( 'row', $this->row );
		$this->assignRef( 'lists', $lists );
		
		parent::display( "form" ) ;
	}	
	
}
?>
