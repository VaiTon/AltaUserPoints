<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 - Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */
// no direct access
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.view' );
class AltauserpointsViewInvite extends JViewLegacy
{
	protected $form;
	function display($tpl = null)
	{
		$app      = JFactory::getApplication();	
		$doc	= JFactory::getDocument();
		$displ 	= "view";
		$points = 0;
		
		$cparams = JComponentHelper::getParams( 'com_altauserpoints' );
		
		
		JHtml::_('behavior.framework', true);
		
		$doc->addStyleSheet(JURI::base(true).'/components/com_altauserpoints/assets/css/altauserpoints.css');
		
		require_once JPATH_SITE.'/components/com_altauserpoints/helper.php';		
		$result = AltaUserPointsHelper::checkRuleEnabled('sysplgaup_invite');
		if($result)
			$points = $result[0]->points;		
	
		JHTML::_('behavior.formvalidation');

		JHTML::_('behavior.modal');
		$setModal = "window.addEvent('domready', function() {
			SqueezeBox.initialize({});

			$$('a.modal').each(function(el) {
				el.addEvent('click', function(e) {
					new Event(e).stop();
					SqueezeBox.fromElement(el);
				});
			});
		});
		";
		
		
		
		$doc->addScriptDeclaration($setModal);		

		
		$model  = $this->getModel('invite', 'AltauserpointsModel');
		$this->referreid = $model->_getReferreid();
		$this->referrer_link = getLinkToInvite( $this->referreid , $cparams->get('systemregistration') );
		$this->user_name = JFactory::getUser()->name ;
		$app_params = $app->getParams();
		$this->params = $app_params->toArray();
		

		$this->assignRef( 'params', $this->params ); 
		$this->assignRef( 'referreid', $this->referreid );	
		$this->assignRef( 'user_name', $this->user_name );			
		$this->assignRef( 'points', $points );		 
		$this->assignRef( 'displ', $displ );		
		$this->assignRef( 'referrer_link', $this->referrer_link );
		
		jimport( 'joomla.form.form' );
		JHtml::_('behavior.keepalive'); 
		
		JForm::addFormPath(JPATH_COMPONENT.'/models/forms');

		$this->form	= $this->get('Form'); 
		parent::display($tpl);
		
	}		
	
	
	function _display_addressbook($tpl = null) 
	{			
		parent::display('addressbook');
	}	
}
?>