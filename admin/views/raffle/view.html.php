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

class altauserpointsViewRaffle extends JViewLegacy {

	function _displaylist($tpl = null) {

		$document	=  JFactory::getDocument();
		
		$logo = '<img src="'. JURI::root() . 'administrator/components/com_altauserpoints/assets/images/icon-48-altauserpoints.png" />&nbsp;&nbsp;';
		
		JToolBarHelper::title( $logo . 'AltaUserPoints :: ' .  JText::_( 'AUP_RAFFLE' ), 'featured' );
		getCpanelToolbar();
		JToolBarHelper::publishList();
		JToolBarHelper::unpublishList();
		JToolBarHelper::divider();
		if (JFactory::getUser()->authorise('core.edit', 'com_altauserpoints')) {
			JToolBarHelper::editList( 'editraffle' );
		}
		if (JFactory::getUser()->authorise('core.create', 'com_altauserpoints')) {
			JToolBarHelper::addNew( 'editraffle' );
		}
		if (JFactory::getUser()->authorise('core.delete', 'com_altauserpoints')) {
			JToolBarHelper::custom( 'deleteraffle', 'delete.png', 'delete.png', JText::_('AUP_DELETE') );		
		}
		getPrefHelpToolbar();	

		$this->assignRef( 'raffle', $this->raffle );
		$this->assignRef( 'lists', $this->lists );
	
		$pagination = new JPagination( $this->total, $this->limitstart, $this->limit );		
		$this->assignRef( 'pagination', $pagination );
		
		parent::display( $tpl) ;
	}
	
	function _edit_raffle($tpl = null) {
		
		$document	=  JFactory::getDocument();
		$language 	= JFactory::getLanguage();
		$tag 		= $language->getTag();
		
		JFactory::getApplication()->input->set( 'hidemainmenu', 1 );
		
		$logo = '<img src="'. JURI::root() . 'administrator/components/com_altauserpoints/assets/images/icon-48-altauserpoints.png" />&nbsp;&nbsp;';

		JToolBarHelper::title( $logo . 'AltaUserPoints :: ' .  JText::_( 'AUP_RAFFLE' ), 'featured' );
		JToolBarHelper::custom( 'cpanel', 'default.png', 'default.png', JText::_('AUP_CPANEL'), false );
		if (JFactory::getUser()->authorise('core.edit.state', 'com_altauserpoints')) {
			JToolbarHelper::apply('applyraffle');
			JToolBarHelper::save( 'saveraffle' );
		}
		JToolBarHelper::cancel( 'cancelraffle' );
		JToolBarHelper::divider();
		JToolBarHelper::preferences('com_altauserpoints');
		if (file_exists( JPATH_COMPONENT . "/help/" . $tag . "/screen.altauserpoints.html")){
			JToolBarHelper::help( $tag . '/screen.altauserpoints', true );
		} else JToolBarHelper::help( 'en-GB/screen.altauserpoints', true );

  		JHTML::_('behavior.calendar');
				
		$lists = array();
		// build the html
		$lists['published'] = JHTML::_('select.booleanlist', 'published', '', $this->row->published);		
		$lists['inscription'] = JHTML::_('select.booleanlist', 'inscription', '', $this->row->inscription);
		$lists['multipleentries'] = JHTML::_('select.booleanlist', 'multipleentries', '', $this->row->multipleentries);		
		
		$lists['removepointstoparticipate'] = JHTML::_('select.booleanlist', 'removepointstoparticipate', '', $this->row->removepointstoparticipate);
		
		$javascript = 'onchange="return mxctoggleSystemRaffle(\'rafflesystemexpand\'+this.value);"';
		$rafflesys[] = JHTML::_('select.option', '0', JText::_( 'AUP_POINTS' ));
		$rafflesys[] = JHTML::_('select.option', '1', JText::_( 'AUP_COUPON_CODES' ));
		$rafflesys[] = JHTML::_('select.option', '2', JText::_( 'AUP_EMAIL_WITH_A_LINK_TO_DOWNLOAD' ));
		$rafflesys[] = JHTML::_('select.option', '3', JText::_( 'AUP_EMAIL_ONLY' ));		
		$lists['rafflesystem'] = JHTML::_( 'select.genericlist', $rafflesys, 'rafflesystem', 'class="inputbox" size="1" ' . $javascript, 'value', 'text', $this->row->rafflesystem );
		//$lists['rafflesystem'] = JHTML::_( 'select.genericlist', $rafflesys, 'rafflesystem' );
		
		$numwin[] = JHTML::_('select.option', '1', '1');
		$numwin[] = JHTML::_('select.option', '2', '2');
		$numwin[] = JHTML::_('select.option', '3', '3');
		$lists['numwinner'] = JHTML::_( 'select.genericlist', $numwin, 'numwinner', 'class="inputbox" size="1"', 'value', 'text', $this->row->numwinner );
		
		$db			    = JFactory::getDBO();
		$query = "SELECT * FROM #__alpha_userpoints_coupons";
		$db->setQuery($query);
		$couponcodes = $db->loadObjectlist();
		
		$selCouponCode[] = JHTML::_('select.option', '0', JText::_( 'AUP_SELECT_A_COUPON_CODE_FOR_THIS_RANK' ));
		if ( $couponcodes ) 
		{
			foreach ($couponcodes as $couponcode) 
			{
				$selCouponCode[] = JHTML::_('select.option', $couponcode->id, $couponcode->couponcode . ' [ '.$couponcode->points . ' ' . JText::_( 'AUP_POINTS' )  . ' ]');
			}
		}
		
		$lists['couponcodeid1'] = JHTML::_( 'select.genericlist', $selCouponCode, 'couponcodeid1', 'class="inputbox" size="1" ', 'value', 'text', $this->row->couponcodeid1 );
		$lists['couponcodeid2'] = JHTML::_( 'select.genericlist', $selCouponCode, 'couponcodeid2', 'class="inputbox" size="1" ', 'value', 'text', $this->row->couponcodeid2 );
		$lists['couponcodeid3'] = JHTML::_( 'select.genericlist', $selCouponCode, 'couponcodeid3', 'class="inputbox" size="1" ', 'value', 'text', $this->row->couponcodeid3 );
		
		$lists['sendcouponbyemail'] = JHTML::_('select.booleanlist', 'sendcouponbyemail', '', $this->row->sendcouponbyemail);
		
		$this->assignRef( 'row', $this->row );
		$this->assignRef( 'lists', $lists );				
	
		parent::display( "form" ) ;
	}
}
?>
