<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

Jimport( 'joomla.application.component.view');

class altauserpointsViewReportsystem extends JViewLegacy {

	function show($tpl = null) {
		
		$document	=  JFactory::getDocument();
		
		JFactory::getApplication()->input->set( 'hidemainmenu', 1 );
		
		$logo = '<img src="'. JURI::root() . 'administrator/components/com_altauserpoints/assets/images/icon-48-altauserpoints.png" />&nbsp;&nbsp;';
		
		JToolBarHelper::title( $logo . 'AltaUserPoints :: ' . JText::_('AUP_REPORT_SYSTEM'), 'config' );
		
		getCpanelToolbar();

		JToolBarHelper::back();
		
		getPrefHelpToolbar();
		
		$this->assignRef('reportsystem', $this->reportsystem);
		
		parent::display( $tpl);
		
	}
}
?>
