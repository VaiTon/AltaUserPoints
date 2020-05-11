<?php
/**
* @package		AltaUserPoints for Joomla 3.x
* @copyright	Copyright (C) 2015-2016. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class modAltaUserPointsCouponCodeHelper {

	public static function checkcoupon($params, $coupon) {
				
		$app = JFactory::getApplication();		
		
		// check if user is logged in		
		$user = JFactory::getUser();
		if ( !$user->id ) 
		{
			echo "<script> alert('".JText::_( 'MODAUP_CP_YOU_MUST_BE_LOGGED' )."'); </script>";
			return;
		}
		
		// insert API AlphaUserPoint
		$api_AUP = JPATH_SITE.'/components/com_altauserpoints/helper.php';
		if ( file_exists($api_AUP))
		{
			require_once ($api_AUP);	
			
			$db = JFactory::getDBO();
			
			$nullDate	= $db->getNullDate();
			$date = JFactory::getDate();
			$now  = $date->toSql();
			
			$query = "SELECT * FROM #__alpha_userpoints_coupons WHERE `couponcode`='$coupon' AND (`expires`>='$now' OR `expires`='0000-00-00 00:00:00')";
			$db->setQuery( $query );
			$result  = $db->loadObjectList();
			if ( $result )
			{			
				$resultCouponExist = 0;
				// active user		
				$referrerid = @$_SESSION['referrerid'];
				
				// check if public or private coupon
				if ( !$result[0]->public )
				{
					// private -> usable once per one user
					$query = "SELECT count(*) FROM #__alpha_userpoints_details WHERE `keyreference`='$coupon' AND `enabled`='1'";
					$db->setQuery( $query );
					$resultCouponExist = $db->loadResult();
					if ( !$resultCouponExist )
					{
						// insert points
						AltaUserPointsHelper::newpoints( 'sysplgaup_couponpointscodes', $referrerid, $result[0]->couponcode, $result[0]->description, $result[0]->points );
					} 
					else 
					{
						$app->enqueueMessage(JText::_('MODAUP_CP_THIS_COUPON_WAS_ALREADY_USED'));
					}
				} 
				elseif ( $result[0]->public )
				{
					// public -> usable once per all users
					$keyreference = $coupon . "##" . $user->id;
					$query = "SELECT count(*) FROM #__alpha_userpoints_details WHERE `keyreference`='$keyreference' AND `enabled`='1'";
					$db->setQuery( $query );
					$resultCouponExist = $db->loadResult();
					if ( !$resultCouponExist )
					{
						// insert points
						AltaUserPointsHelper::newpoints( 'sysplgaup_couponpointscodes', $referrerid, $keyreference, $result[0]->description, $result[0]->points );
					} 
					else 
					{
						$app->enqueueMessage(JText::_('MODAUP_CP_THIS_COUPON_WAS_ALREADY_USED'));
					}				
				} 
			}
			else
			{
				$app->enqueueMessage(JText::_('MODAUP_CP_PLEASE_CHECK_YOUR_COUPON'));
				return;
			}		
		}
	}
}
?>