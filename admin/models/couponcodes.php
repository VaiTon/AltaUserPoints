<?php
/*
 * @component AltaUserPoints
 * @copyright Copyright (C) 2015-2016 Bernard Gilly - Adrien Roussel
 * @license : GNU/GPL
 * @Website : https://www.nordmograph.com/extensions
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die();

jimport( 'joomla.application.component.model' );

class altauserpointsModelCouponcodes extends JmodelLegacy {

	function __construct(){
		parent::__construct();
	}
	
	function _load_couponcodes() {
		$app = JFactory::getApplication();
		
		$db			    = JFactory::getDBO();
				
		$total 			= 0;
		
		// Get the pagination request variables
		$limit = $app->getUserStateFromRequest('com_altauserpoints.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = JFactory::getApplication()->input->get('limitstart', 0, 'int');
		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ( $limit != 0 ? (floor( $limitstart / $limit ) * $limit) : 0);	
		$filter_state = JFactory::getApplication()->input->get('filter_state', '', 'string');
		$filter_category_id = JFactory::getApplication()->input->get('filter_category_id', 0, 'int');		
		
		if ( $filter_state!='' ) {
			$filter = "WHERE a.printable='".$filter_state."'";						
		} else {
			$filter = "WHERE (a.printable='1' OR a.printable='0')";		
		}
		
		if ( $filter_category_id > 1 ) {			
			$filter .= " AND a.category='".$filter_category_id."'";		
		}

		$query = "SELECT a.*, c.title AS category_title FROM #__alpha_userpoints_coupons AS a, #__categories AS c $filter AND c.id=a.category"; 
		$total = @$this->_getListCount($query);
		$result = $this->_getList($query, $limitstart, $limit);
		
		// message if rule disabled
		$query = "SELECT id FROM #__alpha_userpoints_rules WHERE plugin_function = 'sysplgaup_couponpointscodes' AND published = '1'";
		$db->setQuery($query);
		$alert = $db->loadResult();
		
		if ( !$alert ) $app->enqueueMessage( JText::_('AUP_THIS_RULE_IS_DISABLED'));
		
		$lists = array();
		$options[] = JHTML::_('select.option', '', JText::_( 'AUP_ALL' ) );
		$options[] = JHTML::_('select.option', '1', JText::_( 'AUP_PRINTABLE' ) );
		$options[] = JHTML::_('select.option', '0', JText::_( 'AUP_UNPRINTABLE' ) );
		$lists['filter_state'] = JHTML::_('select.genericlist', $options, 'filter_state', 'class="inputbox" size="1" onchange="document.adminForm.submit();"' ,'value', 'text', $filter_state );		
		
		$lists['filter_category_id'] = $filter_category_id;
		$lists['public'] 			 = JHTML::_('select.booleanlist',  'public', 'class="inputbox"', 1 );
		$lists['printable'] 		 = JHTML::_('select.booleanlist',  'printable', 'class="inputbox"', 0 );
		$lists['enabledincrement'] 	 = JHTML::_('select.booleanlist',  'enabledincrement', 'class="inputbox"', 0 );


		return array($result, $total, $limit, $limitstart, $lists);
	
	}
	
	
	function _edit_coupon() {
	
		$db     = JFactory::getDBO();

		$cid 	= JFactory::getApplication()->input->get('cid', array(0), 'array');
		$option = JFactory::getApplication()->input->get('option', '','cmd');
		
		if (!is_array( $cid )) {
			$cid = array(0);
		}

		$row = JTable::getInstance('coupons');
		$row->load( $cid[0] );
		
		$lists = array();
		$lists['public'] 		= JHTML::_('select.booleanlist',  'public', 'class="inputbox"', $row->public );
		$lists['printable'] 	= JHTML::_('select.booleanlist',  'printable', 'class="inputbox"', $row->printable );
		
		$lists['filter_category_id'] = $row->category;
		
		return array($row, $lists); 
	
	}
	
	
	function _delete_coupon() {
		$app = JFactory::getApplication();

		// initialize variables
		$db			= JFactory::getDBO();
		$cid		= JFactory::getApplication()->input->get('cid', array(), 'array');
		$msgType	= '';
		
		JArrayHelper::toInteger($cid);
		
		if (count($cid)) {
		
			$query = "DELETE FROM #__alpha_userpoints_coupons"
					. "\n WHERE (`id` = " . implode(' OR `id` = ', $cid) . ")"
					;
			$db->setQuery($query);
			
			if (!$db->query()) {
				$msg = $db->getErrorMsg();
				$msgType = 'error';
			} else $msg = JText::_('AUP_SUCCESSFULLYDELETED');

		}

		$app->redirect('index.php?option=com_altauserpoints&task=couponcodes', $msg, $msgType);
		//JControllerLegacy::redirect(); 				

	}
	
	function _save_coupon($apply=0)
	{
		$app = JFactory::getApplication();
		// initialize variables
		$db = JFactory::getDBO();
		//$post	= $app->input->post;
		$post	= JRequest::get('post');
		$row = JTable::getInstance('coupons');
		
		$id= $app->input->get( 'id', 0, 'int' );
		
		if ( $post['couponcode']=='' ) {
			$post['couponcode']= $random = $this->createRandomCode();
		}
		
		if (!$row->bind( $post )) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		
		if (!$row->store()) {
			echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
			exit();
		}
		if ($post['printable']==1) {
			// save QRCode png
			$pathQR = JPATH_COMPONENT.'/assets/coupons/QRcode';
			$urlQR = JURI::root().'index.php?option=com_altauserpoints&view=registerqrcode&QRcode='.$post['couponcode'];
			createURLQRcodePNG( $urlQR, 50, $pathQR.'/50/'.$post['couponcode'].'.png' );
			createURLQRcodePNG( $urlQR, 250, $pathQR.'/250/'.$post['couponcode'].'.png' );
		}
		// msg
		$msg = JText::_( 'AUP_DETAILSSAVED' );
		if (!$apply){
			$app->redirect('index.php?option=com_altauserpoints&task=couponcodes', $msg);
		} else {
			$app->redirect('index.php?option=com_altauserpoints&task=editcoupon&cid[]='.$id, $msg);
		}
		//JControllerLegacy::redirect();
	}
	
	function _save_coupongenerator() {
		$app = JFactory::getApplication();

		// initialize variables
		$db = JFactory::getDBO();
		//$post	= $app->input->post;
		$post	= JRequest::get( 'post' );
		$numbercouponcode	= $app->input->get('numbercouponcode', 20, 'int');	
		$numrandomchars		= $app->input->get('numrandomchars', 0, 'int');
		$enabledincrement	= intval($app->input->get('enabledincrement', 0, 'int'));		
		
		if ( $post['points'] )  {
			for ($i=0, $n=$numbercouponcode; $i < $n; $i++) {
			
				$row = JTable::getInstance('coupons');
				
				$row->id = NULL;
				
				$couponcode = "";
				$couponcode .= $post['prefixcouponcode'];
				if ( $numrandomchars   ) $couponcode .= $this->createRandomCode($numrandomchars);					
				if ( $enabledincrement ) $couponcode .= ($i+1);
				$row->couponcode = $couponcode;
				$row->description = $post['description'] ;
				$row->points = $post['points'] ;			
				$row->expires = $post['expires'] ;
				$row->public = $post['public'] ;
				$row->printable = $post['printable'] ;
				if ( $couponcode!='' ) {
					if (!$row->store()) {
						echo "<script> alert('".$row->getError()."'); window.history.go(-1); </script>\n";
						exit();
					}
					if ($post['printable']==1) {
						// save QRCode png
						$pathQR = JPATH_COMPONENT.'/assets/coupons/QRcode';
						$urlQR = JURI::root().'index.php?option=com_altauserpoints&view=registerqrcode&QRcode='.$couponcode;
						createURLQRcodePNG( $urlQR, 50, $pathQR.'/50/'.$couponcode.'.png' );
						createURLQRcodePNG( $urlQR, 250, $pathQR.'/250/'.$couponcode.'.png' );
					}
				}		
			}
		}
		          		
		$msg = JText::_( 'AUP_DETAILSSAVED' );
		
		$app->redirect('index.php?option=com_altauserpoints&task=couponcodes', $msg);
		//JControllerLegacy::redirect();
	}
	
	function _load_qrcodestats ($id) {
		
		$app = JFactory::getApplication();
		
		$db			    = JFactory::getDBO();
				
		$total 			= 0;
		
		// Get the pagination request variables
		$limit = $app->getUserStateFromRequest('com_altauserpoints.limit', 'limit', $app->getCfg('list_limit'), 'int');
		$limitstart = JFactory::getApplication()->input->get('limitstart', 0, 'int');
		// In case limit has been changed, adjust limitstart accordingly
		$limitstart = ( $limit != 0 ? (floor( $limitstart / $limit ) * $limit) : 0);		

		$query = "SELECT * FROM #__alpha_userpoints_qrcodetrack WHERE couponid='".intval($id)."'";
		$total = @$this->_getListCount($query);
		$result = $this->_getList($query, $limitstart, $limit);
		return array($result, $total, $limit, $limitstart);
	
	}

	
	function createRandomCode($n=8) {
	
		$chars = "ABCDEFGHIJKLMNPQRSTUVWXYZ123456789";	
		srand((double)microtime()*1000000);
	
		$i = 0;	
		$code = "";	
		$n = $n - 1;
		
		while ($i <= $n) {	
			$num = rand() % 33;	
			$tmp = substr($chars, $num, 1);	
			$code = $code . $tmp;	
			$i++;	
		}	
	
		return $code;	
	
	}
	
	function _print_coupon() {
	
		$db     = JFactory::getDBO();

		$id 	= JFactory::getApplication()->input->get('id', 0, 'int');
		
		$row = JTable::getInstance('coupons');
		$row->load( $id );		
	
		return $row; 
	
	}


}
?>