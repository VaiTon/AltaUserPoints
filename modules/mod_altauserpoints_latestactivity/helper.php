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

class modAltaUserPointsLatestActivityHelper {

	public static function getList($params) {

		$db			      = JFactory::getDBO();
		$user 			  = JFactory::getUser();	
		
		$count		      = intval($params->get('count', 5));
		$usrname		  = trim($params->get('usrname', 'name'));
		$allmembers		  = intval($params->get('showallmembers',1));
		
		$nullDate	= $db->getNullDate();
		$date = JFactory::getDate();
		$now  = $date->toSql();		
		
		$currentuser  = $user->id;
		$selecteduser = "";
		
		if ( $currentuser && !$allmembers ) 
		{			
			$selecteduser = "AND u.id='".$currentuser."' ";
		}
		
		$displayactivity = '';
		require_once (JPATH_ROOT.'/components/com_altauserpoints/helper.php');
		$version = AltaUserPointsHelper::getAupVersion();
		if (version_compare( $version , '2.0.2', '>=' ))
		{
			$displayactivity = " AND r.displayactivity='1'";
		}
		
		
		$query = "SELECT a.insert_date, a.referreid, aup.userid, a.points AS last_points, a.datareference, u.".$usrname." AS usrname, r.rule_name, r.plugin_function, r.category"
			   . " FROM #__alpha_userpoints_details AS a, #__alpha_userpoints AS aup, #__users AS u, #__alpha_userpoints_rules AS r"
			   . " WHERE aup.referreid=a.referreid " . $selecteduser . "AND aup.userid=u.id AND aup.published='1' AND a.approved='1' AND a.enabled='1' AND (a.expire_date>='".$now."' OR a.expire_date='0000-00-00 00:00:00') AND r.id=a.rule"
			   . $displayactivity
			   . " ORDER BY a.insert_date DESC"
		 	   ;

		$db->setQuery($query, 0, $count);
		$rows = $db->loadObjectList();
	
		return $rows;
	
	}
}
?>