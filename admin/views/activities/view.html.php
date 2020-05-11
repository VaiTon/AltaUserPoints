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

class altauserpointsViewactivities extends JViewLegacy {

	function _displaylist($tpl = null) {
		
		$document	=  JFactory::getDocument();
		
		$logo = '<img src="'. JURI::root() . 'administrator/components/com_altauserpoints/assets/images/icon-48-altauserpoints.png" />&nbsp;&nbsp;';
		
		JToolBarHelper::title( $logo . 'AltaUserPoints :: ' .  JText::_( 'AUP_ACTIVITY' ), 'searchtext' );
		getCpanelToolbar();
		
		if (JFactory::getUser()->authorise('core.create', 'com_altauserpoints')) {
			JToolBarHelper::custom( 'exportallactivitiesallusers', 'upload.png', 'upload.png', JText::_('AUP_EXPORT_ACTIVITIES'), false );
		}
		
		getPrefHelpToolbar();	
	
		$pagination = new JPagination( $this->total, $this->limitstart, $this->limit );
		
		$this->assignRef( 'pagination', $pagination );
		$this->assignRef( 'activities', $this->activities );
		$this->assignRef( 'lists', $this->lists );
		
		parent::display( $tpl) ;
	}	
	
}
?>
