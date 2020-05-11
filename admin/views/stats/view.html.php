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

class altauserpointsViewStats extends JViewLegacy {

	function _display($tpl = null)
	{

		$doc	=  JFactory::getDocument();		
		$doc->addScript( "https://www.google.com/jsapi" );	
		
		$logo = '<img src="'. JURI::root() . 'administrator/components/com_altauserpoints/assets/images/icon-48-altauserpoints.png" />&nbsp;&nbsp;';
	
		JToolBarHelper::title( $logo . 'AltaUserPoints :: ' . JText::_( 'AUP_STATISTICS' ), 'searchtext' );
		getCpanelToolbar();
		JToolBarHelper::back();
		getPrefHelpToolbar();	
		
		$this->assignRef( 'result', $this->result );
		$this->assignRef( 'result2', $this->result2 );
		$this->assignRef( 'date_start', $this->date_start );
		$this->assignRef( 'date_end', $this->date_end );
		$this->assignRef( 'listrules', $this->listrules );
		$this->assignRef( 'communitypoints', $this->communitypoints );
		
		$this->assignRef( 'average_points_earned_by_day', $this->average_points_earned_by_day );
		$this->assignRef( 'average_points_spent_by_day', $this->average_points_spent_by_day );
		
		$this->assignRef( 'numusers', $this->numusers );
		//$this->assignRef( 'ratiomembers', $this->ratiomembers );
		$this->assignRef( 'inactiveusers', $this->inactiveusers );
		$this->assignRef( 'num_days_inactiveusers_rule', $this->num_days_inactiveusers_rule );
		
		parent::display( $tpl);
		
	}
}
?>
