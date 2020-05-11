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

class altauserpointsViewCpanel extends JViewLegacy {

	function show($tpl = null) {	
		
		$language 	= JFactory::getLanguage();
		$tag 		= $language->getTag();
		$doc  = JFactory::getDocument();
    	$doc->addStylesheet( JURI::root().'administrator/components/com_altauserpoints/assets/css/fontello.css' );
		$logo = '<img src="'. JURI::root() . 'administrator/components/com_altauserpoints/assets/images/icon-48-altauserpoints.png" />&nbsp;&nbsp;';
		
		JToolBarHelper::title(   $logo . 'AltaUserPoints :: ' . JText::_( 'AUP_CPANEL' ), 'cpanel' );		
		JToolBarHelper::custom( 'cpanel', 'refresh.png', 'refresh.png', JText::_('AUP_REFRESH'), false );
		$bar = JToolBar::getInstance('toolbar');		
		getPrefHelpToolbar();		
		
		require_once (JPATH_COMPONENT.'/assets/includes/functions.php');
		
		jimport('joomla.html.pane');
		$pane = JPane::getInstance('sliders');
		
		/*
		$scriptMigration = "	window.addEvent('domready', function(){
						$$('a.rpcmigrate').addEvent('click', function(e){	
							// stop the default link event
							// new Event(e).stop();											
							var url = '".JURI::base(true)."/components/com_altauserpoints/install/migration_1.5.x.php;
							var myAjax = new Request({
									url:url,
									method: 'get',						
									onComplete: function(responseText) {
										$('migration').set('html', responseText);
									}
								});
						myAjax.send();
						});
						});";
		$document->addScriptDeclaration($scriptMigration);		
		*/
		
		$this->assignRef('tag', $tag);
		$this->assignRef('pane', $pane);		
		$this->assignRef('top10', $this->top10);
		$this->assignRef('needSync', $this->needSync);
		$this->assignRef('check', $this->check);
		$this->assignRef('params', $this->params);
		$this->assignRef('lastactivities', $this->lastactivities);
		$this->assignRef('synch', $this->synch);
		$this->assignRef('recalculate', $this->recalculate);
		$this->assignRef('communitypoints', $this->communitypoints);		
		
		parent::display( $tpl) ;		
	}
}
?>
