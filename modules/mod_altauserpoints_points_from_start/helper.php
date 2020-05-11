<?php
/**
* @package		AltaUserPoints for Joomla 3.x
* @copyright	Copyright (C) 2008-2016. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// no direct access
defined('_JEXEC') or die('Restricted access');

class modAltaUserPointsPointsFromStartHelper {

	public static function getPoints($params) 
	{

		$db		= JFactory::getDBO();
		
		$earnedpoints = 0;
		$spentpoints  = 0;
		$enabled	  = " AND enabled='1'";
		
		$purged = $params->get('purged', '0');
		
		if ( $purged ) $enabled	= "";
		
		$user 	= JFactory::getUser();  		
		
		if ( $user->id ) 
		{		
			require_once JPATH_ROOT.'/components/com_altauserpoints/helper.php';
			$referreid = AltaUserPointsHelper::getAnyUserReferreID( $user->id );
			
			$query = "SELECT SUM(points) FROM #__alpha_userpoints_details " .
					 "WHERE points>'0' AND referreid='$referreid' AND approved='1' AND `status`='1' $enabled";
			$db->setQuery( $query );
			$earnedpoints = $db->loadResult();			
			
			$query = "SELECT SUM(points) FROM #__alpha_userpoints_details " .
					 "WHERE points<'0' AND referreid='$referreid' AND approved='1' AND `status`='1' $enabled";
			$db->setQuery( $query );
			$spentpoints = $db->loadResult();			
			
		}
	
		return array($earnedpoints, $spentpoints);	
	}
}
?>