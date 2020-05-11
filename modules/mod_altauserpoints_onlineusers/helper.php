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

class modAltaUserPointsOnlineUsersHelper {

	public static function getList($params) {

		$db			      = JFactory::getDBO();
		$user			  = JFactory::getUser();
		$count		      = intval($params->get('count', 5));
		$showavatar		  = intval($params->get('showavatar', 1));
		$showself			= intval($params->get('showself', 0));
		$usrname		  = trim($params->get('usrname', 'name'));
		
		$nullDate	= $db->getNullDate();
		$date = JFactory::getDate();
		$now  = $date->toSql();				
			
		$q = "SELECT DISTINCT u.id AS userid, aup.points, u.".$usrname." AS usrname, aup.referreid FROM #__alpha_userpoints AS aup, #__users AS u, #__session AS s";
		$q .= " WHERE aup.userid=s.userid AND aup.published='1' AND s.userid=u.id AND s.guest='0' AND s.client_id='0' AND s.username!='' ";
		if(!$showself)
			$q .= " AND u.id!='".$user->id."' ";
		$q .= " ORDER BY aup.points DESC, u.$usrname ASC";
		$db->setQuery($q, 0, $count);
		$rows = $db->loadObjectList();
	
		return $rows;
	
	}
}
?>