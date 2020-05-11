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

class AltauserpointsModelRegisterqrcode extends JmodelLegacy {

	function __construct(){
		parent::__construct();
	}

	function attribPoints()
	{	
		$app = JFactory::getApplication();
		$db			    = JFactory::getDBO();
		
		$login  		= JFactory::getApplication()->input->get('login', '', 'username');
		$coupon			= JFactory::getApplication()->input->get('couponCode', '', 'string');
		$trackID		= JFactory::getApplication()->input->get('trackID', '', 'string');
		
		$q = "SELECT id FROM #__alpha_userpoints_qrcodetrack WHERE `trackid`=".$db->quote($trackID);
		$db->setQuery( $q );
		$idTrack = $db->loadResult();
		
		$q = "SELECT id FROM #__users WHERE `username`=".$db->quote($login) . " AND `block`='0'";
		$db->setQuery($q);
		$userID = $db->loadResult();
		
		if ( $userID ) 
		{
			// insert API AlphaUserPoint 
			$api_AUP = JPATH_SITE.'/components/com_altauserpoints/helper.php';
			require_once ($api_AUP);
			
			$referrerid = AltaUserPointsHelper::getAnyUserReferreID( $userID );
			
			$nullDate	= $db->getNullDate();
			$date =& JFactory::getDate();
			$now  = $date->toSql();
			
			$q = "SELECT * FROM #__alpha_userpoints_coupons WHERE `couponcode`=".$db->quote($coupon)." AND (`expires`>='$now' OR `expires`='0000-00-00 00:00:00')";
			$db->setQuery( $q );
			$result  = $db->loadObjectList();
			if ( $result )
			{			
				$resultCouponExist = 0;
			
				// check if public or private coupon
				if ( !$result[0]->public )
				{
					// private -> usable once per one user
					$q = "SELECT count(*) FROM #__alpha_userpoints_details WHERE `keyreference`=".$db->quote($coupon)." AND `enabled`='1'";
					$db->setQuery( $q );
					$resultCouponExist = $db->loadResult();
					if ( !$resultCouponExist )
					{
						// insert points
						AltaUserPointsHelper::newpoints( 'sysplgaup_couponpointscodes', $referrerid, $result[0]->couponcode, $result[0]->description, $result[0]->points );
						//if ( AltaUserPointsHelper::newpoints( 'sysplgaup_couponpointscodes', $referrerid, $result[0]->couponcode, $result[0]->description, $result[0]->points, true )===true ){
							// insert confirmed in track table
							$this->updateTableQRTrack($idTrack);
							return $result[0]->points;
						//}
					} 
					else 
					{
						$msg = (JText::_('AUP_THIS_COUPON_WAS_ALREADY_USED'));
						$app->redirect('index.php?option=com_altauserpoints&view=registerqrcode&QRcode='.$coupon.'&trackID='.$trackID, $msg);
					}
				} 
				elseif ( $result[0]->public )
				{
					// public -> usable once per all users
					$keyreference = $coupon . "##" . $userID;
					$q = "SELECT count(*) FROM #__alpha_userpoints_details WHERE `keyreference`=".$db->quote($keyreference)." AND `enabled`='1'";
					$db->setQuery( $q );
					$resultCouponExist = $db->loadResult();
					if ( !$resultCouponExist )
					{
						// insert points
						AltaUserPointsHelper::newpoints( 'sysplgaup_couponpointscodes', $referrerid, $keyreference, $result[0]->description, $result[0]->points );
						//if ( AltaUserPointsHelper::newpoints( 'sysplgaup_couponpointscodes', $referrerid, $keyreference, $result[0]->description, $result[0]->points, true )===true ){
							// insert confirmed in track table
							$this->updateTableQRTrack($idTrack);
							return $result[0]->points;
						//}
					} 
					else 
					{
						$msg = (JText::_('AUP_THIS_COUPON_WAS_ALREADY_USED'));
						$app->redirect('index.php?option=com_altauserpoints&view=registerqrcode&QRcode='.$coupon.'&trackID='.$trackID, $msg);
					}				
				} 
			}
			else
			{
				$msg = (JText::_('AUP_COUPON_NOT_AVAILABLE'));
        		$app->redirect('index.php?option=com_altauserpoints&view=registerqrcode&QRcode='.$coupon.'&trackID='.$trackID, $msg);	
				
			}		

		
		
		}
		else
		{
			// no username
			$msg = JText::_('ALERTNOTAUTH' );
      		$app->redirect('index.php?option=com_altauserpoints&view=registerqrcode&QRcode='.$coupon.'&trackID='.$trackID, $msg);
		}
		
	}
	
	
	function trackQRcode($trackID='', $couponCode='')
	{	
		if (!$trackID || !$couponCode) return;
		
		$db			= JFactory::getDBO();
		
		//already inserted (e.g. error login)
		$q = "SELECT trackid FROM #__alpha_userpoints_qrcodetrack WHERE `trackid`=".$db->quote($trackID)." ";
		$db->setQuery( $q );
		$alreadytrackid = $db->loadResult();
		if (!$alreadytrackid) 
		{		
			$couponID = 0;		
			
			$jnow		= JFactory::getDate();		
			$now		= $jnow->toSql();
			
			$q = "SELECT id FROM #__alpha_userpoints_coupons WHERE `couponcode`=".$db->quote($couponCode)." AND `printable`='1'";
			$db->setQuery( $q );
			$couponID = $db->loadResult();
			
			$ip		  	= $_SERVER["REMOTE_ADDR"];
			$device		= $_SERVER['HTTP_USER_AGENT'];
					
			$ipDetail 	= countryCityFromIP( $ip );
			$country  	= $ipDetail['country']; 
			$city     	= $ipDetail['city'];
		
			JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_altauserpoints/tables');
			$row = JTable::getInstance('qrcodetrack');
			$row->id				= NULL;
			$row->couponid			= $couponID;
			$row->trackid			= $trackID;
			$row->trackdate			= $now;
			$row->country 			= $country;		
			$row->city				= $city;
			$row->device			= $device;
			$row->ip				= $ip;
			$row->confirmed			= 0;
	
			if ( !$row->store() )
			{
				JFactory::getApplication()->enqueueMessage(  $row->getError(),'error');
			}
		}	
	
	}
	
	function updateTableQRTrack($idTrack)
	{
		$db = JFactory::getDBO();
	
		JTable::addIncludePath(JPATH_ADMINISTRATOR.'/components/com_altauserpoints/tables');
		$row = JTable::getInstance('qrcodetrack');
		$row->load( intval($idTrack) );
		$row->confirmed = 1;
		$db->updateObject( '#__alpha_userpoints_qrcodetrack', $row, 'id' );	
	
	}

}
?>