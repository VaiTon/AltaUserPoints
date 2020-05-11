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

class altauserpointsViewRules extends JViewLegacy {

	public function _displaylist($tpl = null) {
		
		$document	=  JFactory::getDocument();
		
		$document->addStyleDeclaration( ".icon-32-plugin-add {background-image: url(components/com_altauserpoints/assets/images/icon-32-plugin-add.png);}", "text/css" );
		
		$logo = '<img src="'. JURI::root() . 'administrator/components/com_altauserpoints/assets/images/icon-48-altauserpoints.png" />&nbsp;&nbsp;';

		JToolBarHelper::title( $logo . 'AltaUserPoints :: ' .  JText::_( 'AUP_RULES' ), 'article' );
		getCpanelToolbar();
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::divider();
		/*
		if (JFactory::getUser()->authorise('core.plugins', 'com_altauserpoints')) {
			JToolBarHelper::custom( 'plugins', 'power-cord.png', 'power-cord.png', JText::_('AUP_PLUGINS'), false );
			JToolBarHelper::divider();
		}
		*/
		if (JFactory::getUser()->authorise('core.create', 'com_altauserpoints')) {
			JToolBarHelper::addNew( 'editrule' );
		}
		
		JToolBarHelper::custom( 'copyrule', 'copy.png', 'copy.png', JText::_('AUP_COPY') );
		
		if (JFactory::getUser()->authorise('core.edit', 'com_altauserpoints')) {
			JToolBarHelper::editList( 'editrule' );
		}
		
		if (JFactory::getUser()->authorise('core.delete', 'com_altauserpoints')) {
			JToolBarHelper::custom( 'deleterule', 'delete.png', 'delete.png', JText::_('AUP_DELETE') );
		}

		getPrefHelpToolbar();		
		
		isIEAdm();
		
		$this->assignRef( 'rules', $this->rules );
	
		$pagination = new JPagination( $this->total, $this->limitstart, $this->limit );
		$this->assignRef( 'pagination', $pagination );
		$this->assignRef( 'lists', $this->lists );
		
		
		parent::display( $tpl) ;
	}
	
	public function _edit_rule($tpl = null) {
	
		JFactory::getApplication()->input->set('hidemainmenu', 1);
		
		$document	=  JFactory::getDocument();		
		
		$document->addStyleDeclaration( ".icon-32-plugin-add {background-image: url(components/com_altauserpoints/assets/images/icon-32-plugin-add.png);}", "text/css" );
		
		$logo = '<img src="'. JURI::root() . 'administrator/components/com_altauserpoints/assets/images/icon-48-altauserpoints.png" />&nbsp;&nbsp;';

		JToolBarHelper::title( $logo . 'AltaUserPoints :: ' . JText::_( 'AUP_RULES' ), 'article-add' );
		getCpanelToolbar();
		
		JToolBarHelper::custom( 'plugins', 'power-cord.png', 'power-cord.png', JText::_('AUP_PLUGINS'), false );
		JToolBarHelper::divider();
		
		if (JFactory::getUser()->authorise('core.edit.state', 'com_altauserpoints')) {
			JToolbarHelper::apply('applyrule');
			JToolBarHelper::save( 'saverule' );
		}
		
		JToolBarHelper::cancel( 'cancelrule' );
		getPrefHelpToolbar();
		
		JHTML::_('behavior.calendar');
		$document->addScriptDeclaration("window.addEvent('domready', function(){ var JTooltips = new Tips($$('.hasTip'), { maxTitleChars: 50, fixed: false}); });");
		
		$lists = array();
		// build the html radio buttons for published
		$lists['published'] = JHTML::_('select.booleanlist', 'published', '', $this->row->published);		
		$lists['autoapproved'] = JHTML::_('select.booleanlist', 'autoapproved', '', $this->row->autoapproved);
		
		$auth[] = array();
		$auth[] = JHTML::_('select.option', 'Registered', 'Registered');
		$auth[] = JHTML::_('select.option', 'Author', 'Author');
		$auth[] = JHTML::_('select.option', 'Editor', 'Editor');
		$auth[] = JHTML::_('select.option', 'Publisher', 'Publisher');

		$lists['percentage'] = JHTML::_('select.booleanlist', 'percentage', '', $this->row->percentage);
		
		$lists['fixedpoints'] = JHTML::_('select.booleanlist', 'fixedpoints', '', $this->row->fixedpoints);
		
		//$options[] = array();
		$options[] = JHTML::_('select.option', '', JText::_( 'AUP_NONE' ) );
		$options[] = JHTML::_('select.option', 'us', JText::_( 'AUP_CAT_USER' ) );
		$options[] = JHTML::_('select.option', 'co', JText::_( 'AUP_CAT_COMMUNITY' ) );
		$options[] = JHTML::_('select.option', 'ar', JText::_( 'AUP_CAT_ARTICLE' ) );
		$options[] = JHTML::_('select.option', 'li', JText::_( 'AUP_CAT_LINK' ) );
		$options[] = JHTML::_('select.option', 'po', JText::_( 'AUP_CAT_POLL_QUIZZ' ) );		
		$options[] = JHTML::_('select.option', 're', JText::_( 'AUP_CAT_RECOMMEND_INVITE' ) );
		$options[] = JHTML::_('select.option', 'fo', JText::_( 'AUP_CAT_COMMENT_FORUM' ) );
		$options[] = JHTML::_('select.option', 'vi', JText::_( 'AUP_CAT_VIDEO' ) );		
		$options[] = JHTML::_('select.option', 'ph', JText::_( 'CAT_CAT_PHOTO' ) );
		$options[] = JHTML::_('select.option', 'mu', JText::_( 'AUP_CAT_MUSIC' ) );
		$options[] = JHTML::_('select.option', 'sh', JText::_( 'AUP_CAT_SHOPPING' ) );	
		$options[] = JHTML::_('select.option', 'pu', JText::_( 'AUP_CAT_PURCHASING' ) );		
		$options[] = JHTML::_('select.option', 'cd', JText::_( 'AUP_CAT_COUPON_CODE' ) );
		$options[] = JHTML::_('select.option', 'su', JText::_( 'AUP_CAT_SUBSCRIPTION' ) );
		$options[] = JHTML::_('select.option', 'ga', JText::_( 'AUP_CAT_GAMING' ) );
		$options[] = JHTML::_('select.option', 'sy', JText::_( 'AUP_CAT_SYSTEM' ) );	
		$options[] = JHTML::_('select.option', 'ot', JText::_( 'AUP_CAT_OTHER' ) );		
		$lists['category'] = JHTML::_('select.genericlist', $options, 'category', 'class="inputbox" size="1"' ,'value', 'text', $this->row->category );
		
		//$options[] = array();
		$options[] = JHTML::_('select.option', '1', '1 ' . JText::_( 'AUP_DAY' ) );
		$options[] = JHTML::_('select.option', '2', '2 ' . JText::_( 'AUP_DAYS' ) );
		$options[] = JHTML::_('select.option', '3', '3 ' . JText::_( 'AUP_DAYS' ) );
		$options[] = JHTML::_('select.option', '4', '4 ' . JText::_( 'AUP_DAYS' ) );
		$options[] = JHTML::_('select.option', '5', '5 ' . JText::_( 'AUP_DAYS' ) );
		$options[] = JHTML::_('select.option', '6', '6 ' . JText::_( 'AUP_DAYS' ) );		
		$options[] = JHTML::_('select.option', '7', '7 ' . JText::_( 'AUP_DAYS' ) );
		$options[] = JHTML::_('select.option', '8', '8 ' . JText::_( 'AUP_DAYS' ) );
		$options[] = JHTML::_('select.option', '9', '9 ' . JText::_( 'AUP_DAYS' ) );		
		$options[] = JHTML::_('select.option', '10', '10 ' . JText::_( 'AUP_DAYS' ) );
		$options[] = JHTML::_('select.option', '11', '11 ' . JText::_( 'AUP_DAYS' ) );
		$options[] = JHTML::_('select.option', '12', '12 ' . JText::_( 'AUP_DAYS' ) );	
		$options[] = JHTML::_('select.option', '13', '13 ' . JText::_( 'AUP_DAYS' ) );		
		$options[] = JHTML::_('select.option', '14', '14 ' . JText::_( 'AUP_DAYS' ) );
		$options[] = JHTML::_('select.option', '15', '15 ' . JText::_( 'AUP_DAYS' ) );
		$options[] = JHTML::_('select.option', '20', '20 ' . JText::_( 'AUP_DAYS' ) );	
		$options[] = JHTML::_('select.option', '25', '25 ' . JText::_( 'AUP_DAYS' ) );		
		$options[] = JHTML::_('select.option', '30', '30 ' . JText::_( 'AUP_DAYS' ) );	
		$options[] = JHTML::_('select.option', '60', '2 ' . JText::_( 'AUP_MONTHS' ) );		
		$options[] = JHTML::_('select.option', '90', '3 ' . JText::_( 'AUP_MONTHS' ) );
		$options[] = JHTML::_('select.option', '180', '6 ' . JText::_( 'AUP_MONTHS' ) );
		$options[] = JHTML::_('select.option', '365', '1 ' . JText::_( 'AUP_YEAR' ) );
		$options[] = JHTML::_('select.option', '730', '2 ' . JText::_( 'AUP_YEARS' ) );
		$options[] = JHTML::_('select.option', '1825', '5 ' . JText::_( 'AUP_YEARS' ) );
		
		$lists['inactive_preset_period'] = JHTML::_('select.genericlist', $options, 'content_items', 'class="inputbox" size="1"' ,'value', 'text', $this->row->content_items );		
		
		$lists['displaymsg'] = JHTML::_('select.booleanlist', 'displaymsg', '', $this->row->displaymsg);
		
		$lists['notification'] = JHTML::_('select.booleanlist', 'notification', '', $this->row->notification);
		
		//$options[] = array();	
		$options[] = JHTML::_('select.option', '0', JText::_( 'AUP_PLAIN-TEXT' ) );
		$options[] = JHTML::_('select.option', '1', JText::_( 'AUP_HTML' ) );
		$lists['emailformat'] = JHTML::_('select.genericlist', $options, 'emailformat', 'class="inputbox" size="1"' ,'value', 'text', $this->row->emailformat );
		
		$lists['bcc2admin'] = JHTML::_('select.booleanlist', 'bcc2admin', '', $this->row->bcc2admin);
		
		//$options[] = array();
		$options[] = JHTML::_('select.option', '0', JText::_( 'AUP_SELECT_A_METHOD_TO_ATTRIB_POINTS' ) );
		$options[] = JHTML::_('select.option', '1', JText::_( 'AUP_ONLY_ONCE' ) );
		$options[] = JHTML::_('select.option', '2', JText::_( 'AUP_ONCE_PER_DAY' ) );
		$options[] = JHTML::_('select.option', '3', JText::_( 'AUP_ONCE_PER_DAY_MIXED' ) );
		$options[] = JHTML::_('select.option', '5', JText::_( 'AUP_ONCE_PER_WEEK' ) );
		$options[] = JHTML::_('select.option', '6', JText::_( 'AUP_ONCE_PER_MONTH' ) );
		$options[] = JHTML::_('select.option', '7', JText::_( 'AUP_ONCE_PER_YEAR' ) );
		// --------------
		$options[] = JHTML::_('select.option', '4', JText::_( 'AUP_WHENEVER' ) );
		$lists['methods'] = JHTML::_('select.genericlist', $options, 'method', 'class="inputbox" size="1"' ,'value', 'text', $this->row->method );
		
		$lists['groups'] = $this->groups;		
		
		//$options[] = array();
		$options[] = JHTML::_('select.option', '0', JText::_( 'AUP_DEFAULT' ) );
		$options[] = JHTML::_('select.option', '1', '1 ' . JText::_( 'AUP_DAY' ) );
		$options[] = JHTML::_('select.option', '2', '2 ' . JText::_( 'AUP_DAYS' ) );
		$options[] = JHTML::_('select.option', '3', '3 ' . JText::_( 'AUP_DAYS' ) );
		$options[] = JHTML::_('select.option', '4', '4 ' . JText::_( 'AUP_DAYS' ) );
		$options[] = JHTML::_('select.option', '5', '5 ' . JText::_( 'AUP_DAYS' ) );
		$options[] = JHTML::_('select.option', '6', '6 ' . JText::_( 'AUP_DAYS' ) );
		$options[] = JHTML::_('select.option', '7', '7 ' . JText::_( 'AUP_DAYS' ) );
		$options[] = JHTML::_('select.option', '8', '8 ' . JText::_( 'AUP_DAYS' ) );
		$options[] = JHTML::_('select.option', '9', '9 ' . JText::_( 'AUP_DAYS' ) );
		$options[] = JHTML::_('select.option', '10', '10 ' . JText::_( 'AUP_DAYS' ) );
		$options[] = JHTML::_('select.option', '11', '11 ' . JText::_( 'AUP_DAYS' ) );
		$options[] = JHTML::_('select.option', '12', '12 ' . JText::_( 'AUP_DAYS' ) );
		$options[] = JHTML::_('select.option', '13', '13 ' . JText::_( 'AUP_DAYS' ) );
		$options[] = JHTML::_('select.option', '14', '14 ' . JText::_( 'AUP_DAYS' ) );
		$options[] = JHTML::_('select.option', '15', '15 ' . JText::_( 'AUP_DAYS' ) );
		$options[] = JHTML::_('select.option', '20', '20 ' . JText::_( 'AUP_DAYS' ) );
		$options[] = JHTML::_('select.option', '25', '25 ' . JText::_( 'AUP_DAYS' ) );
		$options[] = JHTML::_('select.option', '30', '1 ' . JText::_( 'AUP_MONTH' ) );
		$options[] = JHTML::_('select.option', '60', '2 ' . JText::_( 'AUP_MONTHS' ) );
		$options[] = JHTML::_('select.option', '90', '3 ' . JText::_( 'AUP_MONTHS' ) );
		$options[] = JHTML::_('select.option', '180', '6 ' . JText::_( 'AUP_MONTHS' ) );
		$options[] = JHTML::_('select.option', '360', '1 ' . JText::_( 'AUP_YEAR' ) );
		$options[] = JHTML::_('select.option', '720', '2 ' . JText::_( 'AUP_YEARS' ) );
		$options[] = JHTML::_('select.option', '1080', '3 ' . JText::_( 'AUP_YEARS' ) );
		$options[] = JHTML::_('select.option', '1440', '4 ' . JText::_( 'AUP_YEARS' ) );
		$options[] = JHTML::_('select.option', '1800', '5 ' . JText::_( 'AUP_YEARS' ) );
		$options[] = JHTML::_('select.option', '3600', '10 ' . JText::_( 'AUP_YEARS' ) );
		$lists['type_expire_date'] = JHTML::_('select.genericlist', $options, 'type_expire_date', 'class="inputbox" size="1"' ,'value', 'text', $this->row->type_expire_date );
		
		
		//$options[] = array();	
		$options[] = JHTML::_('select.option', '0', JText::_( 'AUP_NONE' ) );
		foreach ($this->chainedrules as $chain) 
		{
			$options[] = JHTML::_('select.option', $chain->id, JText::_( $chain->rule_name ) );
		
		}
		$lists['linkup'] = JHTML::_('select.genericlist', $options, 'linkup', 'class="inputbox" size="1"' ,'value', 'text', $this->row->linkup );		
		$lists['displayactivity'] = JHTML::_('select.booleanlist', 'displayactivity', '', $this->row->displayactivity);		
		$this->assignRef( 'row', $this->row );
		$this->assignRef( 'lists', $lists );					
		parent::display( "form" ) ;
	}
	
	public function _displaycustompoints() {	
		$this->assignRef( 'cid', $this->cid );
		$this->assignRef( 'name', $this->name );		
		parent::display( "custom" ) ;
	}
	
	public function _displaycustomrulepoints(){		
		$this->assignRef( 'cid', $this->cid );
		parent::display( "custom2" ) ;	
	}
}
?>
