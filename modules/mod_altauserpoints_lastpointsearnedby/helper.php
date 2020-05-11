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

class modAltaUserPointsLastPointsEarnedByHelper {

	public static function getList($params) {

		$db			      	= JFactory::getDBO();
				
		$count		     	= intval($params->get('count', 5));		
		$showdate 			= intval($params->get('showdate', 1));
		$usrname			= trim($params->get('usrname', 'name'));		
		
		$nullDate	= $db->getNullDate();
		$date = JFactory::getDate();
		$now  = $date->toSql();		
		
		$displayactivity = '';
		require_once (JPATH_ROOT.'/components/com_altauserpoints/helper.php');
		$version = AltaUserPointsHelper::getAupVersion();
		$displayactivity = " AND r.displayactivity='1'";		   
		$query = "SELECT a.insert_date, a.referreid, a.points AS last_points, u.".$usrname." AS usrname, aup.userid"
			   . " FROM #__alpha_userpoints_details AS a, #__alpha_userpoints AS aup, #__users AS u, #__alpha_userpoints_rules AS r"
			   . " WHERE aup.referreid=a.referreid AND aup.userid=u.id AND aup.published='1' AND a.approved='1' AND a.enabled='1' AND (a.expire_date>='".$now."' OR a.expire_date='0000-00-00 00:00:00') AND r.id=a.rule"
			   . $displayactivity
			   . " ORDER BY a.insert_date DESC"
		 	   ;

		$db->setQuery($query, 0, $count);
		$rows = $db->loadObjectList();
	
		return $rows;
	
	}
}
?>