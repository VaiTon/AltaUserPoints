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

class altauserpointsViewRegisterqrcode extends JViewLegacy
{

	function display($tpl = null) {		
		
		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::base(true).'/components/com_altauserpoints/assets/css/altauserpoints.css');
		
		//JHTML::_('behavior.mootools');

		$scriptCheckUsername = "	window.addEvent('domready', function(){
						$('login').addEvent('keyup', function(e){						
							// stop the default link event
							new Event(e).stop();
							var url = '".JURI::base(true)."/components/com_altauserpoints/assets/ajax/checkusername.php?n=' + $('login').value;							
							var myAjax = new Request({
									url:url,
									method: 'get',						
									onComplete: function(responseText) { 
										$('statusUSR').set('html', responseText);
									}
								});
						myAjax.send();
						});						
						});";
			
		$document->addScriptDeclaration($scriptCheckUsername);		

		JHTML::_('behavior.formvalidation');
		
		$this->assignRef( 'couponCode',  $this->couponCode );
		$this->assignRef( 'trackID',  $this->trackID );
		
		parent::display($tpl);
	}	
	
	
	
	function displayResult($tpl = null) {	
	
		$this->assignRef( 'points',  $this->points );
		
		parent::display($tpl);
	}	


}
?>