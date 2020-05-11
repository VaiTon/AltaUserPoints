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
jimport('joomla.html.pane');
jimport( 'joomla.html.pagination' );
class altauserpointsViewAccount extends JViewLegacy
{
	function _display($tpl = null) 
	{		
		$document	=  JFactory::getDocument();		
		JHTML::_('behavior.calendar');
		require_once JPATH_COMPONENT_ADMINISTRATOR.'/assets/includes/pane.php';
		$pane   = JPane::getInstance('tabs');
		$slider = JPane::getInstance('sliders');
		$document->addStyleSheet(JURI::base(true).'/components/com_altauserpoints/assets/css/altauserpoints.css');
		//$document->addStyleSheet(JURI::base(true).'/components/com_altauserpoints/assets/css/bar.css');
		//$document->addScript(JURI::base(true).'/components/com_altauserpoints/assets/ajax/maxlength.js');
		// CROP AVATAR
		/*
		$document->addStyleSheet(JURI::base(true).'/components/com_altauserpoints/assets/crop/css/uvumi-crop.css', 'text/css', 'screen');
		$document->addScript(JURI::base(true).'/components/com_altauserpoints/assets/crop/js/UvumiCrop-compressed.js');
		$scriptCrop = "cropperAvatar = new uvumiCropper('cropAvatar',{
				coordinates:true,
				preview:true,
				downloadButton:false,
				saveButton:true
			});";
		$document->addScriptDeclaration($scriptCrop, '');
		*/
		
		isIE ();
		
		// get params definitions
		$params = JComponentHelper::getParams( 'com_altauserpoints' );		
		$enabledUDDEIM = $params->get( 'showUddeimTab', '0' );		
	
		$this->assignRef( 'params', $this->params );
		$this->assignRef( 'cparams', $this->cparams );
		$this->assignRef( 'referreid',	$this->referreid );
		$this->assignRef( 'currenttotalpoints', $this->currenttotalpoints );
		$this->assignRef( 'lastupdate', $this->lastupdate );		
		$this->assignRef( 'rowslastpoints', $this->rowslastpoints );			
		$this->assignRef( 'referraluser', $this->referraluser );
		$this->assignRef( 'referralname', $this->referralname );
		$this->assignRef( 'rowsreferrees', $this->rowsreferrees );
		$this->assignRef( 'userid', $this->userid );
		$this->assignRef( 'userrankinfo', $this->userrankinfo );
		$this->assignRef( 'medalslistuser', $this->medalslistuser );		
		$this->assignRef( 'pane', $pane );
		$this->assignRef( 'slider', $slider );
		$this->assignRef( 'pointsearned', $this->pointsearned );		
		$this->assignRef( 'totalpoints', $this->totalpoints );
		$this->assignRef( 'mypointsearned', $this->mypointsearned );
		$this->assignRef( 'mypointsspent', $this->mypointsspent );		
		$this->assignRef( 'mypointsearnedthismonth', $this->mypointsearnedthismonth);
		$this->assignRef( 'mypointsspentthismonth', $this->mypointsspentthismonth);
		$this->assignRef( 'mypointsearnedthisday', $this->mypointsearnedthisday);
		$this->assignRef( 'mypointsspentthisday', $this->mypointsspentthisday);			
		$this->assignRef( 'myname', $this->myname);
		$this->assignRef( 'myusername', $this->myusername);
		$this->assignRef( 'avatar', $this->avatar);
		$this->assignRef( 'user_info', $this->user_info);
		$this->assignRef( 'useAvatarFrom', $this->useAvatarFrom);
		$this->assignRef( 'mycouponscode', $this->mycouponscode);
		$this->assignRef( 'userinfo', $this->userinfo);
		$this->assignRef( 'enabledUDDEIM', $enabledUDDEIM);
	
		
		$pagination = new JPagination( $this->total, $this->limitstart, $this->limit );		
		$this->assignRef('pagination', $pagination );
		$document->setTitle($this->myusername.' - '.getFormattedPoints( $this->currenttotalpoints ).' '.JText::_('AUP_POINTS'));
		parent::display($tpl);
	}
}
?>